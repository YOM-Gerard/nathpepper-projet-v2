<?php
/**
 * Fonction centralisée pour envoyer et simuler des e-mails HTML propres (Charte Sombre & Dorée)
 */
function envoyerEmailNathpepper($to, $subject, $messageHtml) {
    // En-têtes requis pour le format HTML
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= 'From: Nathpepper <no-reply@nathpepper.com>' . "\r\n";

    // Design aligné sur ta nouvelle charte : Fond #202020 et Beige doré #dbc49d
    $corpsEmail = "
    <div style='background-color: #202020; padding: 20px 0; font-family: \"Inter\", Arial, Helvetica, sans-serif;'>
        <div style='max-width: 600px; margin: 20px auto; border: 1px solid #2d2d2d; border-radius: 8px; overflow: hidden; background-color: #1a1b1c; box-shadow: 0 4px 15px rgba(0,0,0,0.5);'>
            
            <div style='background-color: #18191b; padding: 40px 20px; text-align: center; border-bottom: 1px solid #2d2d2d;'>
                <h1 style='color: #dbc49d; margin: 0; font-size: 28px; font-weight: 700; letter-spacing: 5px; font-family: \"Playfair Display\", Georgia, serif;'>NTHPR.</h1>
                <p style='color: #e4cca2; margin: 6px 0 0 0; font-size: 11px; letter-spacing: 2px; text-transform: uppercase; font-weight: 500;'>Épices Rares & Poivres d'Exception</p>
            </div>
            
            <div style='padding: 40px 40px; color: #e5e5e5; line-height: 1.7; font-size: 15px;'>
                $messageHtml
            </div>
            
            <div style='background-color: #18191b; padding: 30px 20px; text-align: center; font-size: 12px; color: #8a8a8a; border-top: 1px solid #2d2d2d;'>
                <p style='margin: 0 0 6px 0; font-weight: 600; color: #dbc49d;'>Nathpepper E-Commerce SAS</p>
                <p style='margin: 0 0 20px 0; color: #777777;'>12 rue des Poivres Rares, 75001 Paris, France</p>
                <div style='border-top: 1px solid #2d2d2d; margin-bottom: 20px; max-width: 150px; margin-left: auto; margin-right: auto;'></div>
                <p style='margin: 0; font-style: italic; color: #666666;'>Vous recevez cette notification automatique suite à l'activité récente de votre compte.</p>
            </div>
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