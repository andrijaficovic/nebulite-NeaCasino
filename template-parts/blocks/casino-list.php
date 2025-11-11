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

$current_page = (int) $context['page'];
$per_page     = (int) $context['per_page'];
$sort         = (string) $context['sort'];

$found_total  = (int) $query->found_posts;
$shown_count  = min( $found_total, $per_page * $current_page );
$remaining    = max( 0, $found_total - $shown_count );

// Get all games and cryptocurrencies for filters
$all_games = get_posts( array(
	'post_type'      => 'game',
	'post_status'    => 'publish',
	'posts_per_page' => -1,
	'orderby'        => 'title',
	'order'          => 'ASC',
) );

$all_cryptocurrencies = get_posts( array(
	'post_type'      => 'cryptocurrency',
	'post_status'    => 'publish',
	'posts_per_page' => -1,
	'orderby'        => 'title',
	'order'          => 'ASC',
) );

$margin_bottom_value = isset( $margin_bottom ) ? $margin_bottom : '2rem';
?>

<section id="<?php echo esc_attr( $block_id ); ?>" class="casino-list" data-per-page="<?php echo (int) $per_page; ?>" data-sort="<?php echo esc_attr( $sort ); ?>"<?php echo $margin_bottom_value ? ' style="margin-bottom: ' . esc_attr( $margin_bottom_value ) . ';"' : ''; ?>>
	<div class="container">
		<div class="casino-list__controls">
			<button class="casino-list__filters" type="button" aria-expanded="false" aria-controls="<?php echo esc_attr( $block_id ); ?>-filters-modal">
				<svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
					<g clip-path="url(#clip0_7392_52124)">
					<path d="M13.2475 1.05414e-06H0.766137C0.54586 -0.00041535 0.344044 0.122562 0.243413 0.31841C0.141394 0.516895 0.1593 0.75591 0.289634 0.937184L4.86229 7.37879C4.86381 7.38101 4.86548 7.38309 4.867 7.38531C5.03315 7.60961 5.12309 7.88139 5.12351 8.16051V13.4123C5.12254 13.5678 5.18361 13.7173 5.29312 13.8276C5.40278 13.9378 5.55185 14 5.7073 14C5.78628 13.9999 5.86457 13.9842 5.93758 13.9538L8.50705 12.9741C8.73719 12.9038 8.89001 12.6865 8.89001 12.425V8.16051C8.89042 7.88139 8.98036 7.60961 9.14637 7.38531C9.1479 7.38309 9.14956 7.38101 9.15109 7.37879L13.7239 0.937045C13.8542 0.75591 13.8721 0.517034 13.7701 0.318549C13.6696 0.122562 13.4677 -0.00041535 13.2475 1.05414e-06Z" fill="#262847"/>
					</g>
					<defs>
					<clipPath id="clip0_7392_52124">
					<rect width="14" height="14" fill="white"/>
					</clipPath>
					</defs>
				</svg>
				<?php esc_html_e( 'Filters', 'nebulite' ); ?>
			</button>
			<label class="casino-list__sort-label" for="<?php echo esc_attr( $block_id ); ?>-sort"><?php esc_html_e( 'Sort by', 'nebulite' ); ?></label>
			
			<select id="<?php echo esc_attr( $block_id ); ?>-sort" class="casino-list__sort">				
				<option value="rating" <?php selected( $sort, 'rating' ); ?>><?php esc_html_e( 'Rating (desc)', 'nebulite' ); ?></option>
				<option value="newest" <?php selected( $sort, 'newest' ); ?>><?php esc_html_e( 'Newest', 'nebulite' ); ?></option>
			</select>
		</div>

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

	<!-- Filters Modal -->
	<div id="<?php echo esc_attr( $block_id ); ?>-filters-modal" class="casino-filters-modal" role="dialog" aria-modal="true" aria-labelledby="<?php echo esc_attr( $block_id ); ?>-filters-title" hidden>
		<div class="casino-filters-modal__overlay"></div>
		<div class="casino-filters-modal__content">
			<div class="casino-filters-modal__header">
				<h2 id="<?php echo esc_attr( $block_id ); ?>-filters-title" class="casino-filters-modal__title"><?php esc_html_e( 'Filters', 'nebulite' ); ?></h2>
				<button class="casino-filters-modal__close" type="button" aria-label="<?php esc_attr_e( 'Close filters', 'nebulite' ); ?>">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="casino-filters-modal__body">
				<!-- General Filters -->
				<div class="casino-filters-modal__section">
					<h3 class="casino-filters-modal__section-title"><?php esc_html_e( 'General Filters', 'nebulite' ); ?></h3>
					<div class="casino-filters-modal__checkboxes">
						<label class="casino-filters-modal__checkbox-label">
							<input type="checkbox" class="casino-filters-modal__checkbox" name="low_wagering" value="1">
							<span><?php esc_html_e( 'Low Wagering', 'nebulite' ); ?></span>
						</label>
						<label class="casino-filters-modal__checkbox-label">
							<input type="checkbox" class="casino-filters-modal__checkbox" name="low_deposit" value="1">
							<span><?php esc_html_e( 'Low Deposit', 'nebulite' ); ?></span>
						</label>
						<label class="casino-filters-modal__checkbox-label">
							<input type="checkbox" class="casino-filters-modal__checkbox" name="no_kyc" value="1">
							<span><?php esc_html_e( 'No KYC', 'nebulite' ); ?></span>
						</label>
						<label class="casino-filters-modal__checkbox-label">
							<input type="checkbox" class="casino-filters-modal__checkbox" name="chatroom" value="1">
							<span><?php esc_html_e( 'Chatroom', 'nebulite' ); ?></span>
						</label>
						<label class="casino-filters-modal__checkbox-label">
							<input type="checkbox" class="casino-filters-modal__checkbox" name="discord" value="1">
							<span><?php esc_html_e( 'Discord', 'nebulite' ); ?></span>
						</label>
					</div>
				</div>

				<!-- Games Filter -->
				<?php if ( ! empty( $all_games ) ) : ?>
					<div class="casino-filters-modal__section">
						<h3 class="casino-filters-modal__section-title"><?php esc_html_e( 'Games', 'nebulite' ); ?></h3>
						<div class="casino-filters-modal__checkboxes casino-filters-modal__checkboxes--scrollable">
							<?php foreach ( $all_games as $game ) : ?>
								<label class="casino-filters-modal__checkbox-label">
									<input type="checkbox" class="casino-filters-modal__checkbox" name="games[]" value="<?php echo esc_attr( $game->ID ); ?>">
									<span><?php echo esc_html( $game->post_title ); ?></span>
								</label>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endif; ?>

				<!-- Cryptocurrencies Filter -->
				<?php if ( ! empty( $all_cryptocurrencies ) ) : ?>
					<div class="casino-filters-modal__section">
						<h3 class="casino-filters-modal__section-title"><?php esc_html_e( 'Accepted Cryptocurrencies', 'nebulite' ); ?></h3>
						<div class="casino-filters-modal__checkboxes casino-filters-modal__checkboxes--scrollable">
							<?php foreach ( $all_cryptocurrencies as $cryptocurrency ) : ?>
								<label class="casino-filters-modal__checkbox-label">
									<input type="checkbox" class="casino-filters-modal__checkbox" name="cryptocurrencies[]" value="<?php echo esc_attr( $cryptocurrency->ID ); ?>">
									<span><?php echo esc_html( $cryptocurrency->post_title ); ?></span>
								</label>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endif; ?>
			</div>
			<div class="casino-filters-modal__footer">
				<button type="button" class="casino-filters-modal__btn casino-filters-modal__btn--secondary casino-filters-modal__clear">
					<?php esc_html_e( 'Clear All', 'nebulite' ); ?>
				</button>
				<button type="button" class="casino-filters-modal__btn casino-filters-modal__btn--primary casino-filters-modal__apply">
					<?php esc_html_e( 'Apply Filters', 'nebulite' ); ?>
				</button>
			</div>
		</div>
	</div>
</section>


