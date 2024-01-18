<?php if ( is_active_sidebar( 'cta' ) && !(is_page( 'contact-us' ) || is_page( 'collaborate' )) ) : ?>
  <div id="cta" class="text-bg-primary">
    <div class="py-5 container">
        <div class="col-lg-8 offset-2 text-center my-5 py-4">
          <?php dynamic_sidebar( 'cta' ); ?>
        </div>
    </div>
  </div>
<?php elseif ( is_active_sidebar( 'cta_contact' ) && is_page( 'collaborate' ) ) : ?>
  <div id="cta-contact" class="text-bg-primary">
    <div class="py-5 container">
        <div class="col-lg-8 offset-2 text-center my-5 py-4">
          <?php dynamic_sidebar( 'cta_contact' ); ?>
        </div>
    </div>
  </div>
<?php endif; ?>
