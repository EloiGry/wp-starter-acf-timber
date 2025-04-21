<?php
use Timber\Timber;

$context = Timber::context();
$context['products'] = Timber::get_posts(); // Récupère les produits

Timber::render('woocommerce/archive-product.twig', $context);

