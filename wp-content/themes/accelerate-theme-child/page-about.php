<?php

/**
*Template Name: About page
*Template Post Type: About Services
*
*This is the template that displays about pages by default. 
**/

get_header(); ?>

<div id="primary" class="hero-content about">
        <div class="main-content" role="main">
            <p class="about-image">Accerlerate is a strategy and marketing agency located in the heart of NYC. Our goal is to build businesses by making our clients visible and making their customers smile.</p>
            <?php while ( have_posts() ) : the_post(); ?>
				<?php the_content(); ?>
			<?php endwhile; // end of the loop. ?>
        </div>
</div>
<!--services --> 
<div class="our-services">
<h4>Our Services</h4>
            <p id="our-services"> We take pride in our clients and the content we create for them. Here's a brief overview of our offered services. </p>
                <?php while ( have_posts() ) : the_post(); 
                    $subheading = get_field("subheading");
                    $text = get_field("text");
                    $icon = get_field("service_icon");
                    $size = "full";
                ?>
                     <h4><?php echo $subheading ?></h4>
			        <p><?php echo $text ?></p>
				<?php endwhile; //end of the loop ?>         
</div>

<section class="featured-work">
		<div class="site-content">

			
			<ul class="homepage-featured-work">
			<?php query_posts('posts_per_page=4&post_type=services'); ?>
					<?php while ( have_posts() ) : the_post();
					$icon = get_field("service_icon");
                    $size = "small";
                    $text = get_field("text");
			?>
           
			<li class="individual-featured-work">
				<figure>
				<?php echo wp_get_attachment_image($icon, $size); ?>
				</figure>
					<h3><a href="<?php the_permalink(); ?>" class="read-more=link">
					<?php the_title(); ?></a></h3>
			</li>
				<?php endwhile; ?>
			<?php wp_reset_query(); ?>
			</ul>
		</div>
	</section>
<?php get_footer(); ?>