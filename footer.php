<?php
/**
 * The template for displaying the footer
 *
 * @package Nebulite
 */

?>

<footer id="colophon" class="site-footer">
	<div class="container">
		<div class="site-footer__content">
			<div class="site-footer__branding">
				<?php
				if ( has_custom_logo() ) {
					the_custom_logo();
				}
				?>
			</div><!-- .site-footer__branding -->

			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'footer-menu',
					'menu_id'        => 'footer-menu',
					'menu_class'     => 'footer-menu',
					'container'      => false,
					'fallback_cb'    => false,
					'depth'          => 1,
				)
			);
			?>

			<div class="site-footer__copyright">
				<p>&copy; <?php echo esc_html( date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'All rights reserved.', 'nebulite' ); ?></p>
			</div><!-- .site-footer__copyright -->
		</div><!-- .site-footer__content -->
	</div><!-- .container -->
</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
