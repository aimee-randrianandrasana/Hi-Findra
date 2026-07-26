<?php

declare(strict_types=1);

namespace App\Core;

use PHPMailer\PHPMailer\Exception as PHPMailerException;
use PHPMailer\PHPMailer\PHPMailer;

// Centralise l'envoi d'emails via PHPMailer (SMTP)
final class Mailer
{
    public static function envoyer(string $destinataire, string $sujet, string $corpsHtml): bool
    {
        $config = (require dirname(__DIR__, 2) . '/config/config.php')['mail'];

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = $config['host'];
            $mail->Port       = $config['port'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $config['username'];
            $mail->Password   = $config['password'];
            $mail->SMTPSecure = $config['encryption'];
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom($config['from_address'], $config['from_name']);
            $mail->addAddress($destinataire);

            $mail->isHTML(true);
            $mail->Subject = $sujet;
            $mail->Body    = $corpsHtml;

            $mail->send();

            return true;
        } catch (PHPMailerException $e) {
            Logger::error("Echec d'envoi d'email a {$destinataire} : " . $e->getMessage());

            return false;
        }
    }
}
