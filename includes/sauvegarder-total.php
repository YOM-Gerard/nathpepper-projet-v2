<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Récupération des données JSON envoyées par JavaScript
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['total'])) {
    // On enregistre le montant net calculé dans la session de l'acheteur
    $_SESSION['cart_total'] = floatval($data['total']);
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'No total provided']);
}