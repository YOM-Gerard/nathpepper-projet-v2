<?php
session_start();
require_once 'includes/db.php'; // Connexion $pdo

// 1. Sécurité : Si l'utilisateur n'est pas connecté, redirection immédiate
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

try {
    // 2. Récupérer toutes les commandes payées ou en attente de cet utilisateur (de la plus récente à la plus ancienne)
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = :user_id ORDER BY created_at DESC");
    $stmt->execute(['user_id' => $user_id]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    // Affiche la véritable erreur SQL renvoyée par MySQL
    $error_msg = "Erreur technique SQL : " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Commandes - Nathpepper</title>
    <link rel="stylesheet" href="styles/main.css">
    <link rel="stylesheet" href="styles/components.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .orders-container { max-width: 800px; margin: 0 auto; padding: 2rem 1rem; }
        .order-card { background: #fff; border: 1px solid #e0e0e0; border-radius: 8px; margin-bottom: 1.5rem; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
        .order-header { background: #f9f9f9; padding: 15px 20px; border-bottom: 1px solid #e0e0e0; display: flex; justify-content: space-between; flex-wrap: wrap; gap: 10px; align-items: center; }
        .order-header h3 { margin: 0; font-family: var(--font-primary); color: var(--primary-color); }
        .order-body { padding: 20px; }
        .order-item { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f5f5f5; font-size: 0.95rem; }
        .order-item:last-child { border-bottom: none; }
        .status-badge { padding: 4px 10px; border-radius: 20px; font-size: 0.85rem; font-weight: 500; text-transform: uppercase; display: inline-block; }
        .status-paid { background-color: #e8f5e9; color: #2e7d32; }
        .status-pending { background-color: #fff3e0; color: #ef6c00; }
        .no-orders { text-align: center; padding: 40px; background: #f9f9f9; border-radius: 8px; border: 1px dashed #ccc; }
        .btn-invoice { display: inline-block; margin-top: 8px; font-size: 0.8rem; color: #0d47a1; text-decoration: none; font-weight: 600; border: 1px solid #0d47a1; padding: 4px 10px; border-radius: 4px; transition: all 0.2s ease; }
        .btn-invoice:hover { background: #0d47a1; color: #fff; }
    </style>
</head>
<body>

    <?php require_once 'includes/header.php'; ?>

    <main class="container">
        <div style="height: 120px; width: 100%;"></div>

        <section class="orders-container">
            <h2 class="section-title" style="text-align: left; margin-bottom: 0.5rem;">Mon Historique de Commandes</h2>
            <p style="margin-bottom: 2rem; color: #666;">Ravi de vous revoir, <strong><?php echo htmlspecialchars($user_name); ?></strong>. Retrouvez ici le détail de vos achats.</p>

            <?php if (isset($error_msg)): ?>
                <div class="alert-error" style="background: #ffebee; color: #c62828; padding: 15px; border-radius: 4px;"><?php echo $error_msg; ?></div>
            <?php elseif (empty($orders)): ?>
                <div class="no-orders">
                    <p style="font-size: 1.1rem; margin-bottom: 1.5rem;">Vous n'avez pas encore passé de commande avec ce compte.</p>
                    <a href="produits.php" class="btn-primary">Découvrir nos poivres</a>
                </div>
            <?php else: ?>
                
                <?php foreach ($orders as $order): ?>
                    <div class="order-card">
                        <div class="order-header">
                            <div>
                                <h3>Commande n°<?php echo $order['id']; ?></h3>
                                <small style="color: #888;">Passée le : <?php echo date('d/m/Y à H:i', strtotime($order['created_at'])); ?></small>
                                <br>
                                <a href="facture.php?id=<?php echo $order['id']; ?>" target="_blank" class="btn-invoice">📄 Télécharger la facture (PDF)</a>
                            </div>
                            <div style="text-align: right;">
                                <span class="status-badge <?php echo $order['status'] === 'paid' ? 'status-paid' : 'status-pending'; ?>">
                                    <?php echo $order['status'] === 'paid' ? '✓ Payée' : '⏳ En attente'; ?>
                                </span>
                                <div style="margin-top: 5px; font-weight: 700; font-size: 1.1rem; color: var(--dark-color);">
                                    <?php echo number_format($order['total_amount'], 2, ',', ' '); ?> €
                                </div>
                            </div>
                        </div>

                        <div class="order-body">
                            <?php
                            // On va chercher dans la BDD les articles correspondants à CETTE commande spécifique
                            $stmtItems = $pdo->prepare("SELECT * FROM order_items WHERE order_id = :order_id");
                            $stmtItems->execute(['order_id' => $order['id']]);
                            $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
                            
                            foreach ($items as $item):
                            ?>
                                <div class="order-item">
                                    <span>
                                        <strong><?php echo htmlspecialchars($item['product_name']); ?></strong> 
                                        <span style="color: #777; margin-left: 5px;">(x<?php echo $item['quantity']; ?>)</span>
                                    </span>
                                    <span style="font-weight: 500;">
                                        <?php echo number_format($item['price'] * $item['quantity'], 2, ',', ' '); ?> €
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>

            <?php endif; ?>
        </section>
    </main>

    <?php require_once 'includes/footer.php'; ?>

</body>
</html>