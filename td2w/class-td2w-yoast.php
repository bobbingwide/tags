<?php // (C) Copyright Bobbing Wide 2015-2019

/**
 * Set some sensible values for Yoast's SEO
 * 
 * meta_key | type | notes
 * -------- | ----- | -----
 * _yoast_wpseo_metadesc 	 | text 120 - 156 | depends on content
 * _yoast_wpseo_focuskw	| text 
 * _yoast_wpseo_focuskw_text_input | 
 * _yoast_wpseo_linkdex
 */
 
class TD2W_yoast {

	public $posts;
	
	private $metadesc;
	

	function __construct() {
		oik_require( "includes/bw_posts.php" );
		$this->process();
	}
	
	function process() {
		$this->seo_posts( "course" );
		//$this->seo_posts( "page" );
		//$this->seo_posts( "player" );
		//$this->seo_posts( "post" );
		//$this->seo_posts( "trophy" );
		//$this->seo_posts( "event" );
		//$this->seo_posts( "competitor" );
		$this->seo_posts( "result" );
  }	


	function seo_posts( $post_type ) {
		$this->load_posts( $post_type );
		$this->process_posts( $post_type );
		
	}
	
	function load_posts( $post_type ) {
		$args = array( "post_type" => $post_type
								 , "numberposts" => -1
								 
								 );
		$this->posts = bw_get_posts( $args );
	} 
	
	function process_posts( $post_type ) {
		foreach ( $this->posts as $post ) {
			$this->process_post( $post );
		}
	
	}
	
	function process_post( $post ) {
		$this->metadesc( $post );
		if ( $this->metadesc ) {
			update_post_meta( $post->ID, "_yoast_wpseo_metadesc", $this->metadesc );
			echo "ID: {$post->ID} metadesc: {$this->metadesc}" . PHP_EOL;
		}
	}
	
	function metadesc( $post ) {
		$this->metadesc = null;
		switch ( $post->post_type ) {
			case 'competitor':
				$this->process_competitor( $post );
				break;
				
				
			case 'event':
				$this->process_event( $post );
				break;
			
			case 'result':
				$this->process_result( $post );
				break;
				
			case 'attachment':
			case 'course':
			case 'page':
			case 'player':
			case 'post':
			case 'trophy':
			default:
				$this->metadesc = $post->post_title;
				break;
			
		}
	
	}
	
	
	function process_competitor( $post ) {
		$event = $this->get_event( $post );
		$player = $this->get_player( $post );
		$this->metadesc = "$event $player";
		$update = array( "ID" => $post->ID 
									 , "post_title" => $this->metadesc
									 );
		wp_update_post( $update );
	}
	
	
	function process_event( $post ) {
		$course = $this->get_course( $post );
		$trophy = $this->get_trophy( $post );
		$date = $this->get_date( $post );
		$this->metadesc = "$date $course $trophy";
	}
	
	function process_result( $post ) {
		$event = $this->get_event( $post );
		$player = $this->get_player( $post );
		$result_type = $this->get_result_type( $post );
		$this->metadesc = "$event - $result_type - $player";
		
		$update = array( "ID" => $post->ID 
									 , "post_title" => $this->metadesc
									 );
		wp_update_post( $update );
		
		
	}
	
	function get_course( $post ) {
		$id = $this->get_meta( $post->ID, "_course" );
		$course = get_the_title( $id );
		return( $course );
	}
	
	function get_event( $post ) {
		$id = $this->get_meta( $post->ID, "_event" );
		$event = get_the_title( $id );
		return( $event );
	}
	
	function get_player( $post ) {
		$id = $this->get_meta( $post->ID, "_player" );
		$player = get_the_title( $id );
		return( $player );
	}
	
	
	function get_trophy( $post ) {
		$id = $this->get_meta( $post->ID, "_trophy" );
		$trophy = get_the_title( $id );
		return( $trophy );
	}
	
	function get_meta( $id, $field ) {
		$value = get_post_meta( $id, $field, true );
		print_r( $value );
		echo PHP_EOL;
		return( $value );
	}
	
	function get_date( $post ) {
		$date = $this->get_meta( $post->ID, "_date" );
		return( $date );
	}
	
	function get_result_type( $post ) {
		$terms = wp_get_post_terms( $post->ID, "result_type" );
		$term = $terms[0]->name;
		return( $term );
	}
	



}
