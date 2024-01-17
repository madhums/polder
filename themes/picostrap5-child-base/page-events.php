<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

get_header();
?>

<?php include get_stylesheet_directory() . '/partials/page-header.php'; ?>

<?php
$args = array(
    'post_type' => 'event',  // Replace 'event' with your actual post type
    'posts_per_page' => -1, // Display all posts of the 'event' type
);

$event_posts = new WP_Query($args);
?>
<div id="container-content-page" class="container">
    <div class="row my-5">
        <?php

        if ( $event_posts->have_posts() ) :
            while ( $event_posts->have_posts() ) : $event_posts->the_post();
                get_template_part('partials/cards');
            endwhile;
        else :
            _e( 'Sorry, no posts matched your criteria.', 'textdomain' );
        endif;
        ?>
    </div>
</div>


<?php get_footer();
