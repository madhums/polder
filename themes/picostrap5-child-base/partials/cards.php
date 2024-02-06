<?php
/*
This loop is used in the Archive and in the Home [.php] templates.
*/

$is_external_link = false;
$link_attrs = '';
$link_icon = '';
if (get_post_type(get_the_ID()) == 'publication' && get_field('publication_url')) {
  $card_link = get_field('publication_url');
  $is_external_link = true;
  $link_attrs = 'target="_blank"';
  $link_icon = '<i class="ms-2 bi bi-box-arrow-up-right"></i>';
} else {
  $card_link = get_the_permalink();
}

?>




<div class="col-md-6 col-lg-4 col-xl-4 my-5">
  <div class="card border-0 bg-transparent">
    <a href="<?php echo $card_link; ?>" <?php echo $link_attrs; ?>>
      <?php the_post_thumbnail('card_thumb', ['class' => 'w-100 rounded-top']);    ?>
    </a>
    <div class="card-body px-0">
        <?php if (get_post_type(get_the_ID()) == 'event' ): ?>
          <div class="text-muted mb-2"><?php the_field('start_date'); ?> <?php the_field('start_time'); ?> - <?php the_field('location'); ?></div>
        <?php elseif (!get_theme_mod("singlepost_disable_date") && get_post_type(get_the_ID()) != 'lab' ): ?>
          <small class="text-muted"><?php the_date() ?></small>
        <?php endif; ?>

        <h4 class="mb-2"><a href="<?php echo $card_link; ?>"  <?php echo $link_attrs; ?> class="text-reset text-decoration-none"><?php the_title() ?></a></h4>
        <p class="card-text"><?php echo get_the_excerpt(); ?></p>

        <a href="<?php echo $card_link; ?>" class="btn btn-outline-primary" <?php echo $link_attrs; ?>>Read more <?php echo $link_icon; ?></a>
    </div>
  </div>

</div>
