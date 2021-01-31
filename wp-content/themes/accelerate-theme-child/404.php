<?php
/**
 * The template for displaying the 404 page
 *
 * @package WordPress
 * @subpackage Accelerate Marketing
 * @since Accelerate Marketing 2.0
 */

get_header(); ?>

	<div id="primary" class="site-content sidebar">
		<div class="main-content" role="main">
			<?php while ( have_posts() ) : the_post(); ?>
				<h2><?php the_title(); ?></h2>
                <?php the_content(); ?> 
			<?php endwhile; // end of the loop. ?>
		</div><!-- .main-content -->
	</div><!-- #primary -->
    <div class="error">
        <h1>RECALCULATING!</h1>
        <figure>
            <img src="http://localhost/accelerate/wp-content/uploads/2021/01/rv.jpg">
        </figure>
        <p>It's always awkward when your GPS leads you down the wrong path.
            <br>
            Back to our<a href="http://localhost/accelerate">Blog</a></p>
        <br>
        <br>

<?php get_footer(); ?>
