<?php
/**
 * Template: Trending Casinos Block
 *
 * Expects vars: $block_id, $title, $description, $select_casinos, $margin_bottom
 *
 * @package Nebulite
 */

if ( ! isset( $block_id ) ) {
	return;
}

$margin_bottom_value = isset( $margin_bottom ) ? $margin_bottom : '2rem';
$style_attr = '';
if ( $margin_bottom_value ) {
	$style_attr .= 'margin-bottom: ' . esc_attr( $margin_bottom_value ) . ';';
}

// Rank colors
$rank_colors = array(
	1 => '#FFD700', // Gold
	2 => '#C0C0C0', // Silver
	3 => '#CD7F32', // Bronze
);
?>

<section id="<?php echo esc_attr( $block_id ); ?>" class="trending-casinos"<?php echo $style_attr ? ' style="' . esc_attr( $style_attr ) . '"' : ''; ?>>
	<div class="container">
		<?php if ( ! empty( $title ) ) : ?>
			<h2 class="trending-casinos__title"><?php echo esc_html( $title ); ?></h2>
		<?php endif; ?>

		<?php if ( ! empty( $description ) ) : ?>
			<div class="trending-casinos__description">
				<?php echo wp_kses_post( wpautop( $description ) ); ?>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $select_casinos ) && is_array( $select_casinos ) ) : ?>
			<div class="trending-casinos__cards">
				<?php foreach ( $select_casinos as $index => $item ) : ?>
					<?php
					$casino = isset( $item['casino'] ) ? $item['casino'] : null;
					
					// Handle ACF relationship field - it might return an array or object
					if ( empty( $casino ) ) {
						continue;
					}
					
					// If it's an array, get the first item
					if ( is_array( $casino ) && ! empty( $casino ) ) {
						$casino = reset( $casino ); // Get first element
					}
					
					// If it's still not an object, skip
					if ( ! is_object( $casino ) ) {
						continue;
					}
					
					$post_id = isset( $casino->ID ) ? $casino->ID : 0;
					
					if ( empty( $post_id ) ) {
						continue;
					}
					
					$rank    = $index + 1; // 1, 2, 3
					$rank_color = isset( $rank_colors[ $rank ] ) ? $rank_colors[ $rank ] : '#666';
					
					// Get casino fields
					$image   = get_field( 'image', $post_id );
					$rating  = (float) get_field( 'rating', $post_id );
					$bonus   = (string) get_field( 'bonus', $post_id );
					$cta     = get_field( 'cta', $post_id );					
					$code    = (string) get_field( 'code', $post_id );
					
					$img_url = is_array( $image ) && ! empty( $image['url'] ) ? esc_url( $image['url'] ) : esc_url( get_template_directory_uri() . '/assets/images/default-casino-preview.svg' );
					$cta_url = ( is_array( $cta ) && ! empty( $cta['url'] ) ) ? esc_url( $cta['url'] ) : '#';
					$cta_title = ( is_array( $cta ) && ! empty( $cta['title'] ) ) ? esc_html( $cta['title'] ) : __( 'Visit Website', 'nebulite' );										
					?>
					<div class="trending-casinos__card">
						<div class="trending-casinos__header">
							<div class="trending-casinos__logo-wrapper">
								<img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( get_the_title( $post_id ) ); ?>" class="trending-casinos__logo">
								<span class="trending-casinos__rank" style="background-color: <?php echo esc_attr( $rank_color ); ?>;">
									<?php echo (int) $rank; ?>
								</span>
							</div>
							<div class="trending-casinos__info">
								<h3 class="trending-casinos__name"><?php echo esc_html( get_the_title( $post_id ) ); ?></h3>
								<?php if ( ! empty( $website_text ) ) : ?>
									<a href="<?php echo esc_url( $website_url ); ?>" class="trending-casinos__website" target="_blank" rel="nofollow noopener">
										<?php echo $website_text; ?>
									</a>
								<?php endif; ?>
							</div>
							<?php if ( ! empty( $rating ) ) : ?>
								<div class="trending-casinos__rating" aria-label="<?php esc_attr_e( 'Rating', 'nebulite' ); ?>">
									<svg class="trending-casinos__rating-icon" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
										<g clip-path="url(#clip0_<?php echo esc_attr( $block_id . '_' . $rank ); ?>)">
											<path d="M13.0768 5.45567C13.0231 5.29625 12.9265 5.15472 12.7976 5.04666C12.6687 4.93859 12.5125 4.86818 12.3462 4.84317L9.21365 4.37504L7.82678 1.40442C7.75409 1.24623 7.63755 1.11222 7.49099 1.01827C7.34443 0.924325 7.17399 0.87439 6.9999 0.87439C6.82582 0.87439 6.65538 0.924325 6.50882 1.01827C6.36226 1.11222 6.24572 1.24623 6.17303 1.40442L4.78615 4.37504L1.65365 4.85629C1.49015 4.88382 1.33715 4.95508 1.21087 5.06252C1.08458 5.16996 0.989726 5.30957 0.936354 5.46654C0.882982 5.62352 0.873084 5.79201 0.907709 5.95416C0.942335 6.11631 1.02019 6.26606 1.13303 6.38754L3.43428 8.75004L2.89615 12.0619C2.86833 12.2312 2.88869 12.4049 2.95492 12.5632C3.02114 12.7214 3.13055 12.8579 3.27064 12.9569C3.41074 13.0559 3.57586 13.1135 3.74715 13.1232C3.91844 13.1328 4.08898 13.094 4.23928 13.0113L6.9999 11.48L9.76053 13.0113C9.89508 13.0839 10.0452 13.1229 10.198 13.125C10.3859 13.1243 10.569 13.0663 10.723 12.9588C10.8642 12.8602 10.9744 12.7236 11.041 12.5648C11.1076 12.4061 11.1278 12.2317 11.0993 12.0619L10.5655 8.75004L12.8668 6.38754C12.9843 6.26667 13.0661 6.11566 13.1031 5.95121C13.1402 5.78676 13.1311 5.61526 13.0768 5.45567Z" fill="#f6c543"></path>
										</g>
										<defs>
											<clipPath id="clip0_<?php echo esc_attr( $block_id . '_' . $rank ); ?>">
												<rect width="14" height="14" fill="white"></rect>
											</clipPath>
										</defs>
									</svg>
									<span class="trending-casinos__rating-value"><?php echo esc_html( number_format( $rating, 1 ) ); ?></span>
									<span class="trending-casinos__rating-max">/ 5</span>
								</div>
							<?php endif; ?>
						</div>
						
						<?php if ( ! empty( $bonus ) ) : ?>
							<div class="trending-casinos__bonus-section">
								<div class="trending-casinos__bonus">Bonus: <?php echo wp_kses_post( $bonus ); ?>                                    
                                </div>
							</div>
						<?php endif; ?>

						<?php if ( ! empty( $code ) ) : ?>
							<div class="trending-casinos__code-section">
								<div class="trending-casinos__code">Code: <span><?php echo wp_kses_post( $code ); ?></span></div>
							</div>
						<?php endif; ?>
						
						<a href="<?php echo esc_url( $cta_url ); ?>" class="trending-casinos__cta" target="_blank" rel="nofollow noopener">
							<?php echo $cta_title; ?>
						</a>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
