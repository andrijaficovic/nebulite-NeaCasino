<?php
/**
 * ACF Blocks registration and AJAX handlers for Casino List
 *
 * @package Nebulite
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Casino List ACF block.
 */
add_action( 'acf/init', function() {
	if ( ! function_exists( 'acf_register_block_type' ) ) {
		return;
	}

	acf_register_block_type( array(
		'name'            => 'casino-list',
		'title'           => __( 'Casino List', 'nebulite' ),
		'description'     => __( 'Displays the casinos selected within the block settings.', 'nebulite' ),
		'render_callback' => 'nebulite_render_block_casino_list',
		'category'        => 'widgets',
		'icon'            => 'list-view',
		'keywords'        => array( 'casino', 'list', 'igaming' ),
		'mode'            => 'preview',
		'supports'        => array( 'align' => true, 'anchor' => true ),
		'enqueue_assets'  => function() {
			$handle = 'nebulite-casino-list';
			wp_enqueue_script( $handle, get_template_directory_uri() . '/js/casino-list.js', array( 'jquery' ), _S_VERSION, true );
			wp_localize_script( $handle, 'NebuliteCasinoList', array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'nebulite_casino_list' ),
			) );
		},
	) );
} );

/**
 * Register Hero ACF block.
 */
add_action( 'acf/init', function() {
	if ( ! function_exists( 'acf_register_block_type' ) ) {
		return;
	}

	acf_register_block_type( array(
		'name'            => 'hero',
		'title'           => __( 'Hero Block', 'nebulite' ),
		'description'     => __( 'Hero section with background image, title, description and animated results.', 'nebulite' ),
		'render_callback' => 'nebulite_render_block_hero',
		'category'        => 'widgets',
		'icon'            => 'cover-image',
		'keywords'        => array( 'hero', 'banner', 'header' ),
		'mode'            => 'preview',
		'supports'        => array( 'align' => false, 'anchor' => true ),
		'enqueue_assets'  => function() {
			$handle = 'nebulite-hero';
			wp_enqueue_script( $handle, get_template_directory_uri() . '/js/hero.js', array(), _S_VERSION, true );
		},
	) );
} );

/**
 * Register Why Choose Casino ACF block.
 */
add_action( 'acf/init', function() {
	if ( ! function_exists( 'acf_register_block_type' ) ) {
		return;
	}

	acf_register_block_type( array(
		'name'            => 'why-choose-casino',
		'title'           => __( 'Why Choose Casino', 'nebulite' ),
		'description'     => __( 'Display reasons why to choose a casino with icons.', 'nebulite' ),
		'render_callback' => 'nebulite_render_block_why_choose_casino',
		'category'        => 'formatting',
		'icon'            => 'star-filled',
		'keywords'        => array( 'casino', 'reasons', 'features' ),
		'mode'            => 'preview',
		'supports'        => array( 'align' => false, 'anchor' => true ),
	) );
} );

/**
 * Register Trending Casinos ACF block.
 */
add_action( 'acf/init', function() {
	if ( ! function_exists( 'acf_register_block_type' ) ) {
		return;
	}

	acf_register_block_type( array(
		'name'            => 'trending-casinos',
		'title'           => __( 'Trending Casinos', 'nebulite' ),
		'description'     => __( 'Display top 3 trending casinos with rankings.', 'nebulite' ),
		'render_callback' => 'nebulite_render_block_trending_casinos',
		'category'        => 'widgets',
		'icon'            => 'awards',
		'keywords'        => array( 'casino', 'trending', 'top', 'ranking' ),
		'mode'            => 'preview',
		'supports'        => array( 'align' => false, 'anchor' => true ),
	) );
} );

/**
 * Register FAQ Section ACF block.
 */
add_action( 'acf/init', function() {
	if ( ! function_exists( 'acf_register_block_type' ) ) {
		return;
	}

	acf_register_block_type( array(
		'name'            => 'faq-section',
		'title'           => __( 'FAQ Section', 'nebulite' ),
		'description'     => __( 'Display FAQ items with accordion functionality and JSON-LD schema markup.', 'nebulite' ),
		'render_callback' => 'nebulite_render_block_faq_section',
		'category'        => 'widgets',
		'icon'            => 'editor-help',
		'keywords'        => array( 'faq', 'questions', 'answers', 'accordion' ),
		'mode'            => 'preview',
		'supports'        => array( 'align' => false, 'anchor' => true ),
		'enqueue_assets'  => function() {
			$handle = 'nebulite-faq-section';
			wp_enqueue_script( $handle, get_template_directory_uri() . '/js/faq-section.js', array(), _S_VERSION, true );
		},
	) );
} );

/**
 * Convert margin_bottom value to CSS rem value.
 *
 * @param string $value Margin bottom value (none, small, medium, large, extra-large, huge).
 * @return string CSS margin-bottom value in rem.
 */
function nebulite_get_margin_bottom( $value ) {
	$margins = array(
		'none'        => '0',
		'small'       => '1rem',
		'medium'      => '2rem',
		'large'       => '3rem',
		'extra-large' => '4rem',
		'huge'        => '5rem',
	);

	return isset( $margins[ $value ] ) ? $margins[ $value ] : $margins['medium'];
}

/**
 * Render callback: Casino List block.
 *
 * @param array  $block Block settings and attributes.
 * @param string $content Inner HTML.
 * @param bool   $is_preview Whether previewing.
 */
function nebulite_render_block_casino_list( $block, $content = '', $is_preview = false ) {
	$block_id = 'casino-list-' . ( isset( $block['id'] ) ? sanitize_key( $block['id'] ) : wp_generate_uuid4() );
	$margin_bottom = get_field( 'margin_bottom' ) ?: 'medium'; // Default to medium
	$selected_casinos = get_field( 'choose_casinos' );
	$chunk_size = 15;

	$casinos = array();
	if ( ! empty( $selected_casinos ) && is_array( $selected_casinos ) ) {
		foreach ( $selected_casinos as $casino_item ) {
			if ( $casino_item instanceof WP_Post ) {
				$casinos[] = $casino_item->ID;
			} elseif ( is_numeric( $casino_item ) ) {
				$casinos[] = (int) $casino_item;
			} elseif ( is_array( $casino_item ) && isset( $casino_item['ID'] ) ) {
				$casinos[] = (int) $casino_item['ID'];
			}
		}
	}
	
	$initial_casinos = array_slice( $casinos, 0, $chunk_size );

	$vars = array(
		'block_id' => $block_id,
		'casinos'  => $casinos,
		'initial_casinos' => $initial_casinos,
		'chunk_size' => $chunk_size,
		'margin_bottom' => nebulite_get_margin_bottom( $margin_bottom ),
	);
	nebulite_load_template( 'template-parts/blocks/casino-list.php', $vars );
}

/**
 * AJAX: Render additional casino cards.
 */
add_action( 'wp_ajax_nebulite_casino_list', 'nebulite_ajax_casino_list' );
add_action( 'wp_ajax_nopriv_nebulite_casino_list', 'nebulite_ajax_casino_list' );
function nebulite_ajax_casino_list() {
	check_ajax_referer( 'nebulite_casino_list', 'nonce' );

	$ids = isset( $_POST['ids'] ) ? wp_unslash( $_POST['ids'] ) : array();
	if ( empty( $ids ) || ! is_array( $ids ) ) {
		wp_send_json_error( array( 'message' => __( 'No casinos provided.', 'nebulite' ) ) );
	}

	$ids = array_map( 'intval', $ids );
	$ids = array_filter( $ids );

	$offset = isset( $_POST['offset'] ) ? (int) $_POST['offset'] : 0;

	if ( empty( $ids ) ) {
		wp_send_json_error( array( 'message' => __( 'No valid casinos provided.', 'nebulite' ) ) );
	}

	ob_start();
	foreach ( $ids as $position => $casino_id ) {
		nebulite_render_casino_card( $casino_id, $offset + $position + 1 );
	}
	$html = ob_get_clean();

	wp_send_json_success( array(
		'html'  => $html,
		'count' => count( $ids ),
	) );
}

/**
 * Render callback: Hero Block.
 *
 * @param array  $block Block settings and attributes.
 * @param string $content Inner HTML.
 * @param bool   $is_preview Whether previewing.
 */
function nebulite_render_block_hero( $block, $content = '', $is_preview = false ) {
	$block_id = 'hero-' . ( isset( $block['id'] ) ? sanitize_key( $block['id'] ) : wp_generate_uuid4() );
	
	// Get ACF fields
	$background_image = get_field( 'background_image' );
	$title            = get_field( 'title' );
	$description      = get_field( 'description' );
	$results          = get_field( 'results' );
	$margin_bottom    = get_field( 'margin_bottom' ) ?: 'medium'; // Default to medium

	$vars = array(
		'block_id'        => $block_id,
		'background_image' => $background_image,
		'title'           => $title,
		'description'     => $description,
		'results'         => $results,
		'margin_bottom'   => nebulite_get_margin_bottom( $margin_bottom ),
	);

	nebulite_load_template( 'template-parts/blocks/hero.php', $vars );
}

/**
 * Render callback: Why Choose Casino Block.
 *
 * @param array  $block Block settings and attributes.
 * @param string $content Inner HTML.
 * @param bool   $is_preview Whether previewing.
 */
function nebulite_render_block_why_choose_casino( $block, $content = '', $is_preview = false ) {
	$block_id = 'why-choose-casino-' . ( isset( $block['id'] ) ? sanitize_key( $block['id'] ) : wp_generate_uuid4() );
	
	// Get ACF fields
	$title    = get_field( 'title' );
	$reasons  = get_field( 'reasons' );
	$margin_bottom = get_field( 'margin_bottom' ) ?: 'medium'; // Default to medium

	$vars = array(
		'block_id'      => $block_id,
		'title'         => $title,
		'reasons'       => $reasons,
		'margin_bottom' => nebulite_get_margin_bottom( $margin_bottom ),
	);

	nebulite_load_template( 'template-parts/blocks/why-choose-casino.php', $vars );
}

/**
 * Render callback: Trending Casinos Block.
 *
 * @param array  $block Block settings and attributes.
 * @param string $content Inner HTML.
 * @param bool   $is_preview Whether previewing.
 */
function nebulite_render_block_trending_casinos( $block, $content = '', $is_preview = false ) {
	$block_id = 'trending-casinos-' . ( isset( $block['id'] ) ? sanitize_key( $block['id'] ) : wp_generate_uuid4() );
	
	// Get ACF fields
	$title         = get_field( 'title' );
	$description   = get_field( 'description' );
	$select_casinos = get_field( 'select_casinos' );
	$margin_bottom = get_field( 'margin_bottom' ) ?: 'medium';

	$vars = array(
		'block_id'       => $block_id,
		'title'          => $title,
		'description'    => $description,
		'select_casinos' => $select_casinos,
		'margin_bottom'  => nebulite_get_margin_bottom( $margin_bottom ),
	);

	nebulite_load_template( 'template-parts/blocks/trending-casinos.php', $vars );
}

/**
 * Render callback: FAQ Section Block.
 *
 * @param array  $block Block settings and attributes.
 * @param string $content Inner HTML.
 * @param bool   $is_preview Whether previewing.
 */
function nebulite_render_block_faq_section( $block, $content = '', $is_preview = false ) {
	$block_id = 'faq-section-' . ( isset( $block['id'] ) ? sanitize_key( $block['id'] ) : wp_generate_uuid4() );
	
	// Get ACF fields
	$title       = get_field( 'title' );
	$description       = get_field( 'description' );
	$faq_items   = get_field( 'faq_items' );
	$margin_bottom = get_field( 'margin_bottom' ) ?: 'medium';

	$vars = array(
		'block_id'      => $block_id,
		'title'         => $title,
		'description'   => $description,
		'faq_items'     => $faq_items,
		'margin_bottom' => nebulite_get_margin_bottom( $margin_bottom ),
	);

	nebulite_load_template( 'template-parts/blocks/faq-section.php', $vars );
}

/**
 * Utility to load a template with variables scoped.
 */
function nebulite_load_template( $relative_path, array $vars = array() ) {
	$path = get_template_directory() . '/' . ltrim( $relative_path, '/' );
	if ( file_exists( $path ) ) {
		extract( $vars, EXTR_SKIP );
		include $path;
	}
}

/**
 * Render a single casino card (used by template and AJAX).
 */
function nebulite_render_casino_card( $post_id, $position = null ) {
	$image  = get_field( 'image', $post_id );
	$rating = (float) get_field( 'rating', $post_id );
	$bonus  = (string) get_field( 'bonus', $post_id );
	$cta    = get_field( 'cta', $post_id );
	$features_rep = get_field( 'features', $post_id );

	$img_url = is_array( $image ) && ! empty( $image['url'] ) ? esc_url( $image['url'] ) : esc_url( get_template_directory_uri() . '/assets/images/default-casino-preview.svg' );
	$cta_url = ( is_array( $cta ) && ! empty( $cta['url'] ) ) ? esc_url( $cta['url'] ) : '#';
	$cta_title = ( is_array( $cta ) && ! empty( $cta['title'] ) ) ? esc_html( $cta['title'] ) : __( 'Visit', 'nebulite' );

	// Format features as list with tick icons (keep array for rendering).
	$features_list = array();
	if ( ! empty( $features_rep ) && is_array( $features_rep ) ) {
		foreach ( $features_rep as $row ) {
			if ( isset( $row['feature_item'] ) && ! empty( $row['feature_item'] ) ) {
				$features_list[] = esc_html( $row['feature_item'] );
			}
		}
	}

	?>
	<?php
	$logo_background = get_field( 'image_background_color', $post_id );
	$logo_background_style = '';
	if ( ! empty( $logo_background ) && is_string( $logo_background ) ) {
		$logo_background_style = ' style="background-color: ' . esc_attr( $logo_background ) . ';"';
	}
	?>
	<div class="casino-table__row" data-post-id="<?php echo (int) $post_id; ?>">
		<?php if ( null !== $position ) : ?>
			<span class="casino-table__position"><?php echo (int) $position; ?></span>
		<?php endif; ?>
		<div class="casino-table__cell casino-table__cell--casino">
			<div class="casino-table__logo-wrapper"<?php echo $logo_background_style; ?>>
				<img class="casino-table__logo" src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( get_the_title( $post_id ) ); ?>" loading="lazy">
			</div>
			<div class="casino-table__info">
				<h3 class="casino-table__name"><?php echo esc_html( get_the_title( $post_id ) ); ?></h3>
			</div>
		</div>
		<div class="casino-table__cell casino-table__cell--bonus">
			<?php if ( ! empty( $bonus ) ) : ?>
				
				<div class="casino-table__bonus"><?php echo wp_kses_post( $bonus ); ?></div>
			<?php endif; ?>
		</div>
		<div class="casino-table__cell casino-table__cell--features">
			<?php if ( ! empty( $features_list ) ) : ?>
				<ul class="casino-table__features-list">
					<?php foreach ( $features_list as $feature ) : ?>
						<li class="casino-table__features-item">
							<svg class="casino-table__features-icon" width="16" height="17" viewBox="0 0 16 17" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
								<path fill-rule="evenodd" clip-rule="evenodd" d="M7.12117 9.73235L12.4141 4.43945L13.8283 5.85367L7.12117 12.5608L3.41406 8.85367L4.82828 7.43945L7.12117 9.73235Z" fill="#6DBF48"/>
							</svg>
							<span class="casino-table__features-text"><?php echo $feature; ?></span>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
		<div class="casino-table__cell casino-table__cell--rating">
			<div class="casino-table__rating" aria-label="<?php esc_attr_e( 'Rating', 'nebulite' ); ?>">
				<svg class="casino-table__rating-icon" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
					<g clip-path="url(#clip0_rating_<?php echo (int) $post_id; ?>)">
						<path d="M13.0768 5.45567C13.0231 5.29625 12.9265 5.15472 12.7976 5.04666C12.6687 4.93859 12.5125 4.86818 12.3462 4.84317L9.21365 4.37504L7.82678 1.40442C7.75409 1.24623 7.63755 1.11222 7.49099 1.01827C7.34443 0.924325 7.17399 0.87439 6.9999 0.87439C6.82582 0.87439 6.65538 0.924325 6.50882 1.01827C6.36226 1.11222 6.24572 1.24623 6.17303 1.40442L4.78615 4.37504L1.65365 4.85629C1.49015 4.88382 1.33715 4.95508 1.21087 5.06252C1.08458 5.16996 0.989726 5.30957 0.936354 5.46654C0.882982 5.62352 0.873084 5.79201 0.907709 5.95416C0.942335 6.11631 1.02019 6.26606 1.13303 6.38754L3.43428 8.75004L2.89615 12.0619C2.86833 12.2312 2.88869 12.4049 2.95492 12.5632C3.02114 12.7214 3.13055 12.8579 3.27064 12.9569C3.41074 13.0559 3.57586 13.1135 3.74715 13.1232C3.91844 13.1328 4.08898 13.094 4.23928 13.0113L6.9999 11.48L9.76053 13.0113C9.89508 13.0839 10.0452 13.1229 10.198 13.125C10.3859 13.1243 10.569 13.0663 10.723 12.9588C10.8642 12.8602 10.9744 12.7236 11.041 12.5648C11.1076 12.4061 11.1278 12.2317 11.0993 12.0619L10.5655 8.75004L12.8668 6.38754C12.9843 6.26667 13.0661 6.11566 13.1031 5.95121C13.1402 5.78676 13.1311 5.61526 13.0768 5.45567Z" fill="#3E47E0"></path>
					</g>
					<defs>
						<clipPath id="clip0_rating_<?php echo (int) $post_id; ?>">
							<rect width="14" height="14" fill="white"></rect>
						</clipPath>
					</defs>
				</svg>
				<span class="casino-table__rating-value"><?php echo number_format_i18n( $rating, 1 ); ?></span>
				<span class="casino-table__rating-max">/ 5</span>
			</div>
		</div>
		<div class="casino-table__cell casino-table__cell--actions">
			<a class="casino-table__btn casino-table__btn--primary" href="<?php echo esc_url( $cta_url ); ?>" target="_blank" rel="nofollow noopener">
				<?php echo esc_html( $cta_title ); ?>
			</a>
		</div>
	</div>
	<?php
}


