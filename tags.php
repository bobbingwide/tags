<?php // (C) Copyright Bobbing Wide 2015

/*
Plugin Name: TAGS 
Plugin URI: http://www.bobbingwide.com/oik-plugins/tags
Description: The Anchor Golf Society 
Version: 0.0.1
Author: bobbingwide
Author URI: http://www.oik-plugins.com/author/bobbingwide
Text Domain: tags
Domain Path: /languages/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

    Copyright 2015 Bobbing Wide (email : herb@bobbingwide.com )

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License version 2,
    as published by the Free Software Foundation.

    You may NOT assume that you can use any other version of the GPL.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    The license for this software can likely be found here:
    http://www.gnu.org/licenses/gpl-2.0.html

*/


tags_loaded();

/**
 * Function to invoke when plugin loaded
 *
 * Behave accordingly
 */
function tags_loaded() {

	if ( PHP_SAPI == "cli" ) {
		if ( $_SERVER['argv'][0] == "boot-fs.php" )   {
			// This is WP-CLI
		} else {
			//oik_require_lib( "oik-cli" );
			oik_batch_load_cli_functions();
			if ( oik_batch_run_me( __FILE__ ) ) {
				//td2w_run();
				echo "End cli:" . __FUNCTION__ . PHP_EOL; 
			}
		}
	} else {
	 
		
		//echo PHP_SAPI;
		//echo PHP_EOL;
		if ( function_exists( "bw_trace2" ) ) {
			bw_trace2( PHP_SAPI, "tags loaded in WordPress environment?" );
		}
	}	
	
	if ( function_exists( "add_action" ) ) {
		// if ( bw_is_wordpress() ) {
		//add_action( "admin_notices", "oik_batch_activation" );
		add_action( "oik_fields_loaded", "tags_oik_fields_loaded" );
		add_action( "oik_add_shortcodes", "tags_oik_add_shortcodes" );
		add_action( "admin_menu", "tags_admin_menu" );
		add_filter( 'set-screen-option', "tags_set_screen_option", 10, 3 );
		add_action( 'run_tags.php', "td2w_run" );
		add_action( 'the_content', "tags_the_content", 1, 1 );
	}
	
	

}

/**
 * Implement an admin menu
 */
function tags_admin_menu() {
	oik_require( "admin/tags.php", "tags" );
	tags_lazy_admin_menu();
}

/**
 * Implement "oik_fields_loaded" for TAGS to register CPTs
 */
function tags_oik_fields_loaded() {
	tags_register_categories();
	tags_register_post_types();
}

/**	
 * Implement "oik_add_shortcodes" for TAGS
 */
function tags_oik_add_shortcodes() {
	bw_add_shortcode( "tags_events", "tags_events", oik_path( "shortcodes/tags-events.php", "tags"), false );
}



/**
 * Register custom taxonomies
 *
 * Note: It's probably not a good idea to have a post_type of 'result' and a custom taxonomy with the same name.
 * So using 'result_type' for the results taxonomy
 *
 *
 * vocabulary and vocabulary node types
 *
 * vocab |  vid | vocabulary node types |  relations |  hierarchy | multiple | required
 * ----- | ---- | --------------------- | ---------- | ---------- | -------- | --------
 * Newsletter | 2 | blog, event, simplenews, story | 0 | 1 | 0 | 0
 * Forums | 1 | forum| 0 | 1 | 0 | 1
 * Membership | 3 | player, profile | 1 | 1 | | 1
 * Result | 4 | result | 1 | 1 | 0 | 1
 */
function tags_register_categories() {
	//bw_register_custom_category( "forums", "Forums" );
  //bw_register_custom_category( "newsletter", "Newsletter" );
  bw_register_custom_category( "membership", null, "Membership" );
	bw_register_custom_category( "result_type", null, "Result" );
  $labels = array( "labels" => array( "singular_name" => __( "Status" ), "name" => __( "Statuses" ) ) );
	bw_register_custom_category( "playing_status", null, $labels );
}

/** 
 * Register the custom post types for TAGS
 *
 * In the Drupal system we had 1378 nodes to migrate
 *
 * Seq ### | node_type | post_type
 * --- | --------- | ------------
 * 0 | 0 | competitor |  n/a
 * 0 | 0 | forum | n/a 
 * 0 | 0 | panel | n/a
 * 0 | 0 | poll | n/a 
 * 0 | 0 | profile | n/a
 * 1 | 1 | story	| posts 
 * 1 | 83 | blog |	posts
 * 1 | 6 | simplenews | posts
 * 2 | 6 | page | pages 
 * 3 | 19 | trophy	
 * 4 | 41 | course |	course
 * 5 | 108 | player	| players	/ users
 * 6 | 171 | event | event
 * 7 | 93 | competitors | expanded to competitor = linking a player to an event with a playing_status
 * 8 | 784 | result | result = expanded so that there is only one player per result
 */
function tags_register_post_types() {
	tags_register_course();
	tags_register_trophy();
	tags_register_player();
	tags_register_event();
	tags_register_competitor();
	tags_register_result();
}
/**
 * Register a course 
 * 
 * A course is basically a location where an event takes place
 * 
 *
 * Fields:
 * - website URL 
 * - Address: street, additional, city, province, post code
 * - latitude & longitude
 * - Photo
 * 
 * - Length: in yards
 * - Par: 6x to 7x
 * - Holes 9 / 18
 * - ?
 * 
 
 */ 
function tags_register_course() {
	$post_type = "course";
  $post_type_args = array();
  $post_type_args['label'] = 'Courses';
	$post_type_args['singular_label'] = 'Course';
  $post_type_args['description'] = 'Location where an event takes place';
  $post_type_args['supports'] = array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'author' );
  $post_type_args['has_archive'] = true;
  $post_type_args['menu_icon'] = 'dashicons-location-alt';
  bw_register_post_type( $post_type, $post_type_args );
	
  bw_register_field( "_url", "url", "Website" ); 
  bw_register_field( "_address", "textarea", "Address" ); 
	bw_register_field( "_post_code", "text", "Post Code" );
	bw_register_field( "_lat", "numeric", "Latitude" );
	bw_register_field( "_long", "numeric", "Longitude" );
	bw_register_field( "_nid", "numeric", "Original node ID", array( '#theme' => false ) );
	
	bw_register_field_for_object_type( "_url", $post_type );
	bw_register_field_for_object_type( "_address", $post_type );
	bw_register_field_for_object_type( "_post_code", $post_type );
	bw_register_field_for_object_type( "_lat", $post_type );
	bw_register_field_for_object_type( "_long", $post_type );
	bw_register_field_for_object_type( "_nid", $post_type );

}

/**
 * Register a trophy
 *
 * We used to play for quite a lot of trophies, now we only have a few.
 * Don't really need excerpt.
 * We could probably just make this an attachment with a category of Trophy
 */
function tags_register_trophy() {
	$post_type = "trophy";
  $post_type_args = array();
  $post_type_args['label'] = 'Trophies';
	$post_type_args['singular_label'] = 'Trophy';
  $post_type_args['description'] = 'Trophy being played for';
  $post_type_args['supports'] = array( 'title', 'editor', 'thumbnail', 'excerpt' );
  $post_type_args['has_archive'] = true;
  $post_type_args['menu_icon'] = 'dashicons-shield-alt';
  bw_register_post_type( $post_type, $post_type_args );
	bw_register_field_for_object_type( "_nid", $post_type );
}

/**
 * Register an event
 *
 * Fields from 'content_type_event'
 *
 * field_trophy_nid -> _trophy
 * field_course_nid -> _course
 * 
 */
function tags_register_event() { 
	$post_type = "event";
  $post_type_args = array();
  $post_type_args['label'] = 'Events';
  $post_type_args['description'] = 'Event - competition or meeting';
  $post_type_args['supports'] = array( 'title', 'editor', 'thumbnail', 'excerpt', 'home', 'publicize', 'author' );
  $post_type_args['has_archive'] = true;
  $post_type_args['menu_icon'] = 'dashicons-flag';
  bw_register_post_type( $post_type, $post_type_args );
	
  bw_register_field( "_course", "noderef", "Course", array( "type" => "course", "#optional" => true ) ); 
	bw_register_field( "_date", "date", "Date" );
	bw_register_field( "_tee_time", "time", "First tee" );
	bw_register_field( "_cost", "currency", "Cost" );
  bw_register_field( "_trophy", "noderef", "Trophy", array( "type" => "trophy", "#optional" => true ) ); 
	bw_register_field( "_shirt", "text", "Shirt colour" );
	bw_register_field( "_ntps", "select", "NTPs", array( "#multiple" => 18, '#options' => tags_holes() ) );
	bw_register_field( "_notes", "textarea", "Notes" );
	
	bw_register_field_for_object_type( "_course", $post_type );
	bw_register_field_for_object_type( "_date", $post_type );
	bw_register_field_for_object_type( "_tee_time", $post_type );
	bw_register_field_for_object_type( "_cost", $post_type );
	bw_register_field_for_object_type( "_trophy", $post_type );
	bw_register_field_for_object_type( "_shirt", $post_type );
	bw_register_field_for_object_type( "_ntps", $post_type );
	bw_register_field_for_object_type( "_notes", $post_type );
	
	bw_register_field_for_object_type( "_nid", $post_type );
	
}

/**
 * Return the hole IDs
 */
function tags_holes() {
	$holes = array( 0 => "None" );
	for ( $hole= 1; $hole<=18; $hole++ ) {
		$holes[$hole] = $hole;
	}
	return( $holes );
}

/**
 * Register the competitor post type
 *
 * _event
 * _player
 * playing_status - 
 
 */
function tags_register_competitor() {
	$post_type = "competitor";
  $post_type_args = array();
  $post_type_args['label'] = 'Competitors';
  $post_type_args['description'] = 'Competitor at an event';
  $post_type_args['supports'] = array( 'title', 'editor', 'author', 'revisions' );
  $post_type_args['has_archive'] = true;
  $post_type_args['menu_icon'] = 'dashicons-tag';
	$post_type_args['taxonomies'] = array( "playing_status" );
	
  bw_register_post_type( $post_type, $post_type_args );
	bw_register_field_for_object_type( "_event", $post_type );
	bw_register_field_for_object_type( "_player", $post_type );
	bw_register_field_for_object_type( "_nid", $post_type );
} 

/**
 * Register the result post type
 
 * Fields from 'content_type_result'
 *
 * _event - single select
 * _player - single select
 * _details - text field . e.g. actual result, number birdies, which hole for NTP
 * Uses custom taxonomy - result_type 
 */
function tags_register_result() {

	$post_type = "result";
  $post_type_args = array();
  $post_type_args['label'] = 'Results';
  $post_type_args['description'] = 'Result of an event';
  $post_type_args['supports'] = array( 'title', 'home', 'publicize' , 'editor'  );
  $post_type_args['has_archive'] = true;
  $post_type_args['menu_icon'] = 'dashicons-awards';
	$post_type_args['taxonomies'] = array( "result_type" );
  bw_register_post_type( $post_type, $post_type_args );
	
	bw_register_field( "_event", "noderef", "Event", array( "type" => "event" ) );
	bw_register_field( "_player", "noderef", "Player", array( "type" => "player" ) );
	bw_register_field( "_details", "text", "Details" );
	
	bw_register_field_for_object_type( "_event", $post_type ); 
	bw_register_field_for_object_type( "_player", $post_type );
	bw_register_field_for_object_type( "_details", $post_type );
	
	bw_register_field_for_object_type( "_nid", $post_type );

}

/**
 * Register the player post type
 *
 * We keep players separate from users since we have players who will never be users of the site
 * So each player has an optional userref.
 *
 * We can either import the fields from content_type_player ( _woods, _irons, _putter, _ball ) into the main content.
 * or create fields
 *
 
 */
function tags_register_player() {
	//oik_require( "includes/tags-users.php", "tags" );
	//tagu_lazy_register_players();
	$post_type = "player";
  $post_type_args = array();
  $post_type_args['label'] = 'Players';
  $post_type_args['description'] = 'Player at an event';
  $post_type_args['supports'] = array( 'title', 'editor', 'thumbnail', 'excerpt', 'home', 'publicize', 'author' );
  $post_type_args['has_archive'] = true;
  $post_type_args['menu_icon'] = 'dashicons-admin-users';
	$post_type_args['taxonomies'] = array( "membership" );
  bw_register_post_type( $post_type, $post_type_args );
	
	bw_register_field( "_user", "userref", "User" );
	bw_register_field( "_handicap", "numeric", "Handicap" );
	bw_register_field( "_uid", "numeric", "Original user ID", array( '#theme' => false ) );
	//bw_register_field( "_clubs"
	
	bw_register_field_for_object_type( "_user", $post_type );
	bw_register_field_for_object_type( "_handicap", $post_type );
	bw_register_field_for_object_type( "_nid", $post_type );
	bw_register_field_for_object_type( "_uid", $post_type );

}


/**
 * Run the migration from Drupal to WordPress
 *
 */
function td2w_run() {
	//do_action( "init" );
	tags_oik_fields_loaded();
	oik_require( "includes/tagsd2w.php", "tags" );
	td2w_lazy_run();
}

/**
 * The Event content display should consist of a number of sections or tabs
 * to match how it was displayed in anchorgolf.co.uk
 
 [bw_table post_type=competitor meta_key=_event fields=_player,playing_status meta_value=480 numberposts=-1 orderby=_player]

[bw_table post_type=result meta_key=_event fields=result_type,_player,_details,ID meta_value=480 numberposts=-1 orderby=result_type] 

 
	tags_register_competitor();
	tags_register_result();

*/														
															
															
function tags_the_content( $content ) {
  global $post;
  if ( $post ) {
    switch ( $post->post_type ) {
      case "event": 
        $content = tags_the_post_event( $post, $content );
        break;
          
      case "player":
				$content = tags_the_post_player( $post, $content );
				break;
			
			case "trophy": 
        $content = tags_the_post_trophy( $post, $content ); 
        break;
				
			case "course":
				$content = tags_the_post_course( $post, $content );
				break;	
    }
  }  
  return( $content );
}

/**
 * Add some Event content before 'the_content' filtering 
 * 
 * @param post $post
 * @param string $content - the current content
 * @return string - the updated content
 */
function tags_the_post_event( $post, $content ) {
  if ( true || is_single() ) {
    oik_require( "includes/tags-event-content.php", "tags" );
    $content = tags_lazy_event_content( $post );
  }
	return( $content );
}

/**
 * Add some Player content before 'the_content' filtering 
 * 
 * @param post $post
 * @param string $content - the current content
 * @return string - the updated content
 */
function tags_the_post_player( $post, $content ) {
	if ( !is_user_logged_in() ) {
		$content = str_replace( "[bw_fields]", "", $content );
	}
	return( $content );
}

/**
 * Add some Trophy content before 'the_content' filtering 
 * 
 * @param post $post
 * @param string $content - the current content
 * @return string - the updated content
 */
function tags_the_post_trophy( $post, $content ) {
	return( $content );
}

/**
 * Add some Trophy content before 'the_content' filtering 
 * 
 * @param post $post
 * @param string $content - the current content
 * @return string - the updated content
 */
function tags_the_post_course( $post, $content ) {
	return( $content );
}





