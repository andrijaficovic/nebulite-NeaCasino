<?php
/**
 * Template: Casino List Block
 *
 * Expects vars: $block_id, $casinos, $initial_casinos, $chunk_size, $margin_bottom
 *
 * @package Nebulite
 */

if ( ! isset( $block_id, $casinos, $initial_casinos, $chunk_size ) ) {
	return;
}

$total_count     = is_array( $casinos ) ? count( $casinos ) : 0;
$initial_count   = is_array( $initial_casinos ) ? count( $initial_casinos ) : 0;
$remaining_count = max( 0, $total_count - $initial_count );
$offset          = $initial_count;
$chunk_size      = max( 1, (int) $chunk_size );

$margin_bottom_value = isset( $margin_bottom ) ? $margin_bottom : '2rem';
$ids_json = wp_json_encode( array_values( $casinos ) );
if ( false === $ids_json ) {
	$ids_json = '[]';
}
?>

<section
	id="<?php echo esc_attr( $block_id ); ?>"
	class="casino-list"
	data-casino-ids="<?php echo esc_attr( $ids_json ); ?>"
	data-offset="<?php echo (int) $offset; ?>"
	data-chunk-size="<?php echo (int) $chunk_size; ?>"
<?php echo $margin_bottom_value ? ' style="margin-bottom: ' . esc_attr( $margin_bottom_value ) . ';"' : ''; ?>
>
	<div class="container">
		<div class="casino-table">
			<div class="casino-table__header">
				<div class="casino-table__header-cell casino-table__header-cell--casino">
					<?php esc_html_e( 'Καζίνο', 'nebulite' ); ?>
				</div>
				<div class="casino-table__header-cell casino-table__header-cell--bonus">
					<?php esc_html_e( 'Bonus', 'nebulite' ); ?>
				</div>
				<div class="casino-table__header-cell casino-table__header-cell--features">
					<?php esc_html_e( 'Γιατί να γραφτείς', 'nebulite' ); ?>
				</div>
				<div class="casino-table__header-cell casino-table__header-cell--rating">
					<?php esc_html_e( 'Βαθμολογία', 'nebulite' ); ?>
				</div>
				<div class="casino-table__header-cell casino-table__header-cell--website">
					<?php esc_html_e( 'Website', 'nebulite' ); ?>
				</div>
			</div>
			<div class="casino-table__body">
				<?php if ( ! empty( $initial_casinos ) ) : ?>
					<?php foreach ( $initial_casinos as $index => $casino_id ) : ?>
						<?php nebulite_render_casino_card( (int) $casino_id, $index + 1 ); ?>
					<?php endforeach; ?>
				<?php else : ?>
					<p class="casino-list__empty"><?php esc_html_e( 'No casinos found.', 'nebulite' ); ?></p>
				<?php endif; ?>
			</div>
		</div>

		<?php if ( $remaining_count > 0 ) : ?>
			<button class="casino-list__load-more" type="button">
				<?php echo esc_html( sprintf( _n( '%d περισσότερα για εξερεύνηση', '%d περισσότερα για εξερεύνηση', $remaining_count, 'nebulite' ), $remaining_count ) ); ?>
			</button>
		<?php endif; ?>
	</div>
</section>
