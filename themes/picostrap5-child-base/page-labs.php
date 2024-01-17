<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

get_header();
?>

<?php include get_stylesheet_directory() . '/partials/page-header.php'; ?>

<?php
$args = array(
    'post_type' => 'lab',  // Replace 'lab' with your actual post type
    'posts_per_page' => -1, // Display all posts of the 'lab' type
);

$lab_posts = new WP_Query($args);
?>
<div id="container-content-page" class="container">
    <?php the_content(); ?>
    <div class="row my-5">
        <?php

        if ( $lab_posts->have_posts() ) :
            while ( $lab_posts->have_posts() ) : $lab_posts->the_post();
                get_template_part('partials/cards');
            endwhile;
        else :
            ?> <div class="col-md-12 text-muted py-3"><?php _e( 'There are no labs', 'textdomain' ); ?> </div> <?php
        endif;
        ?>
    </div>
</div>


<?php get_footer();
