<?php get_template_part('partials/cta'); ?>

<?php if (is_page('contact-us')) : ?>
    <div class="border-top"></div>
<?php endif; ?>

<!-- @todo add acf option to give a classname -->
<footer>
    <?php

    if (is_active_sidebar( 'footerfull' )): ?>
        <div class="wrapper py-4" id="wrapper-footer-widgets">

            <div class="container my-5 pt-4">

                <div class="row">
                    <?php dynamic_sidebar( 'footerfull' ); ?>
                </div>

            </div>
        </div>
    <?php endif ?>


    <div class="wrapper py-3 copyright" id="wrapper-footer-colophon">
        <div class="container-fluid">

            <div class="row">

                <div class="col text-center">

                    <footer class="site-footer" id="colophon">

                        <div class="site-info">

                            <?php picostrap_site_info(); ?>

                        </div><!-- .site-info -->

                    </footer><!-- #colophon -->

                </div><!--col end -->

            </div><!-- row end -->

        </div><!-- container end -->

    </div><!-- wrapper end -->

</footer>
