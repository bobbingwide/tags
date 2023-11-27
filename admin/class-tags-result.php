<?php // (C) Copyright Bobbing Wide 2015, 2021, 2023

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
	
	private $new_row;
	
	/**
	 * Reference back to the TAGS_results classs
	 */										
	private $results; 
	
	function __construct( $post, $results ) {
		$this->results = $results;
		if ( $post ) {
			$ID = $post->ID;
			$this->ID = $ID;
			$this->event = get_post_meta( $ID, "_event", true );
			$this->player = get_post_meta( $ID, "_player", true );
			$this->result_type = wp_get_post_terms( $ID, "result_type", array( "fields" => "ids" ) );
			//print_r( $this->result_type );
			$this->details = get_post_meta( $ID, "_details", true );
			$this->post = $post;
		} else {
			$this->ID = 0;
			$this->result_type = [ '0' ];  // Sets the result type to 0 for None.
			$this->event = null;
		}
	}
	
	
	function result_type() {
		$terms = get_terms( "result_type", array( "hide_empty" => false, "order" => "ASC" ) );
		$term_array = bw_term_array( $terms );
		//$term_id = get
		$name = $this->name( "result_type" );
		$args = array( '#options' => $term_array );
		if ( !$this->ID ) {
			$args['#optional'] = true;
		}
		return( iselect( $name, $this->result_type[0], $args ) ); 
	}
	
	/**
	 * Return the field name taking into account the result ID
	 */
	function name( $field ) {
		if ( $this->ID ) {
			$name = $field;
			$name .= "[";
			$name .= $this->ID;
			$name .= "]";
		} else {
			$name = "_";
			$name .= $field;
			$name .= "[";
			$name .= $this->new_row;
			$name .= "]";
		}
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
		$name = $this->name( "details" );
		$details = itext( $name, 80, $this->details );
		return( $details );
	}
	
	
	/**
	 * Return a link to the result ID
	 */
	function ID() {
		$permalink = get_permalink( $this->ID );
		$link = retlink( null, $permalink, $this->ID );
		return( $link );		
	}
	
	function player_ID() {
		return( $this->player );
	}
	 
	function event() {
		if ( null === $this->event) {
			return '';
		}
		$permalink = get_permalink( $this->event );
		$link = retlink( null, $permalink, $this->event );
		return( $link );
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
	
	/**
	 * Display the details
	 */
	function display() {
		$row = array();
		$row[] = $this->ID();
		$row[] = $this->event();
		$row[] = $this->result_type();
		$row[] = $this->player();
		$row[] = $this->details();
		//$this->result_type();
		//$row[] = $this->playing_yes();
		//$row[] = $this->playing_no();
		//$row[] = $this->playing_tbc();
		bw_tablerow( $row );
		
	}
	
	function new_rows( $rows ) {
		for ( $this->new_row=1; $this->new_row <= $rows ; $this->new_row++ ) {
			$this->display();
		}
	}
		
		
	
}
