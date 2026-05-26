<?php
session_start();
require_once 'includes/db.php';

$message_success = "";
$message_error = "";

// 1. Récupération et validation des paramètres de l'URL
$token = $_GET['token'] ?? $_POST['token'] ?? '';
$email = $_GET['email'] ?? $_POST['email'] ?? '';

if (empty($token) || empty($email)) {
    $_SESSION['error_login'] = "Le lien de réinitialisation est invalide ou incomplet.";
    header('Location: connexion.php');
    exit();
}

try {
    // 2. Vérifier si le jeton existe, correspond à l'email et n'est pas expiré
    $stmt = $pdo->prepare("SELECT * FROM password_resets WHERE email = :email AND token = :token AND expires_at > NOW()");
    $stmt->execute(['email' => $email, 'token' => $token]);
    $resetRequest = $stmt->fetch();

    if (!$resetRequest) {
        $_SESSION['error_login'] = "Ce lien de récupération a expiré ou est invalide. Veuillez refaire une demande.";
        header('Location: mot-de-passe-oublie.php');
        exit();
    }

    // 3. Traitement de la modification du mot de passe
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        if (empty($new_password) || empty($confirm_password)) {
            $message_error = "Veuillez remplir tous les champs.";
        } elseif ($new_password !== $confirm_password) {
            $message_error = "Les deux mots de passe ne correspondent pas.";
        } elseif (strlen($new_password) < 6) {
            $message_error = "Le mot de passe doit contenir au moins 6 caractères.";
        } else {
            // Hachage sécurisé du nouveau mot de passe
            $hashedPassword = password_hash($new_password, PASSWORD_BCRYPT);

            // Mise à jour de la table 'users'
            $stmtUpdate = $pdo->prepare("UPDATE users SET password = :password WHERE email = :email");
            $stmtUpdate->execute(['password' => $hashedPassword, 'email' => $email]);

            // Nettoyage : On supprime le jeton utilisé pour qu'il ne serve plus
            $stmtDelete = $pdo->prepare("DELETE FROM password_resets WHERE email = :email");
            $stmtDelete->execute(['email' => $email]);

            // Succès ! Message mémorisé en session verte pour la page de connexion
            $_SESSION['success_register'] = "🔒 Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.";
            header('Location: connexion.php');
            exit();
        }
    }

} catch (Exception $e) {
    $message_error = "Erreur technique : " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nathpepper - Nouveau mot de passe</title>
    <link rel="stylesheet" href="styles/main.css">
    <link rel="stylesheet" href="styles/components.css">
    <link rel="stylesheet" href="styles/responsive.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body { background-color: #fcfbfa; color: #1a1b1c; }
        .reset-container { max-width: 500px; margin: 0 auto; padding: 0 1.5rem; }
        .reset-card { background: #fff; padding: 2.5rem; border-radius: 4px; border: 1px solid #1a1b1c; box-shadow: 0 4px 15px rgba(0,0,0,0.01); }
        .alert-error { background-color: #ffebee; color: #c62828; padding: 12px; border-radius: 4px; margin-bottom: 1.5rem; font-family: 'Inter', sans-serif; font-size: 0.9rem; text-align: center; border: 1px solid #ef9a9a; }
        .form-group { margin-bottom: 1.2rem; display: flex; flex-direction: column; gap: 6px; }
        .form-group label { font-family: 'Inter', sans-serif; font-size: 0.85rem; font-weight: 600; }
        .form-group input { width: 100%; padding: 11px; border: 1px solid #ccc; border-radius: 4px; }
    </style>
</head>
<body>

    <?php require_once 'includes/header.php'; ?>

    <main class="container">
        <div style="height: 160px; width: 100%;"></div>

        <div class="reset-container">
            <div class="reset-card">
                <h2 style="font-family: 'Playfair Display', serif; font-size: 1.8rem; font-weight: 600; margin-bottom: 0.5rem;">Nouveau mot de passe</h2>
                <p style="font-family: 'Inter', sans-serif; font-size: 0.9rem; color: #666; margin-bottom: 2rem;">Choisissez un mot de passe sécurisé pour votre compte.</p>

                <?php if (!empty($message_error)): ?>
                    <div class="alert-error"><?php echo $message_error; ?></div>
                <?php endif; ?>

                <form action="reinitialiser-mot-de-passe.php" method="POST" class="contact-form">
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                    <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">

                    <div class="form-group">
                        <label>Nouveau mot de passe</label>
                        <input type="password" name="new_password" required placeholder="Minimum 6 caractères">
                    </div>

                    <div class="form-group">
                        <label>Confirmer le mot de passe</label>
                        <input type="password" name="confirm_password" required placeholder="••••••••">
                    </div>
                    
                    <button type="submit" class="btn-primary" style="width: 100%; padding: 12px; font-weight: 600; margin-top: 1rem;">Mettre à jour mon mot de passe</button>
                </form>
            </div>
        </div>
    </main>

    <div style="height: 100px; width: 100%;"></div>
    <?php require_once 'includes/footer.php'; ?>

</body>
</html>