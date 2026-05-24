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
        // Sécurité pour les noms de variables (gère 'price' ou 'prix')
        const prix = item.price || item.prix || 0;
        const nom = item.name || item.nom || 'Poivre inconnu';
        const image = item.image || item.img || 'images/default-pepper.jpg';
        const quantite = item.quantity || item.qte || 1;
        
        const totalLigne = prix * quantite;
        totalGeneral += totalLigne;

        // On crée la ligne HTML
        const row = document.createElement('tr');
        row.innerHTML = `
            <td><img src="${image}" alt="${nom}" class="cart-item-img"></td>
            <td><strong style="color: var(--primary-color);">${nom}</strong></td>
            <td>${prix.toFixed(2)} €</td>
            <td>
                <span class="qty-value">${quantite}</span>
            </td>
            <td><strong>${totalLigne.toFixed(2)} €</strong></td>
            <td>
                <button class="remove-btn" onclick="supprimerPoivre(${index})">Supprimer</button>
            </td>
        `;
        tableBody.appendChild(row);
    });

    // Mise à jour des totaux en bas à droite
    if (subtotalEl) subtotalEl.textContent = `${totalGeneral.toFixed(2)} €`;
    if (totalEl) totalEl.textContent = `${totalGeneral.toFixed(2)} €`;
}

// Fonction pour supprimer un produit si l'utilisateur clique sur "Supprimer"
window.supprimerPoivre = function(index) {
    window.cart.splice(index, 1); // Enlever du tableau
    localStorage.setItem('cart', JSON.stringify(window.cart)); // Sauvegarder dans le navigateur
    afficherLePanierVisuel(); // Re-dessiner l'écran
    
    // Mettre à jour le badge du header s'il existe sur la page
    const badge = document.querySelector('.cart-count') || document.querySelector('.badge');
    if (badge) {
        const totalQte = window.cart.reduce((sum, item) => sum + (item.quantity || 1), 0);
        badge.textContent = totalQte;
    }
};

// 3. Lancement automatique de l'affichage au chargement de la page
document.addEventListener('DOMContentLoaded', () => {
    afficherLePanierVisuel();

    // 4. Gestion du bouton de paiement Stripe
    const checkoutBtn = document.getElementById('checkout-button');
    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', () => {
            if (window.cart.length === 0) {
                alert("Votre panier est vide !");
                return;
            }

            checkoutBtn.textContent = "Redirection sécurisée...";
            checkoutBtn.disabled = true;

            // Envoi des données au PHP
            fetch('creer-session-paiement.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ items: window.cart })
            })
            .then(response => response.json())
            .then(session => {
                if (session.url) {
                    window.location.href = session.url; // Décollage vers Stripe !
                } else {
                    alert("Erreur Stripe : " + (session.error || "Impossible d'ouvrir la page de paiement."));
                    checkoutBtn.textContent = "Procéder au paiement sécurisé";
                    checkoutBtn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert("Une erreur technique est survenue lors de la liaison avec Stripe.");
                checkoutBtn.textContent = "Procéder au paiement sécurisé";
                checkoutBtn.disabled = false;
            });
        });
    }
});