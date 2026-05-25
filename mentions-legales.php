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
        
        <h2 style="font-family: 'Playfair Display', serif; margin-top: 2rem; color: #1a1b1c;">1. Édition du site</h2>
        <p>Le site internet <strong>nathpepper.com</strong> est édité par Nathpepper, immatriculée au Registre du Commerce et des Sociétés (RCS) [Numéro SIRET à ajouter] et dont le siège social est situé à [Ton Adresse].</p>
        <p>Contact : contact@nathpepper.com</p>

        <h2 style="font-family: 'Playfair Display', serif; margin-top: 2rem; color: #1a1b1c;">2. Hébergement</h2>
        <p>Le site sera hébergé par : [Nom de ton futur hébergeur, ex: o2switch], situé au [Adresse de l'hébergeur].</p>

        <h2 style="font-family: 'Playfair Display', serif; margin-top: 2rem; color: #1a1b1c;">3. Propriété intellectuelle</h2>
        <p>L'ensemble des textes, graphismes, images, logos et icônes présents sur le site sont la propriété exclusive de Nathpepper. Toute reproduction, représentation ou modification, en tout ou partie, est strictement interdite.</p>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>