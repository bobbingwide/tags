<?php

/**
 * @copyright Bobbing Wide 2021
 * @package TAGS
 */

class TAGS_achievements {

	private $player = null;
	private $results = [];
	private $events = [];
	private $event_dates = [];
	private $achievements = [];

	function __construct( $player ) {
		oik_require( 'includes/bw_posts.php');
		$this->player = $player;
		$this->results = null;
		$this->get_all_events();
		$this->get_all_event_dates();
	}

	function get_player_results() {
		$args = [ 'post_type' => 'result',
		          'meta_key' => '_player',
				'meta_value' => $this->player,
			'order' => 'desc',
			'orderby' => 'date',
			'numberposts' => -1
			];
		$this->results = bw_get_posts( $args );

	}

	/**
	 * Accumulates results into achievements
	 */

	function get_player_achievements() {
		foreach ( $this->results as $result ) {
			$event = get_post_meta( $result->ID, '_event', true );
			$event_date = $this->get_event_date( $event );
			$result_type = wp_get_post_terms( $result->ID, 'result_type');
			$result_type = $result_type[0]->name;
			$result_details = get_post_meta( $result->ID, '_details', true );
			$this->create_achievement( $result, $event, $event_date, $result_type, $result_details );
		}
		krsort( $this->achievements );

	}

	function create_achievement( $result, $event, $event_date, $result_type, $result_details ) {
		$this->achievements[$event_date . $result_type . $result->ID ] = [ 'result' => $result, 'event' => $event, 'event_date' => $event_date, 'result_type' => $result_type, 'details' => $result_details ];
	}

	function report_results() {
		//h3( "Achievements");
		//p( "Player: " .  $this->player );
		//p( count( $this->results ));
		//p( count( $this->events ));
		//p( count( $this->achievements));
		sediv( 'cleared clear');
		h2( 'Results');
		$this->report_achievements(['Winner','Runner up','Third']);
		h2( 'Birdies or better');
		$this->report_achievements( ['Birdies','Eagle','Hole-in-one']);
		h2( 'Other' );
		$this->report_achievements( ['NTP', 'NTP in two', 'Longest Drive', 'Miscellaneous' ] );

	}

	function report_achievements( $types ) {
		$atypes=bw_assoc( $types );
		$counts=array_fill_keys( $types, 0 );
		//print_r( $counts );
		$details=[];
		foreach ( $this->achievements as $achievement ) {
			$result_type=$achievement['result_type'];
			if ( bw_array_get( $atypes, $result_type, null ) ) {
				$event_link=$this->event( $achievement['event'] );
				$details[] =[ $event_link, $result_type, $achievement['details'] ];
				//bw_tablerow( [ $event_link, $result_type, $achievement['details'] ] );
				$counts[ $result_type ] ++;
			}
		}

		$this->report_counts( $counts );
		$this->report_details( $details );
	}
	function report_details( $details ) {
		stag( 'table', 'results');
		bw_tablerow( bw_as_array( 'Event,Result,Details') , 'tr', 'th' );
		foreach ( $details as $detail ) {
			//stag( 'tr');
			//stag( 'td', 'event');
			//etag( 'td');
			//etag( 'tr');
			bw_tablerow( $detail );
		}
		etag( 'table');
	}

function report_counts( $counts ) {
	sdiv('counts');
	foreach ( $counts as $type => $count ) {
		if ( $count ) {
			span('type');
			e( "$type: ");
			epan();
			span( 'count');

			e( " $count " );
			epan();
			//e( '&nbsp;');
		}
	}
	ediv();
}

	function get_all_events() {
		$args = [ 'post_type' => 'event'
			 , 'numberposts' => -1
			];
		$this->events = bw_get_posts( $args );

	}

	function get_all_event_dates() {
		//p( count( $this->events ));
		foreach ( $this->events as $event ) {
			$this->event_dates[ $event->ID ] = get_post_meta( $event->ID, "_date", true );
		}
		//print_r( $this->event_dates );
	}

	function get_event_date( $event ) {
		return $this->event_dates[ $event ];
	}

	function event( $event ) {
		//p( 'Eve:' . $event );
		$event_post = get_post( $event );
		$permalink = get_permalink( $event );
		$link = retlink( null, $permalink, $event_post->post_title );
		return( $link );
	}

}