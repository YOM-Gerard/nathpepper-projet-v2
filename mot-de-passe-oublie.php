<?php
session_start();
require_once 'includes/db.php';

$message_success = "";
$message_error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (empty($email)) {
        $message_error = "Veuillez entrer votre adresse email.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message_error = "Le format de l'adresse email est invalide.";
    } else {
        try {
            // 1. Vérifier si l'utilisateur existe vraiment
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch();
            
            if ($user) {
                // 2. Générer un jeton unique et sécurisé
                $token = bin2hex(random_bytes(32));
                
                // 3. Définir une date d'expiration (+1 heure)
                $expiresAt = date("Y-m-d H:i:s", strtotime("+1 hour"));
                
                // 4. Supprimer les anciens jetons de cet utilisateur s'il y en a
                $stmtDelete = $pdo->prepare("DELETE FROM password_resets WHERE email = :email");
                $stmtDelete->execute(['email' => $email]);

                // 5. Enregistrer le nouveau jeton en BDD
                $stmtInsert = $pdo->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (:email, :token, :expires_at)");
                $stmtInsert->execute([
                    'email' => $email,
                    'token' => $token,
                    'expires_at' => $expiresAt
                ]);
                
                // 6. Simulation locale de l'envoi du lien par email
                $resetLink = "reinitialiser-mot-de-passe.php?token=" . $token . "&email=" . urlencode($email);
                
                $message_success = "📩 **Simulation locale de l'envoi d'un email :**<br>Un email de récupération est prêt. Cliquez sur le bouton ci-dessous pour modifier votre mot de passe :<br><br><a href='" . $resetLink . "' class='btn-primary' style='display:inline-block; text-decoration:none; text-align:center;'>Réinitialiser mon mot de passe</a>";
            } else {
                // Message flou par sécurité (pour éviter le vol d'informations d'emails existants)
                $message_success = "📩 Si cette adresse est associée à un compte Nathpepper, un lien de réinitialisation sera envoyé.";
            }
        } catch (Exception $e) {
            $message_error = "Erreur technique : " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nathpepper - Récupération de mot de passe</title>
    <link rel="stylesheet" href="styles/main.css">
    <link rel="stylesheet" href="styles/components.css">
    <link rel="stylesheet" href="styles/responsive.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body { background-color: #fcfbfa; color: #1a1b1c; }
        .recovery-container { max-width: 550px; margin: 0 auto; padding: 0 1.5rem; }
        .recovery-card { background: #fff; padding: 2.5rem; border-radius: 4px; border: 1px solid #1a1b1c; box-shadow: 0 4px 15px rgba(0,0,0,0.01); }
        .alert-error { background-color: #ffebee; color: #c62828; padding: 12px; border-radius: 4px; margin-bottom: 1.5rem; font-family: 'Inter', sans-serif; font-size: 0.9rem; text-align: center; border: 1px solid #ef9a9a; }
        .alert-success { background-color: #e8f5e9; color: #2e7d32; padding: 15px; border-radius: 4px; margin-bottom: 1.5rem; font-family: 'Inter', sans-serif; font-size: 0.9rem; text-align: left; border: 1px solid #a5d6a7; line-height: 1.5; }
    </style>
</head>
<body>

    <?php require_once 'includes/header.php'; ?>

    <main class="container">
        <div style="height: 160px; width: 100%;"></div>

        <div class="recovery-container">
            <div class="recovery-card">
                <h2 style="font-family: 'Playfair Display', serif; font-size: 1.8rem; font-weight: 600; margin-bottom: 0.5rem;">Mot de passe oublié ?</h2>
                <p style="font-family: 'Inter', sans-serif; font-size: 0.9rem; color: #666; margin-bottom: 2rem;">Saisissez votre email. Nous allons générer un jeton d'accès temporaire sécurisé pour reconfigurer votre compte.</p>

                <?php if (!empty($message_error)): ?>
                    <div class="alert-error"><?php echo $message_error; ?></div>
                <?php endif; ?>

                <?php if (!empty($message_success)): ?>
                    <div class="alert-success"><?php echo $message_success; ?></div>
                <?php endif; ?>

                <form action="mot-de-passe-oublie.php" method="POST" class="contact-form">
                    <div class="form-group" style="display: flex; flex-direction: column; gap: 6px; margin-bottom: 1.5rem;">
                        <label style="font-family: 'Inter', sans-serif; font-size: 0.85rem; font-weight: 600;">Votre Adresse Email</label>
                        <input type="email" name="email" required placeholder="votre.email@exemple.com" style="width: 100%; padding: 11px; border: 1px solid #ccc; border-radius: 4px;">
                    </div>
                    
                    <button type="submit" class="btn-primary" style="width: 100%; padding: 12px; font-weight: 600;">Demander un nouveau mot de passe</button>
                    
                    <div style="text-align: center; margin-top: 1.5rem;">
                        <a href="connexion.php" style="font-family: 'Inter', sans-serif; font-size: 0.9rem; color: #1a1b1c; font-weight: 500; text-decoration: underline;">
                            Retour à la page de connexion
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <div style="height: 100px; width: 100%;"></div>
    <?php require_once 'includes/footer.php'; ?>

</body>
</html>