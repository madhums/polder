<div class="py-5 py-xl-6 text-bg-primary-25 text-dark-emphasis">
    <div class="container">
      <h1 class="display-4 text-center fw-medium"><?php the_title(); ?></h1>
      <div class="row justify-content-center">
        <div class="col-md-8">
        <p class="has-medium-font-size">
          <?php echo get_post_field('post_excerpt', get_the_ID()); ?>
        </p>
        </div>
      </div>
  </div>
</div>
