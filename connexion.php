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
            <p class="section-subtitle">Accédez à votre space pour suivre vos commandes de poivres d'exception.</p>

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
                
                <div style="margin-top: 1.5rem; text-align: center; font-family: 'Inter', sans-serif; font-size: 0.9rem;">
                    <span style="color: #666;">Vous n'avez pas de compte ? </span>
                    <a href="#" id="open-register-trigger" style="color: #1a1b1c; font-weight: 600; text-decoration: underline; transition: color 0.2s;">
                        Créez un compte ici
                    </a>
                </div>
            </form>
        </section>
    </main>

    <?php require_once 'includes/footer.php'; ?>

    <div id="account-modal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close" onclick="document.getElementById('account-modal').style.display='none'">&times;</span>
            <div class="modal-header">
                <h2>Mon Compte</h2>
            </div>
            <div class="account-tabs">
                <button class="tab-btn" data-tab="login" onclick="window.location.href='connexion.php'">Connexion</button>
                <button class="tab-btn active" data-tab="register">Inscription</button>
            </div>
            
            <div id="register-form" class="tab-content active" style="display: block;">
                <form action="inscription.php" method="POST" style="margin-top: 1rem;">
                    <div class="form-group">
                        <label for="register-name">Nom</label>
                        <input type="text" id="register-name" name="name" required placeholder="Ex: Jean Dupont">
                    </div>
                    <div class="form-group" style="margin-top: 1rem;">
                        <label for="register-email">Email</label>
                        <input type="email" id="register-email" name="email" required placeholder="votre.email@exemple.com">
                    </div>
                    <div class="form-group" style="margin-top: 1rem;">
                        <label for="register-password">Mot de passe</label>
                        <input type="password" id="register-password" name="password" required placeholder="••••••••">
                    </div>
                    <button type="submit" class="btn-primary" style="width: 100%; margin-top: 1.5rem;">S'inscrire</button>
                </form>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var registerTrigger = document.getElementById('open-register-trigger');
        var accountModal = document.getElementById('account-modal');
        
        if (registerTrigger && accountModal) {
            registerTrigger.addEventListener('click', function(e) {
                e.preventDefault();
                // Ouvre directement la vue inscription intégrée
                accountModal.style.display = 'block';
            });
        }
    });
    </script>

</body>
</html>