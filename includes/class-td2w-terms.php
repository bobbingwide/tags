<?php // (C) Copyright Bobbing Wide 2015

/**
 * Implement the term mapping for posts we're migrating
 *
 */


class TD2W_terms {

	public $terms;
	
	private $results;
	
	
	function __construct() {
		$this->load_terms();
		$this->process_terms();
	}
	
	function load_terms() 
    global $wpdb;
		$request =  "select tid,vid,name,desc from term_data";
		$results = $wpdb->get_results( $request );
	 	$this->results = $results;
	}
	
	function process_terms() {
		foreach ( $this->results as $result ) {
			echo "Find {$result->name}" . PHP_EOL;	 
		}
	}

}

class TD2W_term {

	public $tid;
	public $vid;
	public $name;
	public $desc;
	public $weight;
	
	function __construct( $row ) {
		$this->tid = $row->tid;
	}

	

}


