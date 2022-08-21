<?php // (C) Copyright Bobbing Wide 2015-2019

/**
 * Implement TAGS Event tab content enhancement
 * 
 */
class TAGS_event_content extends TAGS_content {

	//private $query_results;

	function __construct( $post ) {
		parent::__construct( $post );
		$tabs = array();
		$event_passed = $this->event_passed();
		if ( $event_passed && $this->query_results() ) {
			$tabs[] = array( "results", "Results", array( $this, "results" ) );
		}
		if ( $event_passed ) {
			$tabs[] = array( "details", "Details", array( $this, "details" ) );
		}
		if ( $this->query_players() ) {
			$tabs[] = array( "players", "Players", array( $this, "players" ) );
		}
		if ( !$event_passed ) {
			$tabs[] = array( "details", "Details", array( $this, "details" ) );
		}
		$tabs[] = array( "course", "Course", array( $this, "course" ) );
		$this->set_tabs( $tabs );
		$this->enhance_content(); 
  }
	
	/**
	 * Determine which tab should be the default based on the _date of the event
	 *
	 */
	function event_date() {
		$date = get_post_meta( $this->post->ID, "_date", true );
		$event_date = bw_format_date( $date, "U" );
		//echo "Date: $date: $event_date" ;
		return( $event_date );
	}
	
	/**
	 * The event has passed if the event_date is less than or equal to the current time
	 */
	function event_passed() {
		$event_date = $this->event_date();
		return( $event_date <= time() );
	}
	
	/**
	 * Display the Details
	 * 
	 */
	function details() {
		e( $this->content );
		e( "[bw_fields]" );
	}
	
	/**
	 * Display the players for the event
	 *
	 * We also want to display a count of the players by playing_status
	 * Can we use bw_count to do this? - count posts grouping by taxonomy
	 */
	function players() {
		//e( "[players]" );
		$content = sprintf( "[bw_table post_type=competitor meta_key=_event fields=_player,playing_status meta_value=%s numberposts=-1 orderby=title order=ASC]"
											, $this->post->ID
											);
		e( $content ); 
		$groups = sprintf( "[bw_group post_type=competitor meta_key=_event fields=_player,playing_status meta_value=%s numberposts=-1 field='playing_status']"
										, $this->post->ID
									  );#
		e( $groups );									
	  									
	}
	
	/**
	 * Query players 
	 * 
	 * @return array array of posts in the players
	 */
	function query_players() {
		oik_require( "includes/bw_posts.php" );
		$atts = array( "post_type" => "competitor" 
								 , "meta_key" => "_event"
								 , "meta_value" => $this->post->ID
								 , "numberposts" => -1
								 );
		$posts = bw_get_posts( $atts );
		return( $posts );
	}

	/**
	 * Display the Results for the event
	 * 
	 * @TODO - Cannot actually order by result_type - WordPress ignores this. See TRAC #18616
	 * so it orders by the date ASC - which is fine for stuff in the past
	 */
	function results() {
		if ( false ) {
			$this->local_results();
		} else {
			$content=sprintf( '[bw_table post_type=result meta_key=_event fields=result_type,_player,_details meta_value=%s numberposts=-1 orderby=ID ]'
				, $this->post->ID
			);
			e( $content );
		}
	}

	/**
	 * An attempt to build an alternative to the bw_table solution in results()
	 * before I changed the orderby=result_type attribute to orderby=ID
	 * ... which works because I create the results in the correct order using TAGS admin > results.
	 */
	function local_results() {
		if ( $this->query_results ) {
			foreach ( $this->query_results as $query_result ) {
			   $result_type = get_the_term_list( $query_result->ID, 'result_type', "", ",", "" );
			   $player = bw_custom_column_post_meta( '_player', $query_result->ID );
			   $details = bw_custom_column_post_meta( '_details', $query_result->ID );

			}
		}
	}

	/**
	 * Query results 
	 * 
	 * @return array array of posts in the results set
	 */
	function query_results() {
		oik_require( "includes/bw_posts.php" );
		$atts = array( "post_type" => "result" 
								 , "meta_key" => "_event"
								 , "meta_value" => $this->post->ID
								 , "numberposts" => -1
								 );
		$posts = bw_get_posts( $atts );
		//$this->query_results = $posts;
		return( $posts );
	}

	/**
	 * Display the Course
	 */
	function course() {
		$course = get_post_meta( $this->post->ID, "_course", true );
		$content = sprintf( "[bw_pages post_type=course id=%s format=C/_/e thumbnail=full]", $course );
		$content = tags_the_post_course( $course, $content );
		e( $content );

	}
	
	/**
	 * Display common information before the tabs
	 * 
	 * Note: Some of these fields may be defined with #theme_null = false, meaning they won't be
	 * displayed if the content is not set. 
	 */
	function pre_tabs() {
		e( "[bw_fields featured,_tee_time,_cost,_notes]" );
	}
	

}

/**



  $oik_tab = bw_array_get( $_REQUEST, "oik-tab", "description" ); 
  $additional_content = oikp_additional_content_links( $post, $oik_tab );
  if ( $oik_tab ) {
    $tabs = array( "description" => "oikp_display_description"
                 , "faq" => "oikp_display_faq"
                 , "screenshots" => "oikp_display_screenshots"
                 , "changelog" => "oikp_tabulate_pluginversion" 
                 , "shortcodes" => "oikp_display_shortcodes" 
                 , "apiref" => "oikp_display_apiref"
                 , "documentation" => "oikp_display_documentation" 
                 );
    $oik_tab_function = bw_array_get( $tabs, $oik_tab, "oikp_display_unknown" );
    if ( $oik_tab_function ) {
      if ( is_callable( $oik_tab_function ) ) {
        $additional_content .= $oik_tab_function( $post, $slug );
      } else {
        $additional_content .= "Missing: $oik_tab_function";
      }
    }  
  }
  $additional_content .= "</div>";
  return( $additional_content );
*/
