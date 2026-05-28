<?php
session_start();
require_once 'includes/db.php'; // Connexion BDD
require_once 'stripe-php/init.php'; // Charge Stripe manuellement

// 🔌 SCRIPT LÉGER DE CHARGEMENT DU .ENV
$stripeSecretKey = null;
$envPath = __DIR__ . '/.env';

if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value, "\"'\t ");
            if ($name === 'STRIPE_SECRET_KEY') {
                $stripeSecretKey = $value;
                break;
            }
        }
    }
}

if (!$stripeSecretKey) {
    $backupPath = dirname(__DIR__) . '/.env';
    if (file_exists($backupPath)) {
        $lines = file($backupPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) continue;
            if (strpos($line, '=') !== false) {
                list($name, $value) = explode('=', $line, 2);
                if (trim($name) === 'STRIPE_SECRET_KEY') {
                    $stripeSecretKey = trim($value, "\"'\t ");
                    break;
                }
            }
        }
    }
}

if (!$stripeSecretKey) {
    die("Erreur : Impossible de lire la variable STRIPE_SECRET_KEY dans le .env");
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
        // 🛡️ SÉCURITÉ ANTI-DOUBLON SERVEUR :
        // On supprime les anciennes tentatives de cet utilisateur restées 'pending' (non payées)
        // avant d'ouvrir la nouvelle session de paiement.
        $clearStmt = $pdo->prepare("DELETE FROM orders WHERE user_id = :user_id AND status = 'pending'");
        $clearStmt->execute(['user_id' => $userId]);

        // 🌐 DÉTECTION AUTOMATIQUE DE L'URL DE TON SITE (Local ou en ligne)
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'];
        
        // On récupère le dossier actuel (ex: /nathpepper/ ou /nathpepper-projet-v2/)
        $currentDir = dirname($_SERVER['SCRIPT_NAME']);
        $currentDir = rtrim($currentDir, '/\\') . '/';
        
        // Construction de l'adresse de base automatique
        $baseUrl = $protocol . $host . $currentDir;

        // 1. Création de la session Stripe avec les URLs dynamiques
        $checkout_session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => "Votre commande de poivres d'exception - Nathpepper",
                    ],
                    'unit_amount' => round($totalPrice * 100),
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $baseUrl . 'succes.php?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $baseUrl . 'panier.php',
        ]);

        // 2. Insertion en BDD
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, delivery_phone, delivery_address, delivery_zipcode, delivery_city, stripe_session_id, status) VALUES (:user_id, :total, :phone, :address, :zipcode, :city, :stripe_id, 'pending')");
        
        $stmt->execute([
            'user_id'   => $userId,
            'total'     => $totalPrice,
            'phone'     => $phone,
            'address'   => $address,
            'zipcode'   => $zipcode,
            'city'      => $city,
            'stripe_id' => $checkout_session->id
        ]);

        // 3. Redirection vers Stripe
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