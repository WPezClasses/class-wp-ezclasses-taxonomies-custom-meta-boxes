<?php
/** 
 * TODO - 
 *
 * TODO https://github.com/WebDevStudios/WDS_Taxonomy_Radio/blob/master/WDS_Taxonomy_Radio.class.php
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
/*
 * CHANGE LOG
 *
 */

if ( ! class_exists('Class_WP_ezClasses_Taxonomies_Custom_Meta_Boxes') ){
		class Class_WP_ezClasses_Taxonomies_Custom_Meta_Boxes  extends Class_WP_ezClasses_Master_Singleton {
	
			/**
			 * Taxonomy Name - That is, value of $taxonomy as defined in: register_taxonomy( $taxonomy, $object_type, $args );
			 * @var string
			 * @since 0.5.0	
			 */
			protected $_str_taxonomy;
			
			/**
			 * View - Defines form element to be used. Values: radio or select. Default: radio
			 * @var string
			 * @since 0.5.0	
			 */
			protected $str_view;
			
			/**
			 * Add Meta Box - Perhaps we don't want the class to do the add_meta_box() business
			 * @var bool
			 * @since 0.5.0	
			 */
			protected $_bool_add_meta_box;
	
			/**
			 * Post Types - Post types where the meta box should be replaced. Default: All post_types associated with the taxonomy
			 * @var array
			 * @since 0.5.0	
			 */
			protected $_arr_post_types;
			
			/**
			 * Post Types Exclude - Post types where the meta box should *not* be replaced (because sometimes it's easier to just exclude). Default: empty
			 * @var array
			 * @since 0.5.0	
			 */
			protected $_arr_post_types_exclude;

			// Taxonomy object
			protected $_obj_taxonomy;
			

			/**
			 * Add Meta Box Priority - Priority of the add_action('add_meta_box',...). Default: 10
			 * @var integer
			 * @since 0.5.0	
			 */			
			protected $_str_int_add_action_add_meta_box_priority;
	
			/**
			 * Meta Box Title - Title of the new meta box Default: Taxonomy name
			 * @var string
			 * @since 0.5.0	
			 */
			protected $_str_meta_box_title;
	
			/**
			 * Add Meta Box Priority (vertical placement) - Priority passed into the add_meta_box() function. Default: 'high'
			 * @var string
			 * @since 0.5.0	
			 */	
			protected $_str_meta_box_priority;
	
			/**
			 * Add Meta Box Position (column placement) - Position passed into the add_meta_box() function. Default: 'side'
			 * @var string
			 * @since 0.5.0	
			 */	
			protected $_str_meta_box_position;
			
			/**
			 * Help (text) - Additional text below the form element in the "footer" of the meta box. Default: blank
			 * @var string
			 * @since 0.5.0	
			 */	
			protected $_str_help;
			
			/**
			 * Help Class - Class of the paragraph tag ( i.e., <p class="???"> ) that wraps the Help text. Default: 'howto'
			 * @var string
			 * @since 0.5.0	
			 */
			protected $_str_help_class;
			
			/**
			 * Error (text) - If the taxonomy has more than one value stored this lets the user know they need to force a save. Default: 'Oops - Please be sure to Save.'
			 * @var string
			 * @since 0.5.0	
			 */	
			protected $_str_error;
			
			/**
			 * Error Class - Class of the paragraph tag ( i.e., <p class="???"> ) that wraps the Error text. Default: 'howto'
			 * @var string
			 * @since 0.5.0	
			 */
			protected $_str_help_error;
			
			
			// Set to true to hide "None" option & force a term selection
			protected $_bool_force_selection;
			
			/**
			 * Default Taxonomy ID - The default (id) value for a give taxonomy. Especially helpful in enforcing a "required" value.  Default: -1 (i.e., no default tax id)
			 * @var st
			 * @since 0.5.0	
			 */
			protected $_str_default_tax_id;
			
			// all the defaults rolled into one - selectively change (array_merge) the defaults specified in the method taxonomy_defaults()
			protected $_arr_taxonomy_defaults; 
			
			// defaults args for the get_terms() - selectively change (array_merge) the defaults
			protected $_arr_get_terms_defaults;
			
			// selectively change (array_merge) the defaults specified in the method 
			protected $_arr_get_post_types_defaults;
			
			// one-thrid of $_arr_get_post_types_defaults - selectively change (array_merge) the defaults
			protected $_arr_get_post_types_defaults_args;
			
			// one-thrid of $_arr_get_post_types_defaults
			protected $_str_get_post_types_defaults_output;
			
			// one-thrid of $_arr_get_post_types_defaults
			protected $_str_get_post_types_defaults_operator;
	
	
		/**
		 *
		 */
		public function __construct(){
			parent::__construct();
		}	

		/**
		 * 
		 */
		public function ezc_init($arr_args = NULL){   

			$this->taxonomy_custom_meta_boxes($arr_args);
		}	
		
		
		/**
		 *
		 */
		public function taxonomy_custom_meta_boxes($arr_args = NULL){
		
			$bool_validate = true;
			if ( isset($arr_args['validate']) && is_bool($arr_args['validate']) ) {
				$bool_validate = $arr_args['validate'];
			} 
		
			if ( $bool_validate !== false ){
				$arr_validate_return = $this->taxonomy_custom_meta_boxes_validate($arr_args);
				if ($arr_validate_return['status'] === true ) {
					$arr_args = $arr_validate_return['arr_args'];
				} else {
					return $arr_validate_return;		
				}
			}
	
			$arr_args = array_merge($this->taxonomy_custom_meta_boxes_defaults(), $arr_args);	
			
			$this->_str_taxonomy = $arr_args['taxonomy'];
			$this->_str_view = $arr_args['view'];
			$this->_bool_add_meta_box = $arr_args['add_meta_box'];
			$this->_arr_post_types = $arr_args['post_types'];
			$this->_arr_post_types_exclude = $arr_args['post_types_exclude'];	
			$this->_str_int_add_action_add_meta_box_priority = $arr_args['add_action_add_meta_box_priority'];				
			$this->_str_meta_box_title = $arr_args['meta_box_title'];
			$this->_str_meta_box_priority = $arr_args['meta_box_priority'];
			$this->_str_meta_box_position = $arr_args['meta_box_position'];
			$this->_str_help = $arr_args['help'];
			$this->_str_help_class = $arr_args['help_class'];
			$this->_str_error = $arr_args['error'];
			$this->_str_error_class = $arr_args['error_class'];
			$this->_bool_force_selection = $arr_args['force_selection'];
			$this->_str_default_tax_id = $arr_args['default_tax_id'];


			/*
			 * Some quick validations
			 */
			$this->_arr_get_terms_defaults = array();
			if ( isset($arr_args['get_terms_defaults']) && is_array( $arr_args['get_terms_defaults']) ){
				$this->_arr_get_terms_defaults = $arr_args['get_terms_defaults'];
			}	
			
			$this->_arr_get_post_types_defaults = array();
			if ( isset($arr_args['get_post_types_defaults']) && is_array( $arr_args['get_post_types_defaults']) ){
				$this->_arr_get_post_types_defaults = $arr_args['get_post_types_defaults'];
			}
			
			$this->_arr_get_post_types_defaults_args = array();
			if ( isset($arr_args['get_post_types_defaults_args']) && is_array( $arr_args['get_post_types_defaults_args']) ){
				$this->_arr_get_post_types_defaults_args = $arr_args['get_post_types_defaults_args'];
			}

			$this->_str_get_post_types_defaults_output = '';
			if (  isset($arr_args['get_post_types_defaults_output']) && is_array($arr_args['get_post_types_defaults_output']) ){
				$this->_str_get_post_types_defaults_output = $arr_args['get_post_types_defaults_output'];
			}
			
			$this->_str_get_post_types_defaults_operator = '';
			if ( isset($arr_args['get_post_types_defaults_operator']) && is_array($arr_args['get_post_types_defaults_operator']) ){
				$this->_str_get_post_types_defaults_operator = $arr_args['get_post_types_defaults_operator']; 
			}

			// do we want to do the meta_box remove / add business? perhaps we're using this class for some DIY elsewhere? 
			if ( $this->_bool_add_meta_box !== false ) {
				$this->add_action_taxonomy_add_meta_box();
			}
		}
		
		/**
		 *
		 */	
		public function add_action_taxonomy_add_meta_box(){
			add_action( 'add_meta_boxes', array( $this, 'taxonomy_add_meta_box' ), $this->_str_int_add_action_add_meta_box_priority ); 
		}

		/**
		 * Removes and replaces the built-in taxonomy meta_box with our own.
		 */
		public function taxonomy_custom_meta_boxes_validate($arr_args = NULL){
			$str_return_source = get_class() . ' :: taxonomy_custom_meta_boxes_validate()'; 
		
			if ( $this->ezCONFIGS('validate') !== false ){
			
				if ( ! is_array($arr_args) ){
					$arr_args = $this->taxonomy_custom_meta_boxes_defaults();
				}
				
				if ( ( ! isset($arr_args['validate_unset'])) || ( isset($arr_args['validate_unset']) && !is_bool($arr_args['validate_unset']) ) ){
						$arr_args['validate_unset'] = true;
				}
				
				/*
				 * TODO - detailed validation of user defined arg values
				 */
			}
			return array('status' => true, 'msg' => 'success', 'source' => $str_return_source, 'arr_args' => $arr_args);
		}	

		/**
		 * Removes the current meta box for the taxonomy and replaces it with a new meta box 
		 */
		public function taxonomy_add_meta_box() {
	
			// remove the excludes
			$arr_post_types_minus_exclude = array_diff($this->_arr_post_types, $this->_arr_post_types_exclude);
		
			foreach ( $arr_post_types_minus_exclude as $str_key => $str_post_type ) {
		 
				// remove default category type meta_box
				// http://codex.wordpress.org/Function_Reference/remove_meta_box
				remove_meta_box( $this->_str_taxonomy .'div', $str_post_type, 'side' );  
					
				// remove default tag type meta_box
				remove_meta_box( 'tagsdiv-'.$this->_str_taxonomy, $str_post_type, 'side' ); 
					
				// add our custom meta_box
				add_meta_box( $this->_str_taxonomy .'_custom_meta_box', $this->_str_meta_box_title, array( $this, 'taxonomy_add_meta_box_callback' ), $str_post_type, $this->_str_meta_box_position, $this->_str_meta_box_priority );
			 }
		}

		/**
		 * Maps to the desired view
		 */
		public function taxonomy_add_meta_box_callback() {
		
			$str_method = 'taxonomy_add_meta_box_callback_view_' . $this->_str_view;
			if ( method_exists($this, $str_method) ){
			
				$this->$str_method();
			}	
		}
		
		protected function taxonomy_add_meta_box_callback_view_(){
		
			$arr_views = array(
							'radio'		=> true,
							'select'	=> true,
							);
							
			return $arr_views;
		}
		
		
		
		public function taxonomy_add_meta_box_callback_view_radio() {
		
			$str_return_source = get_class() . ' :: taxonomy_add_meta_box_callback_view_radio()'; 

			 // uses same noncename as default box so no save_post hook needed
			 wp_nonce_field( 'taxonomy_'. $this->_str_taxonomy, 'taxonomy_noncename' );

			 /* 
			  * get the taxonomy terms that have been selected for this post 
			  *
			  * http://codex.wordpress.org/Function_Reference/wp_get_object_terms
			  */
			 $arr_wp_get_object_terms = wp_get_object_terms( get_the_ID(), $this->_str_taxonomy ); 
	 
			 /* 
			  * get *all* terms in this taxonomy
			  *
			  * http://codex.wordpress.org/Function_Reference/get_terms
			  */
			 $arr_terms = (array) get_terms( $this->_str_taxonomy, $this->_arr_get_terms_defaults );  
			 
			 // filter the ids out of the terms
			$arr_existing = array();
			if ( ! is_wp_error( $arr_wp_get_object_terms ) && ! empty( $arr_wp_get_object_terms ) ){
			
				// http://codex.wordpress.org/Function_Reference/wp_list_pluck
				$arr_existing = (array) wp_list_pluck( $arr_wp_get_object_terms, 'term_id' );
			} elseif ( is_wp_error( $arr_wp_get_object_terms ) ){
				return array('status' => false, 'msg' => 'Error: is_wp_error() === true', 'source' => $str_return_source, 'tax_slug' => $this->_str_taxonomy, 'str_to_return' => 'error');
			}
									
			/*
			 * TODO - if we DO get an error does that mean to abort?? 
			 */
					
			// Check if taxonomy is hierarchical
			// Terms are saved differently between types
			$bool_hierarchical = $this->taxonomy_get_taxonomy()->hierarchical;
					
			 // default value
			$mix_default_val = '';
			if ( $bool_hierarchical == true ) {
				$mix_default_val = 0;
			}
			 // input name
			$str_name = 'tax_input[' . $this->_str_taxonomy . ']';
			if ( $bool_hierarchical == true ){
				$str_name ='tax_input[' . $this->_str_taxonomy . '][]';
			}
			
			
			 $str_to_return = '';

			 $str_to_return_open = '<div style="margin-bottom: 5px;">';
			 $str_to_return_open .= '<ul id="'. $this->_str_taxonomy .'_taxradiolist" data-wp-lists="list:'. $this->_str_taxonomy .'_tax" class="categorychecklist form-no-clear">';

				// If 'category' force a selection, or _bool_force_selection is true
				/*
				if ( $this->_str_taxonomy == 'category' || $this->_bool_force_selection === true ) {  
					
					 $str_to_return .= '<li id="'. $this->_str_taxonomy .'_tax-0"><label><input value="'. $mix_default_val .'" type="radio" name="'. $str_name .'" id="in-'. $this->_str_taxonomy .'_tax-0" ';
					 if ( empty( $arr_existing ) ){
						$str_to_return .=  ' checked="checked" ';
					 }
					 $str_to_return .=  '> '. sprintf( __( 'No %s', 'wds' ), $this->taxonomy_get_taxonomy()->labels->singular_name ) .'</label></li>';
				}
				*/

			 // loop our terms and check if they're associated with this post
			 $int_cnt = 0;
			 foreach ( $arr_terms as $obj_term ) {
					 $int_cnt++;
					$mix_val = $obj_term->slug;
					if ( $bool_hierarchical === true ){
						$mix_val = $obj_term->term_id;  // <<< ???
					}

					$str_to_return .= '<li id="'. $this->_str_taxonomy .'_tax-'. $obj_term->term_id .'">';
					$str_to_return .= '<label><input value="'. $mix_val .'" type="radio" name="'. $str_name .'" id="in-'. $this->_str_taxonomy .'_tax-'. $obj_term->term_id .'" ';
					// if so, they get "checked"
					if ( ! empty( $arr_existing ) ){
						$str_to_return .= checked( in_array( $obj_term->term_id, $arr_existing ), true, false );
					} else {
					    if ( $this->_str_taxonomy == 'category' && $int_cnt == count($arr_terms) && $this->_str_default_tax_id < 0) {
							$str_to_return .= checked( 1 == 1 , true, false );
						} else {
							$str_to_return .= checked( $obj_term->term_id == $this->_str_default_tax_id , true, false );
						}	
					}
					$str_to_return .= '> '. $obj_term->name .'</label></li>';
			 }
			 $str_help = '';
			 if ( ! empty($this->_str_help_class) &&  ! empty($this->_str_help) ){
				$str_help = '<p class="' . trim($this->_str_help_class) . '">' . trim($this->_str_help) . '</p>';
			 }
			 /*
			  * if there are more than one element in arr_existing then tell the user to resave. this could happen if radio / select is added after the fact
			  */
			 $str_error = '';
			 if ( count($arr_existing) > 1 ){
				$str_help = '<p class="' . trim($this->_str_error_class) . '">' . trim($this->_str_error) . '</p>';
			 }
			 
			 $str_to_return_close = '</ul>'. $str_help . $str_error. '</div>';
			
			// if we're not rending the meta_box then just return the <li> list. the code calling the method will be presumed to be finishing the job
			if ( $this->_bool_add_meta_box !== false ) {
				echo $str_to_return_open . $str_to_return . $str_to_return_close;
				return array('status' => true, 'msg' => 'success', 'source' => $str_return_source, 'tax_slug' => $this->_str_taxonomy, 'str_to_return' => $str_to_return_open . $str_to_return . $str_to_return_close);
			} else {
				return array('status' => true, 'msg' => 'success', 'source' => $str_return_source, 'tax_slug' => $this->_str_taxonomy, 'str_to_return' => $str_to_return);
			}	 
		}
		
		
		public function taxonomy_add_meta_box_callback_view_select() {		

			$str_return_source = get_class() . ' :: taxonomy_add_meta_box_callback_view_select()'; 

			 // uses same noncename as default box so no save_post hook needed
			 wp_nonce_field( 'taxonomy_'. $this->_str_taxonomy, 'taxonomy_noncename' );

			 /* 
			  * get the taxonomy terms that have been selected for this post 
			  *
			  * http://codex.wordpress.org/Function_Reference/wp_get_object_terms
			  */
			 $arr_wp_get_object_terms = wp_get_object_terms( get_the_ID(), $this->_str_taxonomy ); 
	 
			 /* 
			  * get *all* terms in this taxonomy
			  *
			  * http://codex.wordpress.org/Function_Reference/get_terms
			  */
			 $arr_terms = (array) get_terms( $this->_str_taxonomy, $this->_arr_get_terms_defaults );  
			 
			 // filter the ids out of the terms
			$arr_existing = array();
			if ( !is_wp_error( $arr_wp_get_object_terms ) && !empty( $arr_wp_get_object_terms ) ){
			
				// http://codex.wordpress.org/Function_Reference/wp_list_pluck
				$arr_existing = (array) wp_list_pluck( $arr_wp_get_object_terms, 'term_id' );
			} elseif ( is_wp_error( $arr_wp_get_object_terms ) ){
				return array('status' => false, 'msg' => 'Error: is_wp_error() === true', 'source' => $str_return_source, 'tax_slug' => $this->_str_taxonomy, 'str_to_return' => 'error');
			}
									
			/*
			 * TODO - if we DO get an error does that mean to abort?? 
			 */
					
			// Check if taxonomy is hierarchical
			// Terms are saved differently between types
			$bool_hierarchical = $this->taxonomy_get_taxonomy()->hierarchical;
					
			 // default value
			$mix_default_val = '';
			if ( $bool_hierarchical == true ) {
				$mix_default_val = 0;
			}
			
			// input name
			$str_name = 'tax_input[' . $this->_str_taxonomy . ']';
			if ( $bool_hierarchical == true ){
				$str_name ='tax_input[' . $this->_str_taxonomy . '][]';
			}
			
			 $str_to_return = '';

			 $str_to_return_open = '<div style="margin-bottom: 5px;">';
	//		 $str_to_return_open .= '<select id="'. $this->_str_taxonomy .'_taxradiolist" name="'. $str_name .'" id="in-'. $this->_str_taxonomy .'_tax-'. $obj_term->term_id .'" '. ' data-wp-lists="list:'. $this->_str_taxonomy .'_tax" class="categorychecklist form-no-clear">'; // <<< 5px = property

				// If 'category' force a selection, or _bool_force_selection is true
				/*
				if ( $this->_str_taxonomy == 'category' || $this->_bool_force_selection == true ) {   
					 // 
					 $str_to_return .= '<option value="'. $mix_default_val . '" ';
					 $str_to_return .=  checked( empty( $arr_existing ), true, false );
					 $str_to_return .=  '>'. sprintf( __( 'No %s', 'wds' ), $this->taxonomy_get_taxonomy()->labels->singular_name ) .'</option>';
				}
				*/

			 // loop our terms and check if they're associated with this post
			$term_id = '';
			$int_cnt = 0;
			foreach ( $arr_terms as $obj_term ) {
	
				$int_cnt++;
				$mix_val = $obj_term->slug;
				if ( $bool_hierarchical === true ){
					$mix_val = $obj_term->term_id; 
				}

				$str_to_return .= '<option value="'. $mix_val .'" ';
				// if so, they get "checked"
					if ( ! empty( $arr_existing ) ){
						$str_to_return .= selected( in_array( $obj_term->term_id, $arr_existing ), true, false );
					} else {
						if ( $this->_str_taxonomy == 'category' && $int_cnt == count($arr_terms) && $this->_str_default_tax_id < 0) {
							$str_to_return .= selected( '1', '1' , false );	
						} else {
							$str_to_return .= selected( $obj_term->term_id, $this->_str_default_tax_id , false );	
						}
					}
					$str_to_return .= '> '. $obj_term->name .'</option>';
			}

			 $str_to_return_open .= '<select id="'. $this->_str_taxonomy .'_taxradiolist" name="'. $str_name .'" data-wp-lists="list:'. $this->_str_taxonomy .'_tax" class="categorychecklist form-no-clear">';
 
			 $str_help = '';
			 if ( ! empty($this->_str_help_class) &&  ! empty($this->_str_help) ){
				$str_help = '<p class="' . trim($this->_str_help_class) . '">' . trim($this->_str_help) . '</p>';
			 }
			 
			 /*
			  * if there are more than one element in arr_existing then tell the user to resave. this could happen if radio / select is added after the fact
			  */
			 $str_error = '';
			 if ( count($arr_existing) > 1 ){
				$str_help = '<p class="' . trim($this->_str_error_class) . '">' . trim($this->_str_error) . '</p>';
			 }
			 
			 $str_to_return_close = '</select>'. $str_help . $str_error . '</div>'; // <<< use $str_to_echo and not echo, echo, echo.	
			
			// if we're not rending the meta_box then just return the <li> list. the code calling the method will be presumed to be finishing the job
			if ( $this->_bool_add_meta_box !== false ) {
				echo $str_to_return_open . $str_to_return . $str_to_return_close;
				return array('status' => true, 'msg' => 'success', 'source' => $str_return_source, 'tax_slug' => $this->_str_taxonomy, 'str_to_return' => $str_to_return_open . $str_to_return . $str_to_return_close);
			} else {
				return array('status' => true, 'msg' => 'success', 'source' => $str_return_source, 'tax_slug' => $this->_str_taxonomy, 'str_to_return' => $str_to_return);
			}	 
		}
		
		
		/**
		 *
		 */	
		public function taxonomy_custom_meta_boxes_get_post_types() {
			$arr_get_post_types_defaults = $this->taxonomy_custom_meta_boxes_get_post_types_defaults();
			return get_post_types($arr_get_post_types_defaults['args'], $arr_get_post_types_defaults['output'], $arr_get_post_types_defaults['operator']);
		}
		
		/**
		 *
		 */	
		public function taxonomy_get_taxonomy() {
			 if ( $this->_obj_taxonomy !== true ) {  // ???
				$this->_obj_taxonomy = get_taxonomy( $this->_str_taxonomy );
			 } 
			 return $this->_obj_taxonomy;
		}
		
		/**
		 * gets the taxonomy's associated post_types
		 */	
		public function taxonomy_radio_taxonomy_post_types() {
			if ( empty( $this->post_types )){	
				return $this->taxonomy()->object_type;
			}
			return $this->post_types;
		}
		
		/**
		 *
		 */			
		public function taxonomy_custom_meta_boxes_defaults(){
		
			$arr_all_post_types = $this->taxonomy_custom_meta_boxes_get_post_types(); 
			$arr_taxonomy_custom_meta_boxes_get_post_types_defaults = $this->taxonomy_custom_meta_boxes_get_post_types_defaults();
		
			$arr_return = array(
								'taxonomy'								=> 'category',
								'view'									=> 'radio', // coming soon....select
								'add_meta_box'							=> true,
								'post_types'							=> $arr_all_post_types,										
								'post_types_exclude'					=> array('attachment', 'revision', 'nav_menu_item'),
								'add_action_add_meta_box_priority'		=> 10,
								'meta_box_title'							=> 'Categories - Pick One',										
								'meta_box_priority'						=> 'high', 													// 'high', 'core', 'default' or 'low'
								'meta_box_position'						=> 'side', 													// 'normal', 'advanced', or 'side'
								'help'									=> '',
								'help_class'							=> 'howto',
								'error'									=> 'Oops - Please be sure to Save.',
								'error_class'							=> 'howto',
								'force_selection'						=> false,													// bool
								'default_tax_id'						=> -1,													// else, integer of the tax_id you want as the default
								'get_terms_defaults'					=> $this->taxonomy_custom_meta_boxes_get_terms_defaults(),
								'get_post_types_defaults' 				=> $arr_taxonomy_custom_meta_boxes_get_post_types_defaults,
								'get_post_types_defaults_args'			=> $arr_taxonomy_custom_meta_boxes_get_post_types_defaults['args'],
								'get_post_types_defaults_output'		=> $arr_taxonomy_custom_meta_boxes_get_post_types_defaults['output'],
								'get_post_types_defaults_operator'		=> $arr_taxonomy_custom_meta_boxes_get_post_types_defaults['operator'],
							);
							
			if ( isset($this->_arr_taxonomy_defaults) && is_array($this->_arr_taxonomy_defaults) ){
				$arr_return = array_merge($arr_return, $this->_arr_taxonomy_defaults );
			}
				
			// filters allowed? 
			if ( $this->ezCONFIGS('filters') === true ){
				$arr_return_via_filter = apply_filters('filter_taxonomy_custom_meta_boxes_defaults', $arr_return);
				if ( is_array($arr_return_via_filter) ){
						$arr_return = array_merge( $arr_return, $arr_return_via_filter );
				}
			}

			return $arr_return;
		}
		
		/**
		 * http://codex.wordpress.org/Function_Reference/get_terms
		 */			
		public function taxonomy_custom_meta_boxes_get_terms_defaults(){
		
			$arr_get_terms_defaults = array(
										'orderby'       => 'name', 
										'order'         => 'ASC',
										'hide_empty'    => false, 
										'exclude'       => array(), 
										'exclude_tree'  => array(), 
										'include'       => array(),
										'number'        => '', 
										'fields'        => 'all', 
										'slug'          => '', 
										'parent'         => '',
										'hierarchical'  => true, 
										'child_of'      => 0, 
										'get'           => '', 
										'name__like'    => '',
										'pad_counts'    => false, 
										'offset'        => '', 
										'search'        => '', 
										'cache_domain'  => 'core'
									);
											
			if ( isset($this->_arr_get_terms_defaults) && is_array($this->_arr_get_terms_defaults) ){
				// TODO - validate?
				$arr_get_terms_defaults = array_merge($arr_get_terms_defaults, $this->_arr_get_terms_defaults);
			}
			
			// filters allowed? 		
			if ( $this->ezCONFIGS('filters') === true ){
				$arr_get_terms_defaults_via_filter = apply_filters('filter_taxonomy_custom_meta_boxes_get_terms_defaults', $arr_get_terms_defaults);
				if ( is_array($arr_get_terms_defaults_via_filter) ){
					$arr_get_terms_defaults = array_merge( $arr_get_terms_defaults, $arr_get_terms_defaults_via_filter );
				}
			}	
			return $arr_get_terms_defaults;
		}		

		/**
		 *
		 */			
		public function taxonomy_custom_meta_boxes_get_post_types_defaults(){
		
			$arr_get_post_types_defaults_args = array(
													'public'				=> false,
													'publicly_queryable'	=> true,
													'exclude_from_search'	=> false,
													'show_ui'				=> true,
													'capability_type'		=> NULL,
													'hierarchical'			=> NULL,
													'menu_position'			=> NULL,
													'menu_icon'				=> NULL,
													'permalink_epmask'		=> NULL,
													'rewrite'				=> NULL,
													'query_var'				=> NULL,
													'_builtin'				=> NULL,
												);
												
			if ( isset($this->_arr_get_post_types_defaults_args) && is_array($this->_arr_get_post_types_defaults_args) ){
				// TODO - validate?
				$arr_get_post_types_defaults_args = array_merge($arr_get_post_types_defaults_args, $this->_arr_get_post_types_defaults_args);
			}
		
			// filters allowed
			if ( $this->ezCONFIGS('filters') === true ){
				$arr_get_post_types_defaults_args_via_filter = apply_filters('filter_taxonomy_custom_meta_boxes_get_post_types_defaults_args', $arr_get_post_types_defaults_args);
				if ( is_array($arr_get_post_types_defaults_args_via_filter) ){
					$arr_get_post_types_defaults_args = array_merge( $arr_get_post_types_defaults_args, $arr_get_post_types_defaults_args_via_filter );
				}
			}
			
			$str_output = 'names';
			if ( isset($this->_str_get_post_types_defaults_output) && ($this->_str_get_post_types_defaults_output == 'objects') ){
				$str_output = 'objects';
			}
			
			// filters allowed?
			if ( $this->ezCONFIGS('filters') === true ){
				$str_get_post_types_defaults_output_via_filter = apply_filters('filter_taxonomy_custom_meta_boxes_get_post_types_defaults_output', $str_output);
				if ( strtolower($str_get_post_types_defaults_output_via_filter) == 'names' || strtolower($str_get_post_types_defaults_output_via_filter) == 'objects' ){
					$str_output = strtolower($str_get_post_types_defaults_output_via_filter);
				}
			}
						
			$str_operator = 'or';
			if ( isset($this->_str_get_post_types_defaults_operator) && strtolower($this->_str_get_post_types_defaults_operator) == 'and' ){
				$str_operator = 'and';
			}
			
			// filters allowed?
			if ( $this->ezCONFIGS('filters') === true ){
				$str_get_post_types_defaults_operator_via_filter = apply_filters('filter_taxonomy_custom_meta_boxes_get_post_types_defaults_operator', $str_operator);
				if ( strtolower($str_get_post_types_defaults_operator_via_filter) == 'and' || strtolower($str_get_post_types_defaults_operator_via_filter) == 'or' ){
					$str_operator = strtolower($str_get_post_types_defaults_operator_via_filter);
				}
			}
			
			$arr_get_post_types_defaults = array(
												'args'		=> $arr_get_post_types_defaults_args,
												'output'	=> $str_output,
												'operator'	=> $str_operator,
											);
											
			if ( isset($this->_arr_get_post_types_defaults) && is_array($this->_arr_get_post_types_defaults) ){
				$arr_get_post_types_defaults = array_merge($arr_get_post_types_defaults, $this->_arr_get_post_types_defaults);
			}
				
			// filters allowed? 
			if ( $this->ezCONFIGS('filters') === true ){
				$arr_get_post_types_defaults_via_filter = apply_filters('filter_taxonomy_custom_meta_boxes_get_post_types_defaults', $arr_get_post_types_defaults);
				if ( is_array($arr_get_post_types_defaults_via_filter) ){
					$arr_get_post_types_defaults = array_merge( $arr_get_post_types_defaults, $arr_get_post_types_defaults_via_filter );
				}
			}
			return $arr_get_post_types_defaults;
		}

		/**
		 *
		 */			
		public function property_set($arr_args = NULL){
			$str_return_source = get_class() . ' :: property_set()'; 
			
			if ( property_exists( get_class($this), $arr_args['property']) ) {
			
				// TODO - validate? $arr_args['validate'];
				// NOTE: this set could return a success but logic in the code (e.g., is_array() prior to an array_merge might effect expected results
				
				$this->$arr_args['property'] = $arr_args['value'];
				return array('status' => true, 'msg' => 'success', 'source' => $str_return_source, 'property' => $arr_args['property'], 'value' => $arr_args['value']  );

			} else {
				return array('status' => false, 'msg' => 'Error: property: ' . $arr_args['property'] . ' - property_exists() === false', 'source' => $str_return_source, 'property' => $arr_args['property'], 'value' => $arr_args['value']  );
			}
		}
		
	} // close class
} // close if class

?>