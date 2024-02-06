<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

get_header();
?>


<?php
$upcoming_args = array(
    'post_type' => 'event',  // Replace 'event' with your actual post type
    'posts_per_page' => 3, // Display all posts of the 'event' type
    'meta_key' => 'start_date',
    'orderby' => 'meta_value',
    'order' => 'ASC',
    'meta_query' => array(
        array(
            'key' => 'start_date',
            'value' => date('Ymd'),
            'compare' => '>=',
        )
    )
);
$upcoming = new WP_Query($upcoming_args);

$publication_query = array(
    'post_type' => 'publication',  // Replace 'publication' with your actual post type
    'posts_per_page' => 3, // Display all posts of the 'publication' type
);

$publications = new WP_Query($publication_query);

?>

<?php the_content(); ?>

<?php if ( $publications->have_posts() ) : ?>
    <div class="py-5 text-bg-primary-25">
        <div id="container-content-page" class="container py-4">
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12">
                            <h1><?php _e('Recent Publications', 'picostrap5') ?></h1>
                        </div>
                        <?php
                            while ( $publications->have_posts() ) : $publications->the_post();
                                get_template_part('partials/cards');
                            endwhile; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>


<?php if ( $upcoming->have_posts() ) : ?>
    <div class="py-5">
        <div id="container-content-page" class="container py-4">
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12">
                            <h1><?php _e('Upcoming Events', 'picostrap5') ?></h1>
                            <p><?php _e('Upcoming events from Polder, IAS and the surrounding ecosystem', 'picostrap5') ?></p>
                        </div>
                        <?php
                            while ( $upcoming->have_posts() ) : $upcoming->the_post();
                                get_template_part('partials/cards');
                            endwhile; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>



<?php get_footer();
