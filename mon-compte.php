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
    // Récupération des données fraîches de l'utilisateur, y compris l'adresse, la ville, etc.
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->execute(['id' => $userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $user = null;
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
        .account-container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }
        .account-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 3rem;
        }
        @media (min-width: 768px) {
            .account-grid {
                grid-template-columns: 1fr 2fr;
                align-items: start;
            }
        }
        .account-sidebar {
            background: #f7f4ee;
            padding: 2rem;
            border-radius: 6px;
            border: 1px solid #e8e2d5;
            text-align: center;
        }
        .account-card {
            background: #fff;
            padding: 2rem;
            border-radius: 6px;
            border: 1px solid #e8e2d5;
            margin-bottom: 2rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.01);
        }
        .info-row {
            margin-bottom: 1.2rem;
            font-family: 'Inter', sans-serif;
            border-bottom: 1px solid #f9f9f9;
            padding-bottom: 8px;
        }
        .info-label {
            font-weight: 600;
            font-size: 0.8rem;
            color: #777;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .info-value {
            font-size: 1rem;
            color: #1a1b1c;
            margin-top: 4px;
        }
    </style>
</head>
<body>

    <?php require_once 'includes/header.php'; ?>

    <main class="container">
        <div style="height: 140px; width: 100%;"></div>

        <div class="account-container">
            <div class="account-grid">
                
                <aside class="account-sidebar">
                    <h2 style="font-family: 'Playfair Display', serif; font-size: 1.8rem; margin-bottom: 0.5rem; color: #1a1b1c;">
                        Bonjour, <?php echo htmlspecialchars($_SESSION['user_name']); ?> !
                    </h2>
                    <p style="font-family: 'Inter', sans-serif; font-size: 0.9rem; color: #666; margin-bottom: 2rem;">Bienvenue dans votre espace client Nathpepper.</p>
                    
                    <hr style="border: 0; border-top: 1px solid #e8e2d5; margin-bottom: 1.5rem;">
                    
                    <a href="deconnexion.php" class="btn-primary" style="display: block; text-align: center; text-decoration: none; padding: 12px; background: #c62828; color: #fff; border-radius: 4px; font-weight: 600;">
                        Se déconnecter
                    </a>
                </aside>

                <div class="account-content">
                    
                    <div class="account-card">
                        <h3 style="font-family: 'Playfair Display', serif; font-size: 1.4rem; margin-bottom: 1.5rem; color: #1a1b1c; border-bottom: 2px solid #e8e2d5; padding-bottom: 8px;">
                            Vos informations de livraison
                        </h3>
                        
                        <div class="info-row">
                            <div class="info-label">Destinataire</div>
                            <div class="info-value"><?php echo htmlspecialchars($user['name'] ?? 'Non renseigné'); ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Contact Email</div>
                            <div class="info-value"><?php echo htmlspecialchars($user['email'] ?? 'Non renseigné'); ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Téléphone d'expédition</div>
                            <div class="info-value"><?php echo htmlspecialchars($user['phone'] ?? 'Non renseigné'); ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Adresse postale renseignée</div>
                            <div class="info-value">
                                <?php echo nl2br(htmlspecialchars($user['address'] ?? 'Non renseigné')); ?><br>
                                <strong><?php echo htmlspecialchars($user['zipcode'] ?? ''); ?></strong> <?php echo htmlspecialchars($user['city'] ?? ''); ?>
                            </div>
                        </div>
                    </div>

                    <div class="account-card">
                        <h3 style="font-family: 'Playfair Display', serif; font-size: 1.4rem; margin-bottom: 1.5rem; color: #1a1b1c; border-bottom: 2px solid #e8e2d5; padding-bottom: 8px;">
                            Suivi de vos commandes d'exception
                        </h3>
                        <p style="font-family: 'Inter', sans-serif; color: #777; font-style: italic; font-size: 0.95rem;">
                            Aucune commande n'a été enregistrée pour le moment. Vos futurs achats de poivres premium apparaîtront ici.
                        </p>
                    </div>

                </div>

            </div>
        </div>
    </main>

    <div style="height: 100px; width: 100%;"></div>

    <?php require_once 'includes/footer.php'; ?>

</body>
</html>