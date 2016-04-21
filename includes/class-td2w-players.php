<?php // (C) Copyright Bobbing Wide 2015

/**
 * Implement the mapping for players we're migrating
 *
 * Note: It's the player node ID not the UID that we map to the new player post ID 
 *
 */
class TD2W_players {

	public $players;
	
	private $results;
	
	public $users;
	public $terms;
	public $files; 
	
	private $content;
	private $created;
	private $changed;
	
	
	function __construct( $users, $terms, $files ) {
		$this->users = $users;
		$this->terms = $terms;
		$this->files = $files;
		//print_r( $this->users );
		$this->players = array();
		$this->load_players();
		$this->process_players();
		//$this->report();
	}
	
	function load_players() { 
    global $wpdb;
		$request =  "select n.nid, n.uid, n.title, n.created, n.changed, r.body, t.field_woods_value, t.field_irons_value, t.field_putter_value, t.field_ball_value, t.field_handicap_value";
		$request .= " from content_type_player t, node n, node_revisions r  ";
		$request .= " where t.nid = n.nid and type = 'player' and n.nid = r.nid";
		$request .= " order by uid";
		$results = $wpdb->get_results( $request );
	 	$this->results = $results;
		//print_r( $results );
	}
	
	function report() {
		print_r( $this->players );
	}
	
	function process_players() {
		foreach ( $this->results as $result ) {
			$this->content( $result );
			$id = $this->load_player( $result );
			if ( !$id ) {
				$id = $this->create_player( $result );
			} else {
				$this->update_player( $result, $id );
			}
			$this->update_terms( $result, $id );
			$this->set_featured_image( $id, $result );
			$this->players[ $result->nid ] = $id;
			
		}
	}
	
	function map( $tid ) {
		$mapped = bw_array_get( $this->players, $tid, null );
		return( $mapped );
	}
	
	function content( $result ) {
		$content = $result->body;
		$content .= " [bw_fields featured]<!--more-->";
		$content .= $result->field_woods_value;
		$content .= $result->field_irons_value;
		$content .= $result->field_putter_value;
		$content .= $result->field_ball_value;
		$content .= "[bw_fields]";
		$this->content = $content;
		$this->created = bw_format_date( $result->created, "Y-m-d H:i:s" );
		$this->changed = bw_format_date( $result->changed, "Y-m-d H:i:s" );
	}
	
	function load_player( $result ) {
	
		oik_require( "includes/bw_posts.inc" );
	
		$atts = array( "post_type" => "player"
								 , "post_parent" => 0
								 , "meta_key" => "_nid"
								 , "meta_value" => $result->nid
								 );
		$posts = bw_get_posts( $atts );
		if ( $posts ) {
			$id = $posts[0]->ID;
		} else { 
			$id = null;
		}
		return( $id );
	}
	
	
	function create_player( $result ) {
		
		$post = array( "post_type" => "player"
								 , "post_title" => $result->title
								 , "post_name" => $result->title
								 , "post_content" => $this->content
								 , "post_status" => "publish"
								 , "post_date" => $this->created
								 , "post_date_gmt" => $this->created
								 , "post_modified_date"  => $this->changed
								 );
		$_POST['_user'] = $this->users->map( $result->uid );
		$_POST['_handicap'] = $result->field_handicap_value;
		$_POST['_uid'] = $result->uid;
		$_POST['_nid'] = $result->nid;
		$id = wp_insert_post( $post );
		return( $id );
								
	
	}
	
	function update_player( $result, $id ) {
		$post = array( "ID" => $id
								 , "post_content" => $this->content
								 , "post_date" => $this->created
								 , "post_date_gmt" => $this->created
								 , "post_modified_date"  => $this->changed
								 );
		$_POST['_user'] = $this->users->map( $result->uid );
		$_POST['_handicap'] = $result->field_handicap_value;
		$_POST['_uid'] = $result->uid;
		$_POST['_nid'] = $result->nid;
		$id = wp_update_post( $post );
	
	
	}
	
	/**
	 * Update player's membership
	 * 
	
	"SELECT nid, tid FROM `term_node` where nid = $nid"
	
	
	*/
	function update_terms( $result, $id ) {
	
    global $wpdb;
		$nid = $result->nid;
		$request =  "select tid from term_node where nid = $nid";
		$results = $wpdb->get_results( $request );
		//print_r( $results );
		$terms = array();
		foreach ( $results as $term ) {
			$tid = $term->tid;
			$target_term = $this->terms->map( $tid );
			echo "ID: $id, target term: $target_term term: $tid" . PHP_EOL;
			$terms[] = $target_term;
		}
		wp_set_post_terms( $id, $terms, "membership" );
	
	}
	
	function set_featured_image( $id, $result ) {
		$featured = $this->get_featured_image( $result );
		$featured_image = $this->files->map( $featured->field_photo_fid );
		echo "ID: $id, featured: $featured_image" . PHP_EOL;
		update_post_meta( $id, "_thumbnail_id", $featured_image );
		//gob();
		
	
	}
	
	function get_featured_image( $result ) {
		global $wpdb;
		$nid = $result->nid;
		
		$request =  "select field_photo_fid from content_field_photo where nid = $nid  ";
		$results = $wpdb->get_results( $request );
	 	//print_r( $results );
		if ( $results ) {
			$result = $results[0];
		}
		//gob();
		return( $result );
	
	}
	
	

}


