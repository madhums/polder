<?php
/*
        _               _                  _____        _     _ _     _   _   _
       (_)             | |                | ____|      | |   (_) |   | | | | | |
  _ __  _  ___ ___  ___| |_ _ __ __ _ _ __| |__     ___| |__  _| | __| | | |_| |__   ___ _ __ ___   ___
 | '_ \| |/ __/ _ \/ __| __| '__/ _` | '_ \___ \   / __| '_ \| | |/ _` | | __| '_ \ / _ \ '_ ` _ \ / _ \
 | |_) | | (_| (_) \__ \ |_| | | (_| | |_) |__) | | (__| | | | | | (_| | | |_| | | |  __/ | | | | |  __/
 | .__/|_|\___\___/|___/\__|_|  \__,_| .__/____/   \___|_| |_|_|_|\__,_|  \__|_| |_|\___|_| |_| |_|\___|
 | |                                 | |
 |_|                                 |_|


*************************************** WELCOME TO PICOSTRAP ***************************************

********************* THE BEST WAY TO EXPERIENCE SASS, BOOTSTRAP AND WORDPRESS *********************

    PLEASE WATCH THE VIDEOS FOR BEST RESULTS:
    https://www.youtube.com/playlist?list=PLtyHhWhkgYU8i11wu-5KJDBfA9C-D4Bfl

*/


// DE-ENQUEUE PARENT THEME BOOTSTRAP JS BUNDLE
add_action( 'wp_print_scripts', function(){
    wp_dequeue_script( 'bootstrap5' );
    //wp_dequeue_script( 'dark-mode-switch' );  //optionally
}, 100 );

// ENQUEUE THE BOOTSTRAP JS BUNDLE (AND EVENTUALLY MORE LIBS) FROM THE CHILD THEME DIRECTORY
add_action( 'wp_enqueue_scripts', function() {
    //enqueue js in footer, defer
    wp_enqueue_script( 'bootstrap5-childtheme', get_stylesheet_directory_uri() . "/js/bootstrap.bundle.min.js", array(), null, array('strategy' => 'defer', 'in_footer' => true)  );

    //optional: lottie (maybe...)
    //wp_enqueue_script( 'lottie-player', 'https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js', array(), null, array('strategy' => 'defer', 'in_footer' => true)  );

    //optional: rellax
    //wp_enqueue_script( 'rellax', 'https://cdnjs.cloudflare.com/ajax/libs/rellax/1.12.1/rellax.min.js', array(), null, array('strategy' => 'defer', 'in_footer' => true)  );

}, 101);



// ENQUEUE YOUR CUSTOM JS FILES, IF NEEDED
add_action( 'wp_enqueue_scripts', function() {

    //UNCOMMENT next row to include the js/custom.js file globally
    //wp_enqueue_script('custom', get_stylesheet_directory_uri() . '/js/custom.js', array(/* 'jquery' */), null, array('strategy' => 'defer', 'in_footer' => true) );

    //UNCOMMENT next 3 rows to load the js file only on one page
    //if (is_page('mypageslug')) {
    //    wp_enqueue_script('custom', get_stylesheet_directory_uri() . '/js/custom.js', array(/* 'jquery' */), null, array('strategy' => 'defer', 'in_footer' => true) );
    //}

}, 102);

// OPTIONAL: ADD MORE NAV MENUS
//register_nav_menus( array( 'third' => __( 'Third Menu', 'picostrap' ), 'fourth' => __( 'Fourth Menu', 'picostrap' ), 'fifth' => __( 'Fifth Menu', 'picostrap' ), ) );
// THEN USE SHORTCODE:  [lc_nav_menu theme_location="third" container_class="" container_id="" menu_class="navbar-nav"]


// CHECK PARENT THEME VERSION as Bootstrap 5.2 requires an updated SCSSphp, so picostrap5 v2 is required
add_action( 'admin_notices', function  () {
    if( (pico_get_parent_theme_version())>=3.0) return;
	$message = __( 'This Child Theme requires at least Picostrap Version 3.0.0  in order to work properly. Please update the parent theme.', 'picostrap' );
	printf( '<div class="%1$s"><h1>%2$s</h1></div>', esc_attr( 'notice notice-error' ), esc_html( $message ) );
} );

// FOR SECURITY: DISABLE APPLICATION PASSWORDS. Remove if needed (unlikely!)
add_filter( 'wp_is_application_passwords_available', '__return_false' );

// ADD YOUR CUSTOM PHP CODE DOWN BELOW /////////////////////////

function get_labels($label, $plural = null) {
    $singular_name = ucwords($label);
    $plural_name = $plural ?? $singular_name . 's';

    return array(
        'name'                  => _x( $plural_name, 'Post type general name', 'textdomain' ),
        'singular_name'         => _x( $singular_name, 'Post type singular name', 'textdomain' ),
        'menu_name'             => _x( ucwords($plural_name), 'Admin Menu text', 'textdomain' ),
        'name_admin_bar'        => _x( $singular_name, 'Add New on Toolbar', 'textdomain' ),
        'add_new'               => __( 'Add New', 'textdomain' ),
        'add_new_item'          => __( 'Add New ' . $singular_name, 'textdomain' ),
        'new_item'              => __( 'New ' . $singular_name, 'textdomain' ),
        'edit_item'             => __( 'Edit ' . $singular_name, 'textdomain' ),
        'view_item'             => __( 'View ' . $singular_name, 'textdomain' ),
        'all_items'             => __( 'All ' . $plural_name, 'textdomain' ),
        'search_items'          => __( 'Search ' . $plural_name, 'textdomain' ),
        'parent_item_colon'     => __( 'Parent ' . $plural_name . ':', 'textdomain' ),
        'not_found'             => __( 'No ' . $plural_name . ' found.', 'textdomain' ),
        'not_found_in_trash'    => __( 'No ' . $plural_name . ' found in Trash.', 'textdomain' ),
        'archives'              => _x( $singular_name . ' archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'textdomain' ),
        'insert_into_item'      => _x( 'Insert into ' . strtolower($singular_name), 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'textdomain' ),
        'uploaded_to_this_item' => _x( 'Uploaded to this ' . strtolower($singular_name), 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'textdomain' ),
        'filter_items_list'     => _x( 'Filter ' . strtolower($plural_name) . ' list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'textdomain' ),
        'items_list_navigation' => _x( $plural_name . ' list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'textdomain' ),
        'items_list'            => _x( $plural_name . ' list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'textdomain' ),
    );
}

function setup_post_type($type_name, $icon, $plural = null) {
    $args = array(
        'labels'        => get_labels($type_name, $plural),
        'public'        => true,
        'label'         => __( ucwords($plural ?? $type_name . 's'), 'textdomain' ),
        'menu_icon'     => 'dashicons-' . $icon,
        'show_in_rest'  => true,
        'menu_position' => 5,
    );

    register_post_type( $type_name, $args );
    register_taxonomy_for_object_type( 'category', $type_name );
    register_taxonomy_for_object_type( 'post_tag', $type_name );
    add_post_type_support( $type_name, 'thumbnail' );
    add_post_type_support( $type_name, 'excerpt' );
}


/**
 * Lab content type
 */

function lab_setup_post_type() {
    setup_post_type('lab', 'color-picker');
}
add_action( 'init', 'lab_setup_post_type' );


/**
 * News content type
 */

function news_setup_post_type() {
    setup_post_type('news', 'megaphone', 'news');
}
add_action( 'init', 'news_setup_post_type' );


/**
 * Event content type
 */

function event_setup_post_type() {
    setup_post_type('event', 'calendar');
}
add_action( 'init', 'event_setup_post_type' );


/**
 * Publication content type
 */

function publication_setup_post_type() {
    setup_post_type('publication', 'text-page');
}
add_action( 'init', 'publication_setup_post_type' );

// enable excerpts for pages
function add_page_excerpt_support() {
    add_post_type_support( 'page', 'excerpt' );
}
add_action( 'init', 'add_page_excerpt_support' );


// customise read more link for excerpts
function picostrap_all_excerpts_get_more_link( $post_excerpt ) {
    if ( ! is_admin() OR ( isset($_POST['action']) && $_POST['action'] == 'lc_process_dynamic_templating_shortcode') ) {
        $post_excerpt = $post_excerpt . '...<p class="text-start"><a class="btn btn-outline-secondary picostrap-read-more-link mt-3" href="' . esc_url( get_permalink( get_the_ID() ) ) . '">' . __(
            'Read More...',
            'picostrap5'
        ) . '</a></p>';
    }
    return $post_excerpt;
}

// use widgets for cta
function add_cta_widget() {
    register_sidebar(
        array(
            'name'          => __( 'Call To Action', 'picostrap5' ),
            'id'            => 'cta',
            'description'   => __( 'Call to action for all pages', 'picostrap5' ),
            'before_widget' => '<div id="%1$s" class="cta-widget %2$s dynamic-classes">',
            'after_widget'  => '</div><!-- .cta-widget -->',
            'before_title'  => '<h3 class="widget-title">',
            'after_title'   => '</h3>',
        )
    );
}
add_action( 'widgets_init', 'add_cta_widget' );
