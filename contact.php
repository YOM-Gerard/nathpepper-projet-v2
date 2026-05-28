<?php
session_start();
// Si tu as besoin d'une connexion BDD ou de scripts de traitement pour le formulaire, 
// conserve tes inclusions php habituelles tout en haut ici.
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contactez-nous - Nathpepper</title>
    <link rel="stylesheet" href="styles/main.css">
    <link rel="stylesheet" href="styles/components.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body { background-color: #fcfbfa; color: #1a1b1c; }
        .contact-container { max-width: 1100px; margin: 0 auto; padding: 40px 1.5rem; }
        .contact-grid { display: grid; grid-template-columns: 1fr; gap: 3rem; }
        @media (min-width: 768px) {
            .contact-grid { grid-template-columns: 1fr 1fr; align-items: start; }
        }
        .contact-info h2 { font-family: 'Playfair Display', serif; font-size: 2.5rem; margin-bottom: 1.5rem; }
        .contact-info p { font-family: 'Inter', sans-serif; font-size: 1.1rem; line-height: 1.6; color: #444; margin-bottom: 2rem; }
        .contact-card { background: #fff; padding: 2.5rem; border-radius: 4px; border: 1px solid #e8e2d5; box-shadow: 0 4px 15px rgba(0,0,0,0.01); }
        .form-group { margin-bottom: 1.2rem; display: flex; flex-direction: column; gap: 6px; }
        .form-group label { font-family: 'Inter', sans-serif; font-size: 0.85rem; font-weight: 600; }
        .form-group input, .form-group textarea {
            width: 100%; padding: 11px; border: 1px solid #ccc; border-radius: 4px; font-family: 'Inter', sans-serif; font-size: 0.9rem; box-sizing: border-box;
        }
    </style>
</head>
<body>

    <?php require_once 'includes/header.php'; ?>

    <main class="container">
        <div style="height: 140px; width: 100%;"></div>

        <div class="contact-container">
            <div class="contact-grid">
                
                <!-- À GAUCHE : TEXTES RÉELS ET SIMPLES -->
                <div class="contact-info">
                    <h2>Contact</h2>
                    <p>
                        Que vous soyez un professionnel de la cuisine, un passionné ou simplement curieux, vous avez une question sur l'une de nos variétés de poivres ? Écrivez-nous pour composer la sélection parfaite selon vos envies.
                    </p>
                    
                    <div style="border-top: 1px solid #e8e2d5; padding-top: 1.5rem;">
                        <span style="font-family: 'Inter', sans-serif; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #888;">Correspondance</span>
                        <p style="margin-top: 0.5rem; font-size: 1.1rem; font-weight: 500;">
                            Email : <a href="mailto:contact@nathpepper.com" style="color: #1a1b1c; text-decoration: underline;">contact@nathpepper.com</a>
                        </p>
                    </div>
                </div>

                <!-- À DROITE : FORMULAIRE PROPRE -->
                <div class="contact-card">
                    <form action="#" method="POST">
                        <div class="form-group">
                            <label>Nom Complet</label>
                            <input type="text" name="name" required placeholder="Votre nom">
                        </div>
                        
                        <div class="form-group">
                            <label>Adresse E-mail</label>
                            <input type="email" name="email" required placeholder="votre@email.com">
                        </div>
                        
                        <div class="form-group">
                            <label>Votre Message</label>
                            <textarea name="message" rows="5" required placeholder="Posez votre question ici..."></textarea>
                        </div>
                        
                        <button type="submit" class="btn-primary" style="width: 100%; margin-top: 1rem; padding: 12px; background: #1a1b1c; font-weight: 600; border: none; color: #fff; cursor: pointer;">
                            Envoyer le message
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </main>

    <div style="height: 100px; width: 100%;"></div>
    <?php require_once 'includes/footer.php'; ?>

</body>
</html>