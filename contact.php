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
    <link rel="stylesheet" href="styles/components.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">
    <style>
        body { background-color: #202020; color: #dddddd; font-family: 'Inter', sans-serif; }
        .contact-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; max-width: 1000px; margin: 0 auto; padding: 4rem 1rem; }
        .contact-title { font-family: 'Playfair Display', serif; color: #dbc49d; font-size: 2.5rem; margin-bottom: 1.5rem; }
        .info-block { margin-bottom: 2rem; }
        .info-block h4 { color: #e4cca2; margin-bottom: 0.5rem; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1px; }
        .info-block p { color: #bbbbbb; line-height: 1.6; margin: 0; }
        .contact-form { background: #1a1b1c; border: 1px solid #2d2d2d; padding: 2.5rem; border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.3); }
        .form-group { margin-bottom: 1.5rem; text-align: left; }
        .form-group label { display: block; font-size: 0.85rem; color: #aaaaaa; margin-bottom: 0.5rem; font-weight: 500; }
        .form-control { width: 100%; padding: 12px; background: #202020; border: 1px solid #3d3d3d; border-radius: 4px; color: #ffffff; font-family: inherit; font-size: 0.95rem; }
        .form-control:focus { border-color: #dbc49d; outline: none; }
        .btn-gold { background-color: #dbc49d; color: #1a1b1c; border: none; width: 100%; padding: 14px; border-radius: 4px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; cursor: pointer; transition: background 0.2s; }
        .btn-gold:hover { background-color: #e4cca2; }
        @media (max-width: 768px) { .contact-grid { grid-template-columns: 1fr; gap: 2rem; } }
    </style>
</head>
<body>

    <?php require_once 'includes/header.php'; ?>

    <main class="container">
        <div style="height: 120px; width: 100%;"></div>

        <div class="contact-grid">
            <div>
                <h1 class="contact-title">Entrer en Relation</h1>
                <p style="color: #cccccc; line-height: 1.7; margin-bottom: 3rem;">Vous êtes un chef cuisinier, un amateur exigeant ou vous avez simplement une question sur l'une de nos variétés ? Notre service conciergerie est à votre entière disposition.</p>
                
                <div class="info-block">
                    <h4>Le Comptoir Parisien</h4>
                    <p>12 rue des Poivres Rares<br>75001 Paris, France</p>
                </div>

                <div class="info-block">
                    <h4>Correspondance</h4>
                    <p>Mails : contact@nathpepper.com<br>Téléphone : +33 (0)1 42 60 00 00</p>
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