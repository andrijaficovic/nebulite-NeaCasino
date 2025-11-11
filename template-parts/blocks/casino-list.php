<?php
/**
 * Template: Casino List Block
 *
 * Expects vars: $block_id, $context, $query, $margin_bottom
 *
 * @package Nebulite
 */

if ( ! isset( $block_id, $context, $query ) ) {
	return;
}

$current_page = isset( $context['page'] ) ? (int) $context['page'] : 1;
$per_page     = isset( $context['per_page'] ) ? (int) $context['per_page'] : 10;

$found_total = (int) $query->found_posts;
$shown_count = min( $found_total, $per_page * $current_page );
$remaining   = max( 0, $found_total - $shown_count );

$margin_bottom_value = isset( $margin_bottom ) ? $margin_bottom : '2rem';
?>

<section id="<?php echo esc_attr( $block_id ); ?>" class="casino-list" data-per-page="<?php echo (int) $per_page; ?>"<?php echo $margin_bottom_value ? ' style="margin-bottom: ' . esc_attr( $margin_bottom_value ) . ';"' : ''; ?>>
	<div class="container">
		<div class="casino-table">
			<div class="casino-table__header">
				<div class="casino-table__header-cell casino-table__header-cell--casino">
					<?php esc_html_e( 'Casino', 'nebulite' ); ?>
				</div>
				<div class="casino-table__header-cell casino-table__header-cell--bonus">
					<?php esc_html_e( 'Bonus', 'nebulite' ); ?>
				</div>
				<div class="casino-table__header-cell casino-table__header-cell--features">
					<?php esc_html_e( 'Features', 'nebulite' ); ?>
				</div>
				<div class="casino-table__header-cell casino-table__header-cell--rating">
					<?php esc_html_e( 'Rating', 'nebulite' ); ?>
				</div>
				<div class="casino-table__header-cell casino-table__header-cell--website">
					<?php esc_html_e( 'Website', 'nebulite' ); ?>
				</div>
			</div>
			<div class="casino-table__body">
				<?php if ( $query->have_posts() ) : ?>
					<?php while ( $query->have_posts() ) : $query->the_post(); ?>
						<?php nebulite_render_casino_card( get_the_ID() ); ?>
					<?php endwhile; wp_reset_postdata(); ?>
				<?php else : ?>
					<p class="casino-list__empty"><?php esc_html_e( 'No casinos found.', 'nebulite' ); ?></p>
				<?php endif; ?>
			</div>
		</div>

		<?php if ( $remaining > 0 ) : ?>
			<button class="casino-list__load-more" type="button" data-next-page="2">
				<?php echo esc_html( sprintf( _n( '%d more to explore', '%d more to explore', $remaining, 'nebulite' ), $remaining ) ); ?>
				<svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg"> 
					<path fill-rule="evenodd" clip-rule="evenodd" d="M17.75 8L19.1642 9.41421L12.4571 16.1213L5.75 9.41421L7.16421 8L12.4571 13.2929L17.75 8Z" fill="#3e47e0"/>
				</svg>

			</button>
		<?php endif; ?>
	</div>
</section>


