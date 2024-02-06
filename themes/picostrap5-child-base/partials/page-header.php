<?php
  $container_classes = '';
  $has_cover = get_the_post_thumbnail_url();
  if ($has_cover) {
    $container_classes = 'text-bg-dark';
  } else {
    $container_classes = 'text-bg-primary-25 text-dark-emphasis';
  }
?>


<div class="py-5 py-xl-6 position-relative <?php echo $container_classes; ?>" style="background:url(<?php echo get_the_post_thumbnail_url(); ?>)  center / cover no-repeat; z-index: 1;">
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
  <?php if ($has_cover): ?>
    <div class="polder-backdrop"></div>
  <?php endif; ?>
</div>
