<?php // (C) Copyright Bobbing Wide 2015-2019

/**
 * TAGS Results admin page
 *
 * As you can see most of the work is done in OO code
 */
function tags_lazy_results_admin_page() {
  oik_require( "bobbforms.inc" );
	oik_require( "includes/bw_posts.php" );
	oik_require( "admin/class-tags-results.php", "tags" );
	oik_require( "admin/class-tags-result.php", "tags" );
  oik_menu_header( "Results" );
	$results = new TAGS_results();
  oik_box( null, "result_form", "Register results", array( $results, "results_form" ) );
	//oik_box( null, "add_result_form", "Additional results", array( $results, "add_result_form" ) );
  oik_menu_footer();
	bw_flush();
}
