<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<header class="header">
    <div class="nav-container">
        <a href="index.php">
            <img src="public/images/logo-front.png" alt="Nathpepper" class="logo">
        </a>
        
        <ul class="nav-menu" id="nav-menu">
            <li><a href="index.php" class="nav-link">Accueil</a></li>
            <li><a href="produits.php" class="nav-link">Nos poivres</a></li>
            <li><a href="notre-histoire.php" class="nav-link">Notre Histoire</a></li>
            <li><a href="contact.php" class="nav-link">Contact</a></li>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><span class="nav-link user-profile-name" style="cursor: default; font-weight: 500; letter-spacing: 1px;">Compte : <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Client', ENT_QUOTES, 'UTF-8'); ?></span></li>
                <li><a href="mes-commandes.php" class="nav-link">Mes Commandes</a></li>
                <li><a href="deconnexion.php" class="nav-link" style="color: #c62828 !important; font-weight: 500;">Déconnexion</a></li>
            <?php else: ?>
                <li><a href="connexion.php" class="nav-link">Connexion</a></li>
            <?php endif; ?>
        </ul>

        <div class="nav-actions">
            <button id="nav-toggle" class="nav-toggle" aria-label="Ouvrir le menu">
                <span class="burger-bar"></span>
                <span class="burger-bar"></span>
                <span class="burger-bar"></span>
            </button>
            <a href="panier.php" class="btn-cart" id="btn-cart" style="text-decoration: none; display: inline-block;">
                🛒 <span class="cart-count" id="cart-count">0</span>
            </a>
        </div>
    </div>
</header>

<style>
@media (max-width: 768px) {
    /* 1. Bouton Burger Minimaliste */
    .nav-toggle {
        display: flex !important;
        flex-direction: column;
        justify-content: space-between;
        width: 24px;
        height: 15px;
        background: none !important;
        border: none !important;
        cursor: pointer;
        padding: 0;
        z-index: 2100000 !important;
    }

    .burger-bar {
        width: 100%;
        height: 2px;
        background-color: #1a1b1c !important;
        border-radius: 1px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* 2. Rideau Mobile Compacté (Ajusté pour que TOUT tienne à l'écran) */
    .nav-menu {
        display: flex !important; 
        flex-direction: column !important;
        justify-content: center !important;
        align-items: center !important;
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 100% !important;
        height: 100vh !important;
        
        background-color: rgba(255, 255, 255, 0.98) !important;
        backdrop-filter: blur(15px) !important;
        -webkit-backdrop-filter: blur(15px) !important;
        
        margin: 0 !important;
        padding: 1.5rem !important;
        gap: 15px !important; /* Espacement réduit entre les blocs de liens */
        
        opacity: 0 !important;
        visibility: hidden !important;
        transform: translateY(-10px) !important;
        transition: opacity 0.3s ease, transform 0.3s ease, visibility 0.3s !important;
        z-index: 2000000 !important;
    }

    .nav-menu.nath-menu-open {
        opacity: 1 !important;
        visibility: visible !important;
        transform: translateY(0) !important;
    }

    .nav-menu li {
        width: 100% !important;
        text-align: center !important;
        margin: 0 !important;
        list-style: none !important;
    }

    /* 3. Typographie Affinée (Moins grosse, plus équilibrée) */
    .nav-menu li a, .nav-menu li span {
        display: inline-block !important;
        font-family: 'Playfair Display', serif !important;
        font-size: 1.35rem !important; /* Taille descendue de 1.8rem à 1.35rem */
        font-weight: 400 !important;
        color: #1a1b1c !important;
        text-decoration: none !important;
        padding: 5px 0 !important; /* Moins de padding pour gagner de la hauteur */
        letter-spacing: 0.5px;
    }

    /* Style spécifique pour la ligne "Compte : Gilbert" */
    .nav-menu li span.user-profile-name {
        font-size: 1.1rem !important;
        color: #757575 !important;
        border-top: 1px solid #eeeeee;
        margin-top: 5px;
        padding-top: 15px !important;
        width: 60%;
    }

    /* Action tactile */
    .nav-menu li a:active {
        color: #8d6e63 !important;
    }

    /* Croix de fermeture */
    .nav-toggle.open .burger-bar:nth-child(1) {
        transform: translateY(6.5px) rotate(45deg) !important;
    }
    .nav-toggle.open .burger-bar:nth-child(2) {
        opacity: 0 !important;
        transform: translateX(10px) !important;
    }
    .nav-toggle.open .burger-bar:nth-child(3) {
        transform: translateY(-6.5px) rotate(-45deg) !important;
    }
}
</style>

<script>
(function() {
    function setupPremiumMenu() {
        const toggleBtn = document.getElementById('nav-toggle');
        const menuList = document.getElementById('nav-menu');

        if (toggleBtn && menuList) {
            toggleBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                menuList.classList.toggle('nath-menu-open');
                toggleBtn.classList.toggle('open');
            });

            document.addEventListener('click', function(e) {
                if (!menuList.contains(e.target) && !toggleBtn.contains(e.target)) {
                    menuList.classList.remove('nath-menu-open');
                    toggleBtn.classList.remove('open');
                }
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', setupPremiumMenu);
    } else {
        setupPremiumMenu();
    }
})();
</script>