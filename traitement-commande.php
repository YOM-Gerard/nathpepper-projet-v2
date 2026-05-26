<?php
session_start();
require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $totalPrice = $_POST['total_price'] ?? 0;
    $phone = trim($_POST['delivery_phone'] ?? '');
    $address = trim($_POST['delivery_address'] ?? '');
    $zipcode = trim($_POST['delivery_zipcode'] ?? '');
    $city = trim($_POST['delivery_city'] ?? '');

    if (empty($phone) || empty($address) || empty($zipcode) || empty($city)) {
        header('Location: commande.php');
        exit();
    }

    try {
        // 🛠️ CORRECTION : Utilisation exacte de 'total_amount' comme vu sur ta structure de base de données
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, delivery_phone, delivery_address, delivery_zipcode, delivery_city) VALUES (:user_id, :total, :phone, :address, :zipcode, :city)");
        
        $stmt->execute([
            'user_id' => $userId,
            'total'   => $totalPrice, // Le montant récupéré du formulaire
            'phone'   => $phone,
            'address' => $address,
            'zipcode' => $zipcode,
            'city'    => $city
        ]);

        // Message de succès pour la page Mon Compte
        $_SESSION['success_register'] = "🎉 Votre commande a été enregistrée avec succès et est en cours de préparation !";
        
        header('Location: mon-compte.php');
        exit();

    } catch (Exception $e) {
        echo "Erreur d'enregistrement de commande : " . $e->getMessage();
    }
} else {
    header('Location: produits.php');
    exit();
}