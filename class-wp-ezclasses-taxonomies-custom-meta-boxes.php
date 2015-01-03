<?php
/**
 * Remove a default WordPress taxonomy meta box and then add your own meta box using different form elements (e.g., radio buttons or select) to make the selection.
 *
 * Automates the removal of the default WordPress taxonomy meta box (for the specified taxonomy) and then add your own meta box using different form elements (e.g., radio buttons or select) to make the selection.
 *
 * Inspired by: https://github.com/WebDevStudios/WDS_Taxonomy_Radio/blob/master/WDS_Taxonomy_Radio.class.php
 *
 * PHP version 5.3
 *
 * LICENSE: TODO
 *
 * @package WP ezClasses
 * @author Mark Simchock <mark.simchock@alchemyunited.com>
 * @since 0.5.0
 *
 */
/**
 * == Change Log ==
 *
 * -- 0.5.1 - Fri 2 Jan 2015
 * ---- Whoa. Refactoring!
 *
 * -- 0.5.0 - ??
 * ---- Pop the champagne!
 */
/**
 * == TODO ==
 *
 * Change from Public to Protected
 *
 */

if (!class_exists('Class_WP_ezClasses_Taxonomies_Custom_Meta_Boxes')) {
  class Class_WP_ezClasses_Taxonomies_Custom_Meta_Boxes extends Class_WP_ezClasses_Master_Singleton{
  
    protected $_arr_init; // NEW GOOD!!

    /**
     *
     */
    protected function __construct(){
      parent::__construct();
    }

    /**
     *
     */
    public function ez__construct(){
	
      $arr_init_defaults = $this->init_defaults();
      $arr_tax_cmb_todo = $this->taxonomy_cmb_todo();

      $this->_arr_init = WPezHelpers::ez_array_merge(array(
        $arr_init_defaults,
        $arr_tax_cmb_todo
      ));

	 
      if (isset($this->_arr_init['arr_arg_validation']) && ! is_bool($this->_arr_init['arr_arg_validation'])) {
        $this->_arr_init['arr_arg_validation'] = $arr_init_defaults['arr_arg_validation'];
      }

      /* TODO
      if ( $this->_arr_init['arr_arg_validation'] !== false ){
      $arr_validate_return = $this->taxonomy_cmb_validate($arr_args);
      if ($arr_validate_return['status'] === true ) {
      $arr_args = $arr_validate_return['arr_args'];
      } else {
      return $arr_validate_return;
      }
      }
      */

      /**
	   * do we want to do the meta_box remove / add business? 
	   * yes, it's kinda an odd question to ask, but perhaps we're using this class for some DIY elsewhere?
	   */
      if ($this->_arr_init['add_meta_box'] !== false) {
        $this->add_action_taxonomy_amb();
      }
    }

    /**
     * This is where your magic happens. The idea here is to (re) define as little as possible.
     *
     * You can override any of the inti_defaults() here but this is the minimal you'll need to TODO to get going
     *
     * This method remains simply as an example.
     */
    protected function taxonomy_cmb_todo(){
      $arr_taxonomy_cmb_todo = array(
        'taxonomy' => 'category',
        'meta_box_title' => 'TODO',
        'view' => 'select',
        'post_types' => array('post'),
      );
	  
      return $arr_taxonomy_cmb_todo;
    }

    /**
     * currently NA (but it's here just in case)
     */
    protected function setup() {
      $this->_version = '0.5.0';
      $this->_url = plugin_dir_url(__FILE__);
      $this->_path = plugin_dir_path(__FILE__);
      $this->_path_parent = dirname($this->_path);
      $this->_basename = plugin_basename(__FILE__);
      $this->_file = __FILE__;
    }
	

    /**
     * Roll all the other default pieces into one
     */
    protected function init_defaults(){
      $arr_init_defaults = WPezHelpers::ez_array_merge(
	    array(
          $this->ez_defaults() ,
          $this->taxonomy_cmb_defaults() ,
          $this->taxonomy_cmb_post_types() ,
          $this->taxonomy_cmb_post_types_exclude() ,
          $this->taxonomy_cmb_get_terms_defaults() ,
          )
	    );
      return $arr_init_defaults;
    }

    /**
     * currently NA
     */
    protected function ez_defaults(){
      $arr_ez_defaults = array(
        'active'				=> true, 	// currently NA
        'active_true' 			=> false, 	// currently NA (use the active true "filtering")
        'filters'				=> false, 	// currently NA
        'arr_arg_validation'	=> false,	// currently NA
      );
      return $arr_ez_defaults;
    }

    /**
     *
     */
    protected function taxonomy_cmb_defaults(){
	
	  // * = defined elsewhere in this class

      $arr_defaults = array(
        'taxonomy' 					=> 'category',
        'view' 						=> 'select',
        'add_meta_box' 				=> true,

        //*	'post_types'			=> array('posts');
        //*	'post_types_exclude'	=> array('attachment', 'revision', 'nav_menu_item'),

        'add_meta_boxes_priority'	=> 10,
        'meta_box_title' 			=> 'Categories - Pick One',
        'meta_box_priority' 		=> 'high', 			// 'high', 'core', 'default' or 'low'
        'meta_box_position' 		=> 'side', 			// 'normal', 'advanced', or 'side'
        'help'						=> '',				// will show up in the bottom of the meta box. kinda like a helpful hint, if you will			
        'help_class' 				=> 'howto',
        'error' 					=> '<b>Oops - Please be sure to Save.</b>',
        'error_class' 				=> 'howto',
        'no_selection' 				=> false,		 	// bool - add a "None of the Above"? Note: This kinda doesn't work with Categories since WP *always* wants a category and will impose its will on you.
        'no_selection_value' 		=> 0,
        'no_selection_label' 		=> 'No',
        'default_tax_id' 			=> -1, 				// else, integer of the tax_id you want as the default

        //*	'get_terms_defaults'					=> $this->taxonomy_cmb_get_terms_defaults(),
      );

      return $arr_defaults;
    }

    protected function taxonomy_cmb_post_types(){
      return array(
	    'post_types' => get_post_types('', 'names')
		);
    }

	/**
	 * The default is all so there are some within all that should naturally be excluded.
	 */
    protected function taxonomy_cmb_post_types_exclude(){
      return array(
        'post_types_exclude' => array(
          'attachment',
          'revision',
          'nav_menu_item'
          )
        );
    }

    /**
     * http://codex.wordpress.org/Function_Reference/get_terms
     */
    protected function taxonomy_cmb_get_terms_defaults(){
      $arr_get_terms_defaults = array(
        'orderby' => 'name',
        'order' => 'ASC',
        'hide_empty' => false,
        'exclude' => array() ,
        'exclude_tree' => array() ,
        'include' => array() ,
        'number' => '',
        'fields' => 'all',
        'slug' => '',
        'parent' => '',
        'hierarchical' => true,
        'child_of' => 0,
        'get' => '',
        'name__like' => '',
        'pad_counts' => false,
        'offset' => '',
        'search' => '',
        'cache_domain' => 'core'
      );
      return array(
        'get_terms_defaults' => $arr_get_terms_defaults
      );
    }

    /**
     *
     */
    public function add_action_taxonomy_amb(){
	
      $arr_init = $this->_arr_init;
      add_action('add_meta_boxes', array($this,'taxonomy_add_meta_box_swap') , $arr_init['meta_box_priority']);
    }

    /**
     * Removes and replaces the built-in taxonomy meta_box with our own.
     */
    protected function taxonomy_cmb_validate($arr_args = array()){
	
      $str_return_source = get_class() . ' :: taxonomy_custom_meta_boxes_validate()';
	  
	  // TODO

      return array(
        'status' => true,
        'msg' => 'success',
        'source' => $str_return_source,
        'arr_args' => $arr_args
      );
    }

    /**
     * Removes the current meta box for the taxonomy and replaces it with a new meta box
     */
    public function taxonomy_add_meta_box_swap(){
      $arr_init = $this->_arr_init;

      $arr_post_types_minus_exclude = array_diff($arr_init['post_types'], $arr_init['post_types_exclude']);

      foreach($arr_post_types_minus_exclude as $str_key => $str_post_type) {

        // remove default category type meta_box
        // http://codex.wordpress.org/Function_Reference/remove_meta_box
        remove_meta_box($arr_init['taxonomy'] . 'div', $str_post_type, 'side');

        // remove default tag type meta_box
        remove_meta_box('tagsdiv-' . $arr_init['taxonomy'], $str_post_type, 'side');

        // add our custom meta_box
        add_meta_box($arr_init['taxonomy'] . '_custom_meta_box', $arr_init['meta_box_title'], array($this, 'taxonomy_add_meta_box_callback') , $str_post_type, $arr_init['meta_box_position'], $arr_init['meta_box_priority']);
      }
    }

    /**
     * Maps to the desired view
     */
    public function taxonomy_add_meta_box_callback(){
	
      $arr_init = $this->_arr_init;
      $arr_view_active = $this->taxonomy_amb_callback_view_active();
      $str_method = 'taxonomy_amb_callback_view_' . trim(strtolower($arr_init['view']));
      if (isset($arr_view_active[$arr_init['view']]) && $arr_view_active[$arr_init['view']] === true && method_exists($this, $str_method)) {
        $this->$str_method();
      }
      else {
	    // default is select
        $this->taxonomy_amb_callback_view_select();
      }
    }

    /**
     * Which of the current views are okay to use?
     */
    protected function taxonomy_amb_callback_view_active(){
      $arr_views = array(
        'radio' => true,
        'select' => true,
        );
      return $arr_views;
    }

	/**
	 * renders the markup for radio
	 */ 
    public function taxonomy_amb_callback_view_radio(){
	
      $arr_init = $this->_arr_init;
      $str_return_source = get_class() . ' :: taxonomy_amb_callback_view_radio()';

      // uses same noncename as default box so no save_post hook needed
      wp_nonce_field('taxonomy_' . $arr_init['taxonomy'], 'taxonomy_noncename');
	  
      /**
       * get the taxonomy terms that have been selected for this post
       *
       * http://codex.wordpress.org/Function_Reference/wp_get_object_terms
       */
      $arr_wp_get_object_terms = wp_get_object_terms(get_the_ID() , $arr_init['taxonomy']);
	  
      /**
       * get *all* terms in this taxonomy
       *
       * http://codex.wordpress.org/Function_Reference/get_terms
       */
      $arr_terms = (array)get_terms($arr_init['taxonomy'], $arr_init['get_terms_defaults']);

      // filter the ids out of the terms
      $arr_existing = array();
      if ( ! is_wp_error($arr_wp_get_object_terms) && ! empty($arr_wp_get_object_terms) ) {

        // http://codex.wordpress.org/Function_Reference/wp_list_pluck
        $arr_existing = (array)wp_list_pluck($arr_wp_get_object_terms, 'term_id');
      } elseif ( is_wp_error($arr_wp_get_object_terms) ) {
        return array(
          'status' => false,
          'msg' => 'Error: is_wp_error() === true',
          'source' => $str_return_source,
          'tax_slug' => $arr_init['taxonomy'],
          'str_to_return' => 'error'
        );
      }

      /*
      * TODO - if we DO get an error does that mean to abort??
      */

      // Check if taxonomy is hierarchical
      // Terms are saved differently between types

      $bool_hierarchical = $this->taxonomy_get_taxonomy()->hierarchical;

      // default value
      $mix_default_val = '';
      if ($bool_hierarchical == true) {
        $mix_default_val = 1;
      }

      // input name
      $str_name = 'tax_input[' . $arr_init['taxonomy'] . ']';
      if ($bool_hierarchical == true) {
        $str_name = 'tax_input[' . $arr_init['taxonomy'] . '][]';
      }

      $str_to_return = '';
      $str_to_return_open = '<div style="margin-bottom: 5px;">';
      $str_to_return_open.= '<ul id="' . $arr_init['taxonomy'] . '_taxradiolist" data-wp-lists="list:' . $arr_init['taxonomy'] . '_tax" class="categorychecklist form-no-clear">';

      // If 'category' then we need at least one select, or _bool_no_selection is true
      if ($arr_init['no_selection'] === true) {
        $str_to_return.= '<li id="' . $arr_init['taxonomy'] . '_tax-0">';
        $str_to_return.= '<label><input value="' . $arr_init['no_selection_value'] . '" type="radio" name="' . $str_name . '" id="in-' . $arr_init['taxonomy'] . '_tax-0" ';
        if (empty($arr_existing)) {
          $str_to_return.= ' checked="checked" ';
        }

        $str_to_return.= '> ' . $arr_init['no_selection_label'] . ' ' . $this->taxonomy_get_taxonomy()->labels->singular_name . '</label></li>';
      }

      // loop our terms and check if they're associated with this post
      $int_cnt = 0;
      foreach($arr_terms as $obj_term) {
        $int_cnt++;
        $mix_val = $obj_term->slug;
        if ($bool_hierarchical === true) {
          $mix_val = $obj_term->term_id; // <<< ???
        }

        $str_to_return.= '<li id="' . $arr_init['taxonomy'] . '_tax-' . $obj_term->term_id . '">';
        $str_to_return.= '<label><input value="' . $mix_val . '" type="radio" name="' . $str_name . '" id="in-' . $arr_init['taxonomy'] . '_tax-' . $obj_term->term_id . '" ';

        // if so, they get "checked"

        if (!empty($arr_existing)) {
          $str_to_return.= checked(in_array($obj_term->term_id, $arr_existing) , true, false);
        }
        else {
          if ($arr_init['taxonomy'] == 'category' && $int_cnt == count($arr_terms) && $arr_init['default_tax_id'] < 0) {
            $str_to_return.= checked(1 == 1, true, false);
          }
          else {
            $str_to_return.= checked($obj_term->term_id == $arr_init['default_tax_id'], true, false);
          }
        }

        $str_to_return.= '> ' . $obj_term->name . '</label></li>';
      }

      $str_help = '';
      if (!empty($arr_init['help_class']) && !empty($arr_init['help'])) {
        $str_help = '<p class="' . trim($arr_init['help_class']) . '">' . trim($arr_init['help']) . '</p>';
      }

      /*
      * if there are more than one element in arr_existing then tell the user to resave. this could happen if radio / select is added after the fact
      */
      $str_error = '';
      if (count($arr_existing) > 1) {
        $str_help = '<p class="' . trim($arr_init['error_class']) . '">' . trim($arr_init['error']) . '</p>';
      }

      $str_to_return_close = '</ul>' . $str_help . $str_error . '</div>';

      // if we're not rending the meta_box then just return the <li> list. the code calling the method will be presumed to be finishing the job

      if ($arr_init['add_meta_box'] !== false) {
        echo $str_to_return_open . $str_to_return . $str_to_return_close;
        return array(
          'status' => true,
          'msg' => 'success',
          'source' => $str_return_source,
          'tax_slug' => $arr_init['taxonomy'],
          'str_to_return' => $str_to_return_open . $str_to_return . $str_to_return_close
        );
      }
      else {
        return array(
          'status' => true,
          'msg' => 'success',
          'source' => $str_return_source,
          'tax_slug' => $arr_init['taxonomy'],
          'str_to_return' => $str_to_return
        );
      }
    }

    public function taxonomy_amb_callback_view_select(){
      $arr_init = $this->_arr_init;
      $str_return_source = get_class() . ' :: taxonomy_amb_callback_view_select()';

      // uses same noncename as default box so no save_post hook needed
      wp_nonce_field('taxonomy_' . $arr_init['taxonomy'], 'taxonomy_noncename');
	  
      /**
       * get the taxonomy terms that have been selected for this post
       *
       * http://codex.wordpress.org/Function_Reference/wp_get_object_terms
       */
      $arr_wp_get_object_terms = wp_get_object_terms(get_the_ID() , $arr_init['taxonomy']);
	  
      /**
       * get *all* terms in this taxonomy
       *
       * http://codex.wordpress.org/Function_Reference/get_terms
       */
      $arr_terms = (array)get_terms($arr_init['taxonomy'], $arr_init['get_terms_defaults']);

      // filter the ids out of the terms
      $arr_existing = array();
      if (!is_wp_error($arr_wp_get_object_terms) && !empty($arr_wp_get_object_terms)) {

        // http://codex.wordpress.org/Function_Reference/wp_list_pluck

        $arr_existing = (array)wp_list_pluck($arr_wp_get_object_terms, 'term_id');
      }
      elseif (is_wp_error($arr_wp_get_object_terms)) {
        return array(
          'status' => false,
          'msg' => 'Error: is_wp_error() === true',
          'source' => $str_return_source,
          'tax_slug' => $arr_init['taxonomy'],
          'str_to_return' => 'error'
        );
      }

      /**
       * TODO - if we DO get an error does that mean to abort??
       */

      // Check if taxonomy is hierarchical
      // Terms are saved differently between types
      $bool_hierarchical = $this->taxonomy_get_taxonomy()->hierarchical;

      // default value
      $mix_default_val = '';
      if ($bool_hierarchical == true) {
        $mix_default_val = 0;
      }

      // input name
      $str_name = 'tax_input[' . $arr_init['taxonomy'] . ']';
      if ($bool_hierarchical == true) {
        $str_name = 'tax_input[' . $arr_init['taxonomy'] . '][]';
      }

      $str_to_return = '';
      $str_to_return_open = '<div style="margin-bottom: 5px;">';

      // If 'category' force a selection, or _bool_no_selection is true
      if ($arr_init['no_selection'] === true) {
        //
        $str_to_return.= '<option value="' . $arr_init['no_selection_value'] . '" ';
        if (empty($arr_existing)) {
          $str_to_return.= checked(empty($arr_existing) , true, false);
        }
        $str_to_return.= '> ' . $arr_init['no_selection_label'] . ' ' . $this->taxonomy_get_taxonomy()->labels->singular_name . '</option>';
      }

      // loop our terms and check if they're associated with this post
      $term_id = '';
      $int_cnt = 0;
      foreach($arr_terms as $obj_term) {
        $int_cnt++;
        $mix_val = $obj_term->slug;
        if ($bool_hierarchical === true) {
          $mix_val = $obj_term->term_id;
        }

        $str_to_return.= '<option value="' . $mix_val . '" ';

        // if so, they get "checked"

        if (!empty($arr_existing)) {
          $str_to_return.= selected(in_array($obj_term->term_id, $arr_existing) , true, false);
        }
        else {
          if ($arr_init['taxonomy'] == 'category' && $int_cnt == count($arr_terms) && $arr_init['default_tax_id'] < 0) {
            $str_to_return.= selected('1', '1', false);
          }
          else {
            $str_to_return.= selected($obj_term->term_id, $arr_init['default_tax_id'], false);
          }
        }
        $str_to_return.= '> ' . $obj_term->name . '</option>';
      }

      $str_to_return_open.= '<select id="' . $arr_init['taxonomy'] . '_taxradiolist" name="' . $str_name . '" data-wp-lists="list:' . $arr_init['taxonomy'] . '_tax" class="categorychecklist form-no-clear">';
      $str_help = '';
      if (!empty($arr_init['help_class']) && !empty($arr_init['help'])) {
        $str_help = '<p class="' . trim($arr_init['help_class']) . '">' . trim($arr_init['help']) . '</p>';
      }

      /**
       * if there are more than one element in arr_existing then tell the user to resave. this could happen if radio / select is added after the fact
       */
      $str_error = '';
      if (count($arr_existing) > 1) {
        $str_help = '<p class="' . trim($arr_init['error_class']) . '">' . trim($arr_init['error']) . '</p>';
      }

      $str_to_return_close = '</select>' . $str_help . $str_error . '</div>'; // <<< use $str_to_echo and not echo, echo, echo.

      // if we're not rending the meta_box then just return the <li> list. the code calling the method will be presumed to be finishing the job

      if ($arr_init['add_meta_box'] !== false) {
        echo $str_to_return_open . $str_to_return . $str_to_return_close;
        return array(
          'status' => true,
          'msg' => 'success',
          'source' => $str_return_source,
          'tax_slug' => $arr_init['taxonomy'],
          'str_to_return' => $str_to_return_open . $str_to_return . $str_to_return_close
        );
      }
      else {
        return array(
          'status' => true,
          'msg' => 'success',
          'source' => $str_return_source,
          'tax_slug' => $arr_init['taxonomy'],
          'str_to_return' => $str_to_return
        );
      }
    }

    /**
     *
     */
    public function taxonomy_get_taxonomy() {
      $arr_init = $this->_arr_init;

      // if ( $this->_obj_taxonomy !== true ) {  // ???
      // $this->_obj_taxonomy = get_taxonomy( $arr_init['taxonomy'] );
      // }

      return get_taxonomy($arr_init['taxonomy']); //$this->_obj_taxonomy;
    }
	
  } // close class
} // close if class