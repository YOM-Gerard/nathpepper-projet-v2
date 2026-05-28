<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mentions Légales - Nathpepper</title>
    <link rel="stylesheet" href="styles/main.css">
    <link rel="stylesheet" href="styles/components.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main class="container" style="max-width: 800px; margin: 0 auto; padding: 140px 20px 60px 20px; font-family: 'Inter', sans-serif; color: #333; line-height: 1.8;">
        <h1 style="font-family: 'Playfair Display', serif; color: var(--primary-color); margin-bottom: 2rem; font-size: 2.5rem;">Mentions Légales</h1>
        
        <!-- 1. ÉDITION DU SITE -->
        <h2 style="font-family: 'Playfair Display', serif; margin-top: 2rem; color: #1a1b1c;">1. Édition du site</h2>
        <p>
            Le site internet <strong>nathpepper.com</strong> est édité par l'entreprise Nathpepper, dont le siège social est situé au 5 rue des mésanges, 95190 Goussainville.
        </p>
        <p>
            Immatriculée au Registre du Commerce et des Sociétés (RCS) de Pontoise sous le numéro SIREN : 999 449 994 (Numéro SIRET : 99944999400014).
        </p>
        <p>Contact : <a href="mailto:contact@nathpepper.com" style="color: #333;">contact@nathpepper.com</a></p>

        <!-- 2. HÉBERGEMENT (Configuré par défaut avec o2switch) -->
        <h2 style="font-family: 'Playfair Display', serif; margin-top: 2rem; color: #1a1b1c;">2. Hébergement</h2>
        <p>
            Le site internet est hébergé par la société <strong>o2switch</strong>, dont le siège social est situé au Chem. des Pardiaux, 63000 Clermont-Ferrand (Téléphone : +33 (0)4 44 44 60 40).
        </p>

        <!-- 3. PROPRIÉTÉ INTELLECTUELLE -->
        <h2 style="font-family: 'Playfair Display', serif; margin-top: 2rem; color: #1a1b1c;">3. Propriété intellectuelle</h2>
        <p>L'ensemble des textes, graphismes, images, logos et icônes présents sur le site sont la propriété exclusive de Nathpepper. Toute reproduction, représentation ou modification, en tout ou partie, est strictement interdite.</p>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>