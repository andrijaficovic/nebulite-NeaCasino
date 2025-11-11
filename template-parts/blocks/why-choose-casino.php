<?php
/**
 * Template: Why Choose Casino Block
 *
 * Expects vars: $block_id, $title, $reasons, $margin_bottom
 *
 * @package Nebulite
 */

if ( ! isset( $block_id ) ) {
	return;
}

$margin_bottom_value = isset( $margin_bottom ) ? $margin_bottom : '2rem';
$style_attr = 'background-color: #00022C;';
if ( $margin_bottom_value ) {
	$style_attr .= ' margin-bottom: ' . esc_attr( $margin_bottom_value ) . ';';
}
?>

<section id="<?php echo esc_attr( $block_id ); ?>" class="why-choose-casino" style="<?php echo esc_attr( $style_attr ); ?>">
	<div class="container">
		<?php if ( ! empty( $title ) ) : ?>
			<h2 class="why-choose-casino__title"><?php echo esc_html( $title ); ?></h2>
		<?php endif; ?>

		<?php if ( ! empty( $reasons ) && is_array( $reasons ) ) : ?>
			<div class="why-choose-casino__reasons">
				<?php foreach ( $reasons as $item ) : ?>
					<?php
					$reason      = isset( $item['reason'] ) ? esc_html( $item['reason'] ) : '';
					$icon        = isset( $item['icon'] ) ? $item['icon'] : '';
					$description = isset( $item['description'] ) ? $item['description'] : '';
					
					if ( empty( $reason ) ) {
						continue;
					}
					
					$icon_url = '';
					if ( ! empty( $icon ) && is_array( $icon ) && ! empty( $icon['url'] ) ) {
						$icon_url = esc_url( $icon['url'] );
					}
					?>
					<div class="why-choose-casino__item">
						<div class="why-choose-casino__header">
							<?php if ( ! empty( $icon_url ) ) : ?>
								<div class="why-choose-casino__icon-wrapper">
									<img src="<?php echo esc_url( $icon_url ); ?>" alt="<?php echo esc_attr( $reason ); ?>" class="why-choose-casino__icon">
								</div>
							<?php endif; ?>
							<h3 class="why-choose-casino__reason"><?php echo wp_kses( $reason, array( 'br' => array(), 'span' => array() ) ); ?></h3>
						</div>
						<?php if ( ! empty( $description ) ) : ?>
							<div class="why-choose-casino__description">
								<?php echo wp_kses_post( wpautop( $description ) ); ?>
							</div>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
