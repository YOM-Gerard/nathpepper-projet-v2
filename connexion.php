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
            border-radius: 4px;
            margin-bottom: 1.5rem;
            font-family: 'Inter', sans-serif;
            font-size: 0.9rem;
            text-align: center;
            border: 1px solid #ef9a9a;
        }
        .alert-success {
            background-color: #e8f5e9;
            color: #2e7d32;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 1.5rem;
            font-family: 'Inter', sans-serif;
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
        <div class="modal-content" style="max-width: 500px; border-radius: 8px;">
            <span class="close" onclick="document.getElementById('account-modal').style.display='none'">&times;</span>
            <div class="modal-header">
                <h2>Mon Compte</h2>
            </div>
            <div class="account-tabs">
                <button class="tab-btn" onclick="window.location.href='connexion.php'">Connexion</button>
                <button class="tab-btn active">Inscription</button>
            </div>
            
            <div id="secure-register-block" style="max-height: 60vh; overflow-y: auto; padding-right: 5px; margin-top: 15px;">
                <div id="modal-error-box" class="alert-error" style="display: none;"></div>
                
                <form id="nathpepper-independent-form" onsubmit="return false;">
                    <div style="display: flex; gap: 10px; margin-bottom: 12px;">
                        <div class="form-group" style="flex: 1;">
                            <label style="display: block; font-size: 0.85rem; font-weight: 500; margin-bottom: 4px;">Prénom</label>
                            <input type="text" id="secure-firstname" required placeholder="Jean" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                        </div>
                        <div class="form-group" style="flex: 1;">
                            <label style="display: block; font-size: 0.85rem; font-weight: 500; margin-bottom: 4px;">Nom</label>
                            <input type="text" id="secure-lastname" required placeholder="Dupont" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                        </div>
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 12px;">
                        <label style="display: block; font-size: 0.85rem; font-weight: 500; margin-bottom: 4px;">Adresse Email</label>
                        <input type="email" id="secure-email" required placeholder="jean.dupont@exemple.com" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                    </div>

                    <div class="form-group" style="margin-bottom: 12px;">
                        <label style="display: block; font-size: 0.85rem; font-weight: 500; margin-bottom: 4px;">Numéro de téléphone</label>
                        <input type="tel" id="secure-phone" required placeholder="06 12 34 56 78" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                    </div>

                    <div class="form-group" style="margin-bottom: 12px;">
                        <label style="display: block; font-size: 0.85rem; font-weight: 500; margin-bottom: 4px;">Adresse complète de livraison</label>
                        <textarea id="secure-address" required placeholder="Numéro, rue, code postal, ville..." rows="3" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; font-family: inherit; resize: vertical;"></textarea>
                    </div>

                    <div class="form-group" style="margin-bottom: 15px;">
                        <label style="display: block; font-size: 0.85rem; font-weight: 500; margin-bottom: 4px;">Mot de passe</label>
                        <input type="password" id="secure-password" required placeholder="••••••••" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                    </div>
                    
                    <button type="button" id="secure-submit-btn" class="btn-primary" style="width: 100%; padding: 12px; font-weight: 600;">S'inscrire</button>
                </form>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var trigger = document.getElementById('open-register-trigger');
        var modal = document.getElementById('account-modal');
        var submitBtn = document.getElementById('secure-submit-btn');
        var errorBox = document.getElementById('modal-error-box');

        if (trigger && modal) {
            trigger.addEventListener('click', function(e) {
                e.preventDefault();
                modal.style.display = 'block';
            });
        }

        if (submitBtn) {
            submitBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                errorBox.style.display = 'none';

                var payload = {
                    firstname: document.getElementById('secure-firstname').value.trim(),
                    lastname: document.getElementById('secure-lastname').value.trim(),
                    email: document.getElementById('secure-email').value.trim(),
                    phone: document.getElementById('secure-phone').value.trim(),
                    address: document.getElementById('secure-address').value.trim(),
                    password: document.getElementById('secure-password').value
                };

                // Vérification basique côté client avant envoi
                if (!payload.firstname || !payload.lastname || !payload.email || !payload.phone || !payload.address || !payload.password) {
                    errorBox.textContent = "Veuillez remplir tous les champs.";
                    errorBox.style.display = 'block';
                    return;
                }

                // Envoi Fetch natif corrigé
                fetch('inscription.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                })
                .then(function(res) { return res.json(); })
                .then(function(data) {
                    if (data.success) {
                        // Succès -> Redirection vers la boutique
                        window.location.href = 'produits.php';
                    } else {
                        errorBox.textContent = data.message;
                        errorBox.style.display = 'block';
                    }
                })
                .catch(function(err) {
                    errorBox.textContent = "Erreur réseau lors de l'inscription.";
                    errorBox.style.display = 'block';
                });
            });
        }
    });
    </script>

</body>
</html>