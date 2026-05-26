<?php
session_start();
require_once 'includes/db.php'; // Charge l'instance $pdo de ta base de données

header('Content-Type: application/json');

// Récupération sécurisée du flux JSON
$data = json_decode(file_get_contents('php://input'), true);

$firstname = trim($data['firstname'] ?? '');
$lastname  = trim($data['lastname'] ?? '');
$email     = trim($data['email'] ?? '');
$phone     = trim($data['phone'] ?? '');
$address   = trim($data['address'] ?? '');
$password  = $data['password'] ?? '';

if (empty($firstname) || empty($lastname) || empty($email) || empty($phone) || empty($address) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Veuillez renseigner tous les champs requis pour la livraison.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Le format de votre adresse email est invalide.']);
    exit;
}

try {
    // 1. Contrôle des doublons d'email
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Cette adresse email est déjà rattachée à un compte.']);
        exit;
    }

    // 2. Hachage du mot de passe
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // 3. Assemblage des données pour stockage dans le champ 'name'
    $fullProfileName = $firstname . ' ' . $lastname . ' (Tél: ' . $phone . ' - Exp: ' . $address . ')';

    // 4. Écriture immédiate dans la base de données
    $stmtInsert = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
    $stmtInsert->execute([
        'name'     => $fullProfileName,
        'email'    => $email,
        'password' => $hashedPassword
    ]);

    // 5. Initialisation des cookies de session
    $_SESSION['user_id'] = $pdo->lastInsertId();
    $_SESSION['user_name'] = $firstname;
    
    // Message de confirmation vert qui sera affiché si l'utilisateur revient sur la page de connexion
    $_SESSION['success_register'] = "✨ Félicitations " . htmlspecialchars($firstname) . ", vous êtes bien inscrit ! Votre compte de livraison a été configuré avec succès.";

    echo json_encode(['success' => true, 'message' => 'Compte créé !']);
    exit;

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur fatale base de données : ' . $e->getMessage()]);
    exit;
}