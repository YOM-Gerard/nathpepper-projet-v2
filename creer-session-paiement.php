<?php
session_start();

// 1. Inclure la bibliothèque Stripe téléchargée
require_once 'stripe-php/init.php';

// 2. Ta clé secrète directement injectée pour le local (Remplace bien sk_test_... par TA vraie clé Stripe)
$stripe_secret = 'sk_test_REMPLACE_MOI_PAR_TA_VRAIE_CLE';

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
        // Sécurité : on s'assure que l'URL de l'image est valide ou absolue pour Stripe
        $image_url = $item['image'];
        if (strpos($image_url, 'http') !== 0) {
            $image_url = 'http://localhost/nathpepper/' . ltrim($image_url, '/');
        }

        $line_items[] = [
            'price_data' => [
                'currency' => 'eur',
                'product_data' => [
                    'name' => $item['name'],
                    'images' => [$image_url], // URL propre pour Stripe
                ],
                'unit_amount' => round($item['price'] * 100), // Stripe calcule en centimes (15.00 € = 1500)
            ],
            'quantity' => $item['quantity'],
        ];
    }

    // 3. Création de la session de paiement Stripe Checkout
    $session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => $line_items,
        'mode' => 'payment',
        // Les URLs où renvoyer le client après le paiement
        'success_url' => 'http://localhost/nathpepper/succes.php?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url' => 'http://localhost/nathpepper/panier.php',
    ]);

    // On renvoie l'URL de la page Stripe au JavaScript
    echo json_encode(['url' => $session->url]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}