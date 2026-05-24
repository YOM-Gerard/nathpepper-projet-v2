<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Merci pour votre commande ! - Nathpepper</title>
    <link rel="stylesheet" href="styles/main.css">
    <link rel="stylesheet" href="styles/components.css">
</head>
<body>
    <?php require_once 'includes/header.php'; ?>
    <main class="container" style="text-align: center; padding: 100px 20px;">
        <div style="height: 60px;"></div>
        <h1 style="color: #2e7d32; font-family: var(--font-primary); font-size: 3rem; margin-bottom: 1rem;">✓ Paiement Réussi !</h1>
        <p style="font-size: 1.2rem; margin-bottom: 2rem;">Merci pour votre confiance. Votre commande de poivres d'exception est en cours de préparation.</p>
        <a href="produits.php" class="btn-primary">Retourner à la boutique</a>
    </main>
    <script>
        // Le paiement a réussi : on vide immédiatement le panier de la mémoire du navigateur !
        localStorage.removeItem('nathpepper_cart');
    </script>
    <?php require_once 'includes/footer.php'; ?>
    <script>
    // Le paiement a réussi, on efface le panier de la mémoire du navigateur
    localStorage.removeItem('cart');
</script>
</body>
</html>