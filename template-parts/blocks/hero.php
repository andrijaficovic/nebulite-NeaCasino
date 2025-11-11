<?php
/**
 * Template: Hero Block
 *
 * Expects vars: $block_id, $background_image, $title, $description, $results, $margin_bottom
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
		</div>
	</div>
</section>
