<?php 
session_start(); 
require_once 'includes/db.php'; // Connexion BDD avec la variable $pdo

$order_updated = false;

// On vérification si Stripe nous a bien renvoyé l'identifiant de la session de paiement
if (isset($_GET['session_id'])) {
    $stripe_session_id = $_GET['session_id'];

    try {
        // On passe le statut de la commande de 'pending' à 'paid' pour cette session Stripe
        $stmt = $pdo->prepare("UPDATE orders SET status = 'paid' WHERE stripe_session_id = :session_id AND status = 'pending'");
        $stmt->execute(['session_id' => $stripe_session_id]);
        
        // On vérifie si une ligne a bien été modifiée (commande trouvée)
        if ($stmt->rowCount() > 0) {
            $order_updated = true;
        }
    } catch (Exception $e) {
        // En cas d'erreur de requête, on laisse le script continuer sans tout bloquer
        error_log("Erreur de mise à jour de la commande : " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Merci pour votre commande ! - Nathpepper</title>
    <link rel="stylesheet" href="styles/main.css">
    <link rel="stylesheet" href="styles/components.css">
</head>
<body>
    <?php require_once 'includes/header.php'; ?>
    <main class="container" style="text-align: center; padding: 100px 20px;">
        <div style="height: 60px;"></div>
        <h1 style="color: #2e7d32; font-family: var(--font-primary); font-size: 3rem; margin-bottom: 1rem;">✓ Paiement Réussi !</h1>
        <p style="font-size: 1.2rem; margin-bottom: 2rem;">Merci pour votre confiance. Votre commande de poivres d'exception est en cours de préparation.</p>
        
        <?php if (isset($_GET['session_id']) && !$order_updated): ?>
            <p style="color: #666; font-size: 0.9rem; margin-bottom: 2rem;">Note : Cette commande a déjà été validée ou enregistrée.</p>
        <?php endif; ?>

        <a href="index.php" class="btn-primary">Retourner à l'accueil</a>
    </main>

    <script>
        // Le paiement a réussi, on efface proprement toutes les versions possibles du panier du navigateur
        localStorage.removeItem('cart');
        localStorage.removeItem('nathpepper_cart');
    </script>
    <?php require_once 'includes/footer.php'; ?>
</body>
</html>