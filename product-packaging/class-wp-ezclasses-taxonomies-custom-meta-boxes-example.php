<?php
/** 
 * Uber-simple example of what implementing Class_WP_ezClasses_Taxonomies_Custom_Meta_Boxes in your theme / plugin / project might look like.
 *
 * TODO
 *
 * PHP version 5.3
 *
 * LICENSE: TODO
 *
 * @package WPezClasses
 * @author Mark Simchock <mark.simchock@alchemyunited.com>
 * @since 0.5.1
 * @license TODO
 */
 

// No WP? Die! Now!!
if (!defined('ABSPATH')) {
	header( 'HTTP/1.0 403 Forbidden' );
    die();
}

if ( ! class_exists('Class_WP_ezClasses_Taxonomies_Custom_Meta_Boxes_Example') ) {
  class Class_WP_ezClasses_Taxonomies_Custom_Meta_Boxes_Example extends Class_WP_ezClasses_Taxonomies_Custom_Meta_Boxes{
  	
	protected function taxonomy_cmb_todo(){
	
	  $arr_taxonomy_cmb_todo = array(
	    'taxonomy'			=> 'category', 					// note: category is the default but we'll show it again here
		'meta_box_title'	=> 'My New Category Metabox',
		'view'				=> 'radio',						// the other accepted value is 'select'
		'post_types'		=> array('post')				// which posts types get the new metabox for this taxonomy? 
		);
		
		/**
		 * Yup. That's it. It's this ez. 
		 *
		 * If you look in the main class you'll see a number of other defaults that you can override here or override 
		 * entire methods if you prefer. 
		 */
		
	  return  $arr_taxonomy_cmb_todo;
	}

	  
  }
} 

/**
 * And this is how you instantiate: 
 */
$obj_instantiate = Class_WP_ezClasses_Taxonomies_Custom_Meta_Boxes_Example::ez_new();
