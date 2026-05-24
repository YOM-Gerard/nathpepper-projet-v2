<?php
session_start();
session_destroy(); // Détruit la session en cours
header('Location: produits.php'); // Renvoie à la boutique
exit;