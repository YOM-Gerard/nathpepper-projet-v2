<?php 
// 1. On démarre la session en tout premier
session_start(); 

// 2. On se connecte à la base de données
require_once 'includes/db.php'; 

// 3. On récupère les poivres de la BDD
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
                <?php foreach ($products as $product): 
                    $p_id = (int)($product['id'] ?? 0);
                    $p_name = $product['name'] ?? $product['nom'] ?? 'Poivre'; // Initialisation indispensable pour le JS
                    $p_price = (float)($product['price'] ?? 0);
                    $stock_actuel = isset($product['stock']) ? (int)$product['stock'] : 0;
                    $p_image = "public/images/products/" . pathinfo($product['image_url'], PATHINFO_FILENAME) . ".jpg";
                ?>
                    <div class="product-card">
                        <div class="products-image" style="width: 100%; height: 250px; overflow: hidden; display: flex; justify-content: center; align-items: center;">
                            <img src="<?php echo $p_image; ?>" alt="<?php echo htmlspecialchars($p_name, ENT_QUOTES, 'UTF-8'); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <div class="product-info">
                            <h3><?php echo htmlspecialchars($p_name, ENT_QUOTES, 'UTF-8'); ?></h3>
                            <p class="product-description"><?php echo htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                            
                            <div class="product-meta-row" style="display: flex !important; flex-direction: column !important; align-items: stretch !important; gap: 10px !important;">
                                <div class="product-price-weight" style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                                    <span class="product-price"><?php echo number_format((float)$p_price, 2, ',', ' '); ?> €</span>
                                    <span class="product-weight">(<?php echo !empty($product['poids']) ? htmlspecialchars($product['poids'], ENT_QUOTES, 'UTF-8') : '30'; ?>g)</span>
                                </div>
                                
                                <div style="display: flex; gap: 8px; width: 100%; align-items: center;">
                                    <?php if ($stock_actuel <= 0): ?>
                                        <button class="btn-primary add-to-cart-btn out-of-stock" disabled style="background-color: #bbb !important; border-color: #bbb !important; cursor: not-allowed !important; width: 100% !important;">
                                            Rupture
                                        </button>
                                    <?php else: ?>
                                        <select id="qty-prod-<?php echo $p_id; ?>" style="padding: 10px; border: 1px solid #1a1b1c; border-radius: 4px; font-family: 'Inter', sans-serif; font-size: 0.9rem; background: #fff; cursor: pointer;">
                                            <?php 
                                            $max_options = min($stock_actuel, 5);
                                            for ($i = 1; $i <= $max_options; $i++): 
                                            ?>
                                                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                            <?php endfor; ?>
                                        </select>

                                        <button class="btn-primary add-to-cart-btn" 
                                                id="btn-prod-<?php echo $p_id; ?>"
                                                data-stock="<?php echo $stock_actuel; ?>"
                                                style="flex-grow: 1;"
                                                onclick="addToCart(
                                                    '<?php echo $p_id; ?>', 
                                                    '<?php echo addslashes(htmlspecialchars($p_name, ENT_QUOTES, 'UTF-8')); ?>', 
                                                    '<?php echo $p_price; ?>', 
                                                    '<?php echo addslashes(htmlspecialchars($p_image, ENT_QUOTES, 'UTF-8')); ?>'
                                                )">
                                            Ajouter
                                        </button>
                                    <?php endif; ?>
                                </div>
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