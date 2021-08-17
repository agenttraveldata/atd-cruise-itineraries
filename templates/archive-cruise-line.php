<?php
/**
 * The template for displaying Cruise Line archive page
 */

get_header();

?>

<h1>Our Cruise Lines</h1>

<?php if ( have_posts() ) : ?>

    <div class="atd-cfi-ar atd-cfi-ar__<?php echo get_post_type(); ?>">
		<?php while ( have_posts() ) : the_post(); ?>
			<?php atd_cf_get_template_part( 'content/content', 'boxed' ); ?>
		<?php endwhile; ?>
    </div>

	<?php the_posts_pagination(); ?>

<?php else : ?>
	<?php get_template_part( 'content/content', 'none' ); ?>
<?php endif; ?>

<?php get_footer(); ?>
