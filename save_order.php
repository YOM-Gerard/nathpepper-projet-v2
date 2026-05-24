<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'includes/db.php';
header('Content-Type: application/json');

$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Vérification des données reçues
if (empty($data['cart']) || empty($data['client'])) {
    echo json_encode(['success' => false, 'message' => 'Données incomplètes (panier ou formulaire vide).']);
    exit;
}

$cart = $data['cart'];
$client = $data['client'];
$totalPrice = 0;

// Calcul du prix total sécurisé
foreach ($cart as $item) {
    $stmt = $pdo->prepare("SELECT price FROM products WHERE id = ?");
    $stmt->execute([$item['id']]);
    $product = $stmt->fetch();
    if ($product) {
        $totalPrice += $product['price'] * $item['quantity'];
    }
}

try {
    $pdo->beginTransaction();

    // Insertion de la commande avec les infos clients
    $sqlOrder = "INSERT INTO orders (total_price, status, client_name, client_email, client_address, client_city, client_zipcode) 
                 VALUES (?, 'en_attente', ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sqlOrder);
    $stmt->execute([
        $totalPrice,
        $client['name'],
        $client['email'],
        $client['address'],
        $client['city'],
        $client['zipcode']
    ]);
    
    $orderId = $pdo->lastInsertId();

    // Insertion des produits de la commande
    $stmtItem = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    foreach ($cart as $item) {
        $stmtPrice = $pdo->prepare("SELECT price FROM products WHERE id = ?");
        $stmtPrice->execute([$item['id']]);
        $prodPrice = $stmtPrice->fetchColumn();

        $stmtItem->execute([
            $orderId,
            $item['id'],
            $item['quantity'],
            $prodPrice
        ]);
    }

    $pdo->commit();
    echo json_encode(['success' => true, 'order_id' => $orderId]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()]);
}