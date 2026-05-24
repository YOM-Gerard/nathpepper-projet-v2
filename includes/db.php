<?php
// Informations de connexion à la base de données
$host = 'localhost';
$dbname = 'nathpepper_db';
$username = 'root';
$password = ''; // Par défaut sur XAMPP, le mot de passe est vide

try {
    // On crée la connexion sécurisée avec PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // On dit à PHP de lever une grosse erreur si une requête SQL plante
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // On demande à recevoir les résultats sous forme de tableaux associatifs simples
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    // Si la connexion échoue (ex: MySQL éteint), on arrête tout et on affiche l'erreur
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}