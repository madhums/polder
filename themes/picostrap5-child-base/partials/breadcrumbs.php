<!-- Display breadcrumbs -->
<?php if(function_exists('bcn_display_list')):?>
  <div class="mb-4">
  <nav aria-label="breadcrumb" typeof="BreadcrumbList" vocab="https://schema.org/" aria-label="breadcrumb">
      <ol class="breadcrumb">
          <?php bcn_display_list();?>
      </ol>
  </nav>
  </div>
<?php endif; ?>
