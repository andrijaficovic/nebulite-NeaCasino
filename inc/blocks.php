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
		'description'     => __( 'Lists casinos with filters, sorting and load more.', 'nebulite' ),
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
		'sort'     => 'rating', // rating|newest
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
	$sort     = (string) ( $context['sort'] ?? 'rating' );

	$meta_query = array( 'relation' => 'AND' );
	$tax_query  = array();

	$filters = $context['filters'] ?? array();

	// General filters
	if ( isset( $filters['low_wagering'] ) && $filters['low_wagering'] ) {
		$meta_query[] = array(
			'key'     => 'addittional_features_low_wagering',
			'value'   => 1,
			'compare' => '=',
			'type'    => 'NUMERIC',
		);
	}

	if ( isset( $filters['low_deposit'] ) && $filters['low_deposit'] ) {
		$meta_query[] = array(
			'key'     => 'addittional_features_low_deposit',
			'value'   => 1,
			'compare' => '=',
			'type'    => 'NUMERIC',
		);
	}

	if ( isset( $filters['no_kyc'] ) && $filters['no_kyc'] ) {
		$meta_query[] = array(
			'key'     => 'addittional_features_kyc',
			'value'   => 0,
			'compare' => '=',
			'type'    => 'NUMERIC',
		);
	}

	if ( isset( $filters['chatroom'] ) && $filters['chatroom'] ) {
		$meta_query[] = array(
			'key'     => 'addittional_features_chatroom',
			'value'   => 1,
			'compare' => '=',
			'type'    => 'NUMERIC',
		);
	}

	if ( isset( $filters['discord'] ) && $filters['discord'] ) {
		$meta_query[] = array(
			'key'     => 'addittional_features_discord',
			'value'   => 1,
			'compare' => '=',
			'type'    => 'NUMERIC',
		);
	}

	// Build initial query with general filters only (fast, no LIKE queries)
	$initial_args = array(
		'post_type'           => 'casino',
		'post_status'         => 'publish',
		'posts_per_page'      => -1, // Get all for post-processing
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
		'meta_query'          => count( $meta_query ) > 1 ? $meta_query : array(),
	);

	// Check if we need to filter by games or cryptocurrencies (requires post-processing)
	$has_games_filter        = ! empty( $filters['games'] ) && is_array( $filters['games'] );
	$has_crypto_filter       = ! empty( $filters['cryptocurrencies'] ) && is_array( $filters['cryptocurrencies'] );
	$needs_post_processing   = $has_games_filter || $has_crypto_filter;

	if ( $needs_post_processing ) {
		// Get all casinos matching general filters first
		$pre_query = new WP_Query( $initial_args );
		$matched_ids = array();

		if ( $pre_query->have_posts() ) {
			$game_ids = array();
			$crypto_ids = array();

			if ( $has_games_filter ) {
				$game_ids = array_map( 'intval', $filters['games'] );
				$game_ids = array_filter( $game_ids );
			}

			if ( $has_crypto_filter ) {
				$crypto_ids = array_map( 'intval', $filters['cryptocurrencies'] );
				$crypto_ids = array_filter( $crypto_ids );
			}

			// Post-process each casino to check games and cryptocurrencies
			while ( $pre_query->have_posts() ) {
				$pre_query->the_post();
				$casino_id = get_the_ID();
				$matches = true;

				// Check games filter
				if ( $has_games_filter && ! empty( $game_ids ) ) {
					$all_games = get_field( 'games_all_games', $casino_id );
					$choose_games = get_field( 'games_choose_games', $casino_id );

					if ( $all_games ) {
						// Casino has all games, it matches
					} else {
						// Check if casino has ALL selected games
						$casino_game_ids = array();
						if ( is_array( $choose_games ) ) {
							$casino_game_ids = array_map( function( $game ) {
								return is_object( $game ) ? $game->ID : ( is_array( $game ) && isset( $game['ID'] ) ? $game['ID'] : (int) $game );
							}, $choose_games );
						}

						// Casino must have ALL selected games
						$missing_games = array_diff( $game_ids, $casino_game_ids );
						if ( ! empty( $missing_games ) ) {
							$matches = false;
						}
					}
				}

				// Check cryptocurrencies filter
				if ( $matches && $has_crypto_filter && ! empty( $crypto_ids ) ) {
					$all_cryptocurrencies = get_field( 'cryptocurrencies_all_cryptocurrencies', $casino_id );
					$choose_cryptocurrencies = get_field( 'cryptocurrencies_choose_cryptocurrencies', $casino_id );

					if ( $all_cryptocurrencies ) {
						// Casino has all cryptocurrencies, it matches
					} else {
						// Check if casino has ALL selected cryptocurrencies
						$casino_crypto_ids = array();
						if ( is_array( $choose_cryptocurrencies ) ) {
							$casino_crypto_ids = array_map( function( $crypto ) {
								return is_object( $crypto ) ? $crypto->ID : ( is_array( $crypto ) && isset( $crypto['ID'] ) ? $crypto['ID'] : (int) $crypto );
							}, $choose_cryptocurrencies );
						}

						// Casino must have ALL selected cryptocurrencies
						$missing_cryptos = array_diff( $crypto_ids, $casino_crypto_ids );
						if ( ! empty( $missing_cryptos ) ) {
							$matches = false;
						}
					}
				}

				if ( $matches ) {
					$matched_ids[] = $casino_id;
				}
			}
			wp_reset_postdata();
		}

		// If no matches, return empty query
		if ( empty( $matched_ids ) ) {
			$matched_ids = array( 0 );
		}

		// Build final query with post__in
		// Store count for found_posts calculation
		$total_matched = count( $matched_ids );
		
		$args = array(
			'post_type'           => 'casino',
			'post_status'         => 'publish',
			'posts_per_page'      => $per_page,
			'paged'               => $page,
			'ignore_sticky_posts' => true,
			'no_found_rows'       => false,
			'post__in'            => $matched_ids,
		);
	} else {
		// No games/crypto filters, use simple query
		$args = array(
			'post_type'           => 'casino',
			'post_status'         => 'publish',
			'posts_per_page'      => $per_page,
			'paged'               => $page,
			'ignore_sticky_posts' => true,
			'no_found_rows'       => false,
			'meta_query'          => count( $meta_query ) > 1 ? $meta_query : array(),
		);
	}

	if ( $sort === 'newest' ) {
		$args['orderby'] = 'date';
		$args['order']   = 'DESC';
	} else {
		$args['meta_key'] = 'rating';
		$args['orderby']  = array(
			'meta_value_num' => 'DESC',
			'menu_order'     => 'ASC',
			'date'           => 'DESC',
		);
	}

	$query = new WP_Query( $args );
	
	// Fix found_posts when using post__in with post-processing
	if ( $needs_post_processing && isset( $total_matched ) ) {
		$query->found_posts = $total_matched;
		$query->max_num_pages = ceil( $total_matched / $per_page );
	}

	// Post-process sorting when using post__in or when menu_order needs to be respected
	// WordPress doesn't properly respect orderby when post__in is used
	if ( $sort === 'rating' && ( $needs_post_processing || ! empty( $args['post__in'] ) ) ) {
		$posts = $query->posts;
		
		usort( $posts, function( $a, $b ) {
			// First, compare by rating (DESC)
			$rating_a = (float) get_field( 'rating', $a->ID );
			$rating_b = (float) get_field( 'rating', $b->ID );
			
			if ( $rating_a !== $rating_b ) {
				return $rating_b <=> $rating_a; // DESC
			}
			
			// Same rating - compare by menu_order (ASC, starts from 0)
			$menu_order_a = (int) $a->menu_order;
			$menu_order_b = (int) $b->menu_order;
			
			if ( $menu_order_a !== $menu_order_b ) {
				return $menu_order_a <=> $menu_order_b; // ASC
			}
			
			// Same menu_order - compare by date (DESC)
			return strtotime( $b->post_date ) <=> strtotime( $a->post_date ); // DESC
		} );
		
		$query->posts = $posts;
	}

	return $query;
}

/**
 * AJAX: Fetch casinos (filters/sort/pagination)
 */
add_action( 'wp_ajax_nebulite_casino_list', 'nebulite_ajax_casino_list' );
add_action( 'wp_ajax_nopriv_nebulite_casino_list', 'nebulite_ajax_casino_list' );
function nebulite_ajax_casino_list() {
	check_ajax_referer( 'nebulite_casino_list', 'nonce' );

	$per_page = isset( $_POST['per_page'] ) ? (int) $_POST['per_page'] : 10;
	$page     = isset( $_POST['page'] ) ? (int) $_POST['page'] : 1;
	$sort     = isset( $_POST['sort'] ) ? sanitize_text_field( wp_unslash( $_POST['sort'] ) ) : 'rating';

	$filters = array();
	if ( isset( $_POST['filters'] ) && is_array( $_POST['filters'] ) ) {
		$filters = array_map( static function( $v ) {
			return is_scalar( $v ) ? sanitize_text_field( (string) $v ) : $v;
		}, $_POST['filters'] );
	}

	$context = compact( 'per_page', 'page', 'sort' );
	$context['filters'] = $filters;

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
	$requirements = get_field( 'wagering_requirement', $post_id );
	$features_rep = get_field( 'features', $post_id );
	$add_feat = get_field( 'addittional_features', $post_id );
	$games_group = get_field( 'games', $post_id );
	$cryptocurrencies_group = get_field( 'cryptocurrencies', $post_id );

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

	// Format wagering requirements.
	$wagering_list = array();
	if ( ! empty( $requirements ) && is_array( $requirements ) ) {
		foreach ( $requirements as $row ) {
			if ( isset( $row['requirement'] ) && ! empty( $row['requirement'] ) ) {
				$wagering_list[] = esc_html( $row['requirement'] );
			}
		}
	}
	$wagering_display = ! empty( $wagering_list ) ? implode( ', ', $wagering_list ) : '';

	// Get games.
	$games_display = '';
	if ( ! empty( $games_group ) && is_array( $games_group ) ) {
		if ( ! empty( $games_group['all_games'] ) ) {
			// Fetch all games from the site.
			$all_games_query = new WP_Query( array(
				'post_type'      => 'game',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'orderby'        => 'title',
				'order'          => 'ASC',
				'no_found_rows'  => true,
			) );
			
			$game_titles = array();
			if ( $all_games_query->have_posts() ) {
				while ( $all_games_query->have_posts() ) {
					$all_games_query->the_post();
					$game_titles[] = esc_html( get_the_title() );
				}
				wp_reset_postdata();
			}
			$games_display = ! empty( $game_titles ) ? implode( ', ', $game_titles ) : '';
		} elseif ( ! empty( $games_group['choose_games'] ) && is_array( $games_group['choose_games'] ) ) {
			$game_titles = array();
			foreach ( $games_group['choose_games'] as $game_post ) {
				if ( isset( $game_post->post_title ) ) {
					$game_titles[] = esc_html( $game_post->post_title );
				}
			}
			$games_display = ! empty( $game_titles ) ? implode( ', ', $game_titles ) : '';
		}
	}

	// Get cryptocurrencies (collect image + title).
	$cryptocurrency_items = array();
	if ( ! empty( $cryptocurrencies_group ) && is_array( $cryptocurrencies_group ) ) {
		$collect_cryptocurrency_item = static function( $crypto_entry ) use ( &$cryptocurrency_items ) {
			$crypto_id = 0;

			if ( is_object( $crypto_entry ) && isset( $crypto_entry->ID ) ) {
				$crypto_id = (int) $crypto_entry->ID;
			} elseif ( is_numeric( $crypto_entry ) ) {
				$crypto_id = (int) $crypto_entry;
			}

			if ( ! $crypto_id ) {
				return;
			}

			$image = get_field( 'image', $crypto_id );
			if ( empty( $image ) || ! is_array( $image ) || empty( $image['url'] ) ) {
				return;
			}

			$title = get_the_title( $crypto_id );
			if ( empty( $title ) ) {
				return;
			}

			$cryptocurrency_items[] = array(
				'title'     => esc_html( $title ),
				'image_url' => esc_url( $image['url'] ),
			);
		};

		if ( ! empty( $cryptocurrencies_group['all_cryptocurrencies'] ) ) {
			$all_cryptocurrencies_query = new WP_Query( array(
				'post_type'      => 'cryptocurrency',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'orderby'        => 'title',
				'order'          => 'ASC',
				'no_found_rows'  => true,
			) );

			if ( $all_cryptocurrencies_query->have_posts() ) {
				while ( $all_cryptocurrencies_query->have_posts() ) {
					$all_cryptocurrencies_query->the_post();
					$collect_cryptocurrency_item( get_post() );
				}
				wp_reset_postdata();
			}
		} elseif ( ! empty( $cryptocurrencies_group['choose_cryptocurrencies'] ) && is_array( $cryptocurrencies_group['choose_cryptocurrencies'] ) ) {
			foreach ( $cryptocurrencies_group['choose_cryptocurrencies'] as $cryptocurrency_post ) {
				$collect_cryptocurrency_item( $cryptocurrency_post );
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
				<div class="casino-table__bonus"><?php echo esc_html( $bonus ); ?></div>
				
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
			<button class="casino-table__btn casino-table__btn--secondary casino-table__toggle" type="button" aria-expanded="false" data-casino-id="<?php echo (int) $post_id; ?>">
				<span class="casino-table__toggle-text"><?php esc_html_e( 'More Details', 'nebulite' ); ?></span>
				<svg class="casino-table__toggle-icon" width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
					<path fill-rule="evenodd" clip-rule="evenodd" d="M17.75 8L19.1642 9.41421L12.4571 16.1213L5.75 9.41421L7.16421 8L12.4571 13.2929L17.75 8Z" fill="#262847"/>
				</svg>
			</button>
		</div>
		<div class="casino-table__details" hidden>
			<div class="casino-table__details-content">
				<?php if ( ! empty( $bonus ) ) : ?>
					<div class="casino-table__detail-section">
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
						<div class="casino-table__detail-section-text">
							<h4 class="casino-table__detail-title"><?php esc_html_e( 'Bonus', 'nebulite' ); ?></h4>
							<p class="casino-table__detail-text"><?php echo esc_html( $bonus ); ?></p>
						</div>						
					</div>
				<?php endif; ?>
				<?php if ( ! empty( $wagering_list ) ) : ?>
					<div class="casino-table__detail-section">
						<svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
							<g clip-path="url(#clip0_8230_16482)">
							<path d="M13.5333 0H6.86666C5.76159 0 4.70179 0.438987 3.92039 1.22039C3.13898 2.00179 2.7 3.0616 2.7 4.16667V15.8333C2.7 16.3805 2.80777 16.9223 3.01717 17.4278C3.22656 17.9334 3.53347 18.3927 3.92039 18.7796C4.70179 19.561 5.76159 20 6.86666 20H8.68333C8.51671 19.5402 8.4906 19.0413 8.60833 18.5667L9.38333 15.4333C9.49148 14.9914 9.71925 14.5877 10.0417 14.2667L14.75 9.55C15.1375 9.1631 15.5975 8.85647 16.1038 8.64769C16.61 8.43891 17.1524 8.33208 17.7 8.33333V4.16667C17.7 3.0616 17.261 2.00179 16.4796 1.22039C15.6982 0.438987 14.6384 0 13.5333 0ZM10.2 10.8333H6.86666C6.64565 10.8333 6.43369 10.7455 6.27741 10.5893C6.12113 10.433 6.03333 10.221 6.03333 10C6.03333 9.77899 6.12113 9.56702 6.27741 9.41074C6.43369 9.25446 6.64565 9.16667 6.86666 9.16667H10.2C10.421 9.16667 10.633 9.25446 10.7893 9.41074C10.9455 9.56702 11.0333 9.77899 11.0333 10C11.0333 10.221 10.9455 10.433 10.7893 10.5893C10.633 10.7455 10.421 10.8333 10.2 10.8333ZM13.5333 7.5H6.86666C6.64565 7.5 6.43369 7.4122 6.27741 7.25592C6.12113 7.09964 6.03333 6.88768 6.03333 6.66667C6.03333 6.44565 6.12113 6.23369 6.27741 6.07741C6.43369 5.92113 6.64565 5.83333 6.86666 5.83333H13.5333C13.7543 5.83333 13.9663 5.92113 14.1226 6.07741C14.2789 6.23369 14.3667 6.44565 14.3667 6.66667C14.3667 6.88768 14.2789 7.09964 14.1226 7.25592C13.9663 7.4122 13.7543 7.5 13.5333 7.5ZM13.5333 4.16667H6.86666C6.64565 4.16667 6.43369 4.07887 6.27741 3.92259C6.12113 3.76631 6.03333 3.55435 6.03333 3.33333C6.03333 3.11232 6.12113 2.90036 6.27741 2.74408C6.43369 2.5878 6.64565 2.5 6.86666 2.5H13.5333C13.7543 2.5 13.9663 2.5878 14.1226 2.74408C14.2789 2.90036 14.3667 3.11232 14.3667 3.33333C14.3667 3.55435 14.2789 3.76631 14.1226 3.92259C13.9663 4.07887 13.7543 4.16667 13.5333 4.16667Z" fill="#80869E"/>
							<path d="M20.17 12.5C20.1783 13.1587 19.9254 13.7939 19.4667 14.2667L18.8833 14.8583C18.8833 14.8583 15.3417 11.325 15.35 11.325L15.9333 10.7333C16.2788 10.3796 16.7226 10.1377 17.2071 10.039C17.6916 9.9402 18.1946 9.98915 18.651 10.1795C19.1074 10.3698 19.4961 10.6927 19.7669 11.1064C20.0378 11.5201 20.1782 12.0056 20.17 12.5Z" fill="#80869E"/>
							<path d="M17.7 16.0417L14.75 18.9834C14.6461 19.0914 14.5128 19.1667 14.3667 19.2L11.2333 19.975C11.0939 20.0108 10.9476 20.0097 10.8087 19.9717C10.6699 19.9337 10.5434 19.8602 10.4416 19.7584C10.3398 19.6567 10.2663 19.5301 10.2283 19.3913C10.1903 19.2524 10.1892 19.1061 10.225 18.9667L11 15.8334C11.0333 15.6872 11.1086 15.554 11.2167 15.45L14.1667 12.5084C14.1667 12.5084 17.7 16.0334 17.7 16.0417Z" fill="#80869E"/>
							</g>
							<defs>
							<clipPath id="clip0_8230_16482">
							<rect width="20" height="20" fill="white" transform="translate(0.199997)"/>
							</clipPath>
							</defs>
						</svg>
						<div class="casino-table__detail-section-text">
							<h4 class="casino-table__detail-title"><?php esc_html_e( 'Wagering Requirements', 'nebulite' ); ?></h4>
							<ul class="casino-table__detail-list">
								<?php foreach ( $wagering_list as $req ) : ?>
									<li><?php echo $req; ?></li>
								<?php endforeach; ?>
							</ul>
						</div>							
					</div>
				<?php endif; ?>
				<?php if ( ! empty( $games_display ) ) : ?>
					<div class="casino-table__detail-section">
					<svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
						<g clip-path="url(#clip0_8230_16488)">
						<path d="M20.4 14.2101C20.3531 11.6008 20.0449 8.9621 19.5394 6.29882C19.1277 4.44843 17.6465 2.80038 15.2797 2.67968C13.5324 2.6078 13.1945 3.60312 11.2148 3.58398C10.6727 3.58046 10.1309 3.58046 9.58867 3.58398C7.60859 3.60312 7.26992 2.6078 5.52343 2.67968C3.15625 2.80038 1.6289 4.44413 1.2621 6.29882C0.756245 8.9621 0.448042 11.6004 0.401558 14.2098C0.39023 16.0266 2.18398 17.2351 3.36757 17.318C5.6539 17.4906 7.47031 13.459 8.85429 13.4586C9.88593 13.4644 10.9172 13.4648 11.9488 13.4586C13.3332 13.4586 15.148 17.491 17.4359 17.3183C18.6191 17.2355 20.4594 16.0187 20.4004 14.2101H20.4ZM7.85859 9.11874H6.79218V10.1851C6.79218 10.6351 6.42734 11 5.97734 11C5.52734 11 5.1625 10.6351 5.1625 10.1851V9.11874H4.09609C3.64609 9.11874 3.28125 8.7539 3.28125 8.3039C3.28125 7.8539 3.64609 7.48905 4.09609 7.48905H5.1625V6.42265C5.1625 5.97265 5.52734 5.6078 5.97734 5.6078C6.42734 5.6078 6.79218 5.97265 6.79218 6.42265V7.48905H7.85859C8.30859 7.48905 8.67343 7.8539 8.67343 8.3039C8.67343 8.7539 8.30859 9.11874 7.85859 9.11874ZM14.4434 10.9996C13.8055 11.0168 13.275 10.5129 13.2578 9.87538C13.241 9.23554 13.7453 8.70429 14.3828 8.68788C15.0211 8.67187 15.5523 9.17538 15.5687 9.81366C15.5848 10.4519 15.0812 10.9832 14.4434 10.9996ZM16.3672 7.91952C15.7293 7.93749 15.1977 7.43319 15.1801 6.7953C15.1641 6.15624 15.6676 5.62577 16.3059 5.60819C16.9449 5.5914 17.4754 6.09569 17.4926 6.73397C17.5094 7.37187 17.0047 7.90351 16.3672 7.91952Z" fill="#80869E"/>
						</g>
						<defs>
						<clipPath id="clip0_8230_16488">
						<rect width="20" height="20" fill="white" transform="translate(0.399994)"/>
						</clipPath>
						</defs>
					</svg>
						<div class="casino-table__detail-section-text">
							<h4 class="casino-table__detail-title"><?php esc_html_e( 'Games', 'nebulite' ); ?></h4>
							<p class="casino-table__detail-text"><?php echo esc_html( $games_display ); ?></p>
						</div>						
					</div>
				<?php endif; ?>
				<?php if ( ! empty( $cryptocurrency_items ) ) : ?>
					<div class="casino-table__detail-section casino-table__detail-section--crypto">
						<h4 class="casino-table__detail-title"><?php esc_html_e( 'Accepted Cryptocurrencies', 'nebulite' ); ?></h4>
						<ul class="casino-table__crypto-list">
							<?php foreach ( $cryptocurrency_items as $crypto_item ) : ?>
								<li class="casino-table__crypto-item">
									<span class="casino-table__crypto-wrapper">
										<img class="casino-table__crypto-image" src="<?php echo esc_url( $crypto_item['image_url'] ); ?>" alt="<?php echo esc_attr( $crypto_item['title'] ); ?>" loading="lazy">
										<span class="casino-table__crypto-label"><?php echo esc_html( $crypto_item['title'] ); ?></span>
									</span>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>
				<?php if ( ! empty( $add_feat ) && is_array( $add_feat ) ) : ?>
					<div class="casino-table__detail-section casino-table__detail-section--additional-features">
						<h4 class="casino-table__detail-title"><?php esc_html_e( 'Additional Features', 'nebulite' ); ?></h4>
						<ul class="casino-table__detail-list">
							<?php if ( ! empty( $add_feat['established'] ) ) : ?>
								<li><?php esc_html_e( 'Established:', 'nebulite' ); ?> <?php echo esc_html( (string) $add_feat['established'] ); ?></li>
							<?php endif; ?>
							<!-- <li><?php /*esc_html_e( 'VPN Friendly:', 'nebulite' ); ?> <?php echo ! empty( $add_feat['vpn-friendly'] ) ? esc_html__( 'Yes', 'nebulite' ) : esc_html__( 'No', 'nebulite' ); */?></li> -->
							<li><?php esc_html_e( 'KYC:', 'nebulite' ); ?> <?php echo ! empty( $add_feat['kyc'] ) ? esc_html__( 'Required', 'nebulite' ) : esc_html__( 'Not Required', 'nebulite' ); ?></li>
							<li><?php esc_html_e( 'Telegram:', 'nebulite' ); ?> <?php echo ! empty( $add_feat['telegram'] ) ? esc_html__( 'Yes', 'nebulite' ) : esc_html__( 'No', 'nebulite' ); ?></li>
							<li><?php esc_html_e( 'Discord:', 'nebulite' ); ?> <?php echo ! empty( $add_feat['discord'] ) ? esc_html__( 'Yes', 'nebulite' ) : esc_html__( 'No', 'nebulite' ); ?></li>
							<li><?php esc_html_e( 'Chatroom:', 'nebulite' ); ?> <?php echo ! empty( $add_feat['chatroom'] ) ? esc_html__( 'Yes', 'nebulite' ) : esc_html__( 'No', 'nebulite' ); ?></li>
							<li><?php esc_html_e( 'Wagering:', 'nebulite' ); ?> <?php echo ! empty( $add_feat['low_wagering'] ) ? esc_html__( 'Low', 'nebulite' ) : esc_html__( 'Standard', 'nebulite' ); ?></li>
							<li><?php esc_html_e( 'Deposit:', 'nebulite' ); ?> <?php echo ! empty( $add_feat['low_deposit'] ) ? esc_html__( 'Low', 'nebulite' ) : esc_html__( 'Standard', 'nebulite' ); ?></li>
						</ul>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<?php
}


