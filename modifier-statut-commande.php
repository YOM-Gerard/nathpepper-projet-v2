<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/mailer.php';

header('Content-Type: application/json');

// SÉCURITÉ : Vérification de l'admin
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Non authentifié.']);
    exit();
}

$stmtCheck = $pdo->prepare("SELECT is_admin FROM users WHERE id = :id");
$stmtCheck->execute(['id' => $_SESSION['user_id']]);
$user = $stmtCheck->fetch(PDO::FETCH_ASSOC);

if (!$user || $user['is_admin'] != 1) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Accès refusé.']);
    exit();
}

// Récupération des données envoyées par JavaScript
$data = json_decode(file_get_contents('php://input'), true);
$order_id = isset($data['order_id']) ? intval($data['order_id']) : 0;
$new_status = isset($data['status']) ? trim($data['status']) : '';

// Validation des statuts autorisés
$allowed_statuses = ['pending', 'paid', 'processing', 'shipped', 'delivered'];

if ($order_id <= 0 || !in_array($new_status, $allowed_statuses)) {
    echo json_encode(['success' => false, 'message' => 'Données invalides.']);
    exit();
}

try {
    // 1. Mise à jour du statut en BDD
    $stmt = $pdo->prepare("UPDATE orders SET status = :status WHERE id = :id");
    $stmt->execute([
        'status' => $new_status,
        'id' => $order_id
    ]);

    // 2. DÉCLENCHEMENT DU MAIL AUTOMATIQUE À L'EXPÉDITION
    if ($new_status === 'shipped') {
        // Récupérer les informations du client lié à la commande
        $stmtInfo = $pdo->prepare("
            SELECT orders.id, users.email, users.name 
            FROM orders 
            LEFT JOIN users ON orders.user_id = users.id 
            WHERE orders.id = :id
        ");
        $stmtInfo->execute(['id' => $order_id]);
        $info = $stmtInfo->fetch(PDO::FETCH_ASSOC);

        if ($info && !empty($info['email'])) {
            $nomClient = htmlspecialchars($info['name']);
            $sujet = "Bonne nouvelle ! Votre commande Nathpepper #" . $order_id . " a ete expediee !";
            
            // Corps de l'e-mail optimisé avec du pur CSS en ligne pour un rendu e-commerce premium
            $html = "
                <h2 style='color: #b71c1c; margin-top: 0; margin-bottom: 20px; font-size: 20px; border-bottom: 1px solid #f5f5f5; padding-bottom: 10px; font-family: Arial, sans-serif;'>Bonjour " . $nomClient . ",</h2>
                <p style='margin-bottom: 15px; font-family: Arial, sans-serif;'>Bonne nouvelle ! Votre colis a été soigneusement préparé par nos experts et vient d'être remis à notre transporteur partenaire ! 🚚</p>
                <p style='margin-bottom: 15px; font-family: Arial, sans-serif;'>Votre commande référence <strong style='color: #b71c1c;'>#" . $order_id . "</strong> est désormais officiellement en route vers votre adresse de livraison.</p>
                <p style='margin-bottom: 30px; font-family: Arial, sans-serif;'>Vous pouvez suivre la progression de votre acheminement logistique et télécharger votre facture PDF officielle à tout moment depuis votre tableau de bord personnel.</p>
                
                <div style='text-align: center; margin: 30px 0;'>
                    <a href='http://localhost/nathpepper/mes-commandes.php' style='display: inline-block; background-color: #b71c1c; color: #ffffff; padding: 14px 28px; text-decoration: none; border-radius: 4px; font-weight: bold; font-size: 14px; letter-spacing: 0.5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); text-transform: uppercase; font-family: Arial, sans-serif;'>Suivre mon colis</a>
                </div>
                
                <p style='margin-top: 25px; border-top: 1px solid #f5f5f5; padding-top: 15px; font-family: Arial, sans-serif;'>Nous vous remercions pour votre confiance et votre fidélité.<br><br>Sincèrement,<br><strong>L'équipe Nathpepper</strong></p>
            ";

            // Envoi effectif (et écriture du fichier HTML simulé)
            envoyerEmailNathpepper($info['email'], $sujet, $html);
        }
    }

    echo json_encode(['success' => true, 'message' => 'Statut mis à jour et e-mail envoyé avec succès !']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur BDD : ' . $e->getMessage()]);
}