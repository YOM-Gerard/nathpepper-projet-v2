<?php
session_start();
require_once 'includes/db.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notre Histoire - Nathpepper</title>
    <link rel="stylesheet" href="styles/main.css">
    <link rel="stylesheet" href="styles/components.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,600;1,400&display=swap" rel="stylesheet">
    <style>
        body { background-color: #202020; color: #dddddd; font-family: 'Inter', sans-serif; }
        .story-container { max-width: 800px; margin: 0 auto; padding: 4rem 1rem; text-align: center; }
        .story-title { font-family: 'Playfair Display', serif; color: #dbc49d; font-size: 3rem; margin-bottom: 2rem; letter-spacing: 2px; }
        .story-lead { font-size: 1.2rem; color: #e4cca2; font-style: italic; line-height: 1.8; margin-bottom: 3rem; }
        .story-text { font-size: 1.05rem; line-height: 1.9; color: #cccccc; text-align: justify; margin-bottom: 2rem; }
        .story-highlight { border-left: 3px solid #dbc49d; border-right: 3px solid #dbc49d; padding: 1.5rem; margin: 3rem 0; background: #1a1b1c; color: #ffffff; font-weight: 500; }
    </style>
</head>
<body>

    <?php require_once 'includes/header.php'; ?>

    <main class="container">
        <div style="height: 120px; width: 100%;"></div>

        <section class="story-container">
            <h1 class="story-title">L'Origine de la Quête</h1>
            
            <p class="story-lead">
                "Nathpepper n'est pas née d'une simple idée marchande, mais d'une fascination absolue pour la complexité botanique du grain de poivre."
            </p>

            <p class="story-text">
                Tout a commencé lors d'une expédition sur les contreforts de la chaîne des Cardamomes. C'est là, au détour d'une plantation sauvage oubliée des cartes, que la révélation a eu lieu : le vrai poivre possède des notes de cuir, de fruits confits et de terre sacrée qu'aucun produit de masse ne pourra jamais imiter.
            </p>

            <div class="story-highlight">
                Chaque baie que nous sélectionnons est récoltée à la main, grain par grain, par des producteurs passionnés à travers le monde.
            </div>

            <p class="story-text">
                De retour, l'ambition était limpide : bâtir un comptoir d'exception pour les esthètes de la gastronomie. **Nathpepper** est le pont entre ces terroirs d'altitude introuvables et votre table. Nous ne vendons pas qu'une épice, nous partageons un fragment d'histoire, une vibration organique et un savoir-faire millénaire.
            </p>
        </section>
    </main>

    <?php require_once 'includes/footer.php'; ?>

</body>
</html>