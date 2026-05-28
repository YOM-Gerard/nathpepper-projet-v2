<?php
session_start();
require_once 'includes/db.php'; // Connexion BDD
require_once 'stripe-php/init.php'; // Charge Stripe manuellement

// 🔌 CHARGEMENT MANUEL DU .ENV SANS COMPOSER
$envPath = __DIR__ . '/.env';
if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue; // Ignore les commentaires
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
}

// 🔑 Récupération sécurisée de la clé depuis le .env qui vient d'être lu
$stripeSecretKey = $_ENV['STRIPE_SECRET_KEY'] ?? null;

if (!$stripeSecretKey) {
    die("Erreur : La clé STRIPE_SECRET_KEY est introuvable dans ton fichier .env à la racine.");
}

\Stripe\Stripe::setApiKey($stripeSecretKey);

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
        // 1. Création de la session Stripe officielle
        $checkout_session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => "Votre commande de poivres d'exception - Nathpepper",
                    ],
                    'unit_amount' => round($totalPrice * 100), // En centimes
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => 'http://localhost/nathpepper/succes.php?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => 'http://localhost/nathpepper/panier.php',
        ]);

        // 2. Insertion en BDD à l'état 'pending' (En attente de paiement)
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, delivery_phone, delivery_address, delivery_zipcode, delivery_city, stripe_session_id, status) VALUES (:user_id, :total, :phone, :address, :zipcode, :city, :stripe_id, 'pending')");
        
        $stmt->execute([
            'user_id'   => $userId,
            'total'     => $totalPrice,
            'phone'     => $phone,
            'address'   => $address,
            'zipcode'   => $zipcode,
            'city'      => $city,
            'stripe_id' => $checkout_session->id // ID unique de la session de paiement
        ]);

        // 3. Redirection immédiate vers Stripe
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