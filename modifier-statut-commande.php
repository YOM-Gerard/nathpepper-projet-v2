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
            
            // Structure HTML/CSS inline aux couleurs sombres et dorées du site
            $html = <<<HTML
                <h2 style="color: #dbc49d; margin-top: 0; margin-bottom: 24px; font-size: 22px; font-weight: 600; font-family: Arial, sans-serif;">Bonjour {$nomClient},</h2>
                <p style="margin-bottom: 18px; color: #dddddd; font-family: Arial, sans-serif;">Bonne nouvelle ! Votre colis a été soigneusement préparé par notre équipe et vient d'être remis à notre transporteur partenaire ! 🚚</p>
                
                <div style="background-color: #1f1f1f; border: 1px solid #2d2d2d; border-radius: 6px; padding: 20px; margin: 25px 0; font-family: Arial, sans-serif;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="padding: 6px 0; font-size: 14px; color: #aaaaaa; font-family: Arial, sans-serif;">Numéro de commande :</td>
                            <td style="padding: 6px 0; font-size: 14px; font-weight: bold; color: #e4cca2; text-align: right; font-family: Arial, sans-serif;">#{$order_id}</td>
                        </tr>
                        <tr>
                            <td style="padding: 6px 0; font-size: 14px; color: #aaaaaa; font-family: Arial, sans-serif;">Statut logistique :</td>
                            <td style="padding: 6px 0; text-align: right; font-family: Arial, sans-serif;">
                                <span style="background-color: #e8f5e9; color: #2e7d32; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: bold; text-transform: uppercase; display: inline-block;">Expédiée</span>
                            </td>
                        </tr>
                    </table>
                </div>

                <p style="margin-bottom: 30px; color: #dddddd; font-family: Arial, sans-serif;">Vous pouvez suivre la progression de votre acheminement logistique et télécharger votre facture PDF officielle à tout moment depuis votre compte en ligne.</p>
                
                <div style="text-align: center; margin: 35px 0;">
                    <a href="http://localhost/nathpepper/mes-commandes.php" style="display: inline-block; background-color: #dbc49d; color: #1a1b1c; padding: 14px 30px; text-decoration: none; border-radius: 4px; font-weight: 700; font-size: 13px; letter-spacing: 1px; box-shadow: 0 4px 10px rgba(219,196,157,0.15); text-transform: uppercase; font-family: Arial, sans-serif;">Suivre mon colis</a>
                </div>
                
                <p style="margin-top: 30px; border-top: 1px solid #2d2d2d; padding-top: 20px; font-size: 14px; color: #8a8a8a; font-family: Arial, sans-serif;">Nous vous remercions pour votre confiance.<br><br>Sincèrement,<br><strong style="color: #dbc49d;">L'équipe Nathpepper</strong></p>
HTML;

            // Envoi effectif de l'e-mail
            envoyerEmailNathpepper($info['email'], $sujet, $html);
        }
    }

    echo json_encode(['success' => true, 'message' => 'Statut mis à jour et e-mail envoyé avec succès !']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur BDD : ' . $e->getMessage()]);
}