<?php // (C) Copyright Bobbing Wide 2015

/**
 * Implement the mapping for users we're migrating 
 *
 */
class TD2W_users {

	public $users;
	
	private $results;
	
	
	function __construct() {
		$this->users = array();
		$this->load_users();
		$this->process_users();
		$this->report();
	}
	
	function load_users() { 
    global $wpdb;
		$request =  "select uid, name, pass, mail from users where uid > 1 order by uid ";
		$results = $wpdb->get_results( $request );
	 	$this->results = $results;
		print_r( $results );
	}
	
	function report() {
		print_r( $this->users );
		//gob();
	}
	
	function process_users() {
		foreach ( $this->results as $result ) {
			$id = $this->load_user( $result );
			if ( !$id ) {
				$id = $this->create_user( $result );
			} else {
				//$this->update_course( $result, $id );
			}
			$this->users[ $result->uid ] = $id;
			
		}
	}
	
	function map( $uid ) {
		$mapped = bw_array_get( $this->users, $uid, null );
		return( $mapped );
	}
	
	
	function load_user( $result ) {
		$user = bw_get_user( $result->name );
		if ( $user ) {
			$id = $user->ID;
		} else { 
			$id = null;
		}
		return( $id );
	}
	
	
	function create_user( $result ) {
	
		$id = wp_create_user( $result->name, $result->pass, $result->mail );
		return( $id );
								
	
	}
	
	

}


