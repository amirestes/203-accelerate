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
            <?php endwhile; ?>
        </div>
    </div>
    <!-- services -->
    <section class="about-services">
        <div class="about-subheading"
            <?php while ( have_posts() ) : the_post();
                $subheading = get_field('subheading');
                $description = get_field('description');
                $service_icon = get_field('service_icon');
                $size = "small" 
            ?>
                 <div class="services">
                    <h3>Our Services</h3>
                    <p> We take pride in our clients and the content we create for them. Here's a brief overview of our offered services.</p>
                </div>
        </div>
         <div class="about_services">
                <h3><?php echo $subheading; ?></h3>
                <p><?php echo $desccription; ?></p>
                <img src="<?php echo $service_icon; ?>" />
                
                <?php the_content(); ?>
            </div>
        <?php endwhile; //end of the loop. ?>
   </section>
<?php get_footer(); ?>