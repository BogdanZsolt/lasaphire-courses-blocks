<?php
/**
 * Plugin Name:	Lasaphire Courses Blocks
	* Plugin URI:	https://zsoltbogdan.hu
 * Description:	Example static block scaffolded with Create Block tool.
 * Requires at least: 5.9
 * Requires PHP: 7.0
 * Version: 0.1.0
 * Author:	Bogdán Zsolt
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: lasaphire-courses-blocks
 */

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */

if(!defined('ABSPATH')) exit;

if( !class_exists( 'LS_Courses_Blocks' ) ){
	class LS_Courses_Blocks {
		function __construct(){
			add_action( 'init', array( $this, 'create_block_lasaphire_courses_blocks_block_init' ) );
			require_once( plugin_dir_path( __FILE__ ) . 'post-types/class.lasaphire-courses-blocks-cpt.php' );
			$LS_Courses_Post_Type = new LS_Courses_Post_Type();
			add_filter( 'block_categories_all', array( $this, 'ls_new_gutenberg_category'), 10, 2);
			add_action('rest_api_init', array( $this, 'ls_rest_api_image' ));
			add_action( 'init', array( $this, 'lasaphire_course_blocks_register_video_metabox_template' ) );
		}

		public static function activate(){
			update_option( 'rewrite_rules', '' );
		}

		public static function deactivate(){
			flush_rewrite_rules();
		}

		public static function uninstall(){

		}

		public function lasaphire_courses_list_render($attributes){
			// var_dump($attributes);
			$args = array(
				'post_type' => 'ls-courses',
				'post_per_page' => -1,
				'post_parent'	=> 0,
				'post_status'	=> 'publish',
				'order'	=> $attributes['order'],
				'orderby' => $attributes['orderBy'],
			);

			if(isset($attributes['categories'])){
				$args['category__in'] = array_column($attributes['categories'], 'id');
			}

			$recent_posts = get_posts($args);
			$posts = '';
			$class = 'wp-block-ls-courses-list__list';
			if(isset($attributes['isSlide']) && $attributes['isSlide']){
				$class = ' is-slide column-5';
			}
			if(isset($attributes['postLayout']) && $attributes['postLayout'] === 'grid' || isset($attributes['isSlide']) && $attributes['isSlide'] ){
				$class .= ' is-grid';
			}
			if(isset($attributes['columns']) && $attributes['postLayout'] === 'grid' && !$attributes['isSlide']){
				$class .= ' columns-' . $attributes['columns'];
			}
			$posts .= '<ul ' . get_block_wrapper_attributes( array( 'class' => $class ) ) . '>';
			foreach($recent_posts as $post) {
				$title = get_the_title($post);
				$title = $title ? $title : __('(No title)', 'lasaphire-courses-blocks');
				$permalink = get_permalink($post);
				$posts .= '<li>';
				if($attributes['displayFeaturedImage'] && has_post_thumbnail($post)){
					$posts .= get_the_post_thumbnail($post, $attributes['featuredImageSizeSlug'] );
				}
				$posts .= '<div class="container">';
				$posts .= '<a href="' . $permalink . '">' . $title . '</a>';
				$posts .= '</div>';
				$posts .= '</li>';

			}
			$posts .= '</ul>';

			return $posts;
		}

		public function create_block_lasaphire_courses_blocks_block_init() {

			register_block_type( __DIR__ . '/build/courses-list', array(
				'render_callback' => array($this, 'lasaphire_courses_list_render') ) );


			// An Array of Blocks
			$blocks = array(
				'video-meta',
			);

			foreach($blocks as $block){
				register_block_type( __DIR__ . '/build/' . $block);
			}
		}

		// Custom Categories
		public function ls_new_gutenberg_category($categories, $post){
			return array_merge(
				array(
					array(
						'slug'	=> 'la-saphire',
						'title'	=> 'La Saphire',
						'icon' => 'awards',
					),
				),
				$categories,
			);
		}

		/** Adds the featured Image URL to the WP REST API Response */
		public function ls_rest_api_image(){
			register_rest_field( 'ls-courses', 'ls_courses_image', array(
				'get_callback'	=> array( $this, 'ls_get_featured_image' ),
				'update_callback'	=> null,
				'schema'	=> null,
			));
		}

		public function ls_get_featured_image($object, $field_name, $request){
			if($object['featured_media']){
				$img = wp_get_attachment_image_src($object['featured_media'], 'medium');
				return $img[0];
			}
			return false;
		}

		public function lasaphire_course_blocks_register_video_metabox_template(){
			$post_type_object = get_post_type_object( 'ls-courses' );
			$post_type_object->template = array(
				array('ls/video-meta')
			);
		}
	}
}

if( class_exists( 'LS_Courses_Blocks' ) ) {
	register_activation_hook( __FILE__, array( 'LS_Courses_Blocks', 'activate' ) );
	register_deactivation_hook( __FILE__, array( 'LS_Courses_Blocks', 'deactivate' ) );
	register_uninstall_hook( __FILE__, array( 'LS_Courses_Blocks', 'uninstall' ) );
	$ls_courses_blocks = new LS_Courses_Blocks();
}