<?php
/**
 * The template for displaying 404 pages (Not Found)
 *
 * @package Nebulite
 */

get_header();
?>

<main id="primary" class="site-main">
	<section class="error-404 not-found">
		<span class="error-404__glow error-404__glow--top" aria-hidden="true"></span>
		<span class="error-404__glow error-404__glow--bottom" aria-hidden="true"></span>

		<div class="error-404__inner">
			<p class="error-404__status" aria-hidden="true">404</p>

			<h1 class="error-404__title">
				<?php esc_html_e( 'Out of luck this time?', 'nebulite' ); ?>
			</h1>

			<p class="error-404__subtitle">
				<?php esc_html_e( 'The page you’re after just cashed out. Spin your way back to the homepage and try for another win!' ); ?>
			</p>
			

			<div class="error-404__actions">
				<a class="error-404__button error-404__button--primary" href="<?php echo esc_url( home_url(  ) ); ?>">
					<?php esc_html_e( 'Explore Casinos', 'nebulite' ); ?>
				</a>				
			</div>
		</div>
	</section><!-- .error-404 -->
</main><!-- #primary -->

<?php
get_footer();
