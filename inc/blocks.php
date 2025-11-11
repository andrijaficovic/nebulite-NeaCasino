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
		'description'     => __( 'Lists casinos with rating-based ordering and load more.', 'nebulite' ),
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
	$context = array(
		'per_page' => 10,
		'page'     => 1,
	);

	// Initial query (first page).
	$query = nebulite_get_casino_query( $context );

	// Load template part.
	$block_id = 'casino-list-' . ( isset( $block['id'] ) ? sanitize_key( $block['id'] ) : wp_generate_uuid4() );
	$margin_bottom = get_field( 'margin_bottom' ) ?: 'medium'; // Default to medium
	
	$vars = array(
		'block_id' => $block_id,
		'context'  => $context,
		'query'    => $query,
		'margin_bottom' => nebulite_get_margin_bottom( $margin_bottom ),
	);
	nebulite_load_template( 'template-parts/blocks/casino-list.php', $vars );
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
 * Build WP_Query for casinos based on context.
 */
function nebulite_get_casino_query( array $context ) {
	$per_page = max( 1, (int) ( $context['per_page'] ?? 10 ) );
	$page     = max( 1, (int) ( $context['page'] ?? 1 ) );

	$args = array(
		'post_type'           => 'casino',
		'post_status'         => 'publish',
		'posts_per_page'      => $per_page,
		'paged'               => $page,
		'ignore_sticky_posts' => true,
		'no_found_rows'       => false,
		'meta_key'            => 'rating',
		'meta_type'           => 'NUMERIC',
		'orderby'             => array(
			'meta_value_num' => 'DESC',
			'menu_order'     => 'ASC',
			'date'           => 'DESC',
		),
	);

	return new WP_Query( $args );
}

/**
 * AJAX: Fetch casinos (pagination).
 */
add_action( 'wp_ajax_nebulite_casino_list', 'nebulite_ajax_casino_list' );
add_action( 'wp_ajax_nopriv_nebulite_casino_list', 'nebulite_ajax_casino_list' );
function nebulite_ajax_casino_list() {
	check_ajax_referer( 'nebulite_casino_list', 'nonce' );

	$per_page = isset( $_POST['per_page'] ) ? (int) $_POST['per_page'] : 10;
	$page     = isset( $_POST['page'] ) ? (int) $_POST['page'] : 1;
	$context = compact( 'per_page', 'page' );

	$query = nebulite_get_casino_query( $context );

	ob_start();
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			nebulite_render_casino_card( get_the_ID() );
		}
		wp_reset_postdata();
	}
	$html = ob_get_clean();

	wp_send_json_success( array(
		'html'       => $html,
		'found'      => (int) $query->found_posts,
		'page'       => $page,
		'per_page'   => $per_page,
	) );
}

/**
 * Render a single casino card (used by template and AJAX).
 */
function nebulite_render_casino_card( $post_id ) {
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
	<div class="casino-table__row" data-post-id="<?php echo (int) $post_id; ?>">
		<div class="casino-table__cell casino-table__cell--casino">
			<div class="casino-table__logo-wrapper">
				<img class="casino-table__logo" src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( get_the_title( $post_id ) ); ?>" loading="lazy">
			</div>
			<div class="casino-table__info">
				<h3 class="casino-table__name"><?php echo esc_html( get_the_title( $post_id ) ); ?></h3>
			</div>
		</div>
		<div class="casino-table__cell casino-table__cell--bonus">
			<?php if ( ! empty( $bonus ) ) : ?>
				<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
					<g clip-path="url(#clip0_6032_7637)">
					<rect width="20" height="20" rx="10" fill="#150B2B"/>
					<path d="M3.55212 8.08801H16.4463C16.585 8.08801 16.6971 8.2001 16.6971 8.33886V20.1861C16.6971 20.3248 16.5851 20.4369 16.4463 20.4369H3.55212C3.41336 20.4369 3.30127 20.3248 3.30127 20.1861V8.33886C3.30127 8.2001 3.41336 8.08801 3.55212 8.08801Z" fill="#EF3A50"/>
					<path d="M8.19824 12.1339H11.8007V20.4374H8.19824V12.1339Z" fill="#FDD33A"/>
					<path d="M8.19824 12.1339H11.8007V13.4895H8.19824V12.1339Z" fill="#D2A128"/>
					<path d="M6.85558 7.47891C5.40127 7.45491 5.26072 8.87631 5.25184 8.97415C5.24738 9.07023 5.18691 9.15916 5.09083 9.19562C4.96186 9.24542 4.81685 9.17962 4.76705 9.05065C4.5945 8.59788 4.53224 7.91832 4.59538 7.22273C4.64784 6.6499 4.78485 6.06103 5.0152 5.5727C5.25981 5.055 5.61029 4.64496 6.07994 4.46616C6.28454 4.38878 6.50868 4.35586 6.75507 4.37808C8.08575 4.49905 9.95459 6.83133 10.0907 7.00298C10.1289 7.04744 10.1521 7.10528 10.1521 7.16841V7.68078C10.1521 7.70657 10.1476 7.73237 10.1396 7.75728C10.0978 7.88892 9.95641 7.96187 9.82475 7.91917C9.82117 7.91832 8.55987 7.50737 6.85558 7.47891Z" fill="#FDD33A"/>
					<path d="M14.7464 8.97409C14.7374 8.87625 14.596 7.45482 13.1426 7.47885C11.4383 7.50731 10.177 7.91826 10.1734 7.91917C10.0418 7.96185 9.90035 7.88892 9.85855 7.75728C9.85055 7.73237 9.84609 7.70657 9.84609 7.68078H9.84521V7.16842C9.84521 7.10528 9.86922 7.04745 9.90747 7.00298C10.0436 6.8313 11.9124 4.49905 13.2431 4.37808C13.4895 4.35587 13.7136 4.38875 13.9182 4.46617C14.3879 4.64497 14.7384 5.055 14.983 5.5727C15.2133 6.06106 15.3503 6.6499 15.4028 7.22273C15.4659 7.91832 15.4028 8.59788 15.2311 9.05065C15.1813 9.17962 15.0363 9.24546 14.9074 9.19563C14.8113 9.1591 14.7508 9.07017 14.7464 8.97409Z" fill="#FDD33A"/>
					<path d="M9.9998 6.54053C10.5139 6.54053 10.98 6.74955 11.3171 7.08669C11.6543 7.4238 11.8624 7.889 11.8624 8.40315C11.8624 8.91819 11.6543 9.38339 11.3171 9.7205C10.98 10.0576 10.5139 10.2667 9.9998 10.2667C9.48568 10.2667 9.01957 10.0576 8.68246 9.7205C8.34535 9.38339 8.13721 8.91819 8.13721 8.40315C8.13721 7.88903 8.34535 7.42383 8.68246 7.08669C9.01957 6.74955 9.48568 6.54053 9.9998 6.54053Z" fill="#D2A128"/>
					<path d="M2.47253 8.08801H17.5264C17.6651 8.08801 17.7772 8.2001 17.7772 8.33886V11.8826C17.7772 12.0214 17.6652 12.1335 17.5264 12.1335H2.47253C2.33377 12.1335 2.22168 12.0214 2.22168 11.8826V8.33886C2.22171 8.2001 2.33377 8.08801 2.47253 8.08801Z" fill="#FF4A69"/>
					<path d="M7.49805 8.08801H12.5006V12.1334H7.49805V8.08801Z" fill="#FDD33A"/>
					<path fill-rule="evenodd" clip-rule="evenodd" d="M8.16337 8.08818C8.19274 7.91471 8.24608 7.74928 8.31992 7.59627C7.89385 7.53489 7.39752 7.48776 6.85581 7.47885C6.19135 7.46727 5.80087 7.75904 5.57227 8.08815H7.49803L8.16337 8.08818Z" fill="#D2A128"/>
					<path fill-rule="evenodd" clip-rule="evenodd" d="M11.8353 8.08787C11.8059 7.91441 11.7525 7.74897 11.6787 7.59597C12.1048 7.53459 12.6011 7.48746 13.1428 7.47854C13.8073 7.46696 14.1978 7.75873 14.4264 8.08784H12.5006L11.8353 8.08787Z" fill="#D2A128"/>
					</g>
					<defs>
					<clipPath id="clip0_6032_7637">
					<rect width="20" height="20" rx="10" fill="white"/>
					</clipPath>
					</defs>
				</svg>		
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


