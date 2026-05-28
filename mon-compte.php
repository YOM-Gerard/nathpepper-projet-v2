<?php
session_start();
require_once 'includes/db.php';

// Sécurité : Si le client n'est pas connecté, on le redirige vers la page de connexion
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit();
}

$userId = $_SESSION['user_id'];

try {
    // 1. Récupération des données de l'utilisateur
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->execute(['id' => $userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // 2. Récupération de l'historique des commandes du client
    $stmtOrders = $pdo->prepare("SELECT * FROM orders WHERE user_id = :user_id ORDER BY created_at DESC");
    $stmtOrders->execute(['user_id' => $userId]);
    $orders = $stmtOrders->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    $user = null;
    $orders = [];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nathpepper - Mon Compte</title>
    <link rel="stylesheet" href="styles/main.css">
    <link rel="stylesheet" href="styles/components.css">
    <link rel="stylesheet" href="styles/responsive.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #fcfbfa;
            color: #1a1b1c;
        }
        .account-container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }
        .account-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 4rem;
        }
        @media (min-width: 768px) {
            .account-grid {
                grid-template-columns: 1fr 2fr;
                align-items: start;
            }
        }
        .account-sidebar {
            background: #fff;
            padding: 2.5rem;
            border-radius: 4px;
            border: 1px solid #1a1b1c;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.01);
        }
        .account-card {
            background: #fff;
            padding: 2.5rem;
            border-radius: 4px;
            border: 1px solid #e8e2d5;
            margin-bottom: 2.5rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.01);
        }
        .account-card h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 2rem;
            color: #1a1b1c;
            border-bottom: 1px solid #1a1b1c;
            padding-bottom: 10px;
            text-align: left;
        }
        .info-row {
            margin-bottom: 1.5rem;
            font-family: 'Inter', sans-serif;
            text-align: left;
        }
        .info-label {
            font-weight: 600;
            font-size: 0.8rem;
            color: #777;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }
        .info-value {
            font-size: 1rem;
            color: #1a1b1c;
            margin-top: 5px;
        }
        .btn-logout {
            display: block;
            text-align: center;
            text-decoration: none;
            padding: 12px;
            background: #fff;
            color: #c62828;
            border: 1px solid #c62828;
            border-radius: 4px;
            font-family: 'Inter', sans-serif;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.2s ease-in-out;
            margin-top: 1.5rem;
        }
        .btn-logout:hover {
            background: #c62828;
            color: #fff;
        }
        .order-empty-text {
            font-family: 'Inter', sans-serif;
            color: #666;
            font-style: italic;
            font-size: 0.95rem;
            text-align: left;
            line-height: 1.6;
        }
        .order-table {
            width: 100%;
            border-collapse: collapse;
            font-family: 'Inter', sans-serif;
            font-size: 0.95rem;
        }
        .order-table th {
            padding: 10px 5px;
            font-weight: 600;
            border-bottom: 2px solid #1a1b1c;
            text-align: left;
        }
        .order-table td {
            padding: 12px 5px;
            border-bottom: 1px solid #e8e2d5;
            text-align: left;
        }
    </style>
</head>
<body>

    <?php require_once 'includes/header.php'; ?>

    <main class="container">
        <div style="height: 160px; width: 100%;"></div>

        <div class="account-container">
            <div class="account-grid">
                
                <aside class="account-sidebar">
                    <h2 style="font-family: 'Playfair Display', serif; font-size: 2rem; font-weight: 600; margin-bottom: 0.5rem; color: #1a1b1c;">
                        Mon Espace
                    </h2>
                    <p style="font-family: 'Inter', sans-serif; font-size: 0.95rem; color: #666; margin-bottom: 2rem;">
                        Ravi de vous revoir, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Client'); ?>.
                    </p>
                    
                    <hr style="border: 0; border-top: 1px solid #e8e2d5; margin-bottom: 1.5rem;">
                    
                    <a href="deconnexion.php" class="btn-logout">
                        Se déconnecter
                    </a>
                </aside>

                <div class="account-content">
                    
                    <div class="account-card">
                        <h3>Informations de livraison</h3>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                            <div class="info-row">
                                <div class="info-label">Nom du destinataire</div>
                                <div class="info-value"><?php echo htmlspecialchars($user['name'] ?? 'Non renseigné'); ?></div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Téléphone de contact</div>
                                <div class="info-value"><?php echo htmlspecialchars($user['phone'] ?? 'Non renseigné'); ?></div>
                            </div>
                        </div>

                        <div class="info-row" style="margin-top: 0.5rem;">
                            <div class="info-label">Adresse de facturation & d'expédition</div>
                            <div class="info-value" style="line-height: 1.6;">
                                <?php echo nl2br(htmlspecialchars($user['address'] ?? 'Non renseigné')); ?><br>
                                <strong><?php echo htmlspecialchars($user['zipcode'] ?? ''); ?></strong> <?php echo htmlspecialchars($user['city'] ?? ''); ?>
                            </div>
                        </div>
                    </div>

                    <div class="account-card">
                        <h3>Historique des commandes</h3>
                        
                        <?php if (!empty($orders)): ?>
                            <div style="width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch;">
                                <table class="order-table">
                                    <thead>
                                        <tr>
                                            <th>N°</th>
                                            <th>Date</th>
                                            <th>Montant</th>
                                            <th>Statut</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($orders as $order): ?>
                                            <tr>
                                                <td style="font-weight: 500;">#<?php echo $order['id']; ?></td>
                                                <td style="color: #555;"><?php echo date('d/m/Y', strtotime($order['created_at'])); ?></td>
                                                <td style="font-weight: 600;"><?php echo number_format($order['total_amount'], 2, ',', ' '); ?> €</td>
                                                <td>
                                                    <?php 
                                                        $status = $order['status'] ?? 'En préparation';
                                                        $color = '#1a1b1c'; // Noir par défaut
                                                        if ($status === 'Payée' || $status === 'Livrée') $color = '#2e7d32'; // Vert
                                                        if ($status === 'Expédiée') $color = '#0288d1'; // Bleu
                                                        if ($status === 'Annulée') $color = '#c62828'; // Rouge
                                                    ?>
                                                    <span style="display: inline-block; padding: 4px 10px; background: <?php echo $color; ?>; color: #fff; font-size: 0.8rem; font-weight: 600; border-radius: 3px; text-transform: uppercase; letter-spacing: 0.5px;">
                                                        <?php echo htmlspecialchars($status); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="order-empty-text">
                                Vous n'avez pas encore passé de commande. Vos futurs achats de poivres rares et d'exception s'afficheront ici pour vous permettre de suivre leur expédition.
                            </p>
                        <?php endif; ?>
                    </div>

                </div>

            </div>
        </div>
    </main>

    <div style="height: 100px; width: 100%;"></div>

    <?php require_once 'includes/footer.php'; ?>

</body>
</html>