<?php
session_start();
require_once 'includes/db.php'; // Charge l'instance PDO ($pdo)

// Réception de l'intégralité des variables de livraison natives du formulaire
$firstname = trim($_POST['firstname'] ?? '');
$lastname  = trim($_POST['lastname'] ?? '');
$email     = trim($_POST['email'] ?? '');
$phone     = trim($_POST['phone'] ?? '');
$address   = trim($_POST['address'] ?? '');
$zipcode   = trim($_POST['zipcode'] ?? '');
$city      = trim($_POST['city'] ?? '');
$password  = $_POST['password'] ?? '';

// Vérification globale de la complétude du dossier de livraison
if (empty($firstname) || empty($lastname) || empty($email) || empty($phone) || empty($address) || empty($zipcode) || empty($city) || empty($password)) {
    $_SESSION['error_login'] = 'Veuillez remplir toutes les cases (Prénom, Nom, Email, Téléphone, Adresse, Code Postal et Ville).';
    header('Location: connexion.php');
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error_login'] = "Le format de votre adresse email est invalide.";
    header('Location: connexion.php');
    exit();
}

try {
    // 1. Contrôle anti-doublon d'email
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    if ($stmt->fetch()) {
        $_SESSION['error_login'] = 'Cette adresse email est déjà utilisée par un autre compte.';
        header('Location: connexion.php');
        exit();
    }

    // 2. Hachage du mot de passe
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // 3. Fusion des chaînes pour l'identité
    $fullName = $firstname . ' ' . $lastname;

    // 4. Écriture structurée en Base de Données (Une case propre pour chaque info)
    $stmtInsert = $pdo->prepare("INSERT INTO users (name, email, password, phone, address, zipcode, city) VALUES (:name, :email, :password, :phone, :address, :zipcode, :city)");
    $stmtInsert->execute([
        'name'     => $fullName,
        'email'    => $email,
        'password' => $hashedPassword,
        'phone'    => $phone,
        'address'  => $address,
        'zipcode'  => $zipcode,
        'city'     => $city
    ]);

    // 5. Ouverture de la session d'achat
    $_SESSION['user_id'] = $pdo->lastInsertId();
    $_SESSION['user_name'] = $firstname;
    
    // Message vert de confirmation
    $_SESSION['success_register'] = "✨ Félicitations " . htmlspecialchars($firstname) . ", votre compte de livraison a été créé avec succès !";

    // Redirection directe vers ton catalogue
    header('Location: produits.php');
    exit();

} catch (Exception $e) {
    $_SESSION['error_login'] = 'Erreur technique lors de l\'enregistrement : ' . $e->getMessage();
    header('Location: connexion.php');
    exit();
}