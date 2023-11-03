<?php // (C) Copyright Bobbing Wide 2015-2019

/**
 * Implement the mapping for posts we're migrating 
 * 
 * @TODO We need to find all the links and change them to use the new permalinks
 * 
 *
 */
class TD2W_posts {

	public $posts;
	
	private $results;
	
	private $post_type;
	
	
	function __construct() {
		$this->posts = array();
		$this->load_posts();
		$this->process_posts();
		$this->report();
	}
	
	function load_posts() { 
    global $wpdb;
		$request =  "select n.nid, n.title, r.body, r.teaser, n.type, n.created, n.changed  from node n, node_revisions r where n.type in ('story','blog','simplenews', 'page' ) and r.nid = n.nid ";
		$results = $wpdb->get_results( $request );
	 	$this->results = $results;
		//print_r( $results );
	}
	
	function report() {
		print_r( $this->posts );
	}
	
	function process_posts() {
		foreach ( $this->results as $result ) {
			$this->post_type( $result );
			$id = $this->load_post( $result );
			if ( !$id ) {
				$id = $this->create_post( $result );
			} else {
				$this->update_post( $result, $id );
			}
			update_post_meta( $id, "_nid", $result->nid );
			$this->set_fields( $id, $result );
			$this->posts[ $result->nid ] = $id;
			
		}
	}
	
	function post_type( $result ) {
		if ( $result->type == "page" ) {
			$this->post_type = "page";
		} else {
			$this->post_type  = "post";
		}
		
		echo " {$result->type} {$result->title}" . PHP_EOL;
	}
	
	function map( $tid ) {
		$mapped = bw_array_get( $this->posts, $tid, null );
		return( $mapped );
	}
	
	
	function load_post( $result ) {
		oik_require( "includes/bw_posts.php" );
	
		$atts = array( "post_type" => $this->post_type
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
	
	
	function create_post( $result ) {
	
		$created = bw_format_date( $result->created, "Y-m-d H:i:s" );
		$changed = bw_format_date( $result->changed, "Y-m-d H:i:s" );
	
		$post = array( "post_type" => $this->post_type 
								 , "post_title" => $result->title
								 , "post_name" => $result->title
								 , "post_content" => $result->body
								 , "post_status" => "publish" 
								 , "post_date" => $created
								 , "post_modified_date" => $changed
								 );
		$_POST['_nid'] = $result->nid;
		
		$id = wp_insert_post( $post, true );
		return( $id );
								
	
	}
	
	/**
	 * Why not just update post_meta! 
	 */
	function update_post( $result, $id ) {
	
		$created = bw_format_date( $result->created, "Y-m-d H:i:s" );
		$changed = bw_format_date( $result->changed, "Y-m-d H:i:s" );
		$post = array( "ID" => $id
		
								 , "post_date" => $created
								 , "post_modified_date" => $changed
								);
		$_POST['_nid'] = $result->nid;
		wp_update_post( $post );
	}
	
	/**
	 * Set the other fields for a post
	 */
	
	function set_fields( $id, $result ) {
	
	}
	
		

}


