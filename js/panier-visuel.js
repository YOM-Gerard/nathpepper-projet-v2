// 1. Initialisation unique du panier depuis la mémoire du navigateur
if (typeof cart === 'undefined') {
    window.cart = JSON.parse(localStorage.getItem('cart')) || [];
} else {
    window.cart = cart;
}

// 2. Fonction magique pour dessiner le panier à l'écran
function afficherLePanierVisuel() {
    const tableBody = document.getElementById('cart-table-body');
    const subtotalEl = document.getElementById('cart-subtotal');
    const totalEl = document.getElementById('cart-total-price');
    const contentWrapper = document.getElementById('cart-content-wrapper');
    const emptyView = document.getElementById('empty-cart-view');

    // Si le panier est vide en mémoire, on affiche le message de panier vide
    if (!window.cart || window.cart.length === 0) {
        if (contentWrapper) contentWrapper.style.display = 'none';
        if (emptyView) emptyView.style.display = 'block';
        updateBadgeHeader();
        return;
    }

    // Sinon, on cache le message vide et on montre le panier
    if (contentWrapper) contentWrapper.style.display = 'block';
    if (emptyView) emptyView.style.display = 'none';

    if (!tableBody) return;
    tableBody.innerHTML = ''; // On vide le tableau avant de le reconstruire
    
    let totalGeneral = 0;

    // On boucle sur chaque poivre du panier
    window.cart.forEach((item, index) => {
        const prix = item.price || item.prix || 0;
        const nom = item.name || item.nom || 'Poivre inconnu';
        const image = item.image || item.img || 'images/default-pepper.jpg';
        const quantite = item.quantity || item.qte || 1;
        
        const totalLigne = prix * quantite;
        totalGeneral += totalLigne;

        // On crée la ligne HTML
        const row = document.createElement('tr');
        row.innerHTML = `
            <td><img src="${image}" alt="${nom}" class="cart-item-img" style="width: 60px; height: 60px; object-fit: cover; border-radius: 5px;"></td>
            <td><strong style="color: var(--primary-color); font-family: 'Playfair Display', serif; font-size: 1.1rem;">${nom}</strong></td>
            <td>${prix.toFixed(2).replace('.', ',')} €</td>
            <td>
                <div class="qty-controls" style="display: inline-flex; align-items: center; border: 1px solid #ccc; border-radius: 4px; overflow: hidden; background: #fff;">
                    <button onclick="modifierQuantiteTableau(${index}, -1)" style="background: none; border: none; padding: 5px 12px; cursor: pointer; font-weight: bold; font-size: 1rem; transition: background 0.2s;">-</button>
                    <span class="qty-value" style="padding: 0 5px; font-weight: 500; min-width: 20px; text-align: center;">${quantite}</span>
                    <button onclick="modifierQuantiteTableau(${index}, 1)" style="background: none; border: none; padding: 5px 12px; cursor: pointer; font-weight: bold; font-size: 1rem; transition: background 0.2s;">+</button>
                </div>
            </td>
            <td><strong>${totalLigne.toFixed(2).replace('.', ',')} €</strong></td>
            <td>
                <button class="remove-btn" onclick="supprimerPoivre(${index})" style="background: none; border: none; color: #cc0000; cursor: pointer; font-size: 1.1rem; font-weight: 500;" title="Supprimer cet article">🗑️ Supprimer</button>
            </td>
        `;
        tableBody.appendChild(row);
    });

    // Mise à jour des totaux
    if (subtotalEl) subtotalEl.textContent = `${totalGeneral.toFixed(2).replace('.', ',')} €`;
    if (totalEl) totalEl.textContent = `${totalGeneral.toFixed(2).replace('.', ',')} €`;
    
    updateBadgeHeader();
}

// Nouvelle fonction pour gérer l'augmentation et la diminution directement depuis le tableau
window.modifierQuantiteTableau = function(index, changement) {
    window.cart[index].quantity = (window.cart[index].quantity || 1) + changement;
    
    if (window.cart[index].quantity <= 0) {
        window.cart.splice(index, 1);
    }
    
    sauvegarderEtRafraichirTout();
};

// Fonction pour supprimer un produit
window.supprimerPoivre = function(index) {
    window.cart.splice(index, 1);
    sauvegarderEtRafraichirTout();
};

// Fonction outil pour mettre à jour le localStorage et redessiner l'écran
function sauvegarderEtRafraichirTout() {
    localStorage.setItem('cart', JSON.stringify(window.cart));
    afficherLePanierVisuel();
    
    if (typeof updateCartCount === 'function') {
        updateCartCount();
    }
}

// Fonction isolée pour gérer le badge de quantité en haut de l'écran
function updateBadgeHeader() {
    const badge = document.querySelector('.cart-count') || document.querySelector('.badge') || document.getElementById('cart-count');
    if (badge) {
        const totalQte = window.cart.reduce((sum, item) => sum + (item.quantity || 1), 0);
        badge.textContent = totalQte;
    }
}

// 3. Lancement automatique au chargement du DOM
document.addEventListener('DOMContentLoaded', () => {
    afficherLePanierVisuel();

    // 4. Gestion du bouton de paiement avec DOUBLE SÉCURITÉ
    const checkoutBtn = document.getElementById('checkout-button');
    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', () => {
            if (window.cart.length === 0) {
                alert("Votre panier est vide !");
                return;
            }

            checkoutBtn.textContent = "Vérification des stocks...";
            checkoutBtn.disabled = true;

            // ÉTAPE A : Requête vers valider-commande.php
            fetch('valider-commande.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ basket: window.cart })
            })
            .then(response => response.json())
            .then(stockData => {
                if (stockData.success) {
                    // ÉTAPE B : On enchaîne sur la création de session Stripe
                    checkoutBtn.textContent = "Redirection sécurisée...";
                    return fetch('creer-session-paiement.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ items: window.cart })
                    });
                } else {
                    throw new Error(stockData.message);
                }
            })
            .then(response => {
                if (response) return response.json();
            })
            .then(session => {
                if (session && session.url) {
                    window.cart = [];
                    localStorage.setItem('cart', JSON.stringify(window.cart));
                    window.location.href = session.url;
                } else if (session) {
                    alert("Erreur Stripe : " + (session.error || "Impossible d'ouvrir la page de paiement."));
                    checkoutBtn.textContent = "Procéder au paiement sécurisé";
                    checkoutBtn.disabled = false;
                }
            })
            .catch(error => {
                alert("⚠️ " + error.message);
                checkoutBtn.textContent = "Procéder au paiement sécurisé";
                checkoutBtn.disabled = false;
            });
        });
    }
});