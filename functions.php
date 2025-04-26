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

// JS
function votre_theme_enqueue_scripts() {
    // Enqueue votre script JavaScript buildé
    wp_enqueue_script(
        'votre-theme-scripts', // Un handle unique pour votre script
        get_template_directory_uri() . '/build/js/bundle.js', // L'URL de votre fichier buildé
        array(), // Dépendances (laissez vide si votre script n'a pas de dépendances spécifiques à WordPress comme jQuery)
        filemtime(get_template_directory() . '/build/js/bundle.js'), // Versioning basé sur la date de modification du fichier (pour le cache busting)
        true // Charge le script dans le pied de page (meilleure pratique pour la performance)
    );
}

add_action('wp_enqueue_scripts', 'votre_theme_enqueue_scripts');
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

    function desactiver_jquery_frontend() {
        if ( ! is_admin() ) { // S'assurer que cela n'affecte que le frontend
            wp_deregister_script('jquery');
            wp_dequeue_script('jquery');
        }
    }
    add_action( 'wp_enqueue_scripts', 'desactiver_jquery_frontend' );

    // Fonction pour trouver et inclure tous les fichiers *-render.php
function include_all_component_render_files() {
    $components_base_dir = get_template_directory() . '/views/ui/';
    if ( ! is_dir( $components_base_dir ) ) {
        return;
    }

    // Utilise un itérateur récursif pour chercher dans les sous-dossiers
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator( $components_base_dir, RecursiveDirectoryIterator::SKIP_DOTS ), // SKIP_DOTS pour ignorer '.' et '..'
        RecursiveIteratorIterator::LEAVES_ONLY // Ne traiter que les fichiers
    );

    foreach ( $iterator as $file ) {
        // Vérifie que c'est un fichier PHP et qu'il finit par '-render.php'
        if ( $file->isFile() && $file->getExtension() === 'php' && str_ends_with( $file->getFilename(), '-render.php' ) ) {
            require_once $file->getRealPath();
        }
    }
}

// Inclure tous les fichiers render au bon moment
add_action( 'after_setup_theme', 'include_all_component_render_files' ); // Ou 'init' si nécessaire

// Ajouter les fonctions de rendu à Twig
add_filter( 'timber/twig', 'add_all_component_renderers_to_twig' );

function add_all_component_renderers_to_twig( \Twig\Environment $twig ) {
    // Récupère toutes les fonctions définies par l'utilisateur après l'inclusion des fichiers
    $user_functions = get_defined_functions()['user'];

    foreach ( $user_functions as $function_name ) {
        // Définit une convention de nommage claire pour vos renderers, par exemple 'render_component_[nom]'
        // ou simplement vérifier si la fonction existe (moins sûr si d'autres plugins définissent des fonctions similaires)
        // Ici, on suppose que toutes les fonctions chargées par `include_all_component_render_files` sont destinées à Twig.
        // C'est pourquoi il est important que seuls les fichiers `*-render.php` définissent ces fonctions.
         if (strpos($function_name, 'render_') === 0) { // Exemple: toutes les fonctions qui commencent par 'render_'
            $twig->addFunction( new \Twig\TwigFunction( $function_name, $function_name ) );
         }
         // Convention plus stricte si nécessaire:
         // if (strpos($function_name, 'render_component_') === 0 || strpos($function_name, 'render_primitive_') === 0) {
         //     $twig->addFunction( new \Twig\TwigFunction( $function_name, $function_name ) );
         // }
    }

    return $twig;
}
