// Sécurité pour éviter le conflit sur l'accueil
if (typeof cart === 'undefined') {
    window.cart = JSON.parse(localStorage.getItem('cart')) || [];
} else {
    window.cart = cart;
}

// 🛠️ Fonction utilitaire pour afficher une notification premium à l'écran
function showStockNotification(message, isWarning) {
    var isWarningMode = isWarning || false;
    var oldNotification = document.getElementById('stock-toast');
    if (oldNotification) oldNotification.remove();

    var toast = document.createElement('div');
    toast.id = 'stock-toast';
    toast.textContent = message;
    
    toast.style.cssText = "position: fixed; top: 100px; right: 20px; background-color: " + 
        (isWarningMode ? '#c62828' : '#1a1b1c') + 
        "; color: #fff; padding: 12px 24px; border-radius: 4px; font-family: 'Inter', sans-serif; font-size: 0.9rem; font-weight: 500; z-index: 9999999; box-shadow: 0 4px 12px rgba(0,0,0,0.15); transition: opacity 0.3s ease;";

    document.body.appendChild(toast);

    setTimeout(function() {
        toast.style.opacity = '0';
        setTimeout(function() { toast.remove(); }, 300);
    }, 3500);
}

// 1. Fonction pour mettre à jour le petit chiffre du panier dans le header
function updateCartCount() {
    var cartCountElement = document.getElementById('cart-count');
    if (!cartCountElement) return;
    
    var totalItems = 0;
    for (var i = 0; i < window.cart.length; i++) {
        totalItems += (window.cart[i].quantity || 1);
    }
    cartCountElement.textContent = totalItems;
}

// 2. La fonction qui s'enclenche quand on clique sur "Ajouter au panier"
function addToCart(id, name, price, imageUrl) {
    var button = document.getElementById('btn-prod-' + id);
    var qtySelect = document.getElementById('qty-prod-' + id);
    
    // Récupération de la quantité choisie dans le menu déroulant
    var quantityToAdd = qtySelect ? parseInt(qtySelect.value) : 1;
    
    var maxStock = 999; 
    if (button) {
        maxStock = parseInt(button.getAttribute('data-stock')) || 0;
    }

    var existingProductIndex = -1;
    for (var j = 0; j < window.cart.length; j++) {
        if (window.cart[j].id == id) {
            existingProductIndex = j;
            break;
        }
    }
    
    var currentQuantityInCart = 0;
    if (existingProductIndex > -1) {
        currentQuantityInCart = window.cart[existingProductIndex].quantity;
    }

    // SÉCURITÉ : On vérifie si l'ajout demandé dépasse le stock
    if (currentQuantityInCart + quantityToAdd > maxStock) {
        var dispoRestant = maxStock - currentQuantityInCart;
        if (dispoRestant <= 0) {
            showStockNotification("Désolé, la limite de stock maximale est atteinte.", true);
        } else {
            showStockNotification("Désolé, il ne reste que " + dispoRestant + " exemplaire(s) pour votre panier.", true);
        }
        return; 
    }

    // Ajout ou incrémentation de la quantité choisie
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

    // Notifications dynamiques à l'écran
    var stockRestant = maxStock - currentQuantityInCart;
    if (stockRestant === 0) {
        showStockNotification("✨ Vous avez ajouté les derniers exemplaires de \"" + name + "\" !");
    } else if (stockRestant <= 2) {
        showStockNotification("🔥 Plus que " + stockRestant + " articles disponibles pour ce poivre !");
    } else {
        showStockNotification("✓ " + quantityToAdd + " x \"" + name + "\" ajouté(s) au panier.");
    }

    // Verrouillage si rupture atteinte
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

// 3. Fonction pour générer la liste des produits dans le panier modal
function renderCartItems() {
    var cartItemsContainer = document.getElementById('cart-items');
    var cartTotalElement = document.getElementById('cart-total');
    
    if (!cartItemsContainer) return;
    
    if (window.cart.length === 0) {
        cartItemsContainer.innerHTML = '<p class="empty-cart-msg">Votre panier est vide.</p>';
        if (cartTotalElement) cartTotalElement.textContent = '0,00 €';
        return;
    }
    
    var htmlContent = '';
    var grandTotal = 0;
    
    for (var k = 0; k < window.cart.length; k++) {
        var item = window.cart[k];
        var itemTotal = item.price * item.quantity;
        grandTotal += itemTotal;
        
        var originalButton = document.getElementById('btn-prod-' + item.id);
        var maxStock = 999;
        if (originalButton) {
            maxStock = parseInt(originalButton.getAttribute('data-stock')) || 0;
        }

        var plusBtnAttribute = '';
        if (item.quantity >= maxStock) {
            plusBtnAttribute = 'disabled style="opacity: 0.3; cursor: not-allowed;"';
        }
        
        htmlContent += '<div class="cart-item-row" style="display: flex; align-items: center; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #eee;">' +
            '<div class="cart-item-info" style="display: flex; align-items: center; gap: 15px;">' +
                '<img src="' + item.image + '" alt="' + item.name + '" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">' +
                '<div>' +
                    '<h4 style="margin: 0; font-family: \'Playfair Display\', serif;">' + item.name + '</h4>' +
                    '<span style="color: #666; font-size: 0.9rem;">' + item.price.toFixed(2).replace('.', ',') + ' € / unité</span>' +
                '</div>' +
            '</div>' +
            '<div class="cart-item-actions" style="display: flex; align-items: center; gap: 15px;">' +
                '<div class="qty-controls" style="display: flex; align-items: center; border: 1px solid #ccc; border-radius: 4px; overflow: hidden; background: #fff;">' +
                    '<button onclick="changeQuantityMinus(' + k + ')" style="background: none; border: none; padding: 5px 12px; cursor: pointer; font-weight: bold; font-size: 1.1rem;">-</button>' +
                    '<span style="padding: 0 5px; font-weight: 500; min-width: 20px; text-align: center;">' + item.quantity + '</span>' +
                    '<button onclick="changeQuantityPlus(' + k + ')" ' + plusBtnAttribute + ' style="background: none; border: none; padding: 5px 12px; cursor: pointer; font-weight: bold; font-size: 1.1rem;">+</button>' +
                '</div>' +
                '<span class="item-total-price" style="font-weight: 600; min-width: 70px; text-align: right;">' + itemTotal.toFixed(2).replace('.', ',') + ' €</span>' +
                '<button onclick="removeProductFromCart(' + k + ')" style="background: none; border: none; color: #cc0000; cursor: pointer; font-size: 1.2rem; padding: 0 5px;" title="Supprimer l\'article">🗑️</button>' +
            '</div>' +
        '</div>';
    }
    
    cartItemsContainer.innerHTML = htmlContent;
    if (cartTotalElement) {
        cartTotalElement.textContent = grandTotal.toFixed(2).replace('.', ',') + ' €';
    }
}

// 4. Augmenter la quantité (+1)
function changeQuantityPlus(index) {
    var item = window.cart[index];
    var originalButton = document.getElementById('btn-prod-' + item.id);
    var maxStock = 999;
    
    if (originalButton) {
        maxStock = parseInt(originalButton.getAttribute('data-stock')) || 0;
    }

    if (item.quantity < maxStock) {
        window.cart[index].quantity += 1;
        if (window.cart[index].quantity === maxStock) {
            showStockNotification("✨ Dernier exemplaire disponible atteint pour \"" + item.name + "\".");
        }
        saveAndRefreshCart();
    } else {
        showStockNotification("Limite de stock atteinte pour \"" + item.name + "\".", true);
    }
}

// 5. Diminuer la quantité (-1)
function changeQuantityMinus(index) {
    var item = window.cart[index];
    window.cart[index].quantity -= 1;
    
    if (window.cart[index].quantity <= 0) {
        window.cart.splice(index, 1);
    }
    
    var originalButton = document.getElementById('btn-prod-' + item.id);
    var qtySelect = document.getElementById('qty-prod-' + item.id);
    if (originalButton) {
        originalButton.innerText = "Ajouter";
        originalButton.disabled = false;
        originalButton.style.cssText = "";
    }
    if (qtySelect) {
        qtySelect.disabled = false;
        qtySelect.value = "1";
    }

    saveAndRefreshCart();
}

// 6. Supprimer un produit du panier
function removeProductFromCart(index) {
    var item = window.cart[index];
    window.cart.splice(index, 1);
    
    var originalButton = document.getElementById('btn-prod-' + item.id);
    var qtySelect = document.getElementById('qty-prod-' + item.id);
    if (originalButton) {
        originalButton.innerText = "Ajouter";
        originalButton.disabled = false;
        originalButton.style.cssText = "";
    }
    if (qtySelect) {
        qtySelect.disabled = false;
        qtySelect.value = "1";
    }
    
    saveAndRefreshCart();
}

// 7. Synchronisation locale
function saveAndRefreshCart() {
    localStorage.setItem('cart', JSON.stringify(window.cart));
    updateCartCount();
    renderCartItems();
}

// 8. Initialisation DOM
document.addEventListener('DOMContentLoaded', function() {
    updateCartCount();
    
    var cartHeaderBtn = document.getElementById('btn-cart') || document.querySelector('.nav-icon[id*="cart"]');
    if (cartHeaderBtn) {
        cartHeaderBtn.addEventListener('click', renderCartItems);
    }
    
    var clearCartBtn = document.getElementById('clear-cart');
    if (clearCartBtn) {
        clearCartBtn.addEventListener('click', function() {
            for (var m = 0; m < window.cart.length; m++) {
                var item = window.cart[m];
                var originalButton = document.getElementById('btn-prod-' + item.id);
                var qtySelect = document.getElementById('qty-prod-' + item.id);
                if (originalButton) {
                    originalButton.innerText = "Ajouter";
                    originalButton.disabled = false;
                    originalButton.style.cssText = "";
                }
                if (qtySelect) {
                    qtySelect.disabled = false;
                    qtySelect.value = "1";
                }
            }
            window.cart = [];
            saveAndRefreshCart();
        });
    }
});