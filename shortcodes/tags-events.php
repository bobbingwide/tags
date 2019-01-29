<?php // (C) Copyright Bobbing Wide 2015-2019



/**
 * Return the singular instance of the TAGS_event class
 *
 * @return object the TAGS event class
 */
function tags_event() {
	if ( !class_exists( "TAGS_event" ) ) {
		oik_require( "includes/class-tags-event.php", "tags" );
	}
	if ( class_exists( "TAGS_event" ) ) {
		$tags_event = TAGS_event::instance();
	} else {
		die();
	}
	return( $tags_event );
}

/**
 * Implement [tags_events] shortcode
 *
 * @TODO Implement as a nested list to look like a menu 
 * cacheing the output so we can quickly select the events for each year
 * or filter them by date?
 * 
 * 
 * @param array $atts shortcode parameters
 * @param string $content - not expected
 * @param string $tag - the shortcode name
 * @return string the generated HTML
 */ 										 
function tags_events( $atts=null, $content=null, $tag=null ) {
	oik_require( "includes/bw_posts.php" );
	$attr = array( "post_type" => "event" 
							 //, "posts_per_page" => 20
							 , "orderby" => "meta_value"
							 , "order" => "DESC"
							 , "meta_key" => "_date"
							 , "meta_value" => 0
							 , "meta_compare" => ">="
							 , "exclude" => -1 
							 , "numberposts" => -1
							 );
	oik_require( "shortcodes/oik-navi.php" );
	$ret = bw_navi( $attr );
	return( $ret );
								 
	//$atts = oik_navi_shortcode_atts( $atts );
	if ( false ) {
	$posts = bw_get_posts( $attr ); 
							 
	oik_require( "includes/class-tags-event.php", "tags" );
							
	foreach ( $posts as $post ) {
		$event = new TAGS_event( $post );
	
		$event->year_link();
		$event->link();
		
	}	
	}

	return( bw_ret() );
}
