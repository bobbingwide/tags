<?php // (C) Copyright Bobbing Wide 2015

/**
 * TAGS_results
 *
 * 
 */
class TAGS_results {

	public $event;
	
	public $results;
	
	private $posts;
	
	/**
	 * Array of player IDs to name
	 */
	public $competitors;
	
	private $terms;
	
	
	/**
	 * Constructor for TAGS_results
	 */
	function __construct() {
	
	}
	
	/**
	 * Process the forms and display the results table
	 */
	function results_form() {
		$this->update_results();
		$this->add_additional_result();
		$this->display_results_table();
  }
	
	function get_event() {
		$event_id = bw_array_get( $_REQUEST, "event", null );
		$valid = $this->validate_event( $event_id );
		return( $valid );
	}
	
	function validate_event( $event_id ) {
		$this->event = null;
		$event_id = intval( $event_id );
		if ( $event_id ) {
			$event = get_post( $event_id );
			if ( $event && $event->post_type === "event" ) {
				$this->event = $event;
			}
		}
		return( $this->event );
		
	}
	
	
	/**
	 * Load the known results for this event
	 */
	function load_results() {
		if ( $this->get_event() ) {
			$atts = array( "post_type" => "result"
									 , "meta_key" => "_event"
									 , "meta_value" => $this->event->ID
									 , "orderby" => "result_type"
									 , "order" => "ASC"
									 , "numberposts" => -1
																		 );
			$this->posts = bw_get_posts( $atts );
			c( "Posts:" . count( $this->posts ) );
		}
	
	}
	
	/**
	 * Populate the results
	 */
	
	function populate_results() {
		if ( count( $this->posts ) ) {
			$results = array();
			foreach ( $this->posts as $post ) {
				$result = new TAGS_result( $post, $this );
				$results[] = $result;
			}
			$this->results = $results;
		} else {
			$this->create_results();
		}
		
		p( "results:" . count( $this->results ) );
	}
	
	function display_results_rows() {
		$results = $this->results;
		if ( count( $results ) ) {
			stag( "table" );
			bw_tablerow( bw_as_array( "Result,Event,Player,Type,Details" ), "tr", "th" );
			foreach ( $results as $result ) {
				$result->display();
			}
			etag( "table" );
		}
	}
	
	
	/**
	 * Load the competitors for the event
	 * 
	 * We need the IDs of the players
	 * which we obtain by finding the competitors
	 * then finding the player reference.
	 */
	
	function load_competitors() {
	
		if ( $this->get_event() ) {
			$atts = array( "post_type" => "competitor"
									 , "orderby" => "title"
									 , "order" => "ASC"
									 , "numberposts" => -1
									 , "meta_key" => "_event"
									 , "meta_value" => $this->event->ID
									 );
			$competitors = bw_get_posts( $atts );
			p( "competitors:" . count( $competitors ) );
			
			$players = array();
			foreach ( $competitors as $competitor ) {
				$player = get_post_meta( $competitor->ID, "_player", true );
				$players[ $player ] = get_the_title( $player );
			}
			$this->players = $players;
			
		}
	}
	
	
	/**
	 * Display rows for members that aren't yet shown as results
	 */
	function display_members_rows() {
		$members = $this->members;
		if ( count( $members ) ) {
			stag( "table", "narrowfat" );
			bw_tablerow( bw_as_array( "Member,Status" ), "tr", "th" );
			foreach ( $members as $member ) {
				$this->member_display( $member );
			}
			etag( "table" );
		}
	}
	
	/**
	 * Display unattached players
	 * 
	 * Display the list of members that have not been added for this event.
	 */
	function member_display( $member ) {
	
		$row = array();
		$row[] = $member->post_title;
		$row[] = icheckbox( "player_yes[{$member->ID}]", false );
		$row[] = icheckbox( "player_no[{$member->ID}]", false );
		$row[] = icheckbox( "player_tbc[{$member->ID}]", true );
		//bw_tablerow( $row );
		
		bw_radio( "player[{$member->ID}]", $member->post_title, array( "yes", "no", "tbc" ), array( "Yes", "No", "TBC" ), null, array( null, null, null )  );
	
	}
	
	/**
	 * Update the results playing statuses
	 *
	 * The form consists of a series of radio buttons for the 'result' ID and playing status
	 * and more the the 'player' ID and potential playing status
	 * `
   * [page] => tags_results
   * [event] => 478
   * [result] => Array
   *     (
   *         [2592] => yes
   *     )
	 *
   * [player] => Array
   *     (
   *         [203] => tbc
   *     )
	 *
   * [_tags_update_results] => Update
   * `
	 */
	function update_results() {
		if ( $submit = bw_array_get( $_REQUEST, "_tags_update_results", null ) ) {
			c( "Updating results?" );
			if ( $this->verify_nonce() ) {
				c( "Nonce verified" );
				$this->update_existing();
				$this->add_players();
			} else {
				c( "Nonce not verified" );
			}
		}
			
	}
	
	
	/**
	 * Verify the nonce
	 *
	 * It must match with the event ID that should not have been changed
	 * before hitting the "Update" or "Refresh" button
	 * "Refresh" should be allowed at any time
	 * "Update" must be nonce checked.
	 */
	function verify_nonce() {
		$event = $this->get_event();
		$ID = $event->ID;
		$verified = bw_verify_nonce( "_tags_update_results", "_tags_$ID" );
		c( "Verified: $verified" );
		return( $verified );
	}
	
	/**
	 * Update the playing status of each existing result
	 
	 * 1. Validate the result exists by getting the term data
	 * 2. See if it's changed
	 * 3. If so, then update
	 *
	 
	 */
	function update_existing() {
		$results = bw_array_get( $_REQUEST, "result", array() );
		foreach ( $results as $ID => $status ) {
			gob();
		}
	}
	
	/**
	 * Update the terms ( in $this->terms )
	 * setting the new status and removing any previous status in the same group: yes, no, tbc
	 * 
	 */
	
	function update_terms( $ID, $status ) {
		//$terms = array_diff( $this->terms, array( "yes", "no", "tbc" ) );
		//$terms[] = $status;
		//$this->terms = $terms;
		//print_r( $terms );
		wp_set_object_terms( $ID, $terms, "result_type", false );
	}
		
		
	
	function changed_terms( $ID, $status ) {
		$changed = false;
		$this->terms = wp_get_post_terms( $ID, "playing_status" , array( "fields" => "slugs" )  );
		//print_r( $this->terms );
		$changed = !in_array( $status, $this->terms );
		return( $changed );
	}
	
	function add_players() {
		$players = bw_array_get( $_REQUEST, "player", array() );
		foreach ( $players as $ID => $result->type ) {
			gob();
		}
	}
	
	/**
	 * Add a result to the event
	 *
	 * @TODO - Tidy events so that don't have to mess with -'s
	 *
	 * @param integer $ID the player ID
	 * @param string $status the playing status to be created: yes, no, tbc
	 */
	function add_result( $ID, $status ) {
		$event_title = $this->event->post_title;
		$event_title = str_replace( "-", "&#8211;", $event_title );
		$player = get_post( $ID );
		$player_title = $player->post_title;
		$result_type = $this->result_type( $status );
		$metadesc = "$event_title - $result_type - $player_title";
		$post = array( "post_type" => "result"
								 , "post_title" => $metadesc
								 , "post_name" => $metadesc
								 , "post_status" => "publish"
								 , "post_content" => "<!--more-->[bw_fields]"
								 );
		$_POST['_event'] = $this->event->ID ;
		$_POST['_player'] = $player->ID; 
		$_POST['_details'] = $this->details();
		$id = wp_insert_post( $post, true );
		wp_set_object_terms( $id, $status, "playing_status" );
		update_post_meta( $id, "_yoast_wpseo_metadesc", $metadesc );
		return( $id );
	}
	
	/**
	 *
	 */
	function create_results() {
		if ( $this->event ) {
			p( "Creating results for event?" . $this->event->ID );
		} else { 
			p( "Select the event" );
		}
	}
	
	function event_selector() {
		$current_event = $this->get_event();
		bw_form_field_noderef( "event", "", "Select the event", $current_event, array( "#type" => array( "event"), "#optional" => true ));
	
	}

	/**
	 * Display the results table
	 */
	function display_results_table() {
		$this->load_results();
		$this->load_competitors();
		$this->populate_results();
		bw_form();
		$this->event_selector();
		p( isubmit( "_tags_refresh_results", "Refresh",  null, "button-secondary" ) );
		$this->display_results_rows();
		$this->buttons();
		etag( "form" );
		bw_flush();

	}
	
	/**
	 * Display the Update button if an event has been selected
	 *
	 * Include a nonce field for the event ID
	 */	
	function buttons() {
		if ( $this->event ) { 
			p( isubmit( "_tags_update_results", "Update", null, "button-secondary" ) );
			e( wp_nonce_field( "_tags_update_results", "_tags_" . $this->event->ID, true, false ) );
		}
	}
	
	/**
	 * Add an additional player
	 * 
	 */
	function add_additional_result() {
	
		if ( $submit = bw_array_get( $_REQUEST, "_tags_add_player", null ) ) {
			p( "Adding additional player" );
			if ( $this->verify_nonce() ) {
				p( "Nonce verified" );
			 	$ID = bw_array_get( $_REQUEST, "player", null );
				$status = bw_array_get( $_REQUEST, "_playing_status", null );
				$slug = $this->term_slug( $status );
				if ( $ID && $slug ) {
					$this->add_result( $ID, $slug );
				}
			} else {
				p( "Nonce not verified" );
			}
		}	
		
	}
	
	/**
	 * Add an additional player (  result  )
	 *
	 * Note: We use the same nonce field as for the player list
	 * The user can only press one submit button
	 * but could we programmatically submit both?
	 *
	 */
	function add_result_form() {
		if ( $this->event ) {
			bw_form();
			$this->event_selector();
			$this->player_selector();
			$this->result_type();
			//$this->details_field();
			p( isubmit( "_tags_add_player", "Add a player", null, "button-secondary" ) );
			
			e( wp_nonce_field( "_tags_update_results", "_tags_" . $this->event->ID, true, false ) );
			etag( "form" );
			bw_flush();
		} else {
			p( "Select an Event first" );
		}	
	}
	
	function player_selector() {
		bw_form_field_noderef( "player", "", "Select the player", "", array( "#type" => array( "player") ));
	}
	
	function result_type() {
		$terms = get_terms( "result_type", array( "hide_empty" => false, "order" => "ASC" ) );
		$term_array = bw_term_array( $terms );
		bw_form_field_select( "_result_type", "select", "Result type", "", array( "#options" => $term_array ) );
	}
	
	/* 
	 * Map the term ID to a slug	
	 * 
	 * @param integer $status
	 * @return string the slug	
	 */
	function term_slug( $status ) {
		$term = get_term( $status, "result_type" );
		return( $term->slug );
	}
	
	/**
	 * Return the details for this result
	 */ 
	function details() {
		$details = bw_array_get( $_REQUEST, "_details", null ); 
		return( $details );
	}		



}

