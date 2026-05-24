// Gestion des modals
document.addEventListener('DOMContentLoaded', function() {
    // Sélectionner tous les modals et boutons de fermeture
    const modals = document.querySelectorAll('.modal');
    const closeButtons = document.querySelectorAll('.close');
    
    // Fonction pour fermer un modal
    function closeModal(modal) {
        modal.classList.remove('show');
    }
    
    // Fonction pour ouvrir un modal
    function openModal(modal) {
        modal.classList.add('show');
    }
    
    // Ajouter les événements de fermeture
    closeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const modal = this.closest('.modal');
            closeModal(modal);
        });
    });
    
    // Fermer le modal en cliquant à l'extérieur
    modals.forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeModal(modal);
            }
        });
    });
    
    // Fermer le modal avec la touche Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const openModal = document.querySelector('.modal.show');
            if (openModal) {
                closeModal(openModal);
            }
        }
    });
    
    // Gestion du modal de compte
    const accountBtn = document.getElementById('btn-account');
    const accountModal = document.getElementById('account-modal');
    
    if (accountBtn && accountModal) {
        accountBtn.addEventListener('click', function() {
            openModal(accountModal);
        });
    }
    
    // Gestion des onglets du modal de compte
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');
            
            // Retirer la classe active de tous les boutons et contenus
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));
            
            // Ajouter la classe active au bouton cliqué et au contenu correspondant
            this.classList.add('active');
            const targetContent = document.getElementById(targetTab + '-form');
            if (targetContent) {
                targetContent.classList.add('active');
            }
        });
    });
    
    // Gestion des formulaires de connexion et inscription
    const loginForm = document.querySelector('#login-form form');
    const registerForm = document.querySelector('#register-form form');
    
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = document.getElementById('login-email').value;
            const password = document.getElementById('login-password').value;
            
            // Simulation de la connexion
            if (email && password) {
                showNotification('Connexion réussie !', 'success');
                closeModal(accountModal);
                
                // Mettre à jour le bouton de compte
                accountBtn.textContent = 'Mon Profil';
            } else {
                showNotification('Veuillez remplir tous les champs', 'error');
            }
        });
    }
    
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const name = document.getElementById('register-name').value;
            const email = document.getElementById('register-email').value;
            const password = document.getElementById('register-password').value;
            
            // Simulation de l'inscription
            if (name && email && password) {
                showNotification('Inscription réussie ! Vous êtes maintenant connecté.', 'success');
                closeModal(accountModal);
                
                // Mettre à jour le bouton de compte
                accountBtn.textContent = 'Mon Profil';
            } else {
                showNotification('Veuillez remplir tous les champs', 'error');
            }
        });
    }
    
    // Gestion des liens du footer (modals pour les mentions légales, etc.)
    const legalLinks = {
        'mentions-legales': {
            title: 'Mentions Légales',
            content: `
                <h3>Mentions Légales</h3>
                <p><strong>Raison sociale :</strong> Nathpepper SARL</p>
                <p><strong>Email :</strong> contact@nathpepper.com</p>
                <p><strong>Directeur de publication :</strong> Nathpepper</p>
            `
        },
        'cgv': {
            title: 'Conditions Générales de Vente',
            content: `
                <h3>Conditions Générales de Vente</h3>
                <h4>Article 1 - Objet</h4>
                <p>Les présentes conditions générales de vente s'appliquent à toutes les commandes passées sur le site nathpepper.com</p>
                
                <h4>Article 2 - Prix</h4>
                <p>Les prix sont indiqués en euros TTC. Nathpepper se réserve le droit de modifier ses prix à tout moment.</p>
                
                <h4>Article 3 - Commande</h4>
                <p>Toute commande implique l'acceptation pleine et entière des présentes conditions générales de vente.</p>
                
                <h4>Article 4 - Livraison</h4>
                <p>Les délais de livraison sont de 3 à 5 jours ouvrés en France métropolitaine.</p>
                
                <h4>Article 5 - Droit de rétractation</h4>
                <p>Vous disposez d'un délai de 14 jours pour exercer votre droit de rétractation.</p>
            `
        },
        'confidentialite': {
            title: 'Politique de Confidentialité',
            content: `
                <h3>Politique de Confidentialité</h3>
                <h4>Collecte des données</h4>
                <p>Nous collectons uniquement les données nécessaires au traitement de votre commande et à l'amélioration de nos services.</p>
                
                <h4>Utilisation des données</h4>
                <p>Vos données personnelles sont utilisées pour :</p>
                <ul>
                    <li>Traiter vos commandes</li>
                    <li>Vous contacter concernant votre commande</li>
                    <li>Améliorer nos services</li>
                    <li>Vous envoyer notre newsletter (avec votre consentement)</li>
                </ul>
                
                <h4>Protection des données</h4>
                <p>Nous mettons en œuvre toutes les mesures techniques et organisationnelles appropriées pour protéger vos données personnelles.</p>
                
                <h4>Vos droits</h4>
                <p>Conformément au RGPD, vous disposez d'un droit d'accès, de rectification, d'effacement et de portabilité de vos données.</p>
            `
        }
    };
    
    // Créer un modal générique pour le contenu légal
    const legalModal = document.createElement('div');
    legalModal.id = 'legal-modal';
    legalModal.className = 'modal';
    legalModal.innerHTML = `
        <div class="modal-content">
            <span class="close">&times;</span>
            <div class="modal-header">
                <h2 id="legal-title"></h2>
            </div>
            <div id="legal-content"></div>
        </div>
    `;
    document.body.appendChild(legalModal);
    
    // Ajouter l'événement de fermeture au nouveau modal
    const legalCloseBtn = legalModal.querySelector('.close');
    legalCloseBtn.addEventListener('click', function() {
        closeModal(legalModal);
    });
    
    legalModal.addEventListener('click', function(e) {
        if (e.target === legalModal) {
            closeModal(legalModal);
        }
    });
    
    // Ajouter les événements aux liens légaux
    Object.keys(legalLinks).forEach(linkId => {
        const link = document.getElementById(linkId);
        if (link) {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                const legalTitle = document.getElementById('legal-title');
                const legalContent = document.getElementById('legal-content');
                
                legalTitle.textContent = legalLinks[linkId].title;
                legalContent.innerHTML = legalLinks[linkId].content;
                
                openModal(legalModal);
            });
        }
    });
});

// Exporter les fonctions pour les autres modules
window.openModal = function(modal) {
    modal.classList.add('show');
};

window.closeModal = function(modal) {
    modal.classList.remove('show');
};

// 4. Fonction pour augmenter la quantité (+1)
function changeQuantityPlus(index) {
    window.cart[index].quantity += 1;
    saveAndRefreshCart();
}

// 5. Fonction pour diminuer la quantité (-1) ou supprimer si elle tombe à 0
function changeQuantityMinus(index) {
    window.cart[index].quantity -= 1;
    
    if (window.cart[index].quantity <= 0) {
        window.cart.splice(index, 1); // Supprime l'article du tableau s'il n'y en a plus
    }
    saveAndRefreshCart();
}

// 6. Fonction pour supprimer complètement un article (la petite croix/poubelle)
function removeProductFromCart(index) {
    window.cart.splice(index, 1); // Retire l'élément à cet index
    saveAndRefreshCart();
}

// 7. Fonction outil pour sauvegarder la mémoire et mettre à jour l'affichage partout
function saveAndRefreshCart() {
    localStorage.setItem('cart', JSON.stringify(window.cart));
    updateCartCount(); // Met à jour le petit badge du header
    
    // Si la fonction globale pour redessiner le panier visuel existe, on l'exécute
    if (typeof renderCartItems === 'function') {
        renderCartItems();
    } else if (typeof displayCart === 'function') {
        displayCart();
    }
}