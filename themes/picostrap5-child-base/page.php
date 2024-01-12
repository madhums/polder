<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

get_header();
?>
<div class="py-5 py-xl-6 bg-body-tertiary text-dark-emphasis">
    <div class="container">
        <h1 class="display-4 text-center"><?php the_title(); ?></h1>
        <div class="row justify-content-center">
          <div class="col-md-8">
          <p>
            <?php echo get_post_field('post_excerpt', get_the_ID()); ?>
          </p>
          </div>
        </div>
  </div>
</div>

<div id="container-content-page" class="container">
    <div class="row">
        <div class="col-md-12 py-5">
            <?php

            if ( have_posts() ) :
                while ( have_posts() ) : the_post();
                    the_content();
                endwhile;
            else :
                _e( 'Sorry, no posts matched your criteria.', 'textdomain' );
            endif;
            ?>
        </div>
    </div>
</div>


<?php get_footer();
