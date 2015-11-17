<?php // (C) Copyright Bobbing Wide 2015

/**
 * Implement the term mapping for posts we're migrating
 *
 */


class TD2W_terms {

	public $terms;
	
	private $results;
	
	
	function __construct() {
		$this->terms = array();
		$this->load_terms();
		$this->process_terms();
		$this->report();
	}
	
	function load_terms() { 
    global $wpdb;
		$request =  "select tid,vid,name,description from term_data where vid in ( 3,4)";
		$results = $wpdb->get_results( $request );
	 	$this->results = $results;
		//print_r( $results );
	}
	
	function report() {
		print_r( $this->terms );
	}
	
	function process_terms() {
		foreach ( $this->results as $result ) {
			echo "Find {$result->name}" . PHP_EOL; 
			$vid = $result->vid;
			switch ( $vid ) {
				case 3:
					$taxonomy = "membership";
					break;
				case 4: 
					$taxonomy = "result";	
					break;
					
				default: 
					gob();
					continue;
			}
			$term = $result->name;
			$args = array( "name" => $term
									 , "taxonomy" => $taxonomy
									 , "description" => $result->description
									 );
									 
				
			$term	= wp_insert_term( $term, $taxonomy, $args );
			//print_r( $output );
			if (  is_wp_error( $term ) ) {
				//print_r( $term );
				$id = $term->error_data[ 'term_exists'];
				
			} else {
				$id = $term->term_id;
			}
			
			$this->terms[ $result->tid ] = $id;
			
		}
	}
	
	function map( $tid ) {
		$mapped = bw_array_get( $this->terms, $tid, null );
		return( $mapped );
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


