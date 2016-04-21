<?php // (C) Copyright Bobbing Wide 2015

/**
 * Implement the mapping for trophies we're migrating 
 *
 * From  content_type_trophies
 * content_field_image vid, nid, field_image_fid
 *
 */
class TD2W_trophies {

	public $trophies;
	
	private $results;
	
	
	function __construct() {
		$this->trophies = array();
		$this->load_trophies();
		$this->process_trophies();
		$this->report();
	}
	
	function load_trophies() { 
    global $wpdb;
		$request =  "select n.nid, n.title, r.body, r.teaser  from node n, node_revisions r where n.type = 'trophy'  and r.nid = n.nid ";
		$results = $wpdb->get_results( $request );
	 	$this->results = $results;
		print_r( $results );
	}
	
	function report() {
		print_r( $this->trophies );
	}
	
	function process_trophies() {
		foreach ( $this->results as $result ) {
			$id = $this->load_trophy( $result );
			if ( !$id ) {
				$id = $this->create_trophy( $result );
			} else {
				$this->update_trophy( $result, $id );
			}
			$this->set_fields( $id, $result );
			$this->trophies[ $result->nid ] = $id;
			
		}
	}
	
	function map( $tid ) {
		$mapped = bw_array_get( $this->trophies, $tid, null );
		return( $mapped );
	}
	
	
	function load_trophy( $result ) {
		oik_require( "includes/bw_posts.inc" );
	
		$atts = array( "post_type" => "trophy"
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
	
	
	function create_trophy( $result ) {
	
		$post = array( "post_type" => "trophy" 
								 , "post_title" => $result->title
								 , "post_name" => $result->title
								 , "post_content" => $result->body
								 
								 , "post_status" => "publish" 
								 );
		$_POST['_nid'] = $result->nid;
		
		$id = wp_insert_post( $post, true );
		return( $id );
								
	
	}
	
	/**
	 * Why not just update post_meta! 
	 */
	function update_trophy( $result, $id ) {
		$post = array( "ID" => $id
								);
		$_POST['_nid'] = $result->nid;
		wp_update_post( $post );
	}
	
	/**
	 * Set the other fields for a trophy
	 */
	
	function set_fields( $id, $result ) {
	
	}
	
		

}


