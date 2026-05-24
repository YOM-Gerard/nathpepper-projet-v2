<?php
session_start();
require_once 'includes/db.php';

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
    // Mise à jour du statut en BDD
    $stmt = $pdo->prepare("UPDATE orders SET status = :status WHERE id = :id");
    $stmt->execute([
        'status' => $new_status,
        'id' => $order_id
    ]);

    echo json_encode(['success' => true, 'message' => 'Statut mis à jour avec succès !']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur BDD : ' . $e->getMessage()]);
}