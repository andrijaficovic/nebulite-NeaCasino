<?php
/**
 * The template for displaying all pages
 *
 * @package Nebulite
 */

get_header();
?>

<main id="primary" class="site-main">
	<?php
	while ( have_posts() ) :
		the_post();
		?>
		<div class="entry-content">
			<?php the_content(); ?>
		</div>
		<?php
	endwhile;
	?>
</main><!-- #main -->

<?php
get_footer();
