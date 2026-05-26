// Sécurité pour éviter le conflit sur l'accueil
if (typeof cart === 'undefined') {
    window.cart = JSON.parse(localStorage.getItem('cart')) || [];
} else {
    window.cart = cart;
}

// 🛠️ Fonction utilitaire pour afficher une notification premium à l'écran
function showStockNotification(message, isWarning = false) {
    const oldNotification = document.getElementById('stock-toast');
    if (oldNotification) oldNotification.remove();

    const toast = document.createElement('div');
    toast.id = 'stock-toast';
    toast.textContent = message;
    
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
    const qtySelect = document.getElementById(`qty-prod-${id}`);
    
    // 🛠️ On récupère la valeur choisie dans le menu déroulant (vaut 1 si absent)
    const quantityToAdd = qtySelect ? parseInt(qtySelect.value) : 1;
    
    let maxStock = 999; 
    if (button) {
        maxStock = parseInt(button.getAttribute('data-stock')) || 0;
    }

    const existingProductIndex = window.cart.findIndex(item => item.id == id);
    let currentQuantityInCart = 0;

    if (existingProductIndex > -1) {
        currentQuantityInCart = window.cart[existingProductIndex].quantity;
    }

    // SÉCURITÉ : On vérifie si la quantité ajoutée dépasse le stock disponible
    if (currentQuantityInCart + quantityToAdd > maxStock) {
        const dispoRestant = maxStock - currentQuantityInCart;
        if (dispoRestant <= 0) {
            showStockNotification(`Désolé, seuls ${maxStock} exemplaires sont disponibles au total.`, true);
        } else {
            showStockNotification(`Désolé, il ne reste que ${dispoRestant} exemplaire(s) disponible(s) pour votre panier.`, true);
        }
        return; 
    }

    // Incrémentation ou ajout global du produit avec la quantité choisie
    if (existingProductIndex > -1) {
        window.cart[existingProductIndex].quantity += quantityToAdd;
        currentQuantityInCart = window.cart[existingProductIndex].quantity;
    } else {
        window.cart.push({
            id: id,
            name: name,
            price: parseFloat(price),
            image: imageUrl,
            quantity: quantityToAdd
        });
        currentQuantityInCart = quantityToAdd;
    }

    // Notification utilisateur dynamique
    const stockRestant = maxStock - currentQuantityInCart;
    if (stockRestant === 0) {
        showStockNotification(`✨ Vous avez ajouté les derniers exemplaires de "${name}" !`);
    } else if (stockRestant <= 2) {
        showStockNotification(`🔥 Plus que ${stockRestant} articles disponibles pour ce poivre !`);
    } else {
        showStockNotification(`✓ ${quantityToAdd} x "${name}" ajouté(s) au panier.`);
    }

    // Verrouillage immédiat du bouton et du sélecteur si le stock est épuisé
    if (currentQuantityInCart >= maxStock) {
        if (button) {
            button.innerText = "Rupture";
            button.disabled = true;
            button.style.setProperty('background-color', '#bbb', 'important');
            button.style.setProperty('border-color', '#bbb', 'important');
            button.style.setProperty('cursor', 'not-allowed', 'important');
        }
        if (qtySelect) {
            qtySelect.disabled = true;
        }
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
        
        const originalButton = document.getElementById(`btn-prod-${item.id}`);
        let maxStock = 999;
        if (originalButton) {
            maxStock = parseInt(originalButton.getAttribute('data-stock')) || 0;
        }

        const isPlusDisabled = item.quantity >= maxStock ? 'disabled style="opacity: 0.3; cursor: not-allowed;"' : '';
        
        htmlContent += `
            <div class="cart-item-row" style="display: flex; align-items: center; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #eee;">
                <div class="cart-item-info" style="display: flex; align-items: center; gap: 15px;">
                    <img src="${item