<?php
/**
 * @copyright (C) Copyright Bobbing Wide 2021
 * @package tags
 */

/**
 * Implement [tags_achievements] shortcode for TAGS
 */
function tags_achievements( $atts=array(), $content=null, $tag=null ) {
	oik_require( 'includes/class-tags-achievements.php', 'tags' );
	$player = bw_array_get( $atts, 'player', null );
	if ( null === $player ) {
		$player = bw_current_post_id();
	}
	$achievements = new TAGS_achievements( $player );
	$achievements->get_player_results();
	$achievements->get_player_achievements();
	$achievements->report_results();

	return bw_ret();
}




