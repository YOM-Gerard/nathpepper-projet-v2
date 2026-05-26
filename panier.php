<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Panier - Nathpepper</title>
    <link rel="stylesheet" href="styles/main.css">
    <link rel="stylesheet" href="styles/components.css">
    <link rel="stylesheet" href="styles/responsive.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        .cart-page-container {
            margin: 40px auto;
            max-width: 1000px;
        }
        .cart-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2rem;
            background: var(--white);
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow);
        }
        .cart-table th, .cart-table td {
            padding: 15px 20px;
            text-align: left;
            border-bottom: 1px solid var(--gray-light);
        }
        .cart-table th {
            background-color: var(--primary-color);
            color: var(--text-light);
            font-family: var(--font-secondary);
            font-weight: 500;
        }
        .cart-item-img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: var(--border-radius);
        }
        .qty-btn {
            background: var(--gray-light);
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            font-weight: bold;
            border-radius: 4px;
            transition: var(--transition);
        }
        .qty-btn:hover {
            background: var(--primary-color);
            color: var(--text-light);
        }
        .qty-value {
            margin: 0 10px;
            font-weight: 500;
        }
        .remove-btn {
            background: none;
            border: none;
            color: #c62828;
            cursor: pointer;
            text-decoration: underline;
            font-size: 0.9rem;
        }
        .cart-summary {
            background: var(--white);
            padding: 30px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            max-width: 400px;
            margin-left: auto;
            text-align: right;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
            font-size: 1.1rem;
        }
        .summary-total {
            font-family: var(--font-primary);
            font-size: 1.5rem;
            font-weight: 700;
            border-top: 2px solid var(--primary-color);
            padding-top: 1rem;
            margin-top: 1rem;
        }
        .empty-cart-message {
            text-align: center;
            padding: 50px 20px;
            background: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
        }
    </style>
</head>
<body>

    <?php require_once 'includes/header.php'; ?>

    <main class="container">
        <div style="height: 140px; width: 100%;"></div>

        <div class="cart-page-container">
            <h1 class="section-title" style="text-align: left; margin-bottom: 2rem;">
                Votre Panier <?php echo isset($_SESSION['user_name']) ? '- ' . htmlspecialchars($_SESSION['user_name'], ENT_QUOTES, 'UTF-8') : ''; ?>
            </h1>

            <div id="cart-content-wrapper">
                
                <div class="table-responsive" style="width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; margin-bottom: 20px;">
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th>Produit</th>
                                <th>Nom</th>
                                <th>Prix</th>
                                <th>Quantité</th>
                                <th>Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="cart-table-body">
                        </tbody>
                    </table>
                </div>

                <div class="cart-summary">
                    <div class="summary-row">
                        <span>Sous-total</span>
                        <span id="cart-subtotal">0.00 €</span>
                    </div>
                    <div class="summary-row">
                        <span>Livraison</span>
                        <span style="color: #2e7d32; font-weight: 500;">Gratuite</span>
                    </div>
                    <div class="summary-row summary-total">
                        <span>Total</span>
                        <span id="cart-total-price">0.00 €</span>
                    </div>
                    
                    <button id="checkout-button" class="btn-primary" style="width: 100%; margin-top: 1.5rem; padding: 15px;">
                        Procéder au paiement sécurisé
                    </button>
                </div>
            </div>

            <div id="empty-cart-view" class="empty-cart-message" style="display: none;">
                <p style="font-size: 1.2rem; margin-bottom: 1.5rem;">Votre panier est actuellement vide.</p>
                <a href="produits.php" class="btn-primary">Découvrir nos poivres</a>
            </div>
        </div>
    </main>

    <?php require_once 'includes/footer.php'; ?>

    <script src="js/cart.js"></script>
    <script src="js/panier-visuel.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkoutBtn = document.getElementById('checkout-button');
        
        if (checkoutBtn) {
            checkoutBtn.addEventListener('click', function() {
                const totalPriceEl = document.getElementById('cart-total-price');
                if (!totalPriceEl) return;

                const totalText = totalPriceEl.textContent;
                const totalNumeric = parseFloat(totalText.replace(/[^\d.]/g, '')) || 0;

                fetch('includes/sauvegarder-total.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ total: totalNumeric })
                })
                .then(() => {
                    window.location.href = 'commande.php';
                })
                .catch(err => {
                    console.error("Erreur de sauvegarde de session:", err);
                    window.location.href = 'commande.php';
                });
            });
        }
    });
    </script>
</body>
</html>