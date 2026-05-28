<?php
session_start();
require_once 'includes/db.php'; // Connexion BDD
require_once 'stripe-php/init.php'; // Charge Stripe manuellement

// 🔌 SCRIPT LÉGER DE CHARGEMENT DU .ENV (Recherche à la racine du projet)
$stripeSecretKey = null;
$envPath = __DIR__ . '/.env';

if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // On ignore les lignes de commentaires
        if (strpos(trim($line), '#') === 0) continue;
        
        // On cherche la ligne contenant le signe "="
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);
            
            // On nettoie les éventuels guillemets autour de la clé
            $value = trim($value, '"\'');
            
            if ($name === 'STRIPE_SECRET_KEY') {
                $stripeSecretKey = $value;
                break;
            }
        }
    }
}

// 🚨 Sécurité : Si le fichier n'est pas trouvé à la racine, on cherche dans le sous-dossier supérieur
if (!$stripeSecretKey) {
    $backupPath = dirname(__DIR__) . '/.env';
    if (file_exists($backupPath)) {
        $lines = file($backupPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) continue;
            if (strpos($line, '=') !== false) {
                list($name, $value) = explode('=', $line, 2);
                if (trim($name) === 'STRIPE_SECRET_KEY') {
                    $stripeSecretKey = trim($value, '"\'');
                    break;
                }
            }
        }
    }
}

if (!$stripeSecretKey) {
    die("Erreur : Impossible de lire la variable STRIPE_SECRET_KEY. Vérifie qu'elle est bien écrite sous la forme STRIPE_SECRET_KEY=sk_test_... dans ton fichier .env à la racine.");
}

// Initialisation officielle de l'API Stripe
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
        // 1. Création de la session de paiement Stripe
        $checkout_session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => "Votre commande de poivres d'exception - Nathpepper",
                    ],
                    'unit_amount' => round($totalPrice * 100), // En centimes pour Stripe
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            // 🔄 Trouve cette section dans le bloc \Stripe\Checkout\Session::create et remplace :
            'success_url' => 'http://localhost/nathpepper/succes.php?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => 'http://localhost/nathpepper/panier.php',

        // 2. Enregistrement de la commande en BDD à l'état 'pending'
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

        // 3. Redirection automatique vers le formulaire sécurisé Stripe
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