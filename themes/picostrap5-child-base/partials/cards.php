<?php
/*
This loop is used in the Archive and in the Home [.php] templates.
*/
?>

<div class="col-md-6 col-lg-4 col-xl-4 my-5">


  <div class="card border-0">
    <a href="<?php the_permalink() ?>">
      <?php the_post_thumbnail('medium', ['class' => 'w-100 rounded-top']);    ?>
    </a>
    <div class="card-body px-0">
        <?php if (get_post_type(get_the_ID()) == 'event' ): ?>
          <div class="text-muted mb-2"><?php the_field('start_date'); ?> <?php the_field('start_time'); ?> - <?php the_field('location'); ?></div>
        <?php elseif (!get_theme_mod("singlepost_disable_date") && get_post_type(get_the_ID()) != 'lab' ): ?>
          <small class="text-muted"><?php the_date() ?></small>
        <?php endif; ?>

        <h4 class="mb-2"><a href="<?php the_permalink() ?>" class="text-reset text-decoration-none"><?php the_title() ?></a></h4>
        <p class="card-text"><?php echo get_the_excerpt(); ?></p>
    </div>
  </div>

</div>
