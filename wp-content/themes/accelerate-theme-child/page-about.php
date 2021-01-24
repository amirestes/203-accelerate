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
            <?php while ( have_posts() ) : the_post(); 
            $subheading = get_field('subheading');
            $description = get_field('description');
            $icon = get_field('$service_icon');
            ?>
            
    <article class="services">
        <h3><?php echo $subheading; ?></h3>
        <p><?php echo $description; ?></p>

                <?php the_content(); ?>

                </article>
<?php endwhile; ?>
            
        </div>
    </div>

<!--SERVICES -->
<?php get_footer(); ?>