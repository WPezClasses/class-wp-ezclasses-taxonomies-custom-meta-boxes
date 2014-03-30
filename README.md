WP ezClasses - Taxonomies: Custom Meta Boxes
============================================

Automates the removal of the default WordPress taxonomy meta box (for the specified taxonomy) and then add your own meta box using different form elements (e.g., radio buttons or select) to make the selection.


Share This Repo
===============

+ [Twitter](http://twitter.com/share?url=https%3A%2F%2Fgithub.com%2FWPezClasses%2Fclass-wp-ezclasses-taxonomies-custom-meta-boxes%2F&text=%23WordPress%20%3D%3E%20WP%20ezClasses%20-%20Taxonomies%3A%20Custom%20Meta%20Boxes%20%23GitHub%20%40WPezClasses%20Please%20RT)
+ [Google+](https://plus.google.com/share?url=https%3A%2F%2Fgithub.com%2FWPezClasses%2Fclass-wp-ezclasses-taxonomies-custom-meta-boxes%0A&title=WordPress%20%3D%3E%20WP%20ezClasses%20-%20Taxonomies%3A%20Custom%20Meta%20Boxes)
+ [Facebook](http://www.facebook.com/sharer.php?u=https://github.com/WPezClasses/class-wp-ezclasses-taxonomies-custom-meta-boxes&t=WordPress => WP ezClasses - Taxonomies: Custom Meta Boxes)
+ [LinkedIn](http://www.linkedin.com/shareArticle?mini=true&url=https%3A%2F%2Fgithub.com%2FWPezClasses%2Fclass-wp-ezclasses-taxonomies-custom-meta-boxes&title=WordPress%20%3D%3E%20WP%20ezClasses%20-%20Taxonomies%3A%20Custom%20Meta%20Boxes&summary=Remove%20a%20default%20WordPress%20taxonomy%20meta%20box%20and%20then%20add%20your%20own%20meta%20box%20using%20different%20form%20elements%20(e.g.%2C%20radio%20buttons%20or%20select)%20to%20make%20the%20selection.%0A)


Using WP ezClasses - Taxonomies: Custom Meta Boxes
==================================================

If you're not already familiar with WP ezClasses please see the Getting Started repo [TODO]()

Otherwise, it's very simple:

1) Setup your own class that extends this (repo's) class.
<pre><code>
if ( ! class_exists('Class_WP_My_Example_TCMB') ){
	class Class_WP_My_Example_TCMB extends Class_WP_ezClasses_Taxonomies_Custom_Meta_Boxes {
	
		public function ezc_init($arr_args = array()){ 

			$arr_args = WP_ezMethods::array_merge_ez( array($this->tcmb(), $arr_args) );
			$this->taxonomy_custom_meta_boxes($arr_args);
			
		}
		
		/**
		 * Define the args for a given taxonomy here
		 */
		protected function tcmb(){
		
			return array(
						'taxonomy'			=> 'responsive', 
						'meta_box_title'	=> 'Responsive',
						'view'				=> 'select'									
					);
		}
	}
}
</code></pre>

(Note: If you want to skip the Getting Start be prepared to pull errors from unmet dependencies.)


2) On top of that, if you want you can also passed in args when you get an instance.
<pre><code>
$arr_my_args = array(
					'view'				=> 'radio'	
				);
</code></pre>

3) When you get your instance do this:
<pre><code>
$obj_my_tax_cmd = Class_WP_My_Example_TCMB::ezc_get_instance();
</code></pre>

Or if you have args to pass in:
<pre><code>
$obj_my_tax_cmd = Class_WP_My_Example_TCMB::ezc_get_instance($arr_my_args);
</code></pre>


Inspired By 
===========

https://github.com/WebDevStudios/WDS_Taxonomy_Radio/blob/master/WDS_Taxonomy_Radio.class.php