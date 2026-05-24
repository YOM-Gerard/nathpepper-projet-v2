<?php
session_start();
require_once 'includes/db.php';

header('Content-Type: application/json');

// Sécurité : Le client doit être connecté pour commander
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Veuillez vous connecter pour passer commande.']);
    exit();
}

// Récupération du panier envoyé par JavaScript
$data = json_decode(file_get_contents('php://input'), true);
$basket = isset($data['basket']) ? $data['basket'] : [];

if (empty($basket)) {
    echo json_encode(['success' => false, 'message' => 'Votre panier est vide.']);
    exit();
}

try {
    // 1. PHASE DE VÉRIFICATION CHIRURGICALE DES STOCKS
    foreach ($basket as $item) {
        $product_id = intval($item['id']);
        $quantity_requested = intval($item['quantity']);
        $product_name = htmlspecialchars($item['name']);

        // On va chercher le stock exact du poivre en BDD
        $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = :id");
        $stmt->execute(['id' => $product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            echo json_encode(['success' => false, 'message' => "Le produit '{$product_name}' n'existe plus dans notre catalogue."]);
            exit();
        }

        // Si le stock est insuffisant, on bloque instantanément la vente !
        if ($product['stock'] < $quantity_requested) {
            if ($product['stock'] == 0) {
                echo json_encode(['success' => false, 'message' => "Désolé, le '{$product_name}' vient de tomber en rupture de stock."]);
            } else {
                echo json_encode(['success' => false, 'message' => "Désolé, il ne reste plus que {$product['stock']} unité(s) pour le '{$product_name}'."]);
            }
            exit();
        }
    }

    // 2. CRÉATION DE LA COMMANDE EN ATTENTE DE PAIEMENT (PENDING)
    $total_amount = 0;
    foreach ($basket as $item) {
        $total_amount += floatval($item['price']) * intval($item['quantity']);
    }

    // Insertion dans la table 'orders'
    $stmtOrder = $pdo->prepare("INSERT INTO orders (user_id, total_amount, status, created_at) VALUES (:user_id, :total, 'pending', NOW())");
    $stmtOrder->execute([
        'user_id' => $_SESSION['user_id'],
        'total'   => $total_amount
    ]);
    $order_id = $pdo->lastInsertId();

    // Insertion des articles dans 'order_items'
    foreach ($basket as $item) {
        $stmtItem = $pdo->prepare("INSERT INTO order_items (order_id, product_id, product_name, price, quantity) VALUES (:order_id, :product_id, :name, :price, :qty)");
        $stmtItem->execute([
            'order_id'   => $order_id,
            'product_id' => intval($item['id']),
            'name'       => $item['name'],
            'price'      => floatval($item['price']),
            'qty'        => intval($item['quantity'])
        ]);
    }

    // Si tout est valide, on renvoie le feu vert au JavaScript
    echo json_encode(['success' => true, 'order_id' => $order_id]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur technique lors de la validation : ' . $e->getMessage()]);
}