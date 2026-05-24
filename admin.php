<?php
session_start();
require_once 'includes/db.php'; // Connexion $pdo

// 1. SÉCURITÉ : On vérifie si l'utilisateur est connecté ET s'il est admin
// (Ici on vérifie la colonne is_admin, ou tu peux ajouter : && $_SESSION['user_email'] === 'admin@nathpepper.com')
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit();
}

// Vérification du rôle admin en BDD pour être totalement sécurisé
$stmtCheck = $pdo->prepare("SELECT is_admin FROM users WHERE id = :id");
$stmtCheck->execute(['id' => $_SESSION['user_id']]);
$user = $stmtCheck->fetch(PDO::FETCH_ASSOC);

if (!$user || $user['is_admin'] != 1) {
    // Si l'utilisateur n'est pas admin, on le jette poliment vers l'accueil
    header('Location: index.php');
    exit();
}

try {
    // 2. Récupérer TOUTES les commandes du site avec les infos du client si elles existent (LEFT JOIN)
    $stmt = $pdo->query("
        SELECT orders.*, users.name as client_name, users.email as client_email 
        FROM orders 
        LEFT JOIN users ON orders.user_id = users.id 
        ORDER BY orders.created_at DESC
    ");
    $all_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calcul du Chiffre d'Affaires total (uniquement sur les commandes payées)
    $stmtCA = $pdo->query("SELECT SUM(total_amount) as total_ca FROM orders WHERE status = 'paid'");
    $ca_data = $stmtCA->fetch(PDO::FETCH_ASSOC);
    $chiffre_affaires = $ca_data['total_ca'] ?? 0;

} catch (Exception $e) {
    $error_msg = "Erreur lors du chargement des données de gestion : " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Nathpepper</title>
    <link rel="stylesheet" href="styles/main.css">
    <link rel="stylesheet" href="styles/components.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        .admin-container { max-width: 1100px; margin: 0 auto; padding: 2rem 1rem; family: 'Inter', sans-serif; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 2rem; }
        .stat-card { background: #fff; border: 1px solid #e0e0e0; border-radius: 8px; padding: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.01); }
        .stat-number { font-size: 2rem; font-weight: 700; color: var(--primary-color); margin-top: 5px; }
        .admin-table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 8px; overflow: hidden; border: 1px solid #e0e0e0; margin-top: 1rem; }
        .admin-table th, .admin-table td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #e0e0e0; }
        .admin-table th { background: #f5f5f5; font-weight: 600; color: #333; }
        .status-badge { padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: 500; }
        .status-paid { background-color: #e8f5e9; color: #2e7d32; }
        .status-pending { background-color: #fff3e0; color: #ef6c00; }
        .items-list { font-size: 0.85rem; color: #555; margin: 0; padding-left: 15px; }
    </style>
</head>
<body>

    <?php require_once 'includes/header.php'; ?>

    <main class="container">
        <div style="height: 120px; width: 100%;"></div>

        <section class="admin-container">
            <h2 class="section-title" style="text-align: left; margin-bottom: 0.5rem;">Panneau d'Administration</h2>
            <p style="margin-bottom: 2rem; color: #666;">Gestion globale des ventes et de l'activité Nathpepper.</p>

            <?php if (isset($error_msg)): ?>
                <div style="background: #ffebee; color: #c62828; padding: 15px; border-radius: 4px;"><?php echo $error_msg; ?></div>
            <?php else: ?>

                <div class="stats-grid">
                    <div class="stat-card">
                        <small style="color: #777; text-transform: uppercase; font-weight: 600;">Chiffre d'Affaires</small>
                        <div class="stat-number"><?php echo number_format($chiffre_affaires, 2, ',', ' '); ?> €</div>
                    </div>
                    <div class="stat-card">
                        <small style="color: #777; text-transform: uppercase; font-weight: 600;">Commandes Totales</small>
                        <div class="stat-number"><?php echo count($all_orders); ?></div>
                    </div>
                </div>

                <h3>Liste des commandes</h3>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Client</th>
                            <th>Articles commandés</th>
                            <th>Montant</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($all_orders)): ?>
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 30px; color: #888;">Aucune commande enregistrée pour le moment.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($all_orders as $order): ?>
                                <tr>
                                    <td><strong>#<?php echo $order['id']; ?></strong></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                                    <td>
                                        <?php if ($order['user_id']): ?>
                                            <strong><?php echo htmlspecialchars($order['client_name']); ?></strong><br>
                                            <small style="color: #777;"><?php echo htmlspecialchars($order['client_email']); ?></small>
                                        <?php else: ?>
                                            <span style="color: #888; font-style: italic;">Achat Invité</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <ul class="items-list">
                                        <?php
                                        // On va chercher les produits de cette commande
                                        $stmtItems = $pdo->prepare("SELECT * FROM order_items WHERE order_id = :order_id");
                                        $stmtItems->execute(['order_id' => $order['id']]);
                                        $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
                                        foreach ($items as $item) {
                                            echo "<li>" . htmlspecialchars($item['product_name']) . " <strong>(x" . $item['quantity'] . ")</strong></li>";
                                        }
                                        ?>
                                        </ul>
                                    </td>
                                    <td><strong><?php echo number_format($order['total_amount'], 2, ',', ' '); ?> €</strong></td>
                                    <td>
                                        <span class="status-badge <?php echo $order['status'] === 'paid' ? 'status-paid' : 'status-pending'; ?>">
                                            <?php echo $order['status'] === 'paid' ? 'Payée' : 'En attente'; ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>

            <?php endif; ?>
        </section>
    </main>

    <?php require_once 'includes/footer.php'; ?>

</body>
</html>