<?php // (C) Copyright Bobbing Wide 2015

/**
 * Implement [results] shortcode for TAGS
 */
function tags_results( $atts=array(), $content=null, $tag=null ) {
	
	//p( "Results ");
	$year = bw_array_get( $atts, 'year', '2021' );
	$events = tags_results_events( $year );
	stag( 'table' );
	bw_tablerow( [ 'Date', 'Trophy', 'Course', 'Winner', 'Runner Up'], 'tr', 'th');
	foreach ( $events as $event ) {

		$date = tags_results_date( $event );
		$course = tags_results_course( $event );
		$trophy = tags_results_trophy( $event );
		$winners_results = tags_results_results( $event, 'winner'  );
		$runner_ups_results = tags_results_results( $event, 'runner-up');

		if ( null !== $trophy  ) {
		// 	count( $winners_results ) >= 1  && count( $runner_ups_results ) >= 1 ) {
			$winning_players = tags_results_players( $winners_results );
			$runner_uppers = tags_results_players( $runner_ups_results );
			bw_tablerow( [ $date, $trophy, $course, $winning_players, $runner_uppers ] );
		} else {
			//bw_tablerow( [ $date,$trophy, $course, '', '' ]);
		}

	}
	etag( 'table');
	return bw_ret() ;
}

/**
 * Queries the events for a year.
 *
 * Note: The meta_query attribute is an array of arrays.
 * @param $year
 * @return array
 */
function tags_results_events( $year ) {
	oik_require( 'includes/bw_posts.php');

	$meta_query = [ "key" => "_date"
				 ,	"value" => $year
				 ,	"compare" => "LIKE"
				 ];
	$attr = array( "post_type" => "event"
		//, "posts_per_page" => 20

	, "orderby" => "meta_value"
	, "order" => "ASC"
	, 'meta_key' => '_date'
	, "meta_query" => [ $meta_query ]
	, "exclude" => -1
	, "numberposts" => -1
	);
	$events = bw_get_posts( $attr );
	return $events;
}

function tags_results_date( $event ) {
	$date = get_post_meta( $event->ID, '_date', true);
	//time = strtotime( $date );
	$ddMon = bw_format_date( $date, 'd M');
	return $ddMon;
}

function tags_results_course( $event ) {
	$course_id = get_post_meta( $event->ID, '_course', true );

	$course = get_post( $course_id );
	//p( $course->post_title );
	return $course->post_title;

}

function tags_results_trophy( $event ) {
	$trophy_id = get_post_meta( $event->ID, '_trophy', true );
	bw_trace2( $trophy_id, "trophy ID" );
	if ( !$trophy_id ) return null;
	$trophy = get_post( $trophy_id );
	return $trophy->post_title;
}

function tags_results_results( $event, $result_type ) {
	$atts = [ 'post_type' => 'result'
		, 'result_type' => $result_type
		, 'meta_key' => '_event'
		, 'meta_value' => $event->ID
		];
	$results = bw_get_posts( $atts );

	return $results;
}

function tags_results_players( $results ) {
	$players = [];
	foreach ( $results as $result ) {
		$player = tags_results_player( $result );
		$players[] = $player->post_title;

	}
	$players = implode( '<br />', $players );
	return $players;
}

function tags_results_player( $result ) {
	$player_ID = get_post_meta( $result->ID, '_player', true);
	//bw_trace2( $player_ID, "player_ID");
	$player = get_post( $player_ID );
	return $player;
}
