<?php
session_start();
require_once 'includes/db.php'; // On importe la connexion à la BDD (la variable $pdo)

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Sécurité de base : on s'assure que les champs ne sont pas vides
    if (empty($email) || empty($password)) {
        $_SESSION['error_login'] = "Veuillez remplir tous les champs.";
        header('Location: connexion.php');
        exit();
    }

    try {
        // 1. On cherche si un utilisateur possède cet email dans la BDD (on récupère aussi phone et address pour plus tard)
        $stmt = $pdo->prepare("SELECT id, name, password, phone, address, zipcode, city FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // 2. Si l'utilisateur existe, on vérifie son mot de passe haché
        if ($user && password_verify($password, $user['password'])) {
            
            // 🛠️ EXTRACTION DU PRÉNOM POUR LE HEADER
            // On sépare la chaîne "name" par les espaces et on récupère le premier morceau
            $nameParts = explode(' ', $user['name']);
            $firstname = $nameParts[0];

            // Succès : On stocke les informations dans la SESSION globale
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $firstname; // Contient uniquement le prénom désormais
            
            // Redirection vers la boutique
            header('Location: produits.php');
            exit();
        } else {
            // Échec : L'email n'existe pas ou le mot de passe est faux
            $_SESSION['error_login'] = "Identifiants ou mot de passe incorrects.";
            header('Location: connexion.php');
            exit();
        }

    } catch (Exception $e) {
        // En cas de problème avec la base de données
        $_SESSION['error_login'] = "Une erreur technique est survenue. Veuillez réessayer.";
        header('Location: connexion.php');
        exit();
    }
} else {
    header('Location: connexion.php');
    exit();
}