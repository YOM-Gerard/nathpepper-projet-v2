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
                <li><span class="nav-link" style="cursor: default; font-weight: 500;">👤 <?php echo htmlspecialchars($_SESSION['user_name']); ?></span></li>
                <li><a href="mes-commandes.php" class="nav-link">Mes Commandes</a></li>
                <li><a href="deconnexion.php" class="nav-link" style="color: #f44336;">Déconnexion</a></li>
            <?php else: ?>
                <li><a href="connexion.php" class="nav-link">Connexion</a></li>
            <?php endif; ?>
        </ul>

        <div class="nav-actions">
            <button id="nav-toggle" class="nav-toggle" style="display: none; background: none; border: none; font-size: 1.5rem; cursor: pointer;">☰</button>
            <a href="panier.php" class="btn-cart" id="btn-cart" style="text-decoration: none; display: inline-block;">
                🛒 <span class="cart-count" id="cart-count">0</span>
            </a>
        </div>
    </div>
</header>