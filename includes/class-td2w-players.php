<?php // (C) Copyright Bobbing Wide 2015

/**
 * Implement the mapping for players we're migrating 
 *
 */
class TD2W_players {

	public $players;
	
	private $results;
	
	public $users;
	public $terms;
	
	
	function __construct( $users, $terms ) {
		$this->users = $users;
		$this->terms = $terms;
		print_r( $this->users );
		$this->players = array();
		$this->load_players();
		$this->process_players();
		$this->report();
	}
	
	function load_players() { 
    global $wpdb;
		$request =  "select n.nid, n.uid, n.title, t.field_woods_value, t.field_irons_value, t.field_putter_value, t.field_ball_value, t.field_handicap_value";
		$request .= " from content_type_player t, node n  ";
		$request .= " where t.nid = n.nid and type = 'player'";
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
			$id = $this->load_player( $result );
			if ( !$id ) {
				$id = $this->create_player( $result );
			} else {
				//$this->update_course( $result, $id );
			}
			$this->update_terms( $result, $id );
			$this->players[ $result->uid ] = $id;
			
		}
	}
	
	function map( $tid ) {
		$mapped = bw_array_get( $this->players, $tid, null );
		return( $mapped );
	}
	
	
	function load_player( $result ) {
	
		oik_require( "includes/bw_posts.inc" );
	
		$atts = array( "post_type" => "player"
								 , "post_parent" => 0
								 , "meta_key" => "_uid"
								 , "meta_value" => $result->uid
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
		$content = "[bw_fields featured]<!--more-->";
		$content .= $result->field_woods_value;
		$content .= $result->field_irons_value;
		$content .= $result->field_putter_value;
		$content .= $result->field_ball_value;
		$content .= "[bw_fields]";
		
		$post = array( "post_type" => "player"
								 , "post_title" => $result->title
								 , "post_name" => $result->title
								 , "post_content" => $content
								 , "post_status" => "publish"
								 );
		$_POST['_user'] = $this->users->map( $result->uid );
		$_POST['_handicap'] = $result->field_handicap_value;
		$_POST['_uid'] = $result->uid;
		$_POST['_nid'] = $result->nid;
		$id = wp_insert_post( $post );
		return( $id );
								
	
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
		print_r( $results );
		$terms = array();
		foreach ( $results as $term ) {
			$tid = $term->tid;
			$target_term = $this->terms->map( $tid );
			echo "ID: $id, target term: $target_term term: $tid" . PHP_EOL;
			$terms[] = $target_term;
		}
		wp_set_post_terms( $id, $terms, "membership" );
	
	}
	
	

}


