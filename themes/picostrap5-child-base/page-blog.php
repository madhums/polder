<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

get_header();
?>

<?php include get_stylesheet_directory() . '/partials/page-header.php'; ?>

<?php
$args = array(
    'post_type' => 'post',  // Replace 'post' with your actual post type
    'posts_per_page' => 15, // Display all posts of the 'post' type
    'paged' => get_query_var('paged') ? get_query_var('paged') : 1
);

$posts = new WP_Query($args);
?>
<div id="container-content-page" class="container">

    <?php the_content(); ?>

    <div class="row my-5">
        <?php

        if ( $posts->have_posts() ) :
            while ( $posts->have_posts() ) : $posts->the_post();
                get_template_part('partials/cards');
            endwhile;
        else :
            ?> <div class="col-md-12 text-muted py-3"><?php _e( 'There are no blog posts.', 'textdomain' ); ?> </div> <?php
        endif;
        ?>
    </div>

    <div class="my-5">
        <?php paginate($posts->max_num_pages); ?>
    </div>
</div>


<?php get_footer();
