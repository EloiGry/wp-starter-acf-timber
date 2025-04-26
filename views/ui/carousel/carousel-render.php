<?php
use Timber\Timber;
/**
 * Carousel Component Renderer
 * 
 * @param array $args {
 *     Configuration du carousel.
 *     
 *     @type int     $slides_count    Nombre de slides à afficher à la fois. Par défaut 1.
 *     @type bool    $autoplay        Activer l'autoplay. Par défaut false.
 *     @type int     $autoplay_delay  Délai entre les slides en ms. Par défaut 3000.
 *     @type string  $arrows_position Position des flèches ('sides', 'bottom', 'outside', 'none'). Par défaut 'sides'.
 *     @type string  $orientation     Orientation du carousel ('horizontal', 'vertical'). Par défaut 'horizontal'.
 *     @type string  $pagination      Type de pagination ('dots', 'numbers', 'none'). Par défaut 'dots'.
 *     @type string  $transition      Type de transition ('slide', 'fade'). Par défaut 'slide'.
 *     @type bool    $infinite        Carousel en boucle infinie. Par défaut false.
 *     @type int     $page_step       Nombre de slides à faire défiler par navigation. Par défaut 1.
 *     @type int     $gap             Espacement entre les slides en pixels. Par défaut 0.
 *     @type array   $breakpoints     Configuration responsive. Par défaut vide.
 *                                   Format: ['768' => ['slides_count' => 2, 'page_step' => 2]]
 *     @type array   $items           Contenu des slides. Obligatoire.
 * }
 * @return bool|string
 */
function render_carousel($args = []) {
    // Fusion des arguments par défaut avec ceux fournis
    $defaults = [
        'slides_count'    => 1,
        'autoplay'        => false,
        'autoplay_delay'  => 3000,
        'arrows_position' => 'sides',
        'orientation'     => 'horizontal',
        'pagination'      => 'dots',
        'transition'      => 'slide',
        'infinite'        => false,
        'page_step'       => 1,
        'gap'             => 0,
        'breakpoints'     => [],
        'items'           => [],
        'class'           => '',
    ];
    
    $args = wp_parse_args($args, $defaults);
    
    // Vérification de la présence d'items
    if (empty($args['items'])) {
        return false;
    }
    
    // Génération d'un ID unique pour le carousel
    $carousel_id = 'carousel-' . uniqid();
    
    // Préparation des données pour Alpine.js
    $alpine_data = [
        'currentIndex' => 0,
        'items' => count($args['items']),
        'autoplay' => $args['autoplay'],
        'autoplayDelay' => $args['autoplay_delay'],
        'orientation' => $args['orientation'],
        'transition' => $args['transition'],
        'infinite' => $args['infinite'],
        'slidesCount' => $args['slides_count'],
        'pageStep' => $args['page_step'],
        'gap' => $args['gap'],
        'breakpoints' => $args['breakpoints'],
    ];
    
    // Conversion des paramètres Alpine en attributs data-*
    $alpine_params = htmlspecialchars(json_encode($alpine_data), ENT_QUOTES, 'UTF-8');
    
    // Render le template Twig
    return Timber::compile('ui/carousel/carousel.twig', [
        'carousel_id' => $carousel_id,
        'alpine_params' => $alpine_params,
        'args' => $args,
    ]);
}