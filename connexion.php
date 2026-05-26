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
        .alert-success {
            background-color: #e8f5e9;
            color: #2e7d32;
            padding: 12px;
            border-radius: var(--border-radius);
            margin-bottom: 1.5rem;
            font-family: var(--font-secondary);
            font-size: 0.9rem;
            text-align: center;
            border: 1px solid #a5d6a7;
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
                    unset($_SESSION['error_login']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['success_register'])): ?>
                <div class="alert-success">
                    <?php 
                    echo $_SESSION['success_register']; 
                    unset($_SESSION['success_register']);
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
        <div class="modal-content" style="max-width: 550px;">
            <span class="close" onclick="document.getElementById('account-modal').style.display='none'">&times;</span>
            <div class="modal-header">
                <h2>Mon Compte</h2>
            </div>
            <div class="account-tabs">
                <button class="tab-btn" data-tab="login" onclick="window.location.href='connexion.php'">Connexion</button>
                <button class="tab-btn active" data-tab="register">Inscription</button>
            </div>
            
            <div id="register-form" class="tab-content active" style="display: block; max-height: 70vh; overflow-y: auto; padding-right: 5px;">
                <form id="force-native-register" action="inscription.php" method="POST" style="margin-top: 1rem;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label for="reg-firstname">Prénom</label>
                            <input type="text" id="reg-firstname" name="firstname" required placeholder="Ex: Jean">
                        </div>
                        <div class="form-group">
                            <label for="reg-lastname">Nom</label>
                            <input type="text" id="reg-lastname" name="lastname" required placeholder="Ex: Dupont">
                        </div>
                    </div>
                    
                    <div class="form-group" style="margin-top: 1rem;">
                        <label for="reg-email">Email</label>
                        <input type="email" id="reg-email" name="email" required placeholder="jean.dupont@exemple.com">
                    </div>

                    <div class="form-group" style="margin-top: 1rem;">
                        <label for="reg-phone">Numéro de téléphone</label>
                        <input type="tel" id="reg-phone" name="phone" required placeholder="Ex: 06 12 34 56 78">
                    </div>

                    <div class="form-group" style="margin-top: 1rem;">
                        <label for="reg-address">Adresse complète de livraison</label>
                        <textarea id="reg-address" name="address" required placeholder="Rue, code postal, ville, bâtiment..." style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: var(--border-radius); font-family: inherit; resize: vertical;" rows="3"></textarea>
                    </div>

                    <div class="form-group" style="margin-top: 1rem;">
                        <label for="reg-password">Mot de passe</label>
                        <input type="password" id="reg-password" name="password" required placeholder="••••••••">
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
        var registerForm = document.getElementById('force-native-register');
        
        if (registerTrigger && accountModal) {
            registerTrigger.addEventListener('click', function(e) {
                e.preventDefault();
                accountModal.style.display = 'block';
            });
        }

        // Neutralise l'interception AJAX globale de modals.js
        if (registerForm) {
            registerForm.addEventListener('submit', function(e) {
                e.stopPropagation();
            });
        }
    });
    </script>

</body>
</html>