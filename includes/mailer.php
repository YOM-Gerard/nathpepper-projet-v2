<?php
/**
 * Fonction centralisée pour envoyer et simuler des e-mails HTML propres (Couleurs du site)
 */
function envoyerEmailNathpepper($to, $subject, $messageHtml) {
    // En-têtes requis pour le format HTML
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= 'From: Nathpepper <no-reply@nathpepper.com>' . "\r\n";

    // Design aligné sur les codes couleurs du site
    $corpsEmail = "
    <div style='font-family: \"Inter\", Arial, Helvetica, sans-serif; max-width: 600px; margin: 40px auto; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden; background-color: #ffffff; box-shadow: 0 4px 12px rgba(0,0,0,0.03);'>
        <div style='background-color: #b71c1c; padding: 35px 20px; text-align: center; border-bottom: 3px solid #801111;'>
            <h1 style='color: #ffffff; margin: 0; font-size: 26px; font-weight: 700; letter-spacing: 4px; font-family: \"Playfair Display\", Georgia, serif;'>NTHPR.</h1>
            <p style='color: #ffcdd2; margin: 6px 0 0 0; font-size: 11px; letter-spacing: 2px; text-transform: uppercase; font-weight: 500;'>Épices Rares & Poivres d'Exception</p>
        </div>
        
        <div style='padding: 40px 40px; color: #333333; line-height: 1.7; font-size: 15px;'>
            $messageHtml
        </div>
        
        <div style='background-color: #f9f9f9; padding: 30px 20px; text-align: center; font-size: 12px; color: #666666; border-top: 1px solid #e0e0e0;'>
            <p style='margin: 0 0 6px 0; font-weight: 600; color: #333333;'>Nathpepper E-Commerce SAS</p>
            <p style='margin: 0 0 20px 0; color: #777777;'>12 rue des Poivres Rares, 75001 Paris, France</p>
            <div style='border-top: 1px solid #e0e0e0; margin-bottom: 20px; max-width: 150px; margin-left: auto; margin-right: auto;'></div>
            <p style='margin: 0; font-style: italic; color: #999999;'>Vous recevez cette notification automatique suite à l'activité récente de votre compte.</p>
        </div>
    </div>
    ";

    // --- LOG LOCAL (SIMULATION) ---
    $dir = __DIR__ . '/../mails_envoyes/';
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    $nomFichier = $dir . 'mail_' . time() . '_' . preg_replace('/[^a-zA-Z0-9]/', '_', $to) . '.html';
    $contenuLog = "\n\n" . $corpsEmail;
    file_put_contents($nomFichier, $contenuLog);

    // Envoi natif
    @mail($to, $subject, $corpsEmail, $headers);

    return true;
}