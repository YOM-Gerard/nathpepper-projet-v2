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
    
    const totalItems = window.cart.reduce((total, item) => total + (item.quantity || 1), 0);
    cartCountElement.textContent = totalItems;
}

// 2. La fonction qui s'enclenche quand on clique sur "Ajouter au panier"
function addToCart(id, name, price, imageUrl) {
    const existingProductIndex = window.cart.findIndex(item => item.name === name);

    if (existingProductIndex > -1) {
        window.cart[existingProductIndex].quantity += 1;
    } else {
        window.cart.push({
            id: id,
            name: name,
            price: parseFloat(price),
            image: imageUrl,
            quantity: 1
        });
    }

    localStorage.setItem('cart', JSON.stringify(window.cart));
    updateCartCount();
    renderCartItems();
}

// ==========================================
// 🛠️ ACTIONS DU PANIER VISUEL (MODAL)
// ==========================================

// 3. Fonction pour générer le HTML de la liste des produits dans le panier modal
function renderCartItems() {
    const cartItemsContainer = document.getElementById('cart-items');
    const cartTotalElement = document.getElementById('cart-total');
    
    if (!cartItemsContainer) return;
    
    if (window.cart.length === 0) {
        cartItemsContainer.innerHTML = '<p class="empty-cart-msg">Votre panier est vide.</p>';
        if (cartTotalElement) cartTotalElement.textContent = '0,00 €';
        return;
    }
    
    let htmlContent = '';
    let grandTotal = 0;
    
    window.cart.forEach((item, index) => {
        const itemTotal = item.price * item.quantity;
        grandTotal += itemTotal;
        
        htmlContent += `
            <div class="cart-item-row" style="display: flex; align-items: center; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #eee;">
                <div class="cart-item-info" style="display: flex; align-items: center; gap: 15px;">
                    <img src="${item.image}" alt="${item.name}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                    <div>
                        <h4 style="margin: 0; font-family: 'Playfair Display', serif;">${item.name}</h4>
                        <span style="color: #666; font-size: 0.9rem;">${item.price.toFixed(2).replace('.', ',')} € / unité</span>
                    </div>
                </div>
                
                <div class="cart-item-actions" style="display: flex; align-items: center; gap: 15px;">
                    <div class="qty-controls" style="display: flex; align-items: center; border: 1px solid #ccc; border-radius: 4px; overflow: hidden; background: #fff;">
                        <button onclick="changeQuantityMinus(${index})" style="background: none; border: none; padding: 5px 12px; cursor: pointer; font-weight: bold; font-size: 1.1rem;">-</button>
                        <span style="padding: 0 5px; font-weight: 500; min-width: 20px; text-align: center;">${item.quantity}</span>
                        <button onclick="changeQuantityPlus(${index})" style="background: none; border: none; padding: 5px 12px; cursor: pointer; font-weight: bold; font-size: 1.1rem;">+</button>
                    </div>
                    
                    <span class="item-total-price" style="font-weight: 600; min-width: 70px; text-align: right;">${itemTotal.toFixed(2).replace('.', ',')} €</span>
                    
                    <button onclick="removeProductFromCart(${index})" style="background: none; border: none; color: #cc0000; cursor: pointer; font-size: 1.2rem; padding: 0 5px;" title="Supprimer l'article">🗑️</button>
                </div>
            </div>
        `;
    });
    
    cartItemsContainer.innerHTML = htmlContent;
    if (cartTotalElement) {
        cartTotalElement.textContent = grandTotal.toFixed(2).replace('.', ',') + ' €';
    }
}

// 4. Augmenter la quantité (+1)
function changeQuantityPlus(index) {
    window.cart[index].quantity += 1;
    saveAndRefreshCart();
}

// 5. Diminuer la quantité (-1)
function changeQuantityMinus(index) {
    window.cart[index].quantity -= 1;
    if (window.cart[index].quantity <= 0) {
        window.cart.splice(index, 1);
    }
    saveAndRefreshCart();
}

// 6. Supprimer complètement un produit
function removeProductFromCart(index) {
    window.cart.splice(index, 1);
    saveAndRefreshCart();
}

// 7. Fonction utilitaire pour synchroniser les données et redessiner l'interface
function saveAndRefreshCart() {
    localStorage.setItem('cart', JSON.stringify(window.cart));
    updateCartCount();
    renderCartItems();
    
    if (typeof afficherLePanierVisuel === 'function') {
        afficherLePanierVisuel();
    }
}

// 8. Préparation et écouteurs d'événements au chargement DOM
document.addEventListener('DOMContentLoaded', () => {
    updateCartCount();
    
    const cartHeaderBtn = document.getElementById('btn-cart') || document.querySelector('.nav-icon[id*="cart"]');
    if (cartHeaderBtn) {
        cartHeaderBtn.addEventListener('click', renderCartItems);
    }
    
    const clearCartBtn = document.getElementById('clear-cart');
    if (clearCartBtn) {
        clearCartBtn.addEventListener('click', () => {
            window.cart = [];
            saveAndRefreshCart();
        });
    }
});