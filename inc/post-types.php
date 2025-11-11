<?php
/**
 * Register Custom Post Types
 *
 * @package Nebulite
 */

declare(strict_types=1);

/**
 * Register Casino Custom Post Type
 */
function nebulite_register_casino_post_type() {
	$labels = array(
		'name'                  => _x( 'Casinos', 'Post Type General Name', 'nebulite' ),
		'singular_name'         => _x( 'Casino', 'Post Type Singular Name', 'nebulite' ),
		'menu_name'             => __( 'Casinos', 'nebulite' ),
		'name_admin_bar'        => __( 'Casino', 'nebulite' ),
		'archives'              => __( 'Casino Archives', 'nebulite' ),
		'attributes'            => __( 'Casino Attributes', 'nebulite' ),
		'parent_item_colon'     => __( 'Parent Casino:', 'nebulite' ),
		'all_items'             => __( 'All Casinos', 'nebulite' ),
		'add_new_item'          => __( 'Add New Casino', 'nebulite' ),
		'add_new'               => __( 'Add New', 'nebulite' ),
		'new_item'              => __( 'New Casino', 'nebulite' ),
		'edit_item'             => __( 'Edit Casino', 'nebulite' ),
		'update_item'           => __( 'Update Casino', 'nebulite' ),
		'view_item'             => __( 'View Casino', 'nebulite' ),
		'view_items'            => __( 'View Casinos', 'nebulite' ),
		'search_items'          => __( 'Search Casino', 'nebulite' ),
		'not_found'             => __( 'Not found', 'nebulite' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'nebulite' ),
		'featured_image'        => __( 'Featured Image', 'nebulite' ),
		'set_featured_image'    => __( 'Set featured image', 'nebulite' ),
		'remove_featured_image' => __( 'Remove featured image', 'nebulite' ),
		'use_featured_image'    => __( 'Use as featured image', 'nebulite' ),
		'insert_into_item'      => __( 'Insert into casino', 'nebulite' ),
		'uploaded_to_this_item' => __( 'Uploaded to this casino', 'nebulite' ),
		'items_list'            => __( 'Casinos list', 'nebulite' ),
		'items_list_navigation' => __( 'Casinos list navigation', 'nebulite' ),
		'filter_items_list'     => __( 'Filter casinos list', 'nebulite' ),
	);

	$args = array(
		'label'                 => __( 'Casino', 'nebulite' ),
		'description'           => __( 'Casino listings', 'nebulite' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
		'taxonomies'            => array(),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 20,
		'menu_icon'             => 'dashicons-games',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'post',
		'show_in_rest'          => true,
	);

	register_post_type( 'casino', $args );
}
add_action( 'init', 'nebulite_register_casino_post_type', 0 );

/**
 * Register Game Custom Post Type
 */
function nebulite_register_game_post_type() {
	$labels = array(
		'name'                  => _x( 'Games', 'Post Type General Name', 'nebulite' ),
		'singular_name'         => _x( 'Game', 'Post Type Singular Name', 'nebulite' ),
		'menu_name'             => __( 'Games', 'nebulite' ),
		'name_admin_bar'        => __( 'Game', 'nebulite' ),
		'archives'              => __( 'Game Archives', 'nebulite' ),
		'attributes'            => __( 'Game Attributes', 'nebulite' ),
		'parent_item_colon'     => __( 'Parent Game:', 'nebulite' ),
		'all_items'             => __( 'All Games', 'nebulite' ),
		'add_new_item'          => __( 'Add New Game', 'nebulite' ),
		'add_new'               => __( 'Add New', 'nebulite' ),
		'new_item'              => __( 'New Game', 'nebulite' ),
		'edit_item'             => __( 'Edit Game', 'nebulite' ),
		'update_item'           => __( 'Update Game', 'nebulite' ),
		'view_item'             => __( 'View Game', 'nebulite' ),
		'view_items'            => __( 'View Games', 'nebulite' ),
		'search_items'          => __( 'Search Game', 'nebulite' ),
		'not_found'             => __( 'Not found', 'nebulite' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'nebulite' ),
	);

	$args = array(
		'label'                 => __( 'Game', 'nebulite' ),
		'description'           => __( 'Game types', 'nebulite' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor', 'thumbnail' ),
		'taxonomies'            => array(),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 21,
		'menu_icon'             => 'dashicons-admin-customizer',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => false,
		'can_export'            => true,
		'has_archive'           => false,
		'exclude_from_search'   => true,
		'publicly_queryable'    => false,
		'capability_type'       => 'post',
		'show_in_rest'          => true,
	);

	register_post_type( 'game', $args );
}
add_action( 'init', 'nebulite_register_game_post_type', 0 );

/**
 * Register Cryptocurrency Custom Post Type
 */
function nebulite_register_cryptocurrency_post_type() {
	$labels = array(
		'name'                  => _x( 'Cryptocurrencies', 'Post Type General Name', 'nebulite' ),
		'singular_name'         => _x( 'Cryptocurrency', 'Post Type Singular Name', 'nebulite' ),
		'menu_name'             => __( 'Cryptocurrencies', 'nebulite' ),
		'name_admin_bar'        => __( 'Cryptocurrency', 'nebulite' ),
		'archives'              => __( 'Cryptocurrency Archives', 'nebulite' ),
		'attributes'            => __( 'Cryptocurrency Attributes', 'nebulite' ),
		'parent_item_colon'     => __( 'Parent Cryptocurrency:', 'nebulite' ),
		'all_items'             => __( 'All Cryptocurrencies', 'nebulite' ),
		'add_new_item'          => __( 'Add New Cryptocurrency', 'nebulite' ),
		'add_new'               => __( 'Add New', 'nebulite' ),
		'new_item'              => __( 'New Cryptocurrency', 'nebulite' ),
		'edit_item'             => __( 'Edit Cryptocurrency', 'nebulite' ),
		'update_item'           => __( 'Update Cryptocurrency', 'nebulite' ),
		'view_item'             => __( 'View Cryptocurrency', 'nebulite' ),
		'view_items'            => __( 'View Cryptocurrencies', 'nebulite' ),
		'search_items'          => __( 'Search Cryptocurrency', 'nebulite' ),
		'not_found'             => __( 'Not found', 'nebulite' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'nebulite' ),
	);

	$args = array(
		'label'                 => __( 'Cryptocurrency', 'nebulite' ),
		'description'           => __( 'Cryptocurrency cryptocurrencies', 'nebulite' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor', 'thumbnail' ),
		'taxonomies'            => array(),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 22,
		'menu_icon'             => 'dashicons-money-alt',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => false,
		'can_export'            => true,
		'has_archive'           => false,
		'exclude_from_search'   => true,
		'publicly_queryable'    => false,
		'capability_type'       => 'post',
		'show_in_rest'          => true,
	);

	register_post_type( 'cryptocurrency', $args );
}
add_action( 'init', 'nebulite_register_cryptocurrency_post_type', 0 );

