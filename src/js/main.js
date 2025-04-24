// src/js/main.js

import Alpine from 'alpinejs';
import './cart'; // Importer le fichier cart.js

// Expose Alpine to the window for potential debugging (optional)
window.Alpine = Alpine;

// Start Alpine
Alpine.start();

console.log('Alpine.js est initialisé !');
console.log('Code du panier chargé.'); // Confirmation que cart.js est bien importé