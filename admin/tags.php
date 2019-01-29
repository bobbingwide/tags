<?php // (C) Copyright Bobbing Wide 2015-2019

/**
 * Display the TAGS admin menu
 */
function tags_lazy_admin_menu() {
	$hook = add_menu_page( "TAGS admin", "TAGS admin", "manage_options", "tags", "tags_lazy_admin_page" );
  $hook = add_submenu_page( 'tags', __( 'Competitors', 'tags' ), __( "Competitors", 'tags' ), 'manage_options', 'tags_competitors', "tags_competitors_admin_page" );
  $hook = add_submenu_page( 'tags', __( 'Results', 'tags' ), __( "Results", 'tags' ), 'manage_options', 'tags_results', "tags_results_admin_page" );
	add_action( "save_post_course", "tags_save_post_course", 10, 2 );
}

/**
 * Display the TAGS admin page
 */
function tags_lazy_admin_page() {
	p( "TAGS Admin" );
	alink( null, admin_url( "admin.php?page=tags_competitors" ), "Competitors" );
	br();
	alink( null, admin_url( "admin.php?page=tags_results" ), "Results" );
	bw_flush();
}

/**
 * TAGS results admin
 */
function tags_results_admin_page() {
	oik_require( "admin/tags-results.php", "tags" );
	tags_lazy_results_admin_page();
}

/** 
 * TAGS Competitors admin
 */
function tags_competitors_admin_page() {
	oik_require( "admin/tags-competitors.php", "tags" );
	tags_lazy_competitors_admin_page();
}

/**
 * Implement "save_post_course" for course
 *
 * Here we attempt to set the _lat and _long fields if they're null and the _address and/or _post_code are set.
 *
 * Example data:
 * `
     [_address] => (string) "28, Ballantrae Road,,Liverpool,LAN"
    [_post_code] => (string) "L18 6JQ"
    [_lat] => (string) null
    [_long] => (string) null
	 `
 *
 * @param ID $post_id The ID of the post being saved
 * @param object $post the post object
 
 */
function tags_save_post_course( $post_id, $post ) {
	bw_trace2( $_POST, "_POST", true, BW_TRACE_DEBUG );
	oik_require( "admin/oik-admin.inc" );
	$input['postal-code'] = bw_array_get( $_POST, "_post_code", null );
	$input['extended-address'] = bw_array_get( $_POST, "_address", null );
	if ( $input['postal-code'] || $input['extended-address'] ) { 
		$input['lat'] = bw_array_get( $_POST, "_lat", false );
		$input['long'] = bw_array_get( $_POST, "_long", false );
		$input = oik_set_latlng( $input );
		bw_trace2( $input, "input", false, BW_TRACE_VERBOSE );
		$_POST['_lat'] = $input['lat'];
		$_POST['_long'] = $input['long'];
	}
}
