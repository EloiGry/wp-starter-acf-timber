<?php


// Load Composer dependencies.
require_once __DIR__ . '/vendor/autoload.php';

// Initialize Timber.
use Timber\Timber;
Timber::init();

// CSS

function my_theme_enqueue_styles() {
    wp_enqueue_style('main-style', get_stylesheet_uri());
    wp_enqueue_style('tailwind-output', get_template_directory_uri() . '/src/output.css');
}
add_action('wp_enqueue_scripts', 'my_theme_enqueue_styles');


// Theme setup/support
function my_theme_setup() {
    // Ajouter la prise en charge du <title>
    add_theme_support('title-tag');

    add_theme_support( 'custom-logo' );

    add_theme_support( 'post-thumbnails' );

    add_theme_support('woocommerce'); // Active WooCommerce

    // Ajouter les menus
    register_nav_menus([
        'main-menu' => __('Menu Principal', 'my-theme'),
        'footer-menu' => __('Menu Footer', 'my-theme'),
    ]);
}
add_action('after_setup_theme', 'my_theme_setup');

// Add menus to context

function add_to_context($context) {
    $context['menu'] = Timber::get_menu('main-menu');
    $context['footer'] = Timber::get_menu('footer-menu');
    return $context;
}
add_filter('timber/context', 'add_to_context');


// Remove WYSIWYG editor for pages
function disable_wysiwyg_for_pages() {
    if (is_admin() && isset($_GET['post'])) {
        $post_id = $_GET['post'];
        $post = get_post($post_id);
        if ($post && $post->post_type === 'page') {
            remove_post_type_support('page', 'editor');
        }
    }
}
add_action('admin_head', 'disable_wysiwyg_for_pages');

// Custom cart
add_filter('template_include', function ($template) {
    if (is_cart()) {
        return get_template_directory() . '/woocommerce/cart.php';
    }
    return $template;
});

function theme_enqueue_scripts() {
    // Enqueue le script cart.js
    wp_enqueue_script('theme-cart-js', get_template_directory_uri() . '/src/js/cart.js', array(), '1.0', false);

    // Ajouter le nonce à wpApiSettings et le rendre accessible dans le script JavaScript
    wp_localize_script('theme-cart-js', 'wpApiSettings', array(
        'nonce' => wp_create_nonce('wc_store_api')
    ));
}
add_action('wp_enqueue_scripts', 'theme_enqueue_scripts');


function my_acf_admin_head() {
    ?>
    <style type="text/css">
    
        .acf-flexible-content .layout .acf-fc-layout-handle {
            /*background-color: #00B8E4;*/
            background-color: #202428;
            color: #eee;
        }
    
        .acf-repeater.-row > table > tbody > tr > td,
        .acf-repeater.-block > table > tbody > tr > td {
            border-top: 2px solid #202428;
        }
    
        .acf-repeater .acf-row-handle {
            vertical-align: top !important;
            padding-top: 16px;
        }
    
        .acf-repeater .acf-row-handle span {
            font-size: 20px;
            font-weight: bold;
            color: #202428;
        }
    
        .imageUpload img {
            width: 75px;
        }
    
        .acf-repeater .acf-row-handle .acf-icon.-minus {
            top: 30px;
        }
    
    </style>
    <?php
    }
    
    add_action('acf/input/admin_head', 'my_acf_admin_head');

    function disable_jquery_migrate( $scripts ) {
        if ( !is_admin() && isset( $scripts->registered['jquery'] ) ) {
          $scripts->registered['jquery']->deps = array_diff(
            $scripts->registered['jquery']->deps,
            ['jquery-migrate']
          );
        }
      }
      add_action( 'wp_default_scripts', 'disable_jquery_migrate' );