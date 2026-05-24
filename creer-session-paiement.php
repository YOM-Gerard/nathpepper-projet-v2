<?php
session_start();

// 1. Inclure la bibliothèque Stripe téléchargée et la connexion BDD
require_once 'stripe-php/init.php';
require_once 'includes/db.php'; // On importe la variable de connexion $pdo

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
    $total_amount = 0; // On initialise le calcul du montant total pour notre BDD
    
    // On prépare la liste des produits au format exigé par Stripe
    foreach ($items as $item) {
        $price = floatval($item['price'] ?? $item['prix'] ?? 0);
        $quantity = intval($item['quantity'] ?? $item['qte'] ?? 1);
        
        // Accumulation du total général (Ex: 5,09 * 2 = 10,18)
        $total_amount += ($price * $quantity);

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
                'unit_amount' => round($price * 100),
            ],
            'quantity' => $quantity,
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

    // ==========================================
    // 🗄️ SAUVEGARDE EN BASE DE DONNÉES (PENDING)
    // ==========================================
    
    // MODIFICATION : On vérifie si l'acheteur est connecté à un compte utilisateur
    $user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : null;

    // On démarre une transaction PDO pour s'assurer que tout s'enregistre ou rien du tout (évite les bugs)
    $pdo->beginTransaction();

    // Insertion dans la table 'orders' avec la colonne user_id incluse
    $stmtOrder = $pdo->prepare("INSERT INTO orders (stripe_session_id, total_amount, status, user_id) VALUES (:stripe_id, :total, 'pending', :user_id)");
    $stmtOrder->execute([
        'stripe_id' => $session->id,
        'total'     => $total_amount,
        'user_id'   => $user_id // Vaudra l'ID si connecté, ou NULL si achat invité
    ]);
    
    // On récupère l'ID numérique généré par MySQL pour cette commande
    $order_id = $pdo->lastInsertId();

    // 2. Insertion de chaque produit dans la table de détails 'order_items'
    $stmtItem = $pdo->prepare("INSERT INTO order_items (order_id, product_id, product_name, price, quantity) VALUES (:order_id, :product_id, :p_name, :price, :qty)");
    
    foreach ($items as $item) {
        $stmtItem->execute([
            'order_id'   => $order_id,
            'product_id' => intval($item['id'] ?? 0),
            'p_name'     => $item['name'] ?? $item['nom'] ?? 'Poivre',
            'price'      => floatval($item['price'] ?? $item['prix'] ?? 0),
            'qty'        => intval($item['quantity'] ?? $item['qte'] ?? 1)
        ]);
    }

    // On valide définitivement l'écriture des deux tables en BDD
    $pdo->commit();

    // On envoie enfin la réponse JSON avec l'URL Stripe au JavaScript
    echo json_encode(['url' => $session->url]);

} catch (Exception $e) {
    // Si l'écriture en BDD a planté au milieu, on annule tout pour ne pas avoir de données corrompues
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}