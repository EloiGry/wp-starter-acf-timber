<?php


// Load Composer dependencies.
require_once __DIR__ . '/vendor/autoload.php';

// Initialize Timber.
use Timber\Timber;
use Timber\Image;
Timber::init();




function add_to_context($context) {
    $context['menu'] = Timber::get_menu('main-menu');
    return $context;
}
add_filter('timber/context', 'add_to_context');

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

    // Ajouter les menus
    register_nav_menus([
        'main-menu' => __('Menu Principal', 'my-theme'),
    ]);
}
add_action('after_setup_theme', 'my_theme_setup');

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

