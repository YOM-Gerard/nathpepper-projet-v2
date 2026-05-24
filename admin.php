<?php
session_start();
require_once 'includes/db.php'; // Connexion $pdo

// 1. SÉCURITÉ : On vérifie si l'utilisateur est connecté ET s'il est admin
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit();
}

// Vérification du rôle admin en BDD pour être totalement sécurisé
$stmtCheck = $pdo->prepare("SELECT is_admin FROM users WHERE id = :id");
$stmtCheck->execute(['id' => $_SESSION['user_id']]);
$user = $stmtCheck->fetch(PDO::FETCH_ASSOC);

if (!$user || $user['is_admin'] != 1) {
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

    // Calcul du Chiffre d'Affaires total (uniquement sur les commandes encaissées/traitées)
    $stmtCA = $pdo->query("SELECT SUM(total_amount) as total_ca FROM orders WHERE status IN ('paid', 'processing', 'shipped', 'delivered')");
    $ca_data = $stmtCA->fetch(PDO::FETCH_ASSOC);
    $chiffre_affaires = $ca_data['total_ca'] ?? 0;

    // Calcul du Panier Moyen
    $stmtMoyen = $pdo->query("SELECT AVG(total_amount) as panier_moyen FROM orders WHERE status IN ('paid', 'processing', 'shipped', 'delivered')");
    $moyenData = $stmtMoyen->fetch(PDO::FETCH_ASSOC);
    $panier_moyen = $moyenData['panier_moyen'] ?? 0;

    // Récupération du CA quotidien sur les 7 derniers jours pour le graphique Chart.js
    $stmtGraph = $pdo->query("
        SELECT DATE(created_at) as date_vente, SUM(total_amount) as total_jour 
        FROM orders 
        WHERE status IN ('paid', 'processing', 'shipped', 'delivered')
        GROUP BY DATE(created_at) 
        ORDER BY date_vente ASC 
        LIMIT 7
    ");
    $graphData = $stmtGraph->fetchAll(PDO::FETCH_ASSOC);

    // Préparation des variables pour JavaScript (Chart.js)
    $labelsGraph = [];
    $valuesGraph = [];
    foreach ($graphData as $dataRow) {
        $labelsGraph[] = date('d/m', strtotime($dataRow['date_vente']));
        $valuesGraph[] = floatval($dataRow['total_jour']);
    }

} catch (Exception $e) {
    $error_msg = "Erreur lors du chargement des données de gestion : " . $e->getMessage();
    $chiffre_affaires = 0;
    $panier_moyen = 0;
    $labelsGraph = [];
    $valuesGraph = [];
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
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .admin-container { max-width: 1100px; margin: 0 auto; padding: 2rem 1rem; font-family: 'Inter', sans-serif; }
        .admin-nav { display: flex; gap: 15px; margin-bottom: 2rem; border-bottom: 1px solid #ddd; padding-bottom: 10px; }
        .admin-nav a { text-decoration: none; color: #555; font-weight: 500; }
        .admin-nav a.active { color: var(--primary-color); border-bottom: 2px solid var(--primary-color); padding-bottom: 10px; }
        .stats-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 2rem; }
        .stats-cards-stack { display: flex; flex-direction: column; gap: 20px; }
        .stat-card { background: #1a1b1c; border: 1px solid #2d2d2d; border-radius: 8px; padding: 22px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); }
        .stat-number { font-size: 2.2rem; font-weight: 700; color: #dbc49d; margin-top: 5px; font-family: 'Playfair Display', serif; }
        .chart-card { background: #1a1b1c; border: 1px solid #2d2d2d; padding: 20px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); display: flex; flex-direction: column; justify-content: center; }
        .admin-table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 8px; overflow: hidden; border: 1px solid #e0e0e0; margin-top: 1rem; }
        .admin-table th, .admin-table td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #e0e0e0; vertical-align: middle; }
        .admin-table th { background: #f5f5f5; font-weight: 600; color: #333; }
        .items-list { font-size: 0.85rem; color: #555; margin: 0; padding-left: 15px; }
        .status-select { padding: 6px 10px; border-radius: 4px; border: 1px solid #ccc; font-family: 'Inter', sans-serif; font-weight: 500; font-size: 0.85rem; background-color: #fff; cursor: pointer; transition: all 0.3s ease; }
        @media (max-width: 768px) { .stats-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

    <?php require_once 'includes/header.php'; ?>

    <main class="container">
        <div style="height: 120px; width: 100%;"></div>

        <section class="admin-container">
            <div class="admin-nav">
                <a href="admin.php" class="active">📋 Gestion des Commandes</a>
                <a href="admin-produits.php">🌶️ Gestion du Catalogue</a>
            </div>

            <h2 class="section-title" style="text-align: left; margin-bottom: 0.5rem;">Panneau d'Administration</h2>
            <p style="margin-bottom: 2rem; color: #666;">Gestion globale des ventes et de l'activité Nathpepper.</p>

            <?php if (isset($error_msg)): ?>
                <div style="background: #ffebee; color: #c62828; padding: 15px; border-radius: 4px;"><?php echo $error_msg; ?></div>
            <?php else: ?>

                <div class="stats-grid">
                    <div class="stats-cards-stack">
                        <div class="stat-card">
                            <small style="color: #aaaaaa; text-transform: uppercase; font-weight: 600; letter-spacing: 1px; font-size: 0.8rem;">Chiffre d'Affaires Global</small>
                            <div class="stat-number"><?php echo number_format($chiffre_affaires, 2, ',', ' '); ?> €</div>
                        </div>
                        <div class="stat-card">
                            <small style="color: #aaaaaa; text-transform: uppercase; font-weight: 600; letter-spacing: 1px; font-size: 0.8rem;">Valeur du Panier Moyen</small>
                            <div class="stat-number" style="color: #ffffff;"><?php echo number_format($panier_moyen, 2, ',', ' '); ?> €</div>
                        </div>
                    </div>
                    
                    <div class="chart-card">
                        <h4 style="color: #dbc49d; margin: 0 0 15px 0; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; text-align: center; font-weight: 600;">Activité des 7 derniers jours (CA €)</h4>
                        <div style="height: 160px; position: relative;">
                            <canvas id="caChart"></canvas>
                        </div>
                    </div>
                </div>

                <h3 style="margin-top: 2.5rem; font-family: 'Playfair Display', serif; font-size: 1.6rem; color: var(--primary-color);">Liste des commandes</h3>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Client</th>
                            <th>Articles commandés</th>
                            <th>Montant</th>
                            <th>Statut logistique</th>
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
                                        <?php if (!empty($order['user_id'])): ?>
                                            <strong><?php echo htmlspecialchars($order['client_name'] ?? ''); ?></strong><br>
                                            <small style="color: #777;"><?php echo htmlspecialchars($order['client_email'] ?? ''); ?></small>
                                        <?php else: ?>
                                            <span style="color: #888; font-style: italic;">Achat Invité</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <ul class="items-list">
                                        <?php
                                        $stmtItems = $pdo->prepare("SELECT * FROM order_items WHERE order_id = :order_id");
                                        $stmtItems->execute(['order_id' => $order['id']]);
                                        $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
                                        foreach ($items as $item) {
                                            echo "<li>" . htmlspecialchars($item['product_name'] ?? '') . " <strong>(x" . $item['quantity'] . ")</strong></li>";
                                        }
                                        ?>
                                        </ul>
                                    </td>
                                    <td>
                                        <strong><?php echo number_format($order['total_amount'], 2, ',', ' '); ?> €</strong><br>
                                        <a href="facture.php?id=<?php echo $order['id']; ?>" target="_blank" style="font-size: 0.75rem; color: #0d47a1; text-decoration: none; font-weight: 600;">📄 Voir Facture PDF</a>
                                    </td>
                                    <td>
                                        <select class="status-select" data-order-id="<?php echo $order['id']; ?>">
                                            <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>⏳ En attente</option>
                                            <option value="paid" <?php echo $order['status'] === 'paid' ? 'selected' : ''; ?>>📦 Payée (À préparer)</option>
                                            <option value="processing" <?php echo $order['status'] === 'processing' ? 'selected' : ''; ?>>🛠️ En préparation</option>
                                            <option value="shipped" <?php echo $order['status'] === 'shipped' ? 'selected' : ''; ?>>🚚 Expédiée</option>
                                            <option value="delivered" <?php echo $order['status'] === 'delivered' ? 'selected' : ''; ?>>✓ Livrée</option>
                                        </select>
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

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log("Moteur logistique initialisé !");

        // --- RENDER DU GRAPHIQUE ANALYTIQUE CHART.JS ---
        const ctx = document.getElementById('caChart').getContext('2d');
        const labelsDays = <?php echo json_encode($labelsGraph); ?>;
        const dataSales = <?php echo json_encode($valuesGraph); ?>;

        const finalLabels = labelsDays.length > 0 ? labelsDays : ['Pas d\'activité'];
        const finalData = dataSales.length > 0 ? dataSales : [0];

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: finalLabels,
                datasets: