<?php
/**
 * Fonction centralisée pour envoyer des e-mails HTML propres
 */
function envoyerEmailNathpepper($to, $subject, $messageHtml) {
    // En-têtes requis pour envoyer un e-mail au format HTML
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    
    // Expéditeur officiel
    $headers .= 'From: Nathpepper <no-reply@nathpepper.com>' . "\r\n";

    // Design global du mail (Header & Footer style Nathpepper)
    $corpsEmail = "
    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden;'>
        <div style='background-color: #b71c1c; padding: 20px; text-align: center;'>
            <h1 style='color: white; margin: 0; font-size: 24px; letter-spacing: 2px;'>NATHPEPPER</h1>
        </div>
        <div style='padding: 30px; color: #333; line-height: 1.6;'>
            $messageHtml
        </div>
        <div style='background-color: #f5f5f5; padding: 15px; text-align: center; font-size: 12px; color: #777; border-top: 1px solid #e0e0e0;'>
            Nathpepper E-Commerce SAS — 12 rue des Poivres Rares, 75001 Paris<br>
            Vous recevez cet e-mail suite à une activité sur votre compte.
        </div>
    </div>
    ";

    // Envoi effectif (En local sur XAMPP, cela simulera l'envoi ou s'écrira dans les logs de XAMPP)
    return @mail($to, $subject, $corpsEmail, $headers);
}