<?php
session_start();
require_once 'includes/db.php'; // Charge la variable $pdo

// On récupère les données du formulaire classique (POST)
$firstname = trim($_POST['firstname'] ?? '');
$lastname = trim($_POST['lastname'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$address = trim($_POST['address'] ?? '');
$password = $_POST['password'] ?? '';

// On assemble le prénom et le nom pour la colonne 'name' globale
$fullName = $firstname . ' ' . $lastname;

// Vérification de sécurité des données reçues
if (empty($firstname) || empty($lastname) || empty($email) || empty($password) || empty($phone) || empty($address)) {
    $_SESSION['error_login'] = 'Veuillez remplir tous les champs obligatoires (Prénom, Nom, Email, Téléphone, Adresse, Mot de passe).';
    header('Location: connexion.php');
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error_login'] = "Le format de l'adresse email est invalide.";
    header('Location: connexion.php');
    exit();
}

try {
    // 1. Vérifier si l'adresse email existe déjà
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    if ($stmt->fetch()) {
        $_SESSION['error_login'] = 'Cette adresse email est déjà associée à un compte.';
        header('Location: connexion.php');
        exit();
    }

    // 2. Sécuriser le mot de passe par hachage
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // 3. Insérer le nouvel utilisateur avec toutes ses coordonnées
    // Note : Si ta table possède des colonnes spécifiques pour phone et address, ajoute-les ici.
    // Sinon, cette structure écrit de manière sécurisée les éléments de base.
    $stmtInsert = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
    $stmtInsert->execute([
        'name' => $fullName,
        'email' => $email,
        'password' => $hashedPassword
    ]);

    // Enregistrement de l'ID généré
    $newUserId = $pdo->lastInsertId();

    // 💡 Optionnel : Si tu as des colonnes 'phone' et 'address' dans ta table 'users',
    // décommente les lignes ci-dessous pour les mettre à jour en même temps :
    /*
    $stmtUpdateFields = $pdo->prepare("UPDATE users SET phone = :phone, address = :address WHERE id = :id");
    $stmtUpdateFields->execute([
        'phone' => $phone,
        'address' => $address,
        'id' => $newUserId
    ]);
    */

    // 4. Initialisation de la session de connexion automatique
    $_SESSION['user_id'] = $newUserId;
    $_SESSION['user_name'] = $firstname; // On stocke uniquement le prénom pour saluer chaleureusement

    // 5. Création du message de succès demandé !
    $_SESSION['success_register'] = "Félicitations " . htmlspecialchars($firstname) . ", vous êtes bien inscrit ! Votre espace client a été créé avec succès.";

    // Redirection directe vers la boutique
    header('Location: produits.php');
    exit();

} catch (Exception $e) {
    $_SESSION['error_login'] = 'Erreur lors de l\'enregistrement en base de données : ' . $e->getMessage();
    header('Location: connexion.php');
    exit();
}