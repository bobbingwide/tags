<?php // (C) Copyright Bobbing Wide 2015

/**
 * Single result
 *
 * 
 */

class TAGS_result {

	public $ID;
	public $event;
	public $player;
	public $result_type;
	public $details;
	
	public $name;
	private $post;
	
	/**
	 * Reference back to the TAGS_results classs
	 */										
	private $results; 
	
	function __construct( $post, $results ) {
		$this->results = $results;
		$ID = $post->ID;
		$this->ID = $ID;
		$this->event = get_post_meta( $ID, "_event", true );
		$this->player = get_post_meta( $ID, "_player", true );
		$this->result_type = wp_get_post_terms( $ID, "result_type", array( "fields" => "ids" ) );
		//print_r( $this->result_type );
		$this->details = get_post_meta( $ID, "_details", true );
		$this->post = $post;
	}
	
	
	function result_type() {
		$terms = get_terms( "result_type", array( "hide_empty" => false, "order" => "ASC" ) );
		$term_array = bw_term_array( $terms );
		//$term_id = get
		return( iselect( "result_type", $this->result_type[0], array( '#options' => $term_array ) ) ); 
	}
	
	/**
	 * Return the field name taking into account the result ID
	 */
	function name( $name ) {
		$name = $name;
		$name .= "[";
		$name .= $this->ID;
		$name .= "]";
		return( $name );
	}	
	
	/**
	 *
	 * get_the_title( $this->player )
	 */
	function player() {
		$name = $this->name( "player" );
		$players = iselect( $name, $this->player, array( '#options' => $this->results->players ) );
		return( $players  );
	}
	
	function details() {
		$name = $this->name( "_details" );
		$details = itext( $name, 80, $this->details );
		return( $details );
	}
	
	
	function ID() {
		return( $this->ID );		
	}
	
	function player_ID() {
		return( $this->player );
	}
	 
	function event() {
		return( $this->event );
	}
	
	function checked() {
	  /*
		if ( $this->playing['yes'] )
			$checked = array( "checked", null, null );
		} elseif ( $this->playing['no'] ) {
			$checked = array( null, "checked", null );
		} else {
			$checked = array( null, null, "checked" );
		}
		*/
		$checked = array_values( $this->playing );
		return( $checked );
	}
	
	function display() {
		$row = array();
		$row[] = $this->ID();
		$row[] = $this->event();
		$row[] = $this->player();
		$row[] = $this->result_type();
		$row[] = $this->details();
		$this->result_type();
		//$row[] = $this->playing_yes();
		//$row[] = $this->playing_no();
		//$row[] = $this->playing_tbc();
		bw_tablerow( $row );
		
	}
	
}
