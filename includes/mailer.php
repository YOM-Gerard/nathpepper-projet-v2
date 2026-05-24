<?php
/**
 * Fonction centralisée pour envoyer et simuler des e-mails HTML propres (CSS Inline)
 */
function envoyerEmailNathpepper($to, $subject, $messageHtml) {
    // En-têtes requis pour le format HTML
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= 'From: Nathpepper <no-reply@nathpepper.com>' . "\r\n";

    // Design global du mail figé en CSS Inline (Ultra compatible)
    $corpsEmail = "
    <div style='font-family: Arial, Helvetica, sans-serif; max-width: 600px; margin: 40px auto; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden; background-color: #ffffff; box-shadow: 0 4px 12px rgba(0,0,0,0.08);'>
        <div style='background-color: #b71c1c; padding: 30px 20px; text-align: center;'>
            <h1 style='color: #ffffff; margin: 0; font-size: 28px; font-weight: bold; letter-spacing: 4px; font-family: Arial, Helvetica, sans-serif;'>NATHPEPPER</h1>
            <p style='color: #ffcdd2; margin: 5px 0 0 0; font-size: 12px; letter-spacing: 1px; text-transform: uppercase;'>Épices Rares & Poivres d'Exception</p>
        </div>
        
        <div style='padding: 40px 35px; color: #333333; line-height: 1.7; font-size: 15px;'>
            $messageHtml
        </div>
        
        <div style='background-color: #f9f9f9; padding: 25px 20px; text-align: center; font-size: 12px; color: #777777; border-top: 1px solid #eeeeee;'>
            <p style='margin: 0 0 8px 0; font-weight: bold; color: #555555;'>Nathpepper E-Commerce SAS</p>
            <p style='margin: 0 0 15px 0; color: #888888;'>12 rue des Poivres Rares, 75001 Paris, France</p>
            <div style='border-top: 1px dashed #dddddd; margin-bottom: 15px;'></div>
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