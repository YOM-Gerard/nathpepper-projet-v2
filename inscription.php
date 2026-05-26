<?php
session_start();
require_once 'includes/db.php'; // Charge l'instance PDO ($pdo)

header('Content-Type: application/json');

// Réception des données du formulaire HTML classique ($_POST)
$firstname = trim($_POST['firstname'] ?? '');
$lastname  = trim($_POST['lastname'] ?? '');
$email     = trim($_POST['email'] ?? '');
$phone     = trim($_POST['phone'] ?? '');
$address   = trim($_POST['address'] ?? '');
$password  = $_POST['password'] ?? '';

// Vérification de la présence de toutes les informations de livraison (CORRIGÉ !)
if (empty($firstname) || empty($lastname) || empty($email) || empty($phone) || empty($address) || empty($password)) {
    $_SESSION['error_login'] = 'Veuillez renseigner tous les champs obligatoires pour configurer votre compte de livraison.';
    header('Location: connexion.php');
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error_login'] = "L'adresse email saisie possède un format invalide.";
    header('Location: connexion.php');
    exit();
}

try {
    // 1. On contrôle que l'adresse email n'existe pas déjà
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    if ($stmt->fetch()) {
        $_SESSION['error_login'] = 'Cette adresse email est déjà associée à un compte Nathpepper.';
        header('Location: connexion.php');
        exit();
    }

    // 2. Cryptage de sécurité du mot de passe
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // 3. Reconstitution du Nom Complet pour la colonne 'name'
    $fullName = $firstname . ' ' . $lastname;

    // 4. Écriture ordonnée dans la base de données (Chaque donnée dans sa colonne !)
    $stmtInsert = $pdo->prepare("INSERT INTO users (name, email, password, phone, address) VALUES (:name, :email, :password, :phone, :address)");
    $stmtInsert->execute([
        'name'     => $fullName,
        'email'    => $email,
        'password' => $hashedPassword,
        'phone'    => $phone,
        'address'  => $address
    ]);

    // 5. Enregistrement de la session utilisateur
    $_SESSION['user_id'] = $pdo->lastInsertId();
    $_SESSION['user_name'] = $firstname; // Stocke le prénom pour saluer l'utilisateur dans le header
    
    // Le message de confirmation personnalisé avec le prénom
    $_SESSION['success_register'] = "✨ Félicitations " . htmlspecialchars($firstname) . ", vous êtes bien inscrit ! Votre compte de livraison a été configuré avec succès.";

    // Redirection directe vers la boutique
    header('Location: produits.php');
    exit();

} catch (Exception $e) {
    $_SESSION['error_login'] = 'Erreur lors de la création de votre profil en base de données : ' . $e->getMessage();
    header('Location: connexion.php');
    exit();
}