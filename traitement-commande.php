<?php
session_start();
require_once 'includes/db.php'; // Connexion BDD

// 📦 On charge Stripe manuellement depuis ton dossier existant !
require_once 'stripe-php/init.php'; 

// 🔑 METS TA CLÉ SECRÈTE STRIPE DE TEST (sk_test_...) ICI :
\Stripe\Stripe::setApiKey('sk_test_51... METS_TA_CLE_ICI ...');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $totalPrice = floatval($_POST['total_price'] ?? 0);
    $phone = trim($_POST['delivery_phone'] ?? '');
    $address = trim($_POST['delivery_address'] ?? '');
    $zipcode = trim($_POST['delivery_zipcode'] ?? '');
    $city = trim($_POST['delivery_city'] ?? '');

    if (empty($phone) || empty($address) || empty($zipcode) || empty($city) || $totalPrice <= 0) {
        header('Location: commande.php');
        exit();
    }

    try {
        // 1. On crée la vraie session de paiement Stripe
        $checkout_session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => "Votre commande de poivres d'exception - Nathpepper",
                    ],
                    'unit_amount' => round($totalPrice * 100), // En centimes pour Stripe (ex: 15.27 € -> 1527)
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            // On renvoie vers ta page succes.php avec l'ID de session Stripe
            'success_url' => 'http://localhost/Nathpepper/succes.php?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => 'http://localhost/Nathpepper/panier.php',
        ]);

        // 2. On insère la commande en BDD à l'état 'pending' (En attente de paiement)
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, delivery_phone, delivery_address, delivery_zipcode, delivery_city, stripe_session_id, status) VALUES (:user_id, :total, :phone, :address, :zipcode, :city, :stripe_id, 'pending')");
        
        $stmt->execute([
            'user_id'   => $userId,
            'total'     => $totalPrice,
            'phone'     => $phone,
            'address'   => $address,
            'zipcode'   => $zipcode,
            'city'      => $city,
            'stripe_id' => $checkout_session->id // On lie l'ID Stripe unique pour sécuriser l'achat
        ]);

        // 3. Redirection automatique vers le formulaire de carte bleue Stripe
        header("HTTP/1.1 303 See Other");
        header('Location: ' . $checkout_session->url);
        exit();

    } catch (Exception $e) {
        echo "Erreur Stripe : " . $e->getMessage();
    }
} else {
    header('Location: produits.php');
    exit();
}