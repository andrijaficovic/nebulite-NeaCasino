<?php
/**
 * Theme Options (ACF Options Page)
 *
 * @package Nebulite
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register ACF Options Page.
 */
if ( function_exists( 'acf_add_options_page' ) ) {
	acf_add_options_page( array(
		'page_title' => 'Theme Options',
		'menu_title' => 'Theme Options',
		'menu_slug'  => 'theme-options',
		'capability' => 'edit_posts',
		'redirect'   => false,
	) );
}

/**
 * Register ACF Field Groups for Theme Options.
 */
add_action( 'acf/include_fields', function() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group( array(
		'key' => 'group_6914588a32c19',
		'title' => 'Theme Options',
		'fields' => array(
			array(
				'key' => 'field_6914588a1c268',
				'label' => 'Body Background Image',
				'name' => 'body_background_image',
				'aria-label' => '',
				'type' => 'image',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'return_format' => 'array',
				'library' => 'all',
				'min_width' => '',
				'min_height' => '',
				'min_size' => '',
				'max_width' => '',
				'max_height' => '',
				'max_size' => '',
				'mime_types' => '',
				'allow_in_bindings' => 0,
				'preview_size' => 'medium',
			),
		),
		'location' => array(
			array(
				array(
					'param' => 'options_page',
					'operator' => '==',
					'value' => 'theme-options',
				),
			),
		),
		'menu_order' => 0,
		'position' => 'normal',
		'style' => 'default',
		'label_placement' => 'top',
		'instruction_placement' => 'label',
		'hide_on_screen' => '',
		'active' => true,
		'description' => '',
		'show_in_rest' => 0,
		'display_title' => '',
	) );
} );

/**
 * Apply body background image from Theme Options.
 */
add_action( 'wp_head', function() {
	$background_image = get_field( 'body_background_image', 'option' );
	
	if ( empty( $background_image ) ) {
		return;
	}
	
	$image_url = '';
	
	// Handle different return formats
	if ( is_array( $background_image ) && ! empty( $background_image['url'] ) ) {
		$image_url = esc_url( $background_image['url'] );
	} elseif ( is_numeric( $background_image ) ) {
		$image_url = esc_url( wp_get_attachment_image_url( $background_image, 'full' ) );
	} elseif ( is_string( $background_image ) ) {
		$image_url = esc_url( $background_image );
	}
	
	if ( ! empty( $image_url ) ) {
		?>
		<style type="text/css">
			body {
				background-image: url(<?php echo $image_url; ?>);
				background-size: cover;
				background-position: center;
				background-repeat: no-repeat;
				background-attachment: fixed;
			}
		</style>
		<?php
	}
}, 99 );
