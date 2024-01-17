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
        <?php if (!get_theme_mod("singlepost_disable_date") && get_post_type(get_the_ID()) != 'lab' ): ?>
          <small class="text-muted"><?php the_date() ?></small>
        <?php endif; ?>

        <h2><?php the_title() ?></h2>
        <p class="card-text"><?php echo get_the_excerpt(); ?></p>
    </div>
  </div>

</div>
