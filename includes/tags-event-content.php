<?php // (C) Copyright Bobbing Wide 2015, 2016

/**
 * Enhance the content for an Event
 *
 * _date today or in the future - show players
 * _date today or in the past - show results
 * 
 * If there are no players nor results then we should display Details
 * 
 *
 */
function tags_lazy_event_content( $post ) {
  if ( is_single() ) {  
		oik_require( "includes/class-tags-content.php", "tags" );
		oik_require( "includes/class-tags-event-content.php", "tags" );
		oik_require( "includes/class-tags-tab.php", "tags" );
		
		$event = new TAGS_event_content( $post );
		//$event->display();
		
	
		do_action( "oik_add_shortcodes" );
		return( $event->content() );
		
	}
	
	return( $post->post_content );
	

}
