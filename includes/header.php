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
                <li><span class="nav-link" style="cursor: default; font-weight: 500;">👤 <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Client', ENT_QUOTES, 'UTF-8'); ?></span></li>
                <li><a href="mes-commandes.php" class="nav-link">Mes Commandes</a></li>
                <li><a href="deconnexion.php" class="nav-link" style="color: #f44336;">Déconnexion</a></li>
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
    /* 1. Le bouton Burger épuré (Lignes plus élégantes et minimalistes) */
    .nav-toggle {
        display: flex !important;
        flex-direction: column;
        justify-content: space-between;
        width: 24px;
        height: 16px;
        background: none !important;
        border: none !important;
        cursor: pointer;
        padding: 0;
        z-index: 2100000 !important;
    }

    .burger-bar {
        width: 100%;
        height: 2px; /* Lignes affinées pour un rendu haut de gamme */
        background-color: #1a1b1c !important;
        border-radius: 1px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* 2. Le rideau mobile effet "Glassmorphism" (Flou artistique Premium) */
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
        
        /* Fond blanc translucide haut de gamme */
        background-color: rgba(255, 255, 255, 0.98) !important;
        backdrop-filter: blur(15px) !important;
        -webkit-backdrop-filter: blur(15px) !important;
        
        margin: 0 !important;
        padding: 2rem !important;
        gap: 25px !important;
        
        /* Masqué avec opacité à 0 et léger décalage vers le haut */
        opacity: 0 !important;
        visibility: hidden !important;
        transform: translateY(-20px) !important;
        transition: opacity 0.4s ease, transform 0.4s ease, visibility 0.4s !important;
        z-index: 2000000 !important;
        box-shadow: none !important;
    }

    /* Déploiement en douceur lors de l'activation */
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
        opacity: 0;
        transform: translateY(10px);
        transition: opacity 0.3s ease, transform 0.3s ease;
    }

    /* Animation d'apparition séquentielle des liens */
    .nav-menu.nath-menu-open li {
        opacity: 1;
        transform: translateY(0);
    }
    
    /* Décalage temporel des animations pour chaque lien */
    .nav-menu.nath-menu-open li:nth-child(1) { transition-delay: 0.1s; }
    .nav-menu.nath-menu-open li:nth-child(2) { transition-delay: 0.15s; }
    .nav-menu.nath-menu-open li:nth-child(3) { transition-delay: 0.2s; }
    .nav-menu.nath-menu-open li:nth-child(4) { transition-delay: 0.25s; }
    .nav-menu.nath-menu-open li:nth-child(5) { transition-delay: 0.3s; }
    .nav-menu.nath-menu-open li:nth-child(6) { transition-delay: 0.35s; }

    /* 3. Typographie des liens (Style Luxe Épuré) */
    .nav-menu li a, .nav-menu li span {
        display: inline-block !important;
        font-family: 'Playfair Display', serif !important; /* Utilisation de ta police signature */
        font-size: 1.8rem !important;
        font-weight: 400 !important;
        color: #1a1b1c !important;
        text-decoration: none !important;
        padding: 8px 0 !important;
        letter-spacing: 1px;
        transition: color 0.3s ease !important;
    }

    /* Teinte subtile au toucher */
    .nav-menu li a:active {
        color: #8d6e63 !important; /* Couleur chaude d'épice douce */
    }

    /* 4. Animation de la croix (X) minimaliste */
    .nav-toggle.open .burger-bar:nth-child(1) {
        transform: translateY(7px) rotate(45deg) !important;
    }
    .nav-toggle.open .burger-bar:nth-child(2) {
        opacity: 0 !important;
        transform: translateX(10px) !important;
    }
    .nav-toggle.open .burger-bar:nth-child(3) {
        transform: translateY(-7px) rotate(-45deg) !important;
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
                
                // Utilisation de notre classe isolée pour éviter les conflits JS
                menuList.classList.toggle('nath-menu-open');
                toggleBtn.classList.toggle('open');
            });

            // Fermeture intelligente si clic en dehors du rideau ouvert
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