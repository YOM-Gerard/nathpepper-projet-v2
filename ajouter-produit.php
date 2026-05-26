<?php
session_start();
require_once 'includes/db.php'; // Connexion $pdo

// Sécurité Admin
if (!isset($_SESSION['user_id'])) { header('Location: connexion.php'); exit(); }
$stmtCheck = $pdo->prepare("SELECT is_admin FROM users WHERE id = :id");
$stmtCheck->execute(['id' => $_SESSION['user_id']]);
$user = $stmtCheck->fetch(PDO::FETCH_ASSOC);
if (!$user || $user['is_admin'] != 1) { header('Location: index.php'); exit(); }

$is_edit = false;
$product = ['id' => '', 'name' => '', 'price' => '', 'poids' => 30, 'image_url' => ''];

// Mode Modification : On charge les données existantes du poivre
if (isset($_GET['id'])) {
    $is_edit = true;
    $id = intval($_GET['id']);
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $res = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($res) {
        $product = [
            'id' => $res['id'],
            'name' => $res['name'] ?? '',
            'price' => $res['price'] ?? '',
            'poids' => $res['poids'] ?? 30,
            'image_url' => $res['image_url'] ?? '' // Clé harmonisée
        ];
    }
}

// TRAITEMENT DU FORMULAIRE (CREATE & UPDATE)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $poids = intval($_POST['poids']);
    
    // Récupération de l'ancienne image si aucun nouveau chemin n'est fourni
    $image_path = !empty($_POST['current_image']) ? trim($_POST['current_image']) : 'public/images/default-pepper.jpg';

    if (!empty($_POST['image_url'])) {
        $image_path = trim($_POST['image_url']);
    }

    try {
        if ($is_edit) {
            $sql = "UPDATE products SET name = :name, price = :price, poids = :poids, image_url = :image WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'name' => $name, 
                'price' => $price, 
                'poids' => $poids, 
                'image' => $image_path, 
                'id' => $product['id']
            ]);
        } else {
            $sql = "INSERT INTO products (name, price, poids, image_url) VALUES (:name, :price, :poids, :image)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'name' => $name, 
                'price' => $price, 
                'poids' => $poids, 
                'image' => $image_path
            ]);
        }
        header('Location: admin-produits.php');
        exit();
    } catch (Exception $e) {
        $error_msg = "Erreur lors de la sauvegarde : " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $is_edit ? 'Modifier' : 'Ajouter'; ?> un poivre - Nathpepper</title>
    <link rel="stylesheet" href="styles/main.css">
    <link rel="stylesheet" href="styles/components.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        .form-container { max-width: 500px; margin: 0 auto; padding: 2rem 1rem; font-family: 'Inter', sans-serif; }
        .form-card { background: #fff; border: 1px solid #e0e0e0; border-radius: 8px; padding: 25px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
    </style>
</head>
<body>

    <?php require_once 'includes/header.php'; ?>

    <main class="container">
        <div style="height: 120px; width: 100%;"></div>

        <section class="form-container">
            <div style="margin-bottom: 1rem;">
                <a href="admin-produits.php" style="text-decoration: none; color: #666;">← Retour au catalogue</a>
            </div>

            <div class="form-card">
                <h2 style="margin-bottom: 1.5rem; color: var(--primary-color);">
                    <?php echo $is_edit ? 'Modifier le poivre' : 'Ajouter un nouveau poivre'; ?>
                </h2>

                <?php if (isset($error_msg)): ?>
                    <div style="background: #ffebee; color: #c62828; padding: 10px; border-radius: 4px; margin-bottom: 1rem;"><?php echo $error_msg; ?></div>
                <?php endif; ?>

                <form action="" method="POST" class="contact-form">
                    <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($product['image_url']); ?>">

                    <div class="form-group">
                        <label for="name">Nom du Poivre</label>
                        <input type="text" id="name" name="name" required value="<?php echo htmlspecialchars($product['name']); ?>" placeholder="Ex: Poivre Vert de Kampot">
                    </div>

                    <div class="form-group" style="margin-top: 1rem;">
                        <label for="price">Prix (€)</label>
                        <input type="number" id="price" name="price" step="0.01" required value="<?php echo htmlspecialchars($product['price']); ?>" placeholder="Ex: 8.50">
                    </div>

                    <div class="form-group" style="margin-top: 1rem;">
                        <label for="poids">Poids / Grammage (en g)</label>
                        <input type="number" id="poids" name="poids" min="1" required value="<?php echo htmlspecialchars($product['poids']); ?>" placeholder="Ex: 30">
                    </div>

                    <div class="form-group" style="margin-top: 1rem;">
                        <label for="image_url">Chemin de l'image</label>
                        <input type="text" id="image_url" name="image_url" value="<?php echo htmlspecialchars($product['image_url']); ?>" placeholder="Ex: public/images/poivre-vert.jpg">
                    </div>

                    <button type="submit" class="btn-primary" style="width: 100%; margin-top: 1.5rem;">
                        <?php echo $is_edit ? 'Enregistrer les modifications' : 'Ajouter au catalogue'; ?>
                    </button>
                </form>
            </div>
        </section>
    </main>

    <?php require_once 'includes/footer.php'; ?>

</body>
</html>