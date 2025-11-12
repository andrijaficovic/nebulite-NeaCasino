<?php
/**
 * Template: Hero Block
 *
 * Expects vars: $block_id, $background_image, $title, $description, $results, $cta, $featured_casinos, $margin_bottom
 *
 * @package Nebulite
 */

if ( ! isset( $block_id ) ) {
	return;
}

$bg_image_url = '';
if ( ! empty( $background_image ) && is_array( $background_image ) && ! empty( $background_image['url'] ) ) {
	$bg_image_url = esc_url( $background_image['url'] );
}

$margin_bottom_value = isset( $margin_bottom ) ? $margin_bottom : '2rem';
$style_attr = '';
if ( $bg_image_url ) {
	$style_attr .= 'background-image: url(' . esc_attr( $bg_image_url ) . ');';
}
if ( $margin_bottom_value ) {
	$style_attr .= ' margin-bottom: ' . esc_attr( $margin_bottom_value ) . ';';
}
?>

<section id="<?php echo esc_attr( $block_id ); ?>" class="hero-block"<?php echo $style_attr ? ' style="' . esc_attr( $style_attr ) . '"' : ''; ?>>
	<div class="container">
		<div class="hero-block__content">
			<?php if ( ! empty( $title ) ) : ?>
				<h1 class="hero-block__title"><?php echo wp_kses( $title, array( 'br' => array(), 'span' => array() ) ); ?></h1>
			<?php endif; ?>

			<?php if ( ! empty( $description ) ) : ?>
				<div class="hero-block__description">
					<?php echo wp_kses_post( wpautop( $description ) ); ?>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $results ) && is_array( $results ) ) : ?>
				<div class="hero-block__results">
					<?php foreach ( $results as $result ) : ?>
						<?php
						$number = isset( $result['number'] ) ? (int) $result['number'] : 0;
						$desc   = isset( $result['description'] ) ? esc_html( $result['description'] ) : '';
						if ( $number > 0 && ! empty( $desc ) ) :
						?>
							<div class="hero-block__result-item">
								<div class="hero-block__result-number">
									<span class="hero-block__result-number-value" data-target="<?php echo (int) $number; ?>">0</span>+
								</div>
								<div class="hero-block__result-description"><?php echo $desc; ?></div>
							</div>
						<?php endif; ?>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $cta ) && is_array( $cta ) && ! empty( $cta['url'] ) ) : ?>
				<?php
				$cta_url = esc_url( $cta['url'] );
				$cta_title = ! empty( $cta['title'] ) ? esc_html( $cta['title'] ) : __( 'View Casinos', 'nebulite' );
				$cta_target = ! empty( $cta['target'] ) ? esc_attr( $cta['target'] ) : '_self';
				?>
				<div class="hero-block__cta-wrapper">
					<a href="<?php echo $cta_url; ?>" class="hero-block__cta" target="<?php echo $cta_target; ?>" rel="<?php echo ( '_blank' === $cta_target ) ? 'nofollow noopener' : ''; ?>">
						<?php echo $cta_title; ?>
					</a>
				</div>
			<?php endif; ?>
		</div>

		<?php if ( ! empty( $featured_casinos ) && is_array( $featured_casinos ) ) : ?>
			<div class="hero-block__featured-casinos">
				<?php foreach ( $featured_casinos as $casino ) : ?>
					<?php
					$casino_id = 0;
					if ( $casino instanceof WP_Post ) {
						$casino_id = $casino->ID;
					} elseif ( is_numeric( $casino ) ) {
						$casino_id = (int) $casino;
					} elseif ( is_array( $casino ) && isset( $casino['ID'] ) ) {
						$casino_id = (int) $casino['ID'];
					}

					if ( empty( $casino_id ) ) {
						continue;
					}

					$casino_image = get_field( 'image', $casino_id );
					$casino_rating = (float) get_field( 'rating', $casino_id );
					$casino_bonus = (string) get_field( 'bonus', $casino_id );
					$casino_cta = get_field( 'cta', $casino_id );
					$logo_background = get_field( 'image_background_color', $casino_id );
					
					$casino_img_url = is_array( $casino_image ) && ! empty( $casino_image['url'] ) ? esc_url( $casino_image['url'] ) : esc_url( get_template_directory_uri() . '/assets/images/default-casino-preview.svg' );
					$casino_cta_url = ( is_array( $casino_cta ) && ! empty( $casino_cta['url'] ) ) ? esc_url( $casino_cta['url'] ) : '#';
					$casino_cta_title = ( is_array( $casino_cta ) && ! empty( $casino_cta['title'] ) ) ? esc_html( $casino_cta['title'] ) : __( 'Visit', 'nebulite' );
					
					$logo_background_style = '';
					if ( ! empty( $logo_background ) && is_string( $logo_background ) ) {
						$logo_background_style = ' style="background-color: ' . esc_attr( $logo_background ) . ';"';
					}
					?>
					<div class="hero-block__featured-casino">
						<div class="hero-block__featured-casino-logo"<?php echo $logo_background_style; ?>>
							<img src="<?php echo $casino_img_url; ?>" alt="<?php echo esc_attr( get_the_title( $casino_id ) ); ?>" loading="lazy">
						</div>
						<div class="hero-block__featured-casino-info">
							<h3 class="hero-block__featured-casino-name"><?php echo esc_html( get_the_title( $casino_id ) ); ?></h3>
							<?php if ( ! empty( $casino_bonus ) ) : ?>
								<div class="hero-block__featured-casino-bonus"><?php echo wp_kses_post( $casino_bonus ); ?></div>
							<?php endif; ?>
							<?php if ( ! empty( $casino_rating ) ) : ?>
								<div class="hero-block__featured-casino-rating">
									<svg class="hero-block__featured-casino-rating-icon" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
										<path d="M13.0768 5.45567C13.0231 5.29625 12.9265 5.15472 12.7976 5.04666C12.6687 4.93859 12.5125 4.86818 12.3462 4.84317L9.21365 4.37504L7.82678 1.40442C7.75409 1.24623 7.63755 1.11222 7.49099 1.01827C7.34443 0.924325 7.17399 0.87439 6.9999 0.87439C6.82582 0.87439 6.65538 0.924325 6.50882 1.01827C6.36226 1.11222 6.24572 1.24623 6.17303 1.40442L4.78615 4.37504L1.65365 4.85629C1.49015 4.88382 1.33715 4.95508 1.21087 5.06252C1.08458 5.16996 0.989726 5.30957 0.936354 5.46654C0.882982 5.62352 0.873084 5.79201 0.907709 5.95416C0.942335 6.11631 1.02019 6.26606 1.13303 6.38754L3.43428 8.75004L2.89615 12.0619C2.86833 12.2312 2.88869 12.4049 2.95492 12.5632C3.02114 12.7214 3.13055 12.8579 3.27064 12.9569C3.41074 13.0559 3.57586 13.1135 3.74715 13.1232C3.91844 13.1328 4.08898 13.094 4.23928 13.0113L6.9999 11.48L9.76053 13.0113C9.89508 13.0839 10.0452 13.1229 10.198 13.125C10.3859 13.1243 10.569 13.0663 10.723 12.9588C10.8642 12.8602 10.9744 12.7236 11.041 12.5648C11.1076 12.4061 11.1278 12.2317 11.0993 12.0619L10.5655 8.75004L12.8668 6.38754C12.9843 6.26667 13.0661 6.11566 13.1031 5.95121C13.1402 5.78676 13.1311 5.61526 13.0768 5.45567Z" fill="#f6c543"></path>
									</svg>
									<span class="hero-block__featured-casino-rating-value"><?php echo number_format_i18n( $casino_rating, 1 ); ?></span>
									<span class="hero-block__featured-casino-rating-max">/ 5</span>
								</div>
							<?php endif; ?>
						</div>
						<a href="<?php echo $casino_cta_url; ?>" class="hero-block__featured-casino-cta" target="_blank" rel="nofollow noopener">
							<?php echo $casino_cta_title; ?>
						</a>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
