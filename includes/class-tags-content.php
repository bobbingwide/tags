<?php // (C) Copyright Bobbing Wide 2015

/**
 * Content enhancing 
 *
 * Generic implementation of tabbing sections for content
 * using the oik_tabs parameter
 * 
 * Based on the procedural code developed for oik-plugins
 * 
 *
 */
class TAGS_content {
	public $post;
	
	public $tabs;
	
	public $content = null;
	
	function __construct( $post ) {
		$this->post = $post;
		$this->content = $post->post_content;
	}
	
	function set_tabs( $parms ) {
		$tabs = array();
		foreach ( $parms as $parm ) {
			$tab = new TAGS_tab( $parm ); 
			$tabs[ $tab->tab ] = $tab;
		}
		$this->tabs = $tabs;
	}
	
	function default_tab() {
		reset( $this->tabs);
		return( current( $this->tabs) );
	}
	
	/**
	 * Display the tabs
	 */
	
	function show_tabs() {
		$current_tab = $this->selected();
		$url = get_permalink( $this->post->ID );
		sdiv( get_class(). "-info" );
		sul( null, "sections" );
		$tabs = $this->tabs;
		foreach ( $tabs as $tab_name => $tab ) {
			$class = "section-$tab_name" ;
			$target_url = add_query_arg( "oik-tab", $tab_name, $url );
			if ( $tab_name === $current_tab->tab ) {
				stag( "li", "current" );
			} else {
				stag( "li" );
			}
			alink( $class, $target_url, $tab->label ); 
			etag( "li" );
		}
		eul();
		ediv();
		
	
	}
	
	/** 
	 * Set the content based on the selected tab
	 */
	function show_content() {
	
		sediv( "clear" );
		sdiv( get_class(). "-body" );
		$tab = $this->selected();
		call_user_func( $tab->method );
		ediv();
		
		
	}
	
	function selected() {
		$selected = bw_array_get( $_REQUEST, "oik-tab", null );
		if ( $selected ) {
			$selected = bw_array_get( $this->tabs, $selected, null );
		} 
		if ( null === $selected ) {
			$selected = $this->default_tab();
		}
		return( $selected );
	}
	
	/**
	 * Enhance the content depending on the selected tab
	 */
	function enhance_content() {
		$this->push();
		$this->pre_tabs();
		$this->show_tabs();
		$this->show_content();
		$this->pop();
	}
	
	function content() {
		return( $this->content );
	}
	
	/**
	 * Display anything before the tabs
	 */
	function pre_tabs() {
		c( "Pre-tabs" );
	}
	
	function push() {
		bw_push();
	}
	
	function pop() {
		$this->content = bw_ret();
		bw_pop();
	}

	

}
