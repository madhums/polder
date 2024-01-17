<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

get_header();
?>

<?php include get_stylesheet_directory() . '/partials/page-header.php'; ?>

<?php
$upcoming_args = array(
    'post_type' => 'event',  // Replace 'event' with your actual post type
    'posts_per_page' => -1, // Display all posts of the 'event' type
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
?>

<div class="py-5 border-top">
    <div class="container py-4">
        <?php the_content(); ?>
    </div>
</div>

<?php if ( $upcoming->have_posts() ) : ?>
    <div class="py-5 border-top">
        <div id="container-content-page" class="container py-4">
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12">
                            <h2><?php _e('Upcoming Events', 'picostrap5') ?></h2>
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
