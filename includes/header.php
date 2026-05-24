<header class="header">
    <div class="nav-container">
        <a href="index.php">
            <img src="public/images/logo-front.png" alt="Nathpepper" class="logo">
        </a>
        
        <ul class="nav-menu">
            <li><a href="index.php" class="nav-link">Accueil</a></li>
            <li><a href="produits.php" class="nav-link">Nos poivres</a></li>
            <li><a href="index.html#story" class="nav-link">Notre Histoire</a></li>
            <li><a href="index.html#contact" class="nav-link">Contact</a></li>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><span class="nav-link" style="cursor: default;">👤 <?php echo htmlspecialchars($_SESSION['user_name']); ?></span></li>
                <li><a href="deconnexion.php" class="nav-link" style="color: #f44336;">Déconnexion</a></li>
            <?php else: ?>
                <li><a href="connexion.php" class="nav-link">Connexion</a></li>
            <?php endif; ?>
        </ul>

        <div class="nav-actions">
            <a href="panier.php" class="btn-cart" id="btn-cart" style="text-decoration: none; display: inline-block;">
                🛒 <span class="cart-count" id="cart-count">0</span>
            </a>
        </div>
    </div>
</header>