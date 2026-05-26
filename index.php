<?php
// 1. Inclusion du fichier de connexion
require_once 'includes/db.php'; 

try {
    // 2. On récupère les 4 premiers poivres actifs de ta BDD
    $query = $pdo->query("SELECT * FROM products LIMIT 4"); 
    $products = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Sécurité anti-crash
    $products = [];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nathpepper - Poivres d'Exception</title>
    <meta name="description" content="Découvrez notre sélection de poivres d'exception. Nathpepper vous propose des poivres premium pour sublimer vos plats.">
    <link rel="stylesheet" href="styles/main.css">
    <link rel="stylesheet" href="styles/components.css">
    <link rel="stylesheet" href="styles/responsive.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <style>
        .product-image-container {
            width: 100%;
            height: 250px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f9f9f9;
        }
        .product-image-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
        

    <main class="container">
        <section id="accueil" class="hero">
            <div class="hero-content">
                <h1 class="hero-title">L'Excellence du Poivre</h1>
                <p class="hero-subtitle">Découvrez notre sélection de poivres d'exception, cultivés avec passion et respect de l'environnement</p>
                <a href="produits.php" class="btn-primary hero-cta" style="text-decoration: none; display: inline-block; text-align: center;">Découvrir nos poivres</a>
            </div>
            <div class="hero-image">
                <img src="./public/images/products/poivre-exeption.jpg" alt="Poivre de Kampot" class="hero-img">
            </div>
        </section>

        <section id="nos-poivres" class="products-section">
            <div>
                <h2 class="section-title">Nos Poivres d'Exception</h2>
                <p class="section-subtitle">Une sélection rigoureuse de poivres premium pour sublimer vos créations culinaires</p>
                
                <div class="products-grid" id="products-grid">
                    <?php if (!empty($products)): ?>
                        <?php foreach ($products as $product): 
                            $p_id = $product['id'] ?? 0;
                            $p_name = $product['name'] ?? $product['nom'] ?? 'Poivre';
                            $p_price = $product['price'] ?? $product['prix'] ?? 0;
                            $p_desc = $product['description'] ?? '';
                            
                            $db_image = $product['image_url'] ?? '';
                            $filename = pathinfo($db_image, PATHINFO_FILENAME);
                            $p_image = "./public/images/products/" . $filename . ".jpg";
                        ?>
                            <div class="product-card">
                                <div class="product-image-container">
                                    <img src="<?php echo htmlspecialchars($p_image); ?>" alt="<?php echo htmlspecialchars($p_name); ?>">
                                </div>
                                <div class="product-info">
                                    <h3><?php echo htmlspecialchars($p_name); ?></h3>
                                    <p class="product-desc"><?php echo htmlspecialchars($p_desc); ?></p>
                                    
                                    <div class="product-meta-row">
                                        <div class="product-price-weight">
                                            <span class="product-price"><?php echo number_format($p_price, 2, ',', ' '); ?> €</span>
                                            <span class="product-weight">(<?php 
                                                if (isset($product['poids'])) {
                                                    echo htmlspecialchars($product['poids'], ENT_QUOTES, 'UTF-8');
                                                } elseif (isset($product['weight'])) {
                                                    echo htmlspecialchars($product['weight'], ENT_QUOTES, 'UTF-8');
                                                } else {
                                                    echo '30'; 
                                                }
                                            ?>g)</span>
                                        </div>
                                        
                                        <button class="btn-primary btn-add-cart btn-add-to-cart add-to-cart-btn" onclick="addToCart(
                                            '<?php echo $p_id; ?>', 
                                            '<?php echo addslashes($p_name); ?>', 
                                            '<?php echo $p_price; ?>', 
                                            '<?php echo addslashes($p_image); ?>'
                                        )">
                                            Ajouter au panier
                                        </button>
                                    </div>

                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Aucun poivre disponible pour le moment.</p>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <section id="notre-marque" class="brand-section">
            <div>
                <div class="brand-content">
                    <div class="brand-text">
                        <h2 class="section-title">Notre Engagement</h2>
                        <p class="brand-description">
                            Chez Nathpepper, nous croyons que chaque grain de poivre raconte une histoire. 
                            Nous sélectionnons nos poivres directement auprès de producteurs passionnés, 
                            garantissant une qualité exceptionnelle et un commerce équitable.
                        </p>
                        <p class="brand-description">
                            Une partie de nos bénéfices est reversée à des associations luttant contre 
                            la déforestation, car nous croyons en un avenir durable pour notre planète.
                        </p>
                        <div class="brand-values">
                            <div class="value-item">
                                <h3>🌱 Éthique</h3>
                                <p>Commerce équitable and respect des producteurs</p>
                            </div>
                            <div class="value-item">
                                <h3>🌍 Durable</h3>
                                <p>Engagement pour la protection de l'environnement</p>
                            </div>
                            <div class="value-item">
                                <h3>⭐ Qualité</h3>
                                <p>Sélection rigoureuse des meilleurs poivres</p>
                            </div>
                        </div>
                    </div>
                    <div class="brand-image">
                        <img src="./public/images/products/deforestation.jpg" alt="Plantation de poivre" class="brand-img">
                    </div>
                </div>
            </div>
        </section>

        <section id="contact" class="contact-section" style="background-color: #1a1b1c; border-top: 1px solid #2d2d2d; border-bottom: 1px solid #2d2d2d; padding: 5rem 1rem; text-align: center;">
            <div style="max-width: 700px; margin: 0 auto;">
                <h2 style="font-family: 'Playfair Display', Georgia, serif; color: #dbc49d; font-size: 2.3rem; margin-bottom: 1rem; letter-spacing: 1px;">Une demande particulière ?</h2>
                <p style="color: #cccccc; font-size: 1.05rem; line-height: 1.7; margin-bottom: 2.5rem;">Whether you are a chef, an epicurean, or looking for a tailor-made selection, our concierge service is here to assist you.</p>
                <a href="contact.php" style="display: inline-block; background-color: #dbc49d; color: #1a1b1c; padding: 14px 35px; text-decoration: none; border-radius: 4px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; transition: background 0.2s;">
                    Entrer en relation
                </a>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>

    <div id="product-modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div id="modal-body"></div>
        </div>
    </div>

    <div id="cart-modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div class="modal-header">
                <h2>Mon Panier</h2>
            </div>
            <div id="cart-items"></div>
            <div class="cart-total">
                <strong>Total: <span id="cart-total">0,00 €</span></strong>
            </div>
            <div class="cart-actions">
                <button class="btn-primary" id="checkout-btn">Commander</button>
                <button class="btn-secondary" id="clear-cart">Vider le panier</button>
            </div>
        </div>
    </div>

    <div id="account-modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div class="modal-header">
                <h2>Mon Compte</h2>
            </div>
            <div class="account-tabs">
                <button class="tab-btn active" data-tab="login">Connexion</button>
                <button class="tab-btn" data-tab="register">Inscription</button>
            </div>
            <div id="login-form" class="tab-content active">