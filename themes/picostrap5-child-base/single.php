<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

get_header();




if ( have_posts() ) :
    while ( have_posts() ) : the_post();

    if (get_the_post_thumbnail_url()){
        ?><div class="d-flex container-fluid" style="height:50vh;background:url(<?php echo get_the_post_thumbnail_url(); ?>)  center / cover no-repeat;"></div>
    <?php } else {
        ?><div class="d-flex container-fluid" style="height:20vh;"></div>
    <?php } ?>

    <div id="container-content-single" class="container p-5 bg-body rounded" style="margin-top:-150px">
        <div class="row">

            <div class="col-md-10 offset-md-1">
                <!-- Display breadcrumbs -->
                <?php include get_stylesheet_directory() . '/partials/breadcrumbs.php'; ?>

                <h1 class="display-4 fw-medium"><?php the_title(); ?></h1>

                <?php if (!get_theme_mod("singlepost_disable_date") OR !get_theme_mod("singlepost_disable_author")  ): ?>
                    <div class="post-meta" id="single-post-meta">
                        <p class="lead text-secondary">

                            <?php if (!get_theme_mod("singlepost_disable_date") ): ?>
                                <span class="post-date fs-6"><?php the_date(); ?> </span>
                            <?php endif; ?>

                            <?php if (!get_theme_mod("singlepost_disable_author") ): ?>
                                <span class="text-secondary post-author"> <?php _e( 'by', 'picostrap5' ) ?> <?php the_author(); ?></span>
                            <?php endif; ?>
                        </p>
                    </div>
                <?php endif; ?>

            </div><!-- /col -->
        </div>
        <div class="row mb-5 pb-4">
            <div class="col-md-10 offset-md-1">
                <?php if (get_post_type(get_the_ID()) == 'event' ): ?>
                    <div class="fs-5">
                        <div class="my-2">
                            <strong><?php _e('Event date', 'picostrap5') ?></strong>: <?php echo get_field('start_date'); ?> <?php echo get_field('start_time'); ?>
                            <?php if (get_field('end_date')): ?>
                                - <?php echo get_field('end_date'); ?> <?php echo get_field('end_time'); ?>
                            <?php endif; ?>
                        </div>
                        <div class="mt-2 mb-4">
                            <strong><?php _e('Location', 'picostrap5') ?></strong>: <?php echo get_field('location'); ?>
                        </div>
                    </div>
                <?php endif; ?>
                <?php

                the_content();

                if( get_theme_mod("enable_sharing_buttons")) picostrap_the_sharing_buttons();

                edit_post_link( __( 'Edit this post', 'picostrap5' ), '<p class="text-end">', '</p>' );

                // If comments are open or we have at least one comment, load up the comment template.
                if (!get_theme_mod("singlepost_disable_comments")) if ( comments_open() || get_comments_number() ) {
                    comments_template();
                }

                ?>

            </div><!-- /col -->
        </div>
    </div>

<?php
    endwhile;
 else :
     _e( 'Sorry, no posts matched your criteria.', 'picostrap5' );
 endif;
 ?>




<?php get_footer();
