<?php
/**
 * @copyright (C) Copyright Bobbing Wide 2023
 * @package tags
 *
/**
 * Returns the Player membership virtual field.
 */
function bw_fields_get_player_mem( $player ) {
	//bw_trace2();
	//bw_backtrace();
	$player_post = get_post( $player );
	$player_mem = $player_post->post_title;
	if ( tags_should_display_player_mem() ) {
		$player_mem .= tags_get_player_mem( $player );
	}
	$permalink = get_permalink( $player);
	$link = retlink( null, $permalink, $player_mem );
	return $link;
}

/**
 * Gets the player membership string
 *
 * @param $player
 * @return string
 */
function tags_get_player_mem( $player ) {
	$player_mem = ' ';
	$membership_terms = wp_get_post_terms( $player, 'membership');
	if ( is_wp_error( $membership_terms ) ) {
		$player_mem .= 'oops';
	}
	foreach ( $membership_terms as $membership_term  ) {
		switch ( $membership_term->slug ) {
			case 'guest':
			case 'one-off-guest':
				$player_mem .= '(g)';
				break;
			case 'ex-member':
			case 'competitive-guest':
				$player_mem .= '(G)';
				break;
			case 'committee':
			case 'member':
				//  Don't append anything
				break;
			case 'sabbatical':
				$player_mem .= '(S)';
				break;
			default:
				// Captain, Handicap secretary, Honorary, Secretary, Treasurer, View-captain
				// 	$player_mem .= ' '. $membership_term->name;
				// $player_mem .= ' '. $membership_term->slug;
		}

	}
	return $player_mem;
}

/**
 * Determines if the player membership should be displayed.
 *
 *
 * @return bool
 */
function tags_should_display_player_mem() {
	$should_display = true;
	$post = get_post();

	if ( 'event' !== $post->post_type ) {
		$should_display = false;
	} else {
		$event_date = get_post_meta( $post->ID, '_date', true);
		bw_trace2( $event_date, 'event_date', false );
		if ( $event_date < bw_format_date() ) {
			$should_display = false;
		}
	}

	return $should_display;
}


