<?php // (C) Copyright Bobbing Wide 2015

/**
 * Implement the mapping for competitors we're migrating 
 
 * content_field_player: vid, nid, delta, field_player_nid
 * 
 * 6 6 20 26
 * 6 6 19 17  - is delta the sort sequence?
 *
 * From  content_field_competition: vid, nid, field_competition_nid
 
 * 6 6 3 
 * 32 32 34
 
 
												
 * nid is the ID of the competitors node
 *
 * field_competition_nid is the ID of the event
 
 * nid 6 - competitors 31 July Plastmo
 * nid 3 - event 31 July Plastmo
 * nid 26 - player Steve Mouland
 
 * node type='competitors'
 * content_type_competitors: vid, nid - seems unnecessary
 * 
 * node 6 is the 'competitors' node
 * joined by content_field_player.nid to field_player_nid which is ID of a 'player'
 *  and content_field_competition.nid to field_competition_nid which is the ID of the 'event'
 *
 * We need to create a new "competitor" post type which is a single mapping of a 'player' to an 'event'
 * with a taxonomy of "Yes" (29) , "No", "Maybe"   
 
 */
class TD2W_competitors {

	public $competitors;
	
	private $results;
	
	private $events;
	private $players;
	
	private $uniqid; 
	
	private $event_id;
	
	
	function __construct( $events, $players ) {
		$this->events = $events;
		$this->players = $players;
		$this->uniqid = null;
		$this->event_id = 0;
		$this->competitors = array();
		$this->load_competitors();
		$this->process_competitors();
		//$this->report();
	}
	
	function load_competitors() { 
    global $wpdb;
		$request =  "select n.nid, n.title, r.body, n.created, n.changed, c.field_competition_nid cid, p.field_player_nid pid "; 
		$request .= " from node n, content_field_competition c, content_field_player p, node_revisions r ";
		$request .= " where n.type = 'competitors' and n.nid = c.nid and n.nid = p.nid and n.nid = r.nid  ";
		$request .= " order by nid,cid,pid ";
		$results = $wpdb->get_results( $request );
	 	$this->results = $results;
		//print_r( $results );
	}
	
	function report() {
		print_r( $this->competitors );
	}
	
	function process_competitors() {
		foreach ( $this->results as $result ) {
			$this->uniqid( $result );
			$id = $this->load_competitor( $result );
			if ( !$id ) {
				$id = $this->create_competitor( $result );
			} else {
				$this->update_competitor( $result, $id );
			}
			wp_set_post_terms( $id, 29, "playing_status" );
			$this->update_event( $result );
			$this->competitors[ $result->nid ] = $id;
			
		}
	}
	
	function map( $tid ) {
		$mapped = bw_array_get( $this->competitors, $tid, null );
		return( $mapped );
	}
	
	function uniqid( $result ) {
		$uniqid = array();
		$uniqid[] = $result->nid;
		$uniqid[] = $result->cid;
		$uniqid[] = $result->pid;
		
		$this->uniqid = implode( "-", $uniqid );
	}
	
	
	function load_competitor( $result ) {
		oik_require( "includes/bw_posts.inc" );
	
		$atts = array( "post_type" => "competitor"
								 , "post_parent" => 0
								 , "meta_key" => "_nid"
								 , "meta_value" => $this->uniqid
								 );
		$posts = bw_get_posts( $atts ); 
		if ( $posts ) {
			$id = $posts[0]->ID;
		} else { 
			$id = null;
		}
		return( $id );
	}
	
	
	function create_competitor( $result ) {
		$post = array( "post_type" => "competitor" 
								 , "post_title" => $this->uniqid
								 , "post_name" => $this->uniqid
								 , "post_content" => ""
								 , "post_status" => "publish" 
								 );
		$_POST['_nid'] = $this->uniqid;
		$_POST['_event'] = $this->events->map( $result->cid );
		$_POST['_player'] = $this->players->map( $result->pid ); 
		
		
		$id = wp_insert_post( $post, true );
		return( $id );
	}
	
	/**
	 * Why not just update post_meta! 
	 */
	function update_competitor( $result, $id ) {
		$post = array( "ID" => $id  );
		$_POST['_nid'] = $this->uniqid;
		$_POST['_event'] = $this->events->map( $result->cid );
		$_POST['_player'] = $this->players->map( $result->pid ); 
		wp_update_post( $post );
	}
	
	function update_event( $result ) {
		$event_id =  $this->events->map( $result->cid );
		if ( $event_id != $this->event_id ) {
			$this->event_id = $event_id;
      update_post_meta( $event_id, "_notes", $result->body );
		}
	}
	
	

}


