<?php
session_start();
require_once 'includes/db.php'; // Connexion à ta base via $pdo

header('Content-Type: application/json');

// 1. SÉCURITÉ : Le client doit être connecté pour commander
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false, 
        'message' => 'Veuillez vous connecter à votre compte Nathpepper pour finaliser votre commande.'
    ]);
    exit();
}

// 2. RÉCUPÉRATION DU PANIER ENVOYÉ PAR JAVASCRIPT
$data = json_decode(file_get_contents('php://input'), true);
$basket = isset($data['basket']) ? $data['basket'] : [];

if (empty($basket)) {
    echo json_encode([
        'success' => false, 
        'message' => 'Votre panier est vide ou expiré.'
    ]);
    exit();
}

try {
    // 3. PHASE DE VÉRIFICATION CHIRURGICALE DES STOCKS EN BDD
    foreach ($basket as $item) {
        $product_id = intval($item['id']);
        $quantity_requested = intval($item['quantity']);
        $product_name = htmlspecialchars($item['name']);

        // On récupère le stock réel actuel en BDD
        $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = :id");
        $stmt->execute(['id' => $product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            echo json_encode([
                'success' => false, 
                'message' => "Le produit '{$product_name}' n'est plus disponible dans notre catalogue."
            ]);
            exit();
        }

        // Si le client demande plus que ce qu'on a en réserve : ON BLOQUE
        if ($product['stock'] < $quantity_requested) {
            if (intval($product['stock']) === 0) {
                echo json_encode([
                    'success' => false, 
                    'message' => "Désolé, le '{$product_name}' vient tout juste de tomber en rupture de stock."
                ]);
            } else {
                echo json_encode([
                    'success' => false, 
                    'message' => "Désolé, il ne reste plus que {$product['stock']} unité(s) en stock pour le '{$product_name}'."
                ]);
            }
            exit(); // On arrête le script ici pour empêcher la création de la commande
        }
    }

    // 4. LES STOCKS SONT OK : CALCUL DU TOTAL ET CRÉATION DE LA COMMANDE (statut pending)
    $total_amount = 0;
    foreach ($basket as $item) {
        $total_amount += floatval($item['price']) * intval($item['quantity']);
    }

    // Insertion du parent dans la table 'orders'
    $stmtOrder = $pdo->prepare("
        INSERT INTO orders (user_id, total_amount, status, created_at) 
        VALUES (:user_id, :total, 'pending', NOW())
    ");
    $stmtOrder->execute([
        'user_id' => intval($_SESSION['user_id']),
        'total'   => $total_amount
    ]);
    $order_id = $pdo->lastInsertId();

    // Insertion du détail dans la table 'order_items'
    foreach ($basket as $item) {
        $stmtItem = $pdo->prepare("
            INSERT INTO order_items (order_id, product_id, product_name, price, quantity) 
            VALUES (:order_id, :product_id, :name, :price, :qty)
        ");
        $stmtItem->execute([
            'order_id'   => $order_id,
            'product_id' => intval($item['id']),
            'name'       => $item['name'],
            'price'      => floatval($item['price']),
            'qty'        => intval($item['quantity'])
        ]);
    }

    // Tout s'est bien passé, on renvoie le signal de succès et l'ID de commande au JavaScript
    echo json_encode([
        'success' => true, 
        'order_id' => $order_id
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Erreur technique d\'inventaire : ' . $e->getMessage()
    ]);
}