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
    // Récupération du bouton cliqué pour vérifier sa limite data-stock passée par le PHP
    const button = document.getElementById(`btn-prod-${id}`);
    let maxStock = 999; // Par défaut si non renseigné
    
    if (button) {
        maxStock = parseInt(button.getAttribute('data-stock')) || 0;
    }

    const existingProductIndex = window.cart.findIndex(item => item.id == id);
    let currentQuantityInCart = 0;

    if (existingProductIndex > -1) {
        currentQuantityInCart = window.cart[existingProductIndex].quantity;
    }

    // SÉCURITÉ : Si on a déjà atteint la limite du stock, on bloque l'ajout
    if (currentQuantityInCart >= maxStock) {
        if (button) {
            button.innerText = "Rupture";
            button.disabled = true;
            button.style.setProperty('background-color', '#bbb', 'important');
            button.style.setProperty('border-color', '#bbb', 'important');
            button.style.setProperty('cursor', 'not-allowed', 'important');
        }
        return; // On stoppe la fonction
    }

    // Procédure d'ajout standard ou incrémentation
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

    // SÉCURITÉ : Si l'ajout vient d'épuiser le dernier produit disponible, on désactive le bouton de la grille
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
    let grandTotal = 0;
    
    window.cart.forEach((item, index) => {
        const itemTotal = item.price * item.quantity;
        grandTotal += itemTotal;
        
        // On récupère le bouton d'origine sur la page pour connaître la limite de stock de cet item
        const originalButton = document.getElementById(`btn-prod-${item.id}`);
        let maxStock = 999;
        if (originalButton) {
            maxStock = parseInt(originalButton.getAttribute('data-stock')) || 0;
        }

        // SÉCURITÉ MODAL : Si la quantité dans le panier est supérieure ou égale au stock, on désactive ou masque le bouton "+"
        const isPlusDisabled = item.quantity >= maxStock ? 'disabled style="opacity: 0.3; cursor: not-allowed;"' : '';
        
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
                        <button onclick="changeQuantityPlus(${index})" ${isPlusDisabled} style="background: none; border: none; padding: 5px 12px; cursor: pointer; font-weight: bold; font-size: 1.1rem;">+</button>
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

// 4.