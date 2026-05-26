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
    <title>Nathpepper - Espace Client</title>
    <link rel="stylesheet" href="styles/main.css">
    <link rel="stylesheet" href="styles/components.css">
    <link rel="stylesheet" href="styles/responsive.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        .auth-container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }
        .auth-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 4rem;
            background: #fff;
            padding: 2.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.02);
        }
        @media (min-width: 768px) {
            .auth-grid {
                grid-template-columns: 1fr 1fr;
                align-items: start;
            }
            .auth-divider {
                border-left: 1px solid #e8e2d5;
                padding-left: 4rem;
            }
        }
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
        .custom-form-group {
            margin-bottom: 1.2rem;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .custom-form-group label {
            font-family: 'Inter', sans-serif;
            font-size: 0.85rem;
            font-weight: 600;
            color: #1a1b1c;
        }
        .custom-form-group input, .custom-form-group textarea {
            width: 100%;
            padding: 11px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-family: 'Inter', sans-serif;
            font-size: 0.9rem;
            background: #fff;
            transition: border-color 0.2s;
        }
        .custom-form-group input:focus, .custom-form-group textarea:focus {
            border-color: #1a1b1c;
            outline: none;
        }
    </style>
</head>
<body>

    <?php require_once 'includes/header.php'; ?>

    <main class="container">
        <div style="height: 140px; width: 100%;"></div>

        <div class="auth-container">
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

            <div class="auth-grid">
                
                <section class="contact-section" style="margin: 0; max-width: 100%; padding: 0;">
                    <h2 class="section-title" style="text-align: left; font-size: 2rem; margin-bottom: 0.5rem;">Déjà client ?</h2>
                    <p class="section-subtitle" style="text-align: left; margin-bottom: 2rem;">Accédez à votre espace pour suivre vos commandes.</p>

                    <form action="traitement-connexion.php" method="POST" class="contact-form">
                        <div class="form-group">
                            <label for="email">Adresse Email</label>
                            <input type="email" id="email" name="email" required placeholder="votre.email@exemple.com">
                        </div>
                        <div class="form-group" style="margin-top: 1rem;">
                            <label for="password">Mot de passe</label>
                            <input type="password" id="password" name="password" required placeholder="••••••••">
                        </div>
                        <button type="submit" class="btn-primary" style="width: 100%; margin-top: 2rem; padding: 12px;">Se connecter</button>
                    </form>
                </section>

                <section class="contact-section auth-divider" style="margin: 0; max-width: 100%; padding: 0;">
                    <h2 class="section-title" style="text-align: left; font-size: 2rem; margin-bottom: 0.5rem;">Nouveau client ?</h2>
                    <p class="section-subtitle" style="text-align: left; margin-bottom: 2rem;">Créez votre compte de livraison pour commander vos poivres d'exception.</p>

                    <form action="inscription.php" method="POST" class="contact-form">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                            <div class="custom-form-group">
                                <label for="ins-firstname">Prénom</label>
                                <input type="text" id="ins-firstname" name="firstname" required placeholder="Jean">
                            </div>
                            <div class="custom-form-group">
                                <label for="ins-lastname">Nom</label>
                                <input type="text" id="ins-lastname" name="lastname" required placeholder="Dupont">
                            </div>
                        </div>
                        
                        <div class="custom-form-group">
                            <label for="ins-email">Adresse Email</label>
                            <input type="email" id="ins-email" name="email" required placeholder="jean.dupont@exemple.com">
                        </div>

                        <div class="custom-form-group">
                            <label for="ins-phone">Numéro de téléphone</label>
                            <input type="tel" id="ins-phone" name="phone" required placeholder="06 12 34 56 78">
                        </div>

                        <div class="custom-form-group">
                            <label for="ins-address">Adresse complète de livraison</label>
                            <textarea id="ins-address" name="address" required placeholder="Numéro, rue, code postal, ville..." rows="3"></textarea>
                        </div>

                        <div class="custom-form-group">
                            <label for="ins-password">Mot de passe</label>
                            <input type="password" id="ins-password" name="password" required placeholder="••••••••">
                        </div>
                        
                        <button type="submit" class="btn-primary" style="width: 100%; margin-top: 1.5rem; padding: 12px;">Créer mon compte</button>
                    </form>
                </section>

            </div>
        </div>
    </main>

    <div style="height: 100px; width: 100%;"></div>

    <?php require_once 'includes/footer.php'; ?>

</body>
</html>