<?php

if( !class_exists('LS_Courses_Post_Type' ) ){
	class LS_Courses_Post_Type {
		function __construct(){
			add_action( 'init', array( $this, 'create_post_type') );
			add_action( 'init', array( $this, 'register_meta_boxes' ) );
			add_filter( 'manage_ls-courses_posts_columns', array( $this, 'ls_courses_cpt_columns' ) );
			add_action( 'manage_ls-courses_posts_custom_column', array( $this, 'ls_courses_custom_columns' ), 10, 2 );
			add_filter( 'page_row_actions', array( $this, 'page_row_actions'), 10, 2 );
		}

		// if post type is hierarchical then use page_row_actions else post_row_actions
		public function page_row_actions( $actions, $post ){
			if( $post->post_type === 'ls-courses' ){
				$actions['id'] = 'ID: ' . $post->ID;
			}
			return $actions;
		}

		public function ls_courses_custom_columns( $column, $post_id ){
			switch( $column ){
				case 'ls_courses_featured_image':
					the_post_thumbnail( array( 40, 40) );
				break;
			}
		}

		public function ls_courses_cpt_columns( $columns ){
			unset( $columns['date'] );
			$columns['ls_courses_featured_image'] = esc_html__( 'Featured image', 'lasaphire-courses-blocks' );
			$columns['date'] = esc_html__( 'Date', 'lasaphire-courses-blocks' );
			return $columns;
		}

		public function create_post_type(){
			register_post_type(
				'ls-courses',
				array(
					'label'	=> __( 'Ls Course', 'lasaphire-courses-blocks' ),
					'description'	=> __( 'Ls Courses', 'lasaphire-courses-blocks' ),
					'labels'	=> array(
						'name' => __( 'Courses', 'lasaphire-courses-blocks' ),
						'singular_name'	=> __( 'Course', 'lasaphire-courses-blocks' ),
						'add_new_item' => __( 'Add New Course', 'lasaphire-courses-blocks' ),
						'edit item'	=> __( 'Edit Course', 'lasaphire-courses-blocks'),
						'all_items'	=> __( 'All Courses', 'lasaphire-courses-blocks' ),
					),
					'public'	=> true,
					'supports' => array( 'title', 'editor', 'thumbnail', 'custom-fields', 'page-attributes' ),
					'hierarchical'	=> true,
					'show_ui'	=> true,
					'show_in_menu' => true,
					'menu_position'	=> 5,
					'show_in_admin_bar'	=> true,
					'show_in_nav_menus'	=> true,
					'can_export'	=> true,
					'has_archive'	=> true,
					'exclude_from_search'	=> false,
					'publicly_queryable'	=> true,
					'show_in_rest'	=> true,
					'taxonomies' => array('category'),
					'menu_icon'	=> 'dashicons-welcome-learn-more'
				)
			);
		}

		public function register_meta_boxes(){
			register_post_meta(
				'ls-courses',
				'_ls_course_video',
				array(
					'single'	=> true,
					'type'	=> 'string',
					'show_in_rest'	=>	true,
					'sanitize_callback' => 'esc_url_raw',
					'auth_callback' => function(){
						return current_user_can( 'edit_posts' );
					}
				)
			);
		}
	}
}