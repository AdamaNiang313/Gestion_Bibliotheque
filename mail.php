<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/PHPMailer-master/src/Exception.php';
require __DIR__ . '/PHPMailer-master/src/PHPMailer.php';
require __DIR__ . '/PHPMailer-master/src/SMTP.php';

function envoyerEmailInscription($email, $nom) {
    $mail = new PHPMailer(true);
    try {
        // Configuration du serveur SMTP
        $mail->isSMTP();
        $mail->Host = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Username = '277085ef949294'; // À sécuriser avec des variables d'environnement
        $mail->Password = 'ac741f7704ad75'; // À sécuriser avec des variables d'environnement
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Meilleure pratique
        $mail->Port = 2525;
    
        // Destinataires
        $mail->setFrom('admin@votresite.com', 'Admin');
        $mail->addAddress($email, $nom); 
        
        // Contenu de l'email
        $mail->isHTML(true);
        $mail->Subject = 'Vos identifiants de connexion';
        $mail->Body = "Bonjour $nom,<br><br>Votre login : <strong>$email</strong><br>Votre mot de passe : <strong>******</strong><br><br>Cordialement, l'équipe.";

        // Envoi du mail
        $mail->send();
        echo "Email envoyé avec succès.";
    } catch (Exception $e) {
        echo "Erreur lors de l'envoi de l'email : {$mail->ErrorInfo}";
    }
}

// Exemple d'utilisation après l'inscription d'un utilisateur
// envoyerEmailInscription('test@example.com', 'Doe');

?>
