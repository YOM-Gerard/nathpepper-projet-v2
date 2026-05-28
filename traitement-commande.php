<?php
session_start();
require_once 'includes/db.php'; // Connexion BDD avec $pdo

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $totalPrice = floatval($_POST['total_price'] ?? 0);
    $phone = trim($_POST['delivery_phone'] ?? '');
    $address = trim($_POST['delivery_address'] ?? '');
    $zipcode = trim($_POST['delivery_zipcode'] ?? '');
    $city = trim($_POST['delivery_city'] ?? '');

    if (empty($phone) || empty($address) || empty($zipcode) || empty($city)) {
        header('Location: commande.php');
        exit();
    }

    try {
        // On insère la commande en BDD à l'état 'pending' (En attente)
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, delivery_phone, delivery_address, delivery_zipcode, delivery_city, status) VALUES (:user_id, :total, :phone, :address, :zipcode, :city, 'pending')");
        
        $stmt->execute([
            'user_id' => $userId,
            'total'   => $totalPrice,
            'phone'   => $phone,
            'address' => $address,
            'zipcode' => $zipcode,
            'city'    => $city
        ]);

        // On récupère le numéro de la commande qui vient d'être créée
        $orderId = $pdo->lastInsertId();

        // 🚀 Redirection temporaire et sécurisée vers la page de succès
        header('Location: succes.php?order_id=' . $orderId);
        exit();

    } catch (Exception $e) {
        echo "Erreur d'enregistrement de commande : " . $e->getMessage();
    }
} else {
    header('Location: produits.php');
    exit();
}