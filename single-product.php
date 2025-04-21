<?php

use Timber\Timber;
// Assurez-vous que Timber est bien chargé
$context = Timber::context();


// echo '<pre>';
// print_r($context);
// echo '</pre>';

// Afficher le template Twig
Timber::render('woocommerce/single-product.twig', $context);