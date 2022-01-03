<?php // (C) Copyright Bobbing Wide 2015-2019,2022

/**
 * TAGS Competitors admin page
 *
 * As you can see most of the work is done in OO code
 *
 */
function tags_lazy_competitors_admin_page() {
	oik_require_lib( "bobbforms" );
	oik_require( "includes/bw_posts.php" );
	oik_require( "admin/class-tags-competitors.php", "tags" );
	oik_require( "admin/class-tags-competitor.php", "tags" );
	oik_menu_header( "Competitors" );
	$competitors = new TAGS_competitors();
	oik_box( null, "competitors_form", "Register competitors", array( $competitors, "competitors_form" ) );
	oik_box( null, "add_competitor_form", "Additional competitors", array( $competitors, "add_competitor_form" ) );
	oik_menu_footer();
	bw_flush();
}