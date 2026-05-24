// On initialise le panier depuis le localStorage (ou vide s'il n'y a rien)
let cart = JSON.parse(localStorage.getItem('nathpepper_cart')) || [];

// 1. Fonction pour mettre à jour le petit chiffre du panier dans le header
function updateCartCount() {
    const cartCountElement = document.getElementById('cart-count');
    if (!cartCountElement) return;
    
    // On additionne les quantités de tous les articles du panier
    const totalItems = cart.reduce((total, item) => total + item.quantity, 0);
    cartCountElement.textContent = totalItems;
}

// 2. La fonction qui s'enclenche quand on clique sur "Ajouter au panier"
function addToCart(id, name, price, imageUrl) {
    // On regarde si ce poivre est déjà dans le panier
    const existingProductIndex = cart.findIndex(item => item.name === name);

    if (existingProductIndex > -1) {
        // Si oui, on augmente juste sa quantité de 1
        cart[existingProductIndex].quantity += 1;
    } else {
        // Si non, on l'ajoute pour la première fois
        cart.push({
            id: id,
            name: name,
            price: parseFloat(price),
            image: imageUrl,
            quantity: 1
        });
    }

    // On sauvegarde le panier dans la mémoire du navigateur
    localStorage.setItem('nathpepper_cart', JSON.stringify(cart));

    // On met à jour le chiffre en haut de l'écran
    updateCartCount();
}

// 3. On affiche la bonne quantité dès que la page se charge
document.addEventListener('DOMContentLoaded', () => {
    updateCartCount();
});