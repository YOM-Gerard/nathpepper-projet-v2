<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Identifiants de test temporaires
    $test_email = "admin@nathpepper.com";
    $test_password = "password123";
    $test_name = "Gérard";

    if ($email === $test_email && $password === $test_password) {
        // Succès : On stocke les informations de connexion dans la SESSION globale
        $_SESSION['user_id'] = 1;
        $_SESSION['user_name'] = $test_name;
        
        // Redirection vers la boutique
        header('Location: produits.php');
        exit();
    } else {
        // Échec : On crée un message d'erreur et on réaffiche le formulaire
        $_SESSION['error_login'] = "Identifiants incorrects. Indice : admin@nathpepper.com / password123";
        header('Location: connexion.php');
        exit();
    }
} else {
    header('Location: connexion.php');
    exit();
}