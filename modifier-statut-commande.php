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
            
            // Corps de l'e-mail avec charte graphique Nathpepper
            $html = "
                <h2>Bonjour " . $nomClient . ",</h2>
                <p>Votre colis a été soigneusement préparé par notre équipe et vient d'être remis au transporteur ! 🚚</p>
                <p>Votre commande <strong>#" . $order_id . "</strong> est désormais en route vers votre adresse.</p>
                <p>Vous pouvez suivre l'évolution de votre colis et télécharger votre facture à tout moment depuis votre espace client.</p>
                <br>
                <p style='text-align: center;'>
                    <a href='http://localhost/nathpepper/mes-commandes.php' style='display: inline-block; background-color: #b71c1c; color: white; padding: 12px 20px; text-decoration: none; border-radius: 4px; font-weight: bold;'>Voir mes commandes</a>
                </p>
                <br>
                <p>À très bientôt,<br>L'équipe Nathpepper</p>
            ";

            // Envoi effectif de l'e-mail
            envoyerEmailNathpepper($info['email'], $sujet, $html);
        }
    }

    echo json_encode(['success' => true, 'message' => 'Statut mis à jour et e-mail envoyé avec succès !']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur BDD : ' . $e->getMessage()]);
}