<?php

/**
*Template Name: About page
*Template Post Type: About Services
*
*This is the template that displays about pages by default. 
**/

get_header(); ?>

<div id="primary" class="hero-content about">
        <div class="about-content" role="main">
            <p class="about-image">Accerlerate is a strategy and marketing agency located in the heart of NYC. Our goal is to build businesses by making our clients visible and making their customers smile.</p>
            <?php while ( have_posts() ) : the_post(); ?>
				<?php the_content(); ?>
			<?php endwhile; // end of the loop. ?>
        </div>
</div>
<!--services -->

<section class="about-content">
	<div class="our-services">
	<h4>Our Services</h4>
			<p id="our-services"> We take pride in our clients and the content we create for them. Here's a brief overview of our offered services. </p>

	<section class="featured-work">
		<div class="site-content">
			<ul class="homepage-featured-work">
				<?php query_posts('posts_per_page=4&post_type=services'); ?>
					<?php while ( have_posts() ) : the_post();
					$icon = get_field("service_icon");
                    $size = "large";
					$text = get_field("text");
					$subheading = get_field("subheading");
				?>
           
			<li class="about-services">
					<figure class="icon"><?php echo  wp_get_attachment_image($icon, $size) ?></figure>
					<div class="text">
						<h3 class="subheading"><?php echo $subheading ?></h3>
						<p class="text"><?php echo $text ?></p>
					</div>
			</li>
				<?php endwhile; ?>
			<?php wp_reset_query(); ?>

			</ul>
		</div>
	</section>
	<section class="about-cta">
			<div class="contact-us-button">
		    	<h2>Interested in working with us?</h2>
			  	<a class="button" href="<?php echo site_url('/contact-us/') ?>">Contact Us</a>
			</div>
</section>
</div>
</section>
<?php get_footer(); ?>