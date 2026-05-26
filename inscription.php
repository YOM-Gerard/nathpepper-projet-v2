<?php
session_start();
require_once 'includes/db.php'; // Charge ton instance de connexion $pdo

// Réception des données du formulaire HTML classique ($_POST)
$firstname = trim($_POST['firstname'] ?? '');
$lastname  = trim($_POST['lastname'] ?? '');
$email     = trim($_POST['email'] ?? '');
$phone     = trim($_POST['phone'] ?? '');
$address   = trim($_POST['address'] ?? '');
$password  = $_POST['password'] ?? '';

// Contrôle de sécurité sur les données reçues
if (empty($firstname) || empty($lastname) || empty($email) || empty($phone) || empty($address) || empty($password)) {
    $_SESSION['error_login'] = 'Veuillez remplir l\'intégralité des champs de livraison obligatoires.';
    header('Location: connexion.php');
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error_login'] = "Le format de votre adresse email est invalide.";
    header('Location: connexion.php');
    exit();
}

try {
    // 1. On s'assure que l'adresse email n'existe pas déjà
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    if ($stmt->fetch()) {
        $_SESSION['error_login'] = 'Cette adresse email est déjà rattachée à un compte.';
        header('Location: connexion.php');
        exit();
    }

    // 2. Cryptage sécurisé du mot de passe
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // 3. Concaténation de l'identité et des coordonnées pour la colonne globale 'name'
    $fullProfileName = $firstname . ' ' . $lastname . ' (Tél: ' . $phone . ' - Exp: ' . $address . ')';

    // 4. Insertion dans ta table d'utilisateurs
    $stmtInsert = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
    $stmtInsert->execute([
        'name'     => $fullProfileName,
        'email'    => $email,
        'password' => $hashedPassword
    ]);

    // 5. Initialisation des variables de session
    $_SESSION['user_id'] = $pdo->lastInsertId();
    $_SESSION['user_name'] = $firstname;
    
    // Ton message de succès en vert !
    $_SESSION['success_register'] = "✨ Félicitations " . htmlspecialchars($firstname) . ", vous êtes bien inscrit ! Votre compte de livraison premium a été configuré avec succès.";

    // Redirection immédiate vers ton catalogue de poivres !
    header('Location: produits.php');
    exit();

} catch (Exception $e) {
    $_SESSION['error_login'] = 'Erreur technique base de données : ' . $e->getMessage();
    header('Location: connexion.php');
    exit();
}