

<?php
use Timber\Timber;


$context = Timber::context(); 
$context['sections'] = get_field('sections');
Timber::render('index.twig', $context);
