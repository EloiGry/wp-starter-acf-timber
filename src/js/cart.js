document.addEventListener("alpine:init", () => {
  Alpine.store("cart", {
    cart: { items: [], totals: {} },

    async getCart() {
      const res = await fetch("/wp-json/wc/store/cart");
      const data = await res.json();
      this.cart = data;
    },

    async addItemToCart(productId, quantity = 1, variations = []) {
      const res = await fetch("/wp-json/wc/store/v1/cart/add-item", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Nonce: window.wpApiSettings.nonce, // Assurez-vous que le nonce est bien défini
        },
        body: JSON.stringify({
          id: productId,
          quantity: quantity,
          variation: variations, // On envoie les variations si elles sont présentes
        }),
      });

      const data = await res.json();
      this.cart = data; // Mise à jour du panier avec les données renvoyées
    },

    async updateQuantity(itemKey, newQuantity) {
      const res = await fetch("/wp-json/wc/store/v1/cart/update-item", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Nonce: window.wpApiSettings.nonce,
        },
        body: JSON.stringify({
          key: itemKey,
          quantity: newQuantity,
        }),
      });

      const data = await res.json();
      this.cart = data;
    },

    async removeItem(itemKey) {
      const res = await fetch("/wp-json/wc/store/v1/cart/remove-item", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Nonce: window.wpApiSettings.nonce,
        },
        body: JSON.stringify({
          key: itemKey,
        }),
      });

      const data = await res.json();
      this.cart = data;
    },
  });

  // Charger le panier dès le démarrage
  Alpine.store("cart").getCart();

  // 2. Ajoute un écouteur pour re-synchroniser quand l'onglet redevient visible
  //    Utile si l'utilisateur a modifié le panier dans un autre onglet/navigateur.
  document.addEventListener("visibilitychange", () => {
    // 'visible' signifie que l'onglet est de nouveau actif
    if (document.visibilityState === "visible") {
      console.log("Tab became visible, checking cart sync...");
      Alpine.store("cart").getCart(); // Appelle la méthode de synchronisation
    }
  });
});

// N'oubliez pas d'initialiser AlpineJS si vous l'importez et qu'il n'est pas démarré ailleurs
// Alpine.start(); // Décommentez si nécessaire
