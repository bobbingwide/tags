<?php // (C) Copyright Bobbing Wide 2015-2019

/**
 * Implement the mapping for events we're migrating 
 *
 * From  content_type_event: vid, nid, field_date_value, field_date_value2, field_course_nid, field_cost_value, field_trophy_nid, field_shirt_value, field_teetime_value
 *
	
 * content_field_ntps: vid, nid, delta, field_ntps_value
  
 *
 * 
 
	
  bw_register_field( "_course", "noderef", "Course", array( "type" => "course" ) ); 
	bw_register_field( "_date", "date", "Date" );
	bw_register_field( "_tee_time", "time", "First tee" );
	bw_register_field( "_cost", "currency", "Cost" );
  bw_register_field( "_trophy", "noderef", "Trophy", array( "type" => "trophy" ) ); 
	bw_register_field( "_shirt", "select", "Shirt colour" );
	//bw_register_field( "_players", "noderef", "Players", array( "type" => "player" ) );
	_ntps, "select", "NTPs" array( 1-18 ) 
 */
class TD2W_events {

	public $events;
	
	private $results;
	
	
	private $courses;
	private $trophies;
	
	
	function __construct( $courses, $trophies ) {
		$this->courses = $courses;
		$this->trophies = $trophies;
		$this->events = array();
		$this->load_events();
		$this->process_events();
		//$this->report();
	}
	
	function load_events() { 
    global $wpdb;
		$request =  "select n.nid nid, n.title, n.created, n.changed, r.body, r.teaser, c.field_date_value, c.field_date_value2, c.field_course_nid, c.field_cost_value, c.field_trophy_nid, c.field_shirt_value, c.field_teetime_value ";
		$request .= " from node n, content_type_event c, node_revisions r   ";
		$request .= " where n.type = 'event' and n.nid = c.nid and r.nid = n.nid ";
		$results = $wpdb->get_results( $request );
	 	$this->results = $results;
		//print_r( $results );
	}
	
	function report() {
		print_r( $this->events );
	}
	
	function process_events() {
		foreach ( $this->results as $result ) {
			$id = $this->load_event( $result );
			if ( !$id ) {
				$id = $this->create_event( $result );
			} else {
				$this->update_event( $result, $id );
			}
			$this->set_fields( $id, $result );
			//$this->set_featured_image( $id, $result );
			$this->events[ $result->nid ] = $id;
			
		}
	}
	
	function map( $tid ) {
		$mapped = bw_array_get( $this->events, $tid, null );
		return( $mapped );
	}
	
	
	function load_event( $result ) {
		oik_require( "includes/bw_posts.php" );
	
		$atts = array( "post_type" => "event"
								 , "post_parent" => 0
								 , "meta_key" => "_nid"
								 , "meta_value" =>  $result->nid
								 );
		$posts = bw_get_posts( $atts ); 
		if ( $posts ) {
			$id = $posts[0]->ID;
		} else { 
			$id = null;
		}
		return( $id );
	}
	
	
	function create_event( $result ) {
	
		
	
		$post = array( "post_type" => "event" 
								 , "post_title" => $result->title
								 , "post_name" => $result->title
								 , "post_content" => "[bw_fields] " . $result->body
								 , "post_status" => "publish" 
								 , "post_date" => $result->field_date_value
								 );
		$_POST['_nid'] = $result->nid;
		
		$id = wp_insert_post( $post, true );
		return( $id );
								
	
	}
	
	/**
	 * Why not just update post_meta! 
	 */
	function update_event( $result, $id ) {
	
		$created = bw_format_date( $result->created, "Y-m-d H:i:s" );
		$changed = bw_format_date( $result->changed, "Y-m-d H:i:s" );
		$post = array( "ID" => $id
								 , "post_content" => "[bw_fields] " . $result->body 
								 , "post_date" => $created
								 , "post_modified_date" => $changed 
								);
								
		$_POST['_nid'] = $result->nid;
				
		
		wp_update_post( $post );
	}
	
	/**
	 * Set the other fields for an event
	 
	         (
            [nid] => 1446
            [title] => 2-4 Sep 2016 - Lingfield Park
            [field_date_value] => 2016-09-01 23:00:00
            [field_date_value2] => 2016-09-03 23:00:00
            [field_course_nid] => 1449
            [field_cost_value] => 230.00
            [field_trophy_nid] => 240
            [field_shirt_value] => Other
            [field_teetime_value] => 2015-01-01 11:06:00
	 
	 
  bw_register_field( "_course", "noderef", "Course", array( "type" => "course" ) ); 
	bw_register_field( "_date", "date", "Date" );
	bw_register_field( "_tee_time", "time", "First tee" );
	bw_register_field( "_cost", "currency", "Cost" );
  bw_register_field( "_trophy", "noderef", "Trophy", array( "type" => "trophy" ) ); 
	bw_register_field( "_shirt", "select", "Shirt colour" );
	//bw_register_field( "_players", "noderef", "Players", array( "type" => "player" ) );
	_ntps, "select", "NTPs" array( 1-18 ) 
	
	
	//oik_require( "includes/class-td2w-competitors.php", "tags" );
	 
	 */
	
	function set_fields( $id, $result ) {
	
		$course = $this->courses->map( $result->field_course_nid );
    update_post_meta( $id, "_course", $course );
		update_post_meta( $id, "_date", $result->field_date_value );
		list( $teedate, $teetime ) = explode( " ", $result->field_teetime_value .  "  "  );
		$teetime = trim( $teetime );
		
		update_post_meta( $id, "_tee_time", $teetime );
		update_post_meta( $id, "_cost", $result->field_cost_value );
		$trophy = $this->trophies->map( $result->field_trophy_nid );
		update_post_meta( $id, "_trophy", $trophy );
		update_post_meta( $id, "_shirt", $result->field_shirt_value );
		$ntps = $this->get_ntps( $result );
		bw_update_post_meta( $id, "_ntps", $ntps ); 
		
	
	}
	
	function set_featured_image( $id, $result ) {
		$featured = $this->get_featured_image( $result );
		$featured_image = $this->files->map( $featured->field_image_fid );
		echo "ID: $id, featured: $featured_image" . PHP_EOL;
		update_post_meta( $id, "_thumbnail_id", $featured_image );
		
	
	}
	
	function get_featured_image( $result ) {
		global $wpdb;
		$nid = $result->nid;
		
		$request =  "select field_image_fid from content_field_image where nid = $nid  ";
		$results = $wpdb->get_results( $request );
	 	//print_r( $results );
		if ( $results ) {
			$result = $results[0];
		}
		return( $result );
	
	}
	
	/**
	 * Get the NTP holes
	 */
	function get_ntps( $result ) {
		global $wpdb;
		$nid = $result->nid;
		$request =  "select field_ntps_value from content_field_ntps where nid = $nid and field_ntps_value is not null ";
		$results = $wpdb->get_results( $request ); #
		$ntps = array();
		if ( count( $results ) ) {
			//print_r( $results );
			foreach ( $results as $result ) {
				$ntps[] = $result->field_ntps_value;
			}
			//print_r( $ntps );
			//gob();
		}	
		return( $ntps );
	}
		

}


