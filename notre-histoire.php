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
    <link rel="stylesheet" href="styles/responsive.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,600;1,400&display=swap" rel="stylesheet">
    <style>
        /* Fond de page blanc écru signature */
        body { background-color: #fbf9f6 !important; color: #1a1b1c; font-family: 'Inter', sans-serif; }
        
        .story-container { max-width: 800px; margin: 0 auto; padding: 4rem 1.5rem; text-align: center; }
        
        /* Titre en noir profond très chic */
        .story-title { font-family: 'Playfair Display', serif; color: #1a1b1c; font-size: 3rem; margin-bottom: 2rem; letter-spacing: 2px; }
        
        /* Accroche dans une teinte chaude d'épice douce */
        .story-lead { font-size: 1.2rem; color: #8d6e63; font-style: italic; line-height: 1.8; margin-bottom: 3rem; }
        
        /* Texte de lecture sombre pour un confort optimal sur le fond écru */
        .story-text { font-size: 1.05rem; line-height: 1.9; color: #333333; text-align: justify; margin-bottom: 2rem; }
        
        /* Encadré de surbrillance épuré */
        .story-highlight { 
            border-left: 3px solid #1a1b1c; 
            border-right: 3px solid #1a1b1c; 
            padding: 1.5rem; 
            margin: 3rem 0; 
            background: #f2ede4; 
            color: #1a1b1c; 
            font-weight: 500; 
            border-radius: 2px;
        }

        /* Le header reste blanc pur comme sur produits.php */
        .header {
            background-color: #ffffff !important;
            border-bottom: 1px solid #eae5dc !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.02) !important;
        }

        /* ─── AJUSTEMENTS ERGONOMIQUES MOBILE (SOUS 768PX) ─── */
        @media (max-width: 768px) {
            .story-title { font-size: 2.2rem !important; margin-bottom: 1.5rem !important; }
            .story-lead { font-size: 1.1rem !important; margin-bottom: 2rem !important; }
            .story-text { text-align: left !important; font-size: 1rem !important; line-height: 1.8 !important; }
            .story-highlight { padding: 1.2rem !important; margin: 2rem 0 !important; font-size: 0.95rem !important; }
        }
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