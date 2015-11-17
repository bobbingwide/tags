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
				td2w_run();
				echo "End cli:" . __FUNCTION__ . PHP_EOL; 
			}
		}
	} else {
		//echo PHP_SAPI;
		//echo PHP_EOL;
		if ( function_exists( "bw_trace2" ) ) {
			bw_trace2( PHP_SAPI, "tags loaded in WordPress environment?" );
		}
		if ( function_exists( "add_action" ) ) {
			// if ( bw_is_wordpress() ) {
			//add_action( "admin_notices", "oik_batch_activation" );
			add_action( "oik_fields_loaded", "tags_oik_fields_loaded" );
			add_action( "admin_menu", "tags_admin_menu" );
			add_filter( 'set-screen-option', "tags_set_screen_option", 10, 3 );
		}
	}
	

}

/**
 * Implement an admin menu
 */
function tags_admin_menu() {


}

/**
 *
 */
function tags_oik_fields_loaded() {
	tags_register_categories();
	tags_register_post_types();
}

/**
 * Register custom taxonomies
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
	bw_register_custom_category( "result", null, "Result" );
}

/** 
 * Register the custom post types for TAGS
 *
 * In the current system we have 744 nodes to migrate
 *
 * Seq ### | node_type | post_type
 * --- | --------- | ------------
 * 0 | 0 | competitor | 
 * 0 | 0 | forum | 
 * 0 | 0 | panel
 * 0 | 0 | poll | 
 * 0 | 0 | profile | 
 * 1 | 1 | story	| posts 
 * 1 | 42 | blog |	posts
 * 1 | 6 | simplenews | posts
 * 2 | 6 | page | pages 
 * 3 | 19 | trophy	
 * 4 | 31 | course |	course
 * 5 | 81 | player	| players	/ users
 * 6 | 171 | event | event
 * 7 | 35 | competitors | competitors 		= link to event and players
 * 8 | 352 | result | result
 */

function tags_register_post_types() {
	// bw_register_post_type( "blog", $post_type_args );
	// bw_register_post_type( "competitor", $post_type_args );
	//tags_register_competitors()
	tags_register_course();
	//tags_register_trophy();
	//tags_register_event();
	//tags_register_result();
	

	//bw_register_post_type( "competitors", $post_type_args );


	//bw_register_post_type( "event", $post_type_args );

	//bw_register_post_type( "forum", $post_type_args );

	//bw_register_post_type( "page", $post_type_args );

	//bw_register_post_type( "panel", $post_type_args );

	//bw_register_post_type( "player", $post_type_args );

	///bw_register_post_type( "poll", $post_type_args );

	//bw_register_post_type( "profile", $post_type_args );

	//bw_register_post_type( "result", $post_type_args );

	//bw_register_post_type( "simplenews", $post_type_args );

	//bw_register_post_type( "story", $post_type_args );

	//bw_register_post_type( "trophy", $post_type_args );

}
/**
 *  Register a course 
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
  $post_type_args['supports'] = array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'author', 'publicize', 'home' );
  $post_type_args['has_archive'] = true;
  $post_type_args['menu_icon'] = 'dashicons-location-alt';
  bw_register_post_type( $post_type, $post_type_args );
	
	
	
  bw_register_field( "_url", "url", "Website" ); 
  bw_register_field( "_address", "textarea", "Address" ); 
	bw_register_field( "_post_code", "text", "Post Code" );
	bw_register_field( "_lat", "numeric", "Latitude" );
	bw_register_field( "_long", "numeric", "Longitude" );
	
	bw_register_field_for_object_type( "_url", $post_type );
	bw_register_field_for_object_type( "_address", $post_type );
	bw_register_field_for_object_type( "_post_code", $post_type );
	bw_register_field_for_object_type( "_lat", $post_type );
	bw_register_field_for_object_type( "_long", $post_type );
	


}


/**
 * Run the migration from Drupal to WordPress
 *
 */
function td2w_run() {
	oik_require( "includes\tagsd2w.php", "tags" );
	td2w_lazy_run();
	


}




