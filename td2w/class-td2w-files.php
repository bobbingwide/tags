<?php // (C) Copyright Bobbing Wide 2015-2019

/**
 * Implement the mapping for files we're migrating 
 *
 * From files to attachments
 * 
 * 
 * files mapped by fid, uid, filename, filepath, filemime, filesize, status, timestamp
 
 
 */
class TD2W_files {

	public $files;
	
	private $results;
	
	
	function __construct() {
		$this->files = array();
		$this->load_files();
		$this->process_files();
		//$this->report();
	}
	
	function load_files() { 
    global $wpdb;
		$request =  "select fid, filename, filepath, filemime, timestamp from files order by fid ";
		$results = $wpdb->get_results( $request );
	 	$this->results = $results;
		//print_r( $results );
	}
	
	function report() {
		print_r( $this->files );
	}
	
	function process_files() {
		foreach ( $this->results as $result ) {
			$id = $this->load_attachment( $result );
			if ( !$id ) {
				$id = $this->create_attachment( $result );
			}
			update_post_meta( $id, "_nid", $result->fid );
			$this->files[ $result->fid ] = $id;
		}
	}
	
	function load_attachment( $result ) {
		oik_require( "includes/bw_posts.php" );
	
		$atts = array( "post_type" => "attachment"
								 , "post_parent" => 0
								 , "meta_key" => "_nid"
								 , "meta_value" => $result->fid
								 );
		$posts = bw_get_posts( $atts ); 
		if ( $posts ) {
			$id = $posts[0]->ID;
		} else { 
			$id = null;
		}
		return( $id );
	}
	
	function map( $fid ) {
		$mapped = bw_array_get( $this->files, $fid, null );
		return( $mapped );
	}
	
	/**
	 * 4 files missed on the first pass
	
	
Warning: copy(c:/apache/htdocs/tags/sites/default/files/chichester-gc-patio.jpg): failed to open stream: No such file or directory in
 C:\apache\htdocs\tags\wp-content\plugins\tags\includes\class-td2w-files.php on line 62

Warning: copy(c:/apache/htdocs/tags/sites/default/files/Merlin-shield.jpg): failed to open stream: No such file or directory in C:\ap
ache\htdocs\tags\wp-content\plugins\tags\includes\class-td2w-files.php on line 62

Warning: copy(c:/apache/htdocs/tags/sites/default/files/tags-logo-186-in-200.jpg): failed to open stream: No such file or directory i
n C:\apache\htdocs\tags\wp-content\plugins\tags\includes\class-td2w-files.php on line 62

Warning: copy(c:/apache/htdocs/tags/sites/default/files/Andy-Holding.jpg): failed to open stream: No such file or directory in C:\apa
che\htdocs\tags\wp-content\plugins\tags\includes\class-td2w-files.php on line 62
Array

 */
	
	function create_attachment( $result ) {
		require_once(ABSPATH . 'wp-admin/includes/file.php');
		require_once(ABSPATH . 'wp-admin/includes/image.php');
		require_once(ABSPATH . 'wp-admin/includes/media.php');
		
		$filename = $result->filename;
		$full_name = "c:/apache/htdocs/tags/" . $result->filepath;
		$tmp_name = wp_tempnam( $filename );
		copy ( $full_name, $tmp_name );
		$file_array = array( "name" => $filename 
											 , "tmp_name" => $tmp_name 
											 );
											 
		

		$id = media_handle_sideload( $file_array, 0, $filename );
		return( $id );
	}

}


