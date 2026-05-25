<?php
session_start();
require_once 'includes/db.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contactez-nous - Nathpepper</title>
    <link rel="stylesheet" href="styles/main.css">
    <link rel="stylesheet" href="styles/responsive.css">
    <link rel="stylesheet" href="styles/components.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">
    <style>
        /* Fond de page blanc écru lumineux */
        body { background-color: #fbf9f6 !important; color: #1a1b1c; font-family: 'Inter', sans-serif; }
        
        .contact-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; max-width: 1000px; margin: 0 auto; padding: 4rem 1.5rem; }
        
        .contact-title { font-family: 'Playfair Display', serif; color: #1a1b1c; font-size: 2.5rem; margin-bottom: 1.5rem; }
        
        .info-block { margin-bottom: 2rem; }
        .info-block h4 { color: #8d6e63; margin-bottom: 0.5rem; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1px; }
        .info-block p { color: #444444; line-height: 1.6; margin: 0; }
        
        /* Formulaire blanc épuré posé sur le fond écru */
        .contact-form { background: #ffffff; border: 1px solid #eae5dc; padding: 2.5rem; border-radius: 2px; box-shadow: 0 4px 20px rgba(0,0,0,0.02); }
        .form-group { margin-bottom: 1.5rem; text-align: left; }
        .form-group label { display: block; font-size: 0.85rem; color: #1a1b1c; margin-bottom: 0.5rem; font-weight: 500; }
        
        /* Inputs clairs aux contours fins */
        .form-control { width: 100%; padding: 12px; background: #fbf9f6; border: 1px solid #eae5dc; border-radius: 2px; color: #1a1b1c; font-family: inherit; font-size: 0.95rem; box-sizing: border-box; }
        .form-control:focus { border-color: #1a1b1c; outline: none; }
        
        /* Bouton noir mat "Minimaliste Luxe" */
        .btn-gold { background-color: #1a1b1c; color: #ffffff; border: 1px solid #1a1b1c; width: 100%; padding: 14px; border-radius: 2px; font-weight: 600; text-transform: uppercase; letter-spacing: 1.5px; cursor: pointer; transition: all 0.2s ease-in-out; }
        .btn-gold:hover { background-color: #444444; border-color: #444444; }
        .btn-gold:active { background-color: #ffffff; color: #1a1b1c; }

        /* Le header reste blanc pur comme sur produits.php */
        .header {
            background-color: #ffffff !important;
            border-bottom: 1px solid #eae5dc !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.02) !important;
        }

        /* Ajustements d'aération sur smartphone */
        @media (max-width: 768px) { 
            .contact-grid { grid-template-columns: 1fr; gap: 2.5rem; padding: 2rem 1.5rem; } 
            .contact-title { font-size: 2.1rem; }
            .contact-form { padding: 1.8rem; }
        }
    </style>
</head>
<body>

    <?php require_once 'includes/header.php'; ?>

    <main class="container">
        <div style="height: 120px; width: 100%;"></div>

        <div class="contact-grid">
            <div>
                <h1 class="contact-title">Entrer en Relation</h1>
                <p style="color: #444444; line-height: 1.7; margin-bottom: 3rem; font-weight: 300;">Vous êtes un chef cuisinier, un amateur exigeant ou vous avez simplement une question sur l'une de nos variétés ? Notre service conciergerie est à votre entière disposition.</p>
                
                <div class="info-block">
                    <h4>Le Comptoir Parisien</h4>
                    <p style="font-weight: 300;">12 rue des Poivres Rares<br>75001 Paris, France</p>
                </div>

                <div class="info-block">
                    <h4>Correspondance</h4>
                    <p style="font-weight: 300;">Mails : contact@nathpepper.com<br>Téléphone : +33 (0)1 42 60 00 00</p>
                </div>
            </div>

            <div>
                <form class="contact-form" action="#" method="POST" onsubmit="alert('Message simulé avec succès !'); return false;">
                    <div class="form-group">
                        <label for="name">Nom Complet</label>
                        <input type="text" id="name" class="form-control" placeholder="Gilbert Grandcru" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Adresse E-mail</label>
                        <input type="email" id="email" class="form-control" placeholder="gilbert@mail.com" required>
                    </div>

                    <div class="form-group">
                        <label for="message">Votre Message</label>
                        <textarea id="message" class="form-control" rows="5" placeholder="Décrivez votre projet gastronomique ou votre question..." required></textarea>
                    </div>

                    <button type="submit" class="btn-gold">Envoyer le message</button>
                </form>
            </div>
        </div>
    </main>

    <?php require_once 'includes/footer.php'; ?>

</body>
</html>