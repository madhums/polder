<?php if ( is_active_sidebar( 'cta' ) ) : ?>
  <div id="cta" class="border-top text-bg-primary">
    <div class="py-5 container">
        <div class="col-lg-8 offset-2 text-center my-5">
          <?php dynamic_sidebar( 'cta' ); ?>
        </div>
    </div>
  </div>
<?php endif; ?>
