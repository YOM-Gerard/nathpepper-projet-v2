// Sécurité pour éviter le conflit sur l'accueil
if (typeof cart === 'undefined') {
    window.cart = JSON.parse(localStorage.getItem('cart')) || [];
} else {
    window.cart = cart;
}

// 🛠️ Fonction utilitaire pour afficher une notification premium à l'écran
function showStockNotification(message, isWarning = false) {
    // Supprime l'ancienne notification si elle existe déjà
    const oldNotification = document.getElementById('stock-toast');
    if (oldNotification) oldNotification.remove();

    const toast = document.createElement('div');
    toast.id = 'stock-toast';
    toast.textContent = message;
    
    // Style de la notification volante
    toast.style.cssText = `
        position: fixed;
        top: 100px;
        right: 20px;
        background-color: ${isWarning ? '#c62828' : '#1a1b1c'};
        color: #fff;
        padding: 12px 24px;
        border-radius: 4px;
        font-family: 'Inter', sans-serif;
        font-size: 0.9rem;
        font-weight: 500;
        z-index: 9999999;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        transition: opacity 0.3s ease;
    `;

    document.body.appendChild(toast);

    // Disparition automatique après 3,5 secondes
    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 300);
    }, 3500);
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
    const button = document.getElementById(`btn-prod-${id}`);
    let maxStock = 999; 
    
    if (button) {
        maxStock = parseInt(button.getAttribute('data-stock')) || 0;
    }

    const existingProductIndex = window.cart.findIndex(item => item.id == id);
    let currentQuantityInCart = 0;

    if (existingProductIndex > -1) {
        currentQuantityInCart = window.cart[existingProductIndex].quantity;
    }

    // SÉCURITÉ : Limite de stock déjà atteinte
    if (currentQuantityInCart >= maxStock) {
        showStockNotification(`Désolé, seuls ${maxStock} exemplaires sont disponibles.`, true);
        if (button) {
            button.innerText = "Rupture";
            button.disabled = true;
            button.style.setProperty('background-color', '#bbb', 'important');
            button.style.setProperty('border-color', '#bbb', 'important');
            button.style.setProperty('cursor', 'not-allowed', 'important');
        }
        return; 
    }

    // Incrémentation ou ajout du produit
    if (existingProductIndex > -1) {
        window.cart[existingProductIndex].quantity += 1;
        currentQuantityInCart = window.cart[existingProductIndex].quantity;
    } else {
        window.cart.push({
            id: id,
            name: name,
            price: parseFloat(price),
            image: imageUrl,
            quantity: 1
        });
        currentQuantityInCart = 1;
    }

    // Notification utilisateur au moment de l'ajout
    const stockRestant = maxStock - currentQuantityInCart;
    if (stockRestant === 0) {
        showStockNotification(`✨ Vous avez ajouté le dernier exemplaire de "${name}" !`);
    } else if (stockRestant <= 2) {
        showStockNotification(`🔥 Plus que ${stockRestant} articles disponibles pour ce poivre !`);
    } else {
        showStockNotification(`✓ "${name}" a bien été ajouté au panier.`);
    }

    // Verrouillage immédiat du bouton de la grille si le stock est épuisé
    if (currentQuantityInCart >= maxStock && button) {
        button.innerText = "Rupture";
        button.disabled = true;
        button.style.setProperty('background-color', '#bbb', 'important');
        button.style.setProperty('border-color', '#bbb', 'important');
        button.style.setProperty('cursor', 'not-allowed', 'important');
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