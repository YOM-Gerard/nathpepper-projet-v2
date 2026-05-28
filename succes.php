<?php 
session_start(); 
require_once 'includes/db.php'; // Connexion BDD avec la variable $pdo

$order_updated = false;
$invoice_created = null;

// On vérification si Stripe nous a bien renvoyé l'identifiant de la session de paiement
if (isset($_GET['session_id'])) {
    $stripe_session_id = $_GET['session_id'];

    try {
        // 1. On cherche d'abord si la commande existe et quel est son état actuel
        $checkStmt = $pdo->prepare("SELECT id, invoice_number FROM orders WHERE stripe_session_id = :session_id");
        $checkStmt->execute(['session_id' => $stripe_session_id]);
        $order = $checkStmt->fetch(PDO::FETCH_ASSOC);

        if ($order) {
            // Si la commande n'a pas encore de numéro de facture (premier chargement du succès)
            if (empty($order['invoice_number'])) {
                
                // 🔢 ALGORITHME DE FACTURATION CONTINUE :
                // On cherche le numéro de facture le plus élevé de l'année en cours
                $year = date('Y');
                $prefix = "FAC-" . $year . "-"; // Exemple: FAC-2026-
                
                $invoiceStmt = $pdo->prepare("SELECT invoice_number FROM orders WHERE invoice_number LIKE :prefix ORDER BY invoice_number DESC LIMIT 1");
                $invoiceStmt->execute(['prefix' => $prefix . '%']);
                $lastInvoice = $invoiceStmt->fetch(PDO::FETCH_ASSOC);

                if ($lastInvoice) {
                    // On extrait le numéro (ex: si FAC-2026-0034 -> on prend 34)
                    $lastNumber = (int)str_replace($prefix, '', $lastInvoice['invoice_number']);
                    $nextNumber = $lastNumber + 1;
                } else {
                    // Si c'est la toute première facture de l'année
                    $nextNumber = 1;
                }

                // On formate le numéro pour qu'il ait toujours 4 chiffres (ex: 1 devient 0001, 34 devient 0034)
                $invoice_created = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

                // 2. On met à jour la commande : statut 'paid' ET le numéro de facture unique et continu
                $stmt = $pdo->prepare("UPDATE orders SET status = 'paid', invoice_number = :invoice_number WHERE stripe_session_id = :session_id AND status = 'pending'");
                $stmt->execute([
                    'invoice_number' => $invoice_created,
                    'session_id'     => $stripe_session_id
                ]);
            } else {
                // Si l'utilisateur rafraîchit la page, la facture existe déjà, on la récupère juste pour l'affichage
                $invoice_created = $order['invoice_number'];
            }

            $order_updated = true;

            // 🔒 SÉCURITÉ SERVEUR : On nettoie les variables de session liées au panier
            if (isset($_SESSION['cart'])) { unset($_SESSION['cart']); }
            if (isset($_SESSION['panier'])) { unset($_SESSION['panier']); }
            if (isset($_SESSION['total_price'])) { unset($_SESSION['total_price']); }
        }
    } catch (Exception $e) {
        error_log("Erreur de mise à jour de la facture : " . $e->getMessage());
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
        <p style="font-size: 1.2rem; margin-bottom: 1rem;">Merci pour votre confiance. Votre commande de poivres d'exception est en cours de préparation.</p>
        
        <?php if (!empty($invoice_created)): ?>
            <p style="font-size: 1.1rem; font-weight: 600; color: #1a1b1c; margin-bottom: 2rem;">
                Numéro de facture officielle : <span style="font-family: monospace; background: #e8e2d5; padding: 4px 8px; border-radius: 4px;"><?php echo $invoice_created; ?></span>
            </p>
        <?php endif; ?>

        <?php if (isset($_GET['session_id']) && !$order_updated): ?>
            <p style="color: #666; font-size: 0.9rem; margin-bottom: 2rem;">Note : Cette commande a déjà été validée ou enregistrée.</p>
        <?php endif; ?>

        <a href="index.php" class="btn-primary">Retourner à l'accueil</a>
    </main>

    <script>
        // 🧹 On vide TOUTES les clés possibles pour être sûr à 100 % que le panier tombe à 0 côté client
        localStorage.removeItem('cart');
        localStorage.removeItem('nathpepper_cart');
        localStorage.removeItem('shopping_cart');
        localStorage.removeItem('panier');
        
        // On dit au navigateur de rafraîchir l'affichage du menu s'il existe
        if (typeof updateCartCount === 'function') { updateCartCount(); }
    </script>
    
    <?php require_once 'includes/footer.php'; ?>
</body>
</html>