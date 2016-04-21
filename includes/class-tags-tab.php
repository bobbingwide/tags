<?php // (C) Copyright Bobbing Wide 2015

/**
 * Implement an "oik-tab" for a TAGS post type
 *
 * 
 */
class TAGS_tab {
	/**
	 * oik-tab value
	 */
	public $tab;
	
	/**
	 * Label in the list
	 */
	public $label;
	
	/**
	 * Implementing method
	 */
	public $method;
 
	/**
	 * Construct a TAGS_tab object
	 *
	 * @param array $parm expected to contain 3 values: tag, label and method
	 */
	function __construct( $parm ) {
		$this->set_tab( $parm[0], $parm[1], $parm[2] ) ;
	}
	
	/**
	 * Set the values for the TAGS_tab object
	 *
	 * @TODO Can we determine the tag and label from the method?
	 */
	function set_tab( $tab, $label, $method ) {
		$this->tab = $tab;
		$this->label = $label;
		$this->method = $method;
	}

}

