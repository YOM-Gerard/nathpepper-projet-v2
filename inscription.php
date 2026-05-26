<?php
session_start();
require_once 'includes/db.php'; // Utilise la variable $pdo

// On détecte la nature de la requête : JSON (AJAX) ou POST classique (Formulaire HTML standard)
$data = json_decode(file_get_contents('php://input'), true);

if ($data) {
    // Si c'est du JSON (Modale interceptée par modals.js)
    $name = trim($data['name'] ?? '');
    $email = trim($data['email'] ?? '');
    $password = $data['password'] ?? '';
    $is_ajax = true;
} else {
    // Si c'est du POST standard (Formulaire HTML classique en secours)
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $is_ajax = false;
}

// ─── 1. VÉRIFICATION DES CHAMPS REQUIS ───
if (empty($name) || empty($email) || empty($password)) {
    if ($is_ajax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Veuillez remplir tous les champs obligatoires.']);
    } else {
        $_SESSION['error_login'] = 'Veuillez remplir tous les champs obligatoires.';
        header('Location: connexion.php');
    }
    exit;
}

// ─── 2. VÉRIFICATION DU FORMAT DE L'EMAIL ───
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    if ($is_ajax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Format d\'email invalide.']);
    } else {
        $_SESSION['error_login'] = "Format d'email invalide.";
        header('Location: connexion.php');
    }
    exit;
}

try {
    // ─── 3. VÉRIFICATION DES DOUBLONS DANS LA BDD ───
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    if ($stmt->fetch()) {
        if ($is_ajax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Cet email est déjà associé à un compte.']);
        } else {
            $_SESSION['error_login'] = 'Cet email est déjà associé à un compte.';
            header('Location: connexion.php');
        }
        exit;
    }

    // ─── 4. HACHAGE SÉCURISÉ DU MOT DE PASSE ───
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // ─── 5. INSERTION DE L'UTILISATEUR ───
    $stmtInsert = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
    $stmtInsert->execute([
        'name' => $name,
        'email' => $email,
        'password' => $hashedPassword
    ]);

    // ─── 6. CONNEXION AUTOMATIQUE DE LA SESSION ───
    $_SESSION['user_id'] = $pdo->lastInsertId();
    $_SESSION['user_name'] = $name;

    // ─── 7. RÉPONSE ET REDIRECTION DYNAMIQUE ───
    if ($is_ajax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Inscription réussie !', 'user_name' => $name]);
    } else {
        // Si le script s'exécute de manière classique, on redirige vers le catalogue des poivres
        header('Location: produits.php');
    }
    exit;

} catch (Exception $e) {
    if ($is_ajax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Erreur technique : ' . $e->getMessage()]);
    } else {
        $_SESSION['error_login'] = 'Erreur technique : ' . $e->getMessage();
        header('Location: connexion.php');
    }
    exit;
}