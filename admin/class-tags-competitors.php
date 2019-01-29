<?php // (C) Copyright Bobbing Wide 2015-2019

/**
 * TAGS_competitors
 *
 * 
 */
class TAGS_competitors {

	public $event;
	
	public $competitors = [];
	
	//private static $instance;
	
	private $posts = [];
	
	
	public $members = [];
	
	private $terms = [];
	
	/**
	 * Return a single instance of this class
	 */
	//public static function instance() {
	//	if ( !isset( self::$instance ) && !( self::$instance instanceof TAGS_competitors ) ) {
	//		self::$instance = new TAGS_competitors;
	//	}
	//	return self::$instance;
	//}
	
	/**
	 * Constructor for TAGS_competitors
	 */
	function __construct() {
	
	}
	
	/**
	 * Process the forms and display the competitors table
	 */
	function competitors_form() {
		$this->update_competitors();
		$this->add_additional_competitor();
		$this->display_competitors_table();
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
	 * Load the known competitors for this event
	 */
	function load_competitors() {
		if ( $this->get_event() ) {
			$atts = array( "post_type" => "competitor"
									 , "meta_key" => "_event"
									 , "meta_value" => $this->event->ID
									 , "orderby" => "title"
									 , "order" => "ASC"
									 , "numberposts" => -1
									 );
			$this->posts = bw_get_posts( $atts );
			c( "Posts:" . count( $this->posts ) );
		}
	
	}
	
	function populate_competitors() {
		if ( count( $this->posts ) ) {
			$competitors = array();
			foreach ( $this->posts as $post ) {
				$competitor = new TAGS_competitor( $post );
				$competitors[] = $competitor;
			}
			$this->competitors = $competitors;
		} else {
			$this->create_competitors();
		}
		
		c( "Competitors:" . count( $this->competitors ) );
	}
	
	function display_competitors_rows() {
		$competitors = $this->competitors;
		if ( count( $competitors ) ) {
			stag( "table" );
			bw_tablerow( bw_as_array( "Player,Status" ), "tr", "th" );
			foreach ( $competitors as $competitor ) {
				$competitor->display();
				$player_id = $competitor->player_ID();
				$this->done_member( $player_id );
			}
			etag( "table" );
		}
	}
	
	function load_members() {
	
		if ( $this->get_event() ) {
			$atts = array( "post_type" => "player"
									 , "membership" => "member"
									 , "orderby" => "title"
									 , "order" => "ASC"
									 , "numberposts" => -1
									 );
			$this->members = bw_get_posts( $atts );
			p( "Members:" . count( $this->members ) );
		}
	}
	
	/**
	 * Remove members who are already catered for
	 */
	function done_member( $player_id ) {
		foreach ( $this->members as $key => $member ) {
			if ( $member->ID == $player_id ) {
				//p( "Removing $player_id from members" ); 
				unset( $this->members[ $key ] );
				break;
			}
		} 
	
	}
	
	/**
	 * Display rows for members that aren't yet shown as competitors
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
	 * Update the Competitors playing statuses
	 *
	 * The form consists of a series of radio buttons for the 'competitor' ID and playing status
	 * and more the the 'player' ID and potential playing status
	 * `
   * [page] => tags_competitors
   * [event] => 478
   * [competitor] => Array
   *     (
   *         [2592] => yes
   *     )
	 *
   * [player] => Array
   *     (
   *         [203] => tbc
   *     )
	 *
   * [_tags_update_competitors] => Update
   * `
	 */
	function update_competitors() {
		if ( $submit = bw_array_get( $_REQUEST, "_tags_update_competitors", null ) ) {
			c( "Updating competitors?" );
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
		$verified = bw_verify_nonce( "_tags_update_competitors", "_tags_$ID" );
		c( "Verified: $verified" );
		return( $verified );
	}
	
	/**
	 * Update the playing status of each existing competitor
	 
	 * 1. Validate the competitor exists by getting the term data
	 * 2. See if it's changed
	 * 3. If so, then update
	 *
	 
	 */
	function update_existing() {
		$competitors = bw_array_get( $_REQUEST, "competitor", array() );
		foreach ( $competitors as $ID => $status ) {
			if ( $this->changed_terms( $ID, $status ) ) {
			p( "Updating $ID $status" ); 
				$this->update_terms( $ID, $status );
			}
		}
	}
	
	/**
	 * Update the terms ( in $this->terms )
	 * setting the new status and removing any previous status in the same group: yes, no, tbc
	 * 
	 */
	
	function update_terms( $ID, $status ) {
		$terms = array_diff( $this->terms, array( "yes", "no", "tbc" ) );
		$terms[] = $status;
		$this->terms = $terms;
		//print_r( $terms );
		wp_set_object_terms( $ID, $terms, "playing_status", false );
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
		foreach ( $players as $ID => $status ) {
			c( "Adding $ID $status" );
			$this->add_competitor( $ID, $status );
		}
	}
	
	/**
	 * Add a competitor to the event
	 *
	 * @TODO - Tidy events so that don't have to mess with -'s
	 *
	 * @param integer $ID the player ID
	 * @param string $status the playing status to be created: yes, no, tbc
	 */
	function add_competitor( $ID, $status ) {
		$event_title = $this->event->post_title;
		$event_title = str_replace( "-", "&#8211;", $event_title );
		$player = get_post( $ID );
		$player_title = $player->post_title;
		$metadesc = "$event_title $player_title";
		$post = array( "post_type" => "competitor"
								 , "post_title" => $metadesc
								 , "post_name" => $metadesc
								 , "post_status" => "publish"
								 , "post_content" => "<!--more-->"
								 );
		$_POST['_event'] = $this->event->ID ;
		$_POST['_player'] = $player->ID; 
		$id = wp_insert_post( $post, true );
		wp_set_object_terms( $id, $status, "playing_status" );
		update_post_meta( $id, "_yoast_wpseo_metadesc", $metadesc );
		return( $id );
	}
	
	/**
	 *
	 */
	function create_competitors() {
		if ( $this->event ) {
			p( "Creating competitors for event?" . $this->event->ID );
		} else { 
			p( "Select the event" );
		}
	}
	
	function event_selector() {
		$current_event = $this->get_event();
		if ( !$current_event ) {
			$current_event = $this->next_event();
		}
		bw_form_field_noderef( "event", "", "Select the event", $current_event, array( "#type" => array( "event"), "#optional" => true ));
	
	}

	/**
	 * Return the Event post for the next event
	 * [bw_related post_type="event" numberposts=1 meta_key="_date" order=ASC meta_compare="GE" meta_value=.
	 * orderby=meta_value format="L/F/_/C M e" thumbnail=full exclude=12926 class=next-event]
	 */
	function next_event() {
		$atts = array( "post_type" => "event"
		, "meta_key" => "_date"
		, "meta_value" => bw_format_date()
		, "meta_compare" => ">="
		, "numberposts" => 1
		, "post_parent" => 0
		, "orderby" => "meta_value"

		);
		$posts = bw_get_posts( $atts );
		//print_r( $posts );
		if ( $posts  && count( $posts ) ) {
			$event = $posts[0];
		} else {
			$event = null;
		}
		return $event;

	}

	/**
	 * Display the competitors table
	 */
	function display_competitors_table() {
		$this->load_competitors();
		$this->load_members();
		$this->populate_competitors();
		bw_form();
		$this->event_selector();
		p( isubmit( "_tags_refresh_competitors", "Refresh",  null, "button-secondary" ) );
		$this->display_competitors_rows();
		$this->display_members_rows();
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
			p( isubmit( "_tags_update_competitors", "Update", null, "button-secondary" ) );
			e( wp_nonce_field( "_tags_update_competitors", "_tags_" . $this->event->ID, true, false ) );
		}
	}
	
	/**
	 * Add an additional player
	 * 
	 */
	function add_additional_competitor() {
	
		if ( $submit = bw_array_get( $_REQUEST, "_tags_add_player", null ) ) {
			p( "Adding additional player" );
			if ( $this->verify_nonce() ) {
				p( "Nonce verified" );
			 	$ID = bw_array_get( $_REQUEST, "player", null );
				$status = bw_array_get( $_REQUEST, "_playing_status", null );
				$slug = $this->term_slug( $status );
				if ( $ID && $slug ) {
					$this->add_competitor( $ID, $slug );
				}
			} else {
				p( "Nonce not verified" );
			}
		}	
		
	}
	
	/**
	 * Add an additional player (  competitor  )
	 *
	 * Note: We use the same nonce field as for the player list
	 * The user can only press one submit button
	 * but could we programmatically submit both?
	 *
	 */
	function add_competitor_form() {
		if ( $this->event ) {
			bw_form();
			$this->event_selector();
			$this->player_selector();
			$this->playing_status();
			p( isubmit( "_tags_add_player", "Add a player", null, "button-secondary" ) );
			
			e( wp_nonce_field( "_tags_update_competitors", "_tags_" . $this->event->ID, true, false ) );
			etag( "form" );
			bw_flush();
		} else {
			p( "Select an Event first" );
		}	
	}
	
	function player_selector() {
		bw_form_field_noderef( "player", "", "Select the player", "", array( "#type" => array( "player") ));
	}
	
	function playing_status() {
		$terms = get_terms( "playing_status", array( "hide_empty" => false, "order" => "DESC" ) );
		$term_array = bw_term_array( $terms );
		bw_form_field_select( "_playing_status", "select", "Select the status", "", array( "#options" => $term_array ) );
	}
	
	/* 
	 * Map the term ID to a slug	
	 * 
	 * @param integer $status
	 * @return string the slug	
	 */
	function term_slug( $status ) {
		$term = get_term( $status, "playing_status" );
		return( $term->slug );
	} 		



}

