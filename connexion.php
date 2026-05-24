<?php
session_start();

// Si l'utilisateur est déjà connecté, on le redirige directement vers la boutique
if (isset($_SESSION['user_id'])) {
    header('Location: produits.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nathpepper - Connexion</title>
    <link rel="stylesheet" href="styles/main.css">
    <link rel="stylesheet" href="styles/components.css">
    <link rel="stylesheet" href="styles/responsive.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        .alert-error {
            background-color: #ffebee;
            color: #c62828;
            padding: 12px;
            border-radius: var(--border-radius);
            margin-bottom: 1.5rem;
            font-family: var(--font-secondary);
            font-size: 0.9rem;
            text-align: center;
            border: 1px solid #ef9a9a;
        }
    </style>
</head>
<body>

    <?php require_once 'includes/header.php'; ?>

    <main class="container">
        <div style="height: 140px; width: 100%;"></div>

        <section class="contact-section" style="max-width: 500px; margin: 0 auto;">
            <h2 class="section-title">Connexion</h2>
            <p class="section-subtitle">Accédez à votre espace pour suivre vos commandes de poivres d'exception.</p>

            <?php if (isset($_SESSION['error_login'])): ?>
                <div class="alert-error">
                    <?php 
                    echo $_SESSION['error_login']; 
                    unset($_SESSION['error_login']); // On efface l'erreur après affichage
                    ?>
                </div>
            <?php endif; ?>

            <form action="traitement-connexion.php" method="POST" class="contact-form">
                <div class="form-group">
                    <label for="email">Adresse Email</label>
                    <input type="email" id="email" name="email" required placeholder="votre.email@exemple.com">
                </div>
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" required placeholder="••••••••">
                </div>
                <button type="submit" class="btn-primary" style="width: 100%; margin-top: 1rem;">Se connecter</button>
            </form>
        </section>
    </main>

    <?php require_once 'includes/footer.php'; ?>

</body>
</html>