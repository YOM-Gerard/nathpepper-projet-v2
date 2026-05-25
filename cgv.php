<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conditions Générales de Vente - Nathpepper</title>
    <link rel="stylesheet" href="styles/main.css">
    <link rel="stylesheet" href="styles/components.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main class="container" style="max-width: 800px; margin: 0 auto; padding: 140px 20px 60px 20px; font-family: 'Inter', sans-serif; color: #333; line-height: 1.8;">
        <h1 style="font-family: 'Playfair Display', serif; color: var(--primary-color); margin-bottom: 2rem; font-size: 2.2rem;">Conditions Générales de Vente</h1>
        
        <h2 style="font-family: 'Playfair Display', serif; margin-top: 2rem; color: #1a1b1c;">1. Objet</h2>
        <p>Les présentes Conditions Générales de Vente (CGV) régissent les relations contractuelles entre Nathpepper et toute personne effectuant un achat sur le site internet nathpepper.com.</p>

        <h2 style="font-family: 'Playfair Display', serif; margin-top: 2rem; color: #1a1b1c;">2. Produits et Tarifs</h2>
        <p>Les poivres d'exception proposés sont décrits avec la plus grande précision possible. Les prix affichés sont en Euros (€) toutes taxes comprises (TTC).</p>

        <h2 style="font-family: 'Playfair Display', serif; margin-top: 2rem; color: #1a1b1c;">3. Paiement</h2>
        <p>Le règlement des achats s'effectue de manière sécurisée via la plateforme de paiement **Stripe** par carte bancaire.</p>

        <h2 style="font-family: 'Playfair Display', serif; margin-top: 2rem; color: #1a1b1c;">4. Droit de rétractation</h2>
        <p>Conformément à la législation européenne, le consommateur dispose d'un délai de <strong>14 jours</strong> à compter de la réception de ses produits pour exercer son droit de rétractation, sans avoir à justifier de motifs ni à payer de pénalités (les frais de retour restant à sa charge).</p>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>