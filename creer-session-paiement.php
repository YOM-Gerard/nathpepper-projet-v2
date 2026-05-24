<?php
session_start();

// 1. Inclure la bibliothèque Stripe téléchargée
require_once 'stripe-php/init.php';

// 2. Décodeur maison de fichier .env (Version blindée contre les espaces/guillemets)
if (file_exists(__DIR__ . '/.env')) {
    $lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue; // Ignore les commentaires
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            
            // Nettoyage complet : on enlève les espaces, les guillemets simples ou doubles
            $clean_name = trim($name);
            $clean_value = trim($value);
            $clean_value = trim($clean_value, '"\''); 
            
            $_ENV[$clean_name] = $clean_value;
        }
    }
}

// 3. Récupération sécurisée de la clé
$stripe_secret = $_ENV['STRIPE_SECRET_KEY'] ?? null;

if (!$stripe_secret) {
    http_response_code(500);
    die(json_encode(['error' => 'Cle Stripe introuvable dans le fichier .env local.']));
}

\Stripe\Stripe::setApiKey($stripe_secret);

header('Content-Type: application/json');
try {
    // On récupère les données du panier envoyées en JSON par le JavaScript
    $jsonObj = json_decode(file_get_contents('php://input'), true);
    $items = $jsonObj['items'] ?? [];

    if (empty($items)) {
        throw new Exception('Le panier est vide.');
    }

    $line_items = [];
    
    // On prépare la liste des produits au format exigé par Stripe
    foreach ($items as $item) {
        $image_url = $item['image'] ?? 'images/default-pepper.jpg';
        if (strpos($image_url, 'http') !== 0) {
            $image_url = 'http://localhost/nathpepper/' . ltrim($image_url, '/');
        }

        $line_items[] = [
            'price_data' => [
                'currency' => 'eur',
                'product_data' => [
                    'name' => $item['name'] ?? $item['nom'] ?? 'Poivre',
                    'images' => [$image_url],
                ],
                'unit_amount' => round(($item['price'] ?? $item['prix'] ?? 0) * 100),
            ],
            'quantity' => $item['quantity'] ?? $item['qte'] ?? 1,
        ];
    }

    // Création de la session de paiement Stripe Checkout
    $session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => $line_items,
        'mode' => 'payment',
        'success_url' => 'http://localhost/nathpepper/succes.php?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url' => 'http://localhost/nathpepper/panier.php',
    ]);

    echo json_encode(['url' => $session->url]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}