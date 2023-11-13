<?php // (C) Copyright Bobbing Wide 2015, 2022, 2023

/**
 * Single competitor
 *
 * 
 */

class TAGS_competitor {

	public $ID;
	public $event;
	public $player;
	public $statuses;
	public $playing;
	
	public $name;
	private $post;
	
	function __construct( $post ) {
		$ID = $post->ID;
		$this->ID = $ID;
		$this->event = get_post_meta( $ID, "_event", true );
		$this->player = get_post_meta( $ID, "_player", true );
		$this->statuses = wp_get_post_terms( $ID, "playing_status" );
		$this->post = $post;
	}
	
	function create_competitor() {
	
	}
	
	function update_competitor() {
	
	}

	function playing_buggy() {
		return $this->playing( 'buggy');
	}
	
	function playing_yes() {
		
		return( $this->playing('yes') );
	}
	
	function playing_no() {
		return( $this->playing('no') );
	}
	
	function playing_tbc() {
		return( $this->playing('tbc') );
	}
	
	function playing( $name ) {
		$checkbox = "event_{$name}[" . $this->ID . "]";
		$it = icheckbox( $checkbox, $this->playing[ $name ] ); 
		return( $it );
		
	} 
	
	function statuses() {
		$this->playing['yes'] = false;
		$this->playing['buggy'] = false;
		$this->playing['no'] = false;
		$this->playing['tbc'] = false;
		foreach ( $this->statuses as $status ) {
			$this->playing[ $status->slug ] = "checked";
		}
	}
	
	function player() {
		return( get_the_title( $this->player ) );
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
		//$row = array();
		//$row[] = $this->ID();
		//$row[] = $this->event();
		//$row[] = $this->player();
		$this->statuses();
		//$row[] = $this->playing_yes();
		//$row[] = $this->playing_no();
		//$row[] = $this->playing_tbc();
		//bw_tablerow( $row );
		
		$checked_array = $this->checked();
		
		
		
		
		bw_radio( "competitor[{$this->ID}]"
						, $this->player()
						, array( "yes", "buggy", "no", "tbc" )
						, array( "Yes", "Buggy", "No", "TBC" )
						, null
						, $checked_array 
						);
	}
	
}
