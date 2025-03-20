<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start(); // Assure-toi que la session est démarrée
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';
require 'PHPMailer-master/src/Exception.php';
require_once 'database.php';

$id_exemplaire = $_GET['id'] ?? null;
$id_adherent = $_SESSION['id'];

if ($id_exemplaire) {
    // Récupérer les informations de l'adhérent
    $sql_adherent = "SELECT nom, prenom, email FROM user WHERE id = ?";
    $stmt_adherent = mysqli_prepare($connexion, $sql_adherent);
    mysqli_stmt_bind_param($stmt_adherent, 'i', $id_adherent);
    mysqli_stmt_execute($stmt_adherent);
    $result_adherent = mysqli_stmt_get_result($stmt_adherent);
    $adherent = mysqli_fetch_assoc($result_adherent);

    if (!$adherent) {
        die("Erreur : Adhérent non trouvé.");
    }

    // Récupérer les informations du livre
    $sql_livre = "SELECT l.titre FROM livre l INNER JOIN exemplaire e ON l.id = e.id_l WHERE e.id = ?";
    $stmt_livre = mysqli_prepare($connexion, $sql_livre);
    mysqli_stmt_bind_param($stmt_livre, 'i', $id_exemplaire);
    mysqli_stmt_execute($stmt_livre);
    $result_livre = mysqli_stmt_get_result($stmt_livre);
    $livre = mysqli_fetch_assoc($result_livre);

    if (!$livre) {
        die("Erreur : Livre non trouvé.");
    }

    // Configuration de PHPMailer
    $mail = new PHPMailer\PHPMailer\PHPMailer();
    try {
        $mail->isSMTP();
        $mail->Host = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Username = '277085ef949294'; // Remplace par ton identifiant MailTrap
        $mail->Password = 'ac741f7704ad75'; // Remplace par ton mot de passe MailTrap
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 2525;

        $mail->setFrom('bibliotheque@example.com', 'Bibliotheque');
        $mail->addAddress($adherent['email']);

        $mail->isHTML(true);
        $mail->Subject = "Confirmation d'emprunt";
        $mail->Body    = "<h3>Bonjour {$adherent['prenom']} {$adherent['nom']},</h3>
                        <p>Vous avez emprunté le livre <strong>{$livre['titre']}</strong> le <strong>" . date('Y-m-d') . "</strong>.</p>
                        <p>Date de retour prévue : <strong>" . date('Y-m-d', strtotime('+14 days')) . "</strong>.</p>
                        <p>Vous avez actuellement <strong>" . ($nb_emprunts + 1) . "</strong> emprunts en cours. La limite est de 3 emprunts.</p>
                        <p>Merci de respecter la date de retour.</p>
                        <p><a href='generate_pdf.php?id=$id_exemplaire'>Télécharger le rapport en PDF</a></p>";

        if ($mail->send()) {
            echo "<div class='alert alert-success'>Email envoyé avec succès.</div>";
        } else {
            throw new Exception("Erreur lors de l'envoi de l'email : {$mail->ErrorInfo}");
        }
    } catch (Exception $e) {
        echo "<div class='alert alert-danger'>{$e->getMessage()}</div>";
    }
} else {
    echo "<div class='alert alert-warning'>ID exemplaire manquant.</div>";
}
?>