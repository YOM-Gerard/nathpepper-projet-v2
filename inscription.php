<?php
session_start();
require_once 'includes/db.php'; // Utilise la variable $pdo

header('Content-Type: application/json');

// Récupération des données POST envoyées en JSON
$data = json_decode(file_get_contents('php://input'), true);

$name = trim($data['name'] ?? '');
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

if (empty($name) || empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Veuillez remplir tous les champs obligatoires.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Format d\'email invalide.']);
    exit;
}

try {
    // 1. Vérifier si l'adresse email existe déjà
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Cet email est déjà associé à un compte.']);
        exit;
    }

    // 2. Sécuriser le mot de passe par hachage
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // 3. Insérer le nouvel utilisateur
    $stmtInsert = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
    $stmtInsert->execute([
        'name' => $name,
        'email' => $email,
        'password' => $hashedPassword
    ]);

    // 4. Connecter automatiquement l'utilisateur après inscription
    $_SESSION['user_id'] = $pdo->lastInsertId();
    $_SESSION['user_name'] = $name;

    echo json_encode(['success' => true, 'message' => 'Inscription réussie !', 'user_name' => $name]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur technique : ' . $e->getMessage()]);
}