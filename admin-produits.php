<?php
session_start();
require_once 'includes/db.php'; // Connexion $pdo

// 1. SÉCURITÉ : Vérification stricte du rôle admin
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit();
}

$stmtCheck = $pdo->prepare("SELECT is_admin FROM users WHERE id = :id");
$stmtCheck->execute(['id' => $_SESSION['user_id']]);
$user = $stmtCheck->fetch(PDO::FETCH_ASSOC);

if (!$user || $user['is_admin'] != 1) {
    header('Location: index.php');
    exit();
}

// 2. GESTION DE LA SUPPRESSION (DELETE)
if (isset($_GET['delete'])) {
    $id_to_delete = intval($_GET['delete']);
    try {
        $stmtDel = $pdo->prepare("DELETE FROM products WHERE id = :id");
        $stmtDel->execute(['id' => $id_to_delete]);
        header('Location: admin-produits.php?success=deleted');
        exit();
    } catch (Exception $e) {
        $error_msg = "Erreur lors de la suppression : " . $e->getMessage();
    }
}

// 3. RÉCUPÉRATION DE TOUS LES PRODUITS (READ)
try {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error_msg = "Erreur de chargement du catalogue : " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Catalogue - Nathpepper</title>
    <link rel="stylesheet" href="styles/main.css">
    <link rel="stylesheet" href="styles/components.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .admin-container { max-width: 1100px; margin: 0 auto; padding: 2rem 1rem; font-family: 'Inter', sans-serif; }
        .admin-nav { display: flex; gap: 15px; margin-bottom: 2rem; border-bottom: 1px solid #ddd; padding-bottom: 10px; }
        .admin-nav a { text-decoration: none; color: #555; font-weight: 500; }
        .admin-nav a.active { color: var(--primary-color); border-bottom: 2px solid var(--primary-color); padding-bottom: 10px; }
        .header-actions { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
        .admin-table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 8px; overflow: hidden; border: 1px solid #e0e0e0; }
        .admin-table th, .admin-table td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #e0e0e0; vertical-align: middle; }
        .admin-table th { background: #f5f5f5; font-weight: 600; }
        .prod-img { width: 50px; height: 50px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd; }
        .btn-action { padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 0.85rem; font-weight: 500; display: inline-block; }
        .btn-edit { background: #e3f2fd; color: #0d47a1; margin-right: 5px; }
        .btn-delete { background: #ffebee; color: #c62828; }
        .alert-success { background: #e8f5e9; color: #2e7d32; padding: 12px; border-radius: 4px; margin-bottom: 1.5rem; }
    </style>
</head>
<body>

    <?php require_once 'includes/header.php'; ?>

    <main class="container">
        <div style="height: 120px; width: 100%;"></div>

        <section class="admin-container">
            <div class="admin-nav">
                <a href="admin.php">📋 Gestion des Commandes</a>
                <a href="admin-produits.php" class="active">🌶️ Gestion du Catalogue</a>
            </div>

            <?php if (isset($_GET['success']) && $_GET['success'] === 'deleted'): ?>
                <div class="alert-success">✓ Le produit a bien été retiré du catalogue.</div>
            <?php endif; ?>

            <?php if (isset($error_msg)): ?>
                <div style="background: #ffebee; color: #c62828; padding: 15px; border-radius: 4px;"><?php echo $error_msg; ?></div>
            <?php endif; ?>

            <div class="header-actions">
                <h3>Vos Poivres en ligne</h3>
                <a href="ajouter-produit.php" class="btn-primary" style="padding: 10px 20px;">+ Ajouter un poivre</a>
            </div>

            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Visuel</th>
                        <th>Nom du produit</th>
                        <th>Prix</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($products)): ?>
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 30px; color: #888;">Aucun produit dans la base de données.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($products as $prod): ?>
                            <tr>
                                <td>
                                    <?php 
                                    // CORRECTION : Utilisation de la colonne image_url avec fallback sur le logo s'il n'y a rien
                                    $img_src = !empty($prod['image_url']) ? $prod['image_url'] : 'public/images/logo-front.png'; 
                                    ?>
                                    <img src="<?php echo htmlspecialchars($img_src); ?>" class="prod-img" alt="Visuel poivre">
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($prod['name'] ?? $prod['nom']); ?></strong>
                                </td>
                                <td><strong><?php echo number_format($prod['price'] ?? $prod['prix'], 2, ',', ' '); ?> €</strong></td>
                                <td>
                                    <a href="ajouter-produit.php?id=<?php echo $prod['id']; ?>" class="btn-action btn-edit">Modifier</a>
                                    <a href="admin-produits.php?delete=<?php echo $prod['id']; ?>" class="btn-action btn-delete" onclick="return confirm('Voulez-vous vraiment supprimer ce poivre du catalogue ?');">Supprimer</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </main>

    <?php require_once 'includes/footer.php'; ?>

</body>
</html><?php
session_start();
require_once 'includes/db.php'; // Connexion $pdo

// 1. SÉCURITÉ : Vérification stricte du rôle admin
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit();
}

$stmtCheck = $pdo->prepare("SELECT is_admin FROM users WHERE id = :id");
$stmtCheck->execute(['id' => $_SESSION['user_id']]);
$user = $stmtCheck->fetch(PDO::FETCH_ASSOC);

if (!$user || $user['is_admin'] != 1) {
    header('Location: index.php');
    exit();
}

// 2. GESTION DE LA SUPPRESSION (DELETE)
if (isset($_GET['delete'])) {
    $id_to_delete = intval($_GET['delete']);
    try {
        $stmtDel = $pdo->prepare("DELETE FROM products WHERE id = :id");
        $stmtDel->execute(['id' => $id_to_delete]);
        header('Location: admin-produits.php?success=deleted');
        exit();
    } catch (Exception $e) {
        $error_msg = "Erreur lors de la suppression : " . $e->getMessage();
    }
}

// 3. RÉCUPÉRATION DE TOUS LES PRODUITS (READ)
try {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error_msg = "Erreur de chargement du catalogue : " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Catalogue - Nathpepper</title>
    <link rel="stylesheet" href="styles/main.css">
    <link rel="stylesheet" href="styles/components.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .admin-container { max-width: 1100px; margin: 0 auto; padding: 2rem 1rem; font-family: 'Inter', sans-serif; }
        .admin-nav { display: flex; gap: 15px; margin-bottom: 2rem; border-bottom: 1px solid #ddd; padding-bottom: 10px; }
        .admin-nav a { text-decoration: none; color: #555; font-weight: 500; }
        .admin-nav a.active { color: var(--primary-color); border-bottom: 2px solid var(--primary-color); padding-bottom: 10px; }
        .header-actions { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
        .admin-table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 8px; overflow: hidden; border: 1px solid #e0e0e0; }
        .admin-table th, .admin-table td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #e0e0e0; vertical-align: middle; }
        .admin-table th { background: #f5f5f5; font-weight: 600; }
        .prod-img { width: 50px; height: 50px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd; }
        .btn-action { padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 0.85rem; font-weight: 500; display: inline-block; }
        .btn-edit { background: #e3f2fd; color: #0d47a1; margin-right: 5px; }
        .btn-delete { background: #ffebee; color: #c62828; }
        .alert-success { background: #e8f5e9; color: #2e7d32; padding: 12px; border-radius: 4px; margin-bottom: 1.5rem; }
    </style>
</head>
<body>

    <?php require_once 'includes/header.php'; ?>

    <main class="container">
        <div style="height: 120px; width: 100%;"></div>

        <section class="admin-container">
            <div class="admin-nav">
                <a href="admin.php">📋 Gestion des Commandes</a>
                <a href="admin-produits.php" class="active">🌶️ Gestion du Catalogue</a>
            </div>

            <?php if (isset($_GET['success']) && $_GET['success'] === 'deleted'): ?>
                <div class="alert-success">✓ Le produit a bien été retiré du catalogue.</div>
            <?php endif; ?>

            <?php if (isset($error_msg)): ?>
                <div style="background: #ffebee; color: #c62828; padding: 15px; border-radius: 4px;"><?php echo $error_msg; ?></div>
            <?php endif; ?>

            <div class="header-actions">
                <h3>Vos Poivres en ligne</h3>
                <a href="ajouter-produit.php" class="btn-primary" style="padding: 10px 20px;">+ Ajouter un poivre</a>
            </div>

            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Visuel</th>
                        <th>Nom du produit</th>
                        <th>Prix</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($products)): ?>
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 30px; color: #888;">Aucun produit dans la base de données.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($products as $prod): ?>
                            <tr>
                                <td>
                                    <?php 
                                    // CORRECTION : Utilisation de la colonne image_url avec fallback sur le logo s'il n'y a rien
                                    $img_src = !empty($prod['image_url']) ? $prod['image_url'] : 'public/images/logo-front.png'; 
                                    ?>
                                    <img src="<?php echo htmlspecialchars($img_src); ?>" class="prod-img" alt="Visuel poivre">
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($prod['name'] ?? $prod['nom']); ?></strong>
                                </td>
                                <td><strong><?php echo number_format($prod['price'] ?? $prod['prix'], 2, ',', ' '); ?> €</strong></td>
                                <td>
                                    <a href="ajouter-produit.php?id=<?php echo $prod['id']; ?>" class="btn-action btn-edit">Modifier</a>
                                    <a href="admin-produits.php?delete=<?php echo $prod['id']; ?>" class="btn-action btn-delete" onclick="return confirm('Voulez-vous vraiment supprimer ce poivre du catalogue ?');">Supprimer</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </main>

    <?php require_once 'includes/footer.php'; ?>

</body>
</html>