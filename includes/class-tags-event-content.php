<?php // (C) Copyright Bobbing Wide 2015

/**
 * Implement TAGS Event tab content enhancement
 * 
 */
class TAGS_event_content extends TAGS_content {

	function __construct( $post ) {
		parent::__construct( $post );
		$tabs = array();
		if ( $this->event_passed() ) {
			$tabs[] = array( "results", "Results", array( $this, "results" ) );
		}
		$tabs[] = array( "players", "Players", array( $this, "players" ) );
		$tabs[] = array( "details", "Details", array( $this, "details" ) );
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
	
	function details() {
		e( "[bw_fields]" );
	}
	
	/**
	 * Display the players for the event
	 */
	function players() {
		//e( "[players]" );
		$content = sprintf( "[bw_table post_type=competitor meta_key=_event fields=_player,playing_status meta_value=%s numberposts=-1 orderby=title]" 
											, $this->post->ID
											);
		e( $content );										
	}
	
	/**
	 * Display the Results for the event
	 */
	function results() {
		$content = sprintf( '[bw_table post_type=result meta_key=_event fields=result_type,_player,_details,ID meta_value=%s numberposts=-1 orderby=result_type]'
											, $this->post->ID
											);
		e( $content ); 
	}
	
	/**
	 * Display the Course
	 */
	function course() {
		$course = get_post_meta( $this->post->ID, "_course", true );
		$content = sprintf( "[bw_pages post_type=course id=%s format=C/_/e thumbnail=full]", $course );
		e( $content );																			 
	}
	
	/**
	 * Display common information before the tabs
	 */
	function pre_tabs() {
		e( "[bw_fields featured,_tee_time,_cost]" );
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
