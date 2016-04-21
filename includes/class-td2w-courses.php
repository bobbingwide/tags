<?php // (C) Copyright Bobbing Wide 2015

/**
 * Implement the mapping for courses we're migrating 
 *
 * From  content_type_course
 * content_field_image vid, nid, field_image_fid
 *
 * 
 
	
  bw_register_field( "_url", "url", "Website" ); 
  bw_register_field( "_address", "textarea", "Address" ); 
	bw_register_field( "_post_code", "text", "Post Code" );
	bw_register_field( "_lat", "numeric", "Latitude" );
	bw_register_field( "_long", "numeric", "Longitude" );
 */
class TD2W_courses {

	public $courses;
	
	private $results;
	
	private $files;
	private $content;
	private $created;
	private $changed;
	
	
	function __construct( $files ) {
		$this->files = $files;
		$this->courses = array();
		$this->load_courses();
		$this->process_courses();
		//$this->report();
	}
	
	function load_courses() { 
    global $wpdb;
		$request =  "select n.nid nid, n.title, n.created, n.changed, c.field_website_url, r.body ";
		$request .= " from node n, content_type_course c, node_revisions r "; 
		$request .= " where n.type = 'course' and n.vid = c.vid and n.nid = r.nid ";
		$results = $wpdb->get_results( $request );
	 	$this->results = $results;
		//echo "Courses: " . PHP_EOL;
		//print_r( $results );
	}
	
	function report() {
		print_r( $this->courses );
	}
	
	function process_courses() {
		foreach ( $this->results as $result ) {
			$this->content( $result );
			$id = $this->load_course( $result );
			if ( !$id ) {
				$id = $this->create_course( $result );
			} else {
				$this->update_course( $result, $id );
			}
			$this->set_fields( $id, $result );
			$this->set_featured_image( $id, $result );
			$this->courses[ $result->nid ] = $id;
			
		}
	}
	
	function map( $tid ) {
		$mapped = bw_array_get( $this->courses, $tid, null );
		return( $mapped );
	}
	
	
	function load_course( $result ) {
		oik_require( "includes/bw_posts.inc" );
	
		$atts = array( "post_type" => "course"
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
	
	function content( $result ) {
		$content = $result->body;
		$content .= "<!--more-->[bw_fields]";
		$this->content = $content;
		$this->created = bw_format_date( $result->created, "Y-m-d H:i:s" );
		$this->changed = bw_format_date( $result->changed, "Y-m-d H:i:s" );
	}
	
	
	
	
	function create_course( $result ) {
		$post = array( "post_type" => "course" 
								 , "post_title" => $result->title
								 , "post_name" => $result->title
								 , "post_content" => $this->content
								 , "post_date" => $this->created
								 , "post_date_gmt" => $this->created
								 , "post_modified_date" => $changed
								 );
		$_POST['_url'] = $result->field_website_url;
		$_POST['_nid'] = $result->nid;
		
		$id = wp_insert_post( $post, true );
		return( $id );
								
	
	}
	
	/**
	 * Why not just update post_meta! 
	 */
	function update_course( $result, $id ) {
		$post = array( "ID" => $id
								 , "post_content" => $this->content
								 , "post_date" => $this->created
								 , "post_date_gmt" => $this->created
								 , "post_modified_date" => $this->changed
								);
								
		$_POST['_url'] = $result->field_website_url;
		$_POST['_nid'] = $result->nid;
		wp_update_post( $post );
	}
	
	/**
	 * Set the other fields for a course
	 
	 
  bw_register_field( "_address", "textarea", "Address" ); 
	bw_register_field( "_post_code", "text", "Post Code" );
	bw_register_field( "_lat", "numeric", "Latitude" );
	bw_register_field( "_long", "numeric", "Longitude" );
	 */
	
	function set_fields( $id, $result ) {
	
		$location = $this->get_location( $result );
		//print_r( $location );
		//$_POST["_address" ] =
		//$_POST["_post_code"] = $location->postal_code;
		$address = array();
		$address[] = $location->street;
		$address[] = $location->additional;
		$address[] = $location->city;
		$address[] = $location->province;
		$address = implode( ",", $address ); 
		echo "$id $address" . PHP_EOL;
		update_post_meta( $id, "_address", $address );
		update_post_meta( $id, "_post_code", $location->postal_code );
		update_post_meta( $id, "_lat", $location->latitude );
		update_post_meta( $id, "_long", $location->longitude ); 
		
	
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
	
	function get_location( $result ) {
		global $wpdb;
		$nid = $result->nid;
		
		$request =  "select name, street, additional, city, province, postal_code, latitude, longitude ";
		$request .= " from location l, location_instance i where i.lid = l.lid and i.nid = $nid  ";
		$results = $wpdb->get_results( $request );
	 	//print_r( $results );
		if ( $results ) {
			$result = $results[0];
		}
		return( $result );
	}
		

}


