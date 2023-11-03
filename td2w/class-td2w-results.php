<?php // (C) Copyright Bobbing Wide 2015-2019

/**
 * Implement the mapping for results we're migrating 
 * 
 *
 * "result_type" comes from term_node: nid, vid, tid
 *
 * 
 * 51 = result
 *
 * node type='result' - 784 rows
 * 
 * content_field_competition: vid nid field_competition_nid	51 51 10		- link back to the event
 * content_field_player: vid nid field_player_nid	51 51 19, 51 51 27		- note: multiple players possible
 * content_type_result: vid nid field_details_value 51 51 "52 combined stableford points"
 
 * 
 */
class TD2W_results {

	public $results;
	
	private $rows;
	
	private $terms;
	private $events;
	private $players;
	
	private $uniqid;
	
	
	function __construct( $terms, $events, $players ) {
		$this->terms = $terms;
		$this->events = $events;
		$this->players = $players;
		$this->results = array();
		$this->empty_results();
		$this->load_results();
		$this->process_results();
		//$this->report();
	}
	
	function load_results() { 
    global $wpdb;
		$request =  "select n.nid nid, t.tid, n.title, r.field_details_value, c.field_competition_nid cid, p.field_player_nid pid  ";
		$request .= " from node n, term_node t, content_type_result r, content_field_competition c, content_field_player p  ";
		$request .= " where n.type = 'result' and n.nid = t.nid and n.nid = r.nid and n.nid = c.nid and n.nid = p.nid  ";
		$request .= " order by cid, tid ";
		$rows = $wpdb->get_results( $request );
	 	$this->rows = $rows;
		//print_r( $rows );
	}
	
	function report() {
		print_r( $this->rows );
	}
	
	function process_results() {
		foreach ( $this->rows as $result ) {
		
			$this->uniqid( $result );
			$id = $this->load_result( $result );
			if ( !$id ) {
				$id = $this->create_result( $result );
			} else {
				$this->update_result( $result, $id );
			}
			$this->update_terms( $result, $id );
			$this->results[ $result->nid ] = $id;
			
		}
	}
	
	function map( $tid ) {
		$mapped = bw_array_get( $this->results, $tid, null );
		return( $mapped );
	}
	
	
	
	function uniqid( $result ) {
		$uniqid = array();
		$uniqid[] = $result->nid;
		$uniqid[] = $result->cid;
		$uniqid[] = $result->pid;
		
		$this->uniqid = implode( "-", $uniqid );
	}
	
	
	function load_result( $result ) {
		oik_require( "includes/bw_posts.php" );
	
		$atts = array( "post_type" => "result"
								 , "post_parent" => 0
								 , "meta_key" => "_nid"
								 , "meta_value" =>  $this->uniqid
								 );
		$posts = bw_get_posts( $atts ); 
		if ( $posts ) {
			$id = $posts[0]->ID;
		} else { 
			$id = null;
		}
		return( $id );
	}
	
	
	function create_result( $result ) {
		$content = $result->field_details_value;
		$content .= " <!--more-->[bw_fields]";
		$post = array( "post_type" => "result" 
								 , "post_title" => $result->title
								 , "post_name" => $result->title
								 , "post_content" => $content
								 , "post_status" => "publish" 
								 );
		$_POST['_nid'] = $this->uniqid;
		$_POST['_event'] = $this->events->map( $result->cid );
		$_POST['_player'] = $this->players->map( $result->pid ); 
		$_POST['_details'] = $result->field_details_value;
		$id = wp_insert_post( $post, true );
		return( $id );
								
	
	}
	
	/**
	 * Why not just update post_meta! 
	 */
	function update_result( $result, $id ) {
		$post = array( "ID" => $id
								);
								
		$_POST['_nid'] = $this->uniqid;
		$_POST['_event'] = $this->events->map( $result->cid );
		$_POST['_player'] = $this->players->map( $result->pid ); 
		$_POST['_details'] = $result->field_details_value;
		wp_update_post( $post );
	}
	
	
	
	/**
	 * Update the result_type - there should only be one per node
   */
	function update_terms( $result, $id ) {
		$target_term = $this->terms->map( $result->tid );
		echo "ID: $id, target term: $target_term " . PHP_EOL;
		wp_set_post_terms( $id, $target_term, "result_type" );
	
	}
	
	/**
	 * We've created too many results so we need to delete them and start again
	 * 
	 */
	function empty_results() {
		$args = array( "post_type" => "result"
								, "number_posts" => -1
								);
		$posts = bw_get_posts( $args );
		foreach ( $posts as $post) {
			wp_delete_post( $post->ID, true );
		}
	}
		

}


