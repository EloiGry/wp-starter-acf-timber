<?php
use Timber\Timber;


$context = Timber::context(); 


Timber::render('single.twig', $context);
