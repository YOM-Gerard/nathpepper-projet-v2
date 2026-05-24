<?php 
// 1. On démarre la session en tout premier
session_start(); 

// 2. On se connecte à la base de données
require_once 'includes/db.php'; 

// 3. On récupère les 4 poivres de la BDD
$stmt = $pdo->query("SELECT * FROM products");
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nathpepper - Nos Poivres</title>
    
    <link rel="stylesheet" href="styles/main.css">
    <link rel="stylesheet" href="styles/components.css">
    <link rel="stylesheet" href="styles/responsive.css">
    
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>

    <?php require_once 'includes/header.php'; ?>

    <main class="container">
        <div style="height: 120px; width: 100%;"></div>

        <section class="products-page">
            <h1 class="section-title">Nos Poivres</h1>
            <p class="section-subtitle">Découvrez l'intégralité de notre collection premium.</p>
            
            <div class="products-grid">
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <div class="products-image" style="width: 100%; height: 250px; overflow: hidden; display: flex; justify-content: center; align-items: center;">
                            <img src="public/images/products/<?php echo pathinfo($product['image_url'], PATHINFO_FILENAME); ?>.jpg" alt="<?php echo $product['name']; ?>" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <div class="product-info">
                            <h3><?php echo $product['name']; ?></h3>
                            <p class="product-description"><?php echo $product['description']; ?></p>
                            <div class="product-bottom">
                                <span class="product-price"><?php echo number_format($product['price'], 2, ',', ' '); ?> €</span>
                                <button class="btn-add-cart" onclick="addToCart(<?php echo $product['id']; ?>, '<?php echo addslashes($product['name']); ?>', <?php echo $product['price']; ?>, 'public/images/products/<?php echo pathinfo($product['image_url'], PATHINFO_FILENAME); ?>.jpg')">
                                    Ajouter au panier
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </main>


    <?php require_once 'includes/footer.php'; ?>

</body>
</html>