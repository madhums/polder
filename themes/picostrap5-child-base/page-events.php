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

$past_args = array(
    'post_type' => 'event',
    'posts_per_page' => 15,
    'paged' => get_query_var('paged') ? get_query_var('paged') : 1,
    'meta_key' => 'start_date',
    'orderby' => 'meta_value',
    'order' => 'ASC',
    'meta_query' => array(
        array(
            'key' => 'start_date',
            'value' => date('Ymd'),
            'compare' => '<',
        )
    )
);

$past = new WP_Query($past_args);


?>
<div id="container-content-page" class="container">
    <?php the_content(); ?>
    <div class="row my-5">
        <div class="col-md-12">
            <h2><?php _e('Upcoming Events', 'picostrap5') ?></h2>
        </div>
        <?php

        if ( $upcoming->have_posts() ) :
            while ( $upcoming->have_posts() ) : $upcoming->the_post();
                get_template_part('partials/cards');
            endwhile;
        else :
            ?> <div class="col-md-12 text-muted py-3"><?php _e( 'There are no upcoming events.', 'textdomain' ); ?> </div> <?php
        endif;
        ?>
    </div>

    <div class="row my-5">
        <div class="col-md-12">
            <h2><?php _e('Past Events', 'picostrap5') ?></h2>
        </div>
        <?php

        if ( $past->have_posts() ) :
            while ( $past->have_posts() ) : $past->the_post();
                get_template_part('partials/cards');
            endwhile;
        else :
            ?> <div class="col-md-12 text-muted py-3"><?php _e( 'There are no past events', 'textdomain' ); ?> </div> <?php
        endif;
        ?>
    </div>

    <div class="my-5">
        <?php paginate($past->max_num_pages); ?>
    </div>
</div>


<?php get_footer();
