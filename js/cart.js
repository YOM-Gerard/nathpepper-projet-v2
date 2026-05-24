// Sécurité pour éviter le conflit sur l'accueil
if (typeof cart === 'undefined') {
    window.cart = JSON.parse(localStorage.getItem('cart')) || [];
} else {
    window.cart = cart;
}

// 1. Fonction pour mettre à jour le petit chiffre du panier dans le header
function updateCartCount() {
    const cartCountElement = document.getElementById('cart-count');
    if (!cartCountElement) return;
    
    // On additionne les quantités de tous les articles du panier en utilisant window.cart
    const totalItems = window.cart.reduce((total, item) => total + (item.quantity || 1), 0);
    cartCountElement.textContent = totalItems;
}

// 2. La fonction qui s'enclenche quand on clique sur "Ajouter au panier"
function addToCart(id, name, price, imageUrl) {
    // On regarde si ce poivre est déjà dans le panier window.cart
    const existingProductIndex = window.cart.findIndex(item => item.name === name);

    if (existingProductIndex > -1) {
        // Si oui, on augmente juste sa quantité de 1
        window.cart[existingProductIndex].quantity += 1;
    } else {
        // Si non, on l'ajoute pour la première fois
        window.cart.push({
            id: id,
            name: name,
            price: parseFloat(price),
            image: imageUrl,
            quantity: 1
        });
    }

    // CORRECTION ICI : On utilise la même clé 'cart' partout sur le site !
    localStorage.setItem('cart', JSON.stringify(window.cart));

    // On met à jour le chiffre en haut de l'écran
    updateCartCount();
}

// 3. On affiche la bonne quantité dès que la page se charge
document.addEventListener('DOMContentLoaded', () => {
    updateCartCount();
});