<?php
session_start();
require_once 'includes/db.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contactez-nous - Nathpepper</title>
    <link rel="stylesheet" href="styles/main.css">
    <link rel="stylesheet" href="styles/responsive.css">
    <link rel="stylesheet" href="styles/components.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@500;600&display=swap" rel="stylesheet">
    <style>
        /* Fond de page blanc écru lumineux */
        body { background-color: #fbf9f6 !important; color: #1a1b1c; font-family: 'Inter', sans-serif; }
        
        .contact-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; max-width: 1000px; margin: 0 auto; padding: 4rem 1.5rem; }
        
        .contact-title { font-family: 'Playfair Display', serif; color: #1a1b1c; font-size: 2.5rem; margin-bottom: 1.5rem; }
        
        .info-block { margin-bottom: 2rem; }
        .info-block h4 { color: #8d6e63; margin-bottom: 0.5rem; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1px; }
        .info-block p { color: #444444; line-height: 1.6; margin: 0; }
        
        /* Formulaire blanc épuré posé sur le fond écru */
        .contact-form { background: #ffffff; border: 1px solid #eae5dc; padding: 2.5rem; border-radius: 2px; box-shadow: 0 4px 20px rgba(0,0,0,0.02); }
        .form-group { margin-bottom: 1.5rem; text-align: left; }
        .form-group label { display: block; font-size: 0.85rem; color: #1a1b1c; margin-bottom: 0.5rem; font-weight: 500; }
        
        /* Inputs clairs aux contours fins */
        .form-control { width: 100%; padding: 12px; background: #fbf9f6; border: 1px solid #eae5dc; border-radius: 2px; color: #1a1b1c; font-family: inherit; font-size: 0.95rem; box-sizing: border-box; }
        .form-control:focus { border-color: #1a1b1c; outline: none; }
        
        /* ─── SARTORIAL REDESIGN DU BOUTON D'ENVOI ─── */
        .btn-gold { 
            background-color: #1a1b1c !important; /* Même couleur noire mate que ton footer */
            color: #ffffff !important; 
            border: 1px solid #1a1b1c !important; 
            width: 100%; 
            padding: 14px; 
            
            /* Bords plus arrondis et accueillants */
            border-radius: 6px !important; 
            
            /* Police Haute Gastronomie / Édition */
            font-family: 'Playfair Display', serif !important; 
            font-size: 1.05rem !important; 
            font-weight: 500 !important; /* Lettrage plus fin et sophistiqué */
            text-transform: none !important; /* On retire les majuscules massives */
            letter-spacing: 0.5px !important;
            
            cursor: pointer; 
            transition: all 0.2s ease-in-out !important; 
        }
        
        /* Légère réaction au survol */
        .btn-gold:hover { 
            background-color: #333333 !important; 
            border-color: #333333 !important; 
        }
        
        /* Action tactile mobile */
        .btn-gold:active { 
            background-color: #fbf9f6 !important; 
            color: #1a1b1c !important; 
        }

        /* Le header reste blanc pur comme sur produits.php */
        .header {
            background-color: #ffffff !important;
            border-bottom: 1px solid #eae5dc !important;
            box-shadow: 0