<?php // (C) Copyright Bobbing Wide 2015

/**
 * Display the TAGS admin menu
 */
function tags_lazy_admin_menu() {
	$hook = add_menu_page( "TAGS admin", "TAGS admin", "manage_options", "tags", "tags_lazy_admin_page" );
  $hook = add_submenu_page( 'tags', __( 'Competitors', 'tags' ), __( "Competitors", 'tags' ), 'manage_options', 'tags_competitors', "tags_competitors_admin_page" );
  $hook = add_submenu_page( 'tags', __( 'Results', 'tags' ), __( "Results", 'tags' ), 'manage_options', 'tags_results', "tags_results_admin_page" );
}

/**
 * Display the TAGS admin page
 */
function tags_lazy_admin_page() {
	p( "TAGS Admin" );
	alink( null, admin_url( "admin.php?page=tags_competitors" ), "Competitors" );
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
