<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

get_header();
?>

<?php include get_stylesheet_directory() . '/partials/page-header.php'; ?>

<?php
$args = array(
    'post_type' => 'news',  // Replace 'news' with your actual post type
    'posts_per_page' => 15, // Display all posts of the 'news' type
    'paged' => get_query_var('paged') ? get_query_var('paged') : 1,
);

$news_posts = new WP_Query($args);
?>
<div id="container-content-page" class="container">
    <?php the_content(); ?>
    <div class="row my-5">
        <?php

        if ( $news_posts->have_posts() ) :
            while ( $news_posts->have_posts() ) : $news_posts->the_post();
                get_template_part('partials/cards');
            endwhile;
        else :
            ?> <div class="col-md-12 text-muted py-3"><?php _e( 'There are no news items.', 'textdomain' ); ?> </div> <?php
        endif;
        ?>
    </div>
    <div class="my-5">
        <?php paginate($news_posts->max_num_pages); ?>
    </div>
</div>


<?php get_footer();
