<?php // (C) Copyright Bobbing Wide 2015

/**
 * TAGS_event class
 */


class TAGS_event {
	public $post;
	
	private static $current_year = null;
	private static $year_changed = null;
	private $year;
	
	
	/**
	 * @var TAGS_event - the true instance
	 */
	//private static $instance;

	/**
	 * Return a single instance of this class
	 *
	 * @return object 
	 */
	//public static function instance() {
//		if ( !isset( self::$instance ) && !( self::$instance instanceof TAGS_event ) ) {
//			self::$instance = new TAGS_event;
//		}
//		return self::$instance;
//	}
	
	function __construct( $post ) {
		
		$this->post = $post;
		$this->set_year_changed();
		
	}
	function set_year_changed() {
		$new_year = $this->get_year();
		self::$year_changed = self::$current_year <> $new_year;
		if ( self::$year_changed ) {
			self::$current_year = $new_year;
		}
	}
	
	function year_changed() {
		return( self::$year_changed ); 
	}
	
	function get_year() {
		$ID = $this->post->ID;
		$date = get_post_meta( $ID, "_date", true );
		$this->year = bw_format_date( $date, "Y" );
		//e( "Date: {$this->year}" );
		return( $this->year );
	}
	
	function year_link() {
		if ( $this->year_changed() ) {
			//e( "Year link" );
			li( self::$current_year );
		}
	}
	
	function link() {
		bw_format_list( $this->post, null );
	}
		

}
