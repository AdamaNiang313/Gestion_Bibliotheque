<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/PHPMailer-master/src/Exception.php';
require __DIR__ . '/PHPMailer-master/src/PHPMailer.php';
require __DIR__ . '/PHPMailer-master/src/SMTP.php';

// Fonction pour configurer PHPMailer
function configurerMailer() {
    $mail = new PHPMailer(true);
    
    try {
        // Configuration SMTP
        $mail->isSMTP();
        $mail->Host = getenv('SMTP_HOST') ?: 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Username = getenv('SMTP_USER') ?: '277085ef949294';
        $mail->Password = getenv('SMTP_PASS') ?: 'ac741f7704ad75';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = getenv('SMTP_PORT') ?: 2525;
        
        // Configuration de base
        $mail->CharSet = 'UTF-8';
        $mail->setFrom(getenv('MAIL_FROM') ?: 'no-reply@bibliotheque.com', 'Bibliothèque');
        
        return $mail;
    } catch (Exception $e) {
        throw new Exception("Erreur de configuration du mailer: " . $e->getMessage());
    }
}

// Fonction pour envoyer un e-mail après inscription
function envoyerEmailInscription($email, $nom, $password = null) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Adresse email invalide");
    }

    $mail = configurerMailer();
    
    try {
        // Destinataires
        $mail->addAddress($email, $nom); 
        
        // Contenu de l'email
        $mail->isHTML(true);
        $mail->Subject = 'Vos identifiants de connexion';
        
        $passwordDisplay = $password ? "<strong>$password</strong>" : "le mot de passe que vous avez choisi";
        $mail->Body = "
            <h3>Bonjour $nom,</h3>
            <p>Votre compte a été créé avec succès.</p>
            <p>Vos identifiants :</p>
            <ul>
                <li>Login : <strong>$email</strong></li>
                <li>Mot de passe : $passwordDisplay</li>
            </ul>
            <p>Cordialement,<br>L'équipe de la bibliothèque.</p>
        ";

        // Envoi du mail
        $mail->send();
        return true;
    } catch (Exception $e) {
        throw new Exception("Erreur lors de l'envoi de l'email d'inscription: " . $mail->ErrorInfo);
    }
}

// Fonction pour envoyer un e-mail après emprunt
function envoyerEmailEmprunt($email, $nom, $titreLivre, $nbEmprunts, $idExemplaire = null) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Adresse email invalide");
    }

    $mail = configurerMailer();
    
    try {
        // Destinataires
        $mail->addAddress($email, $nom); 
        
        // Contenu de l'email
        $mail->isHTML(true);
        $mail->Subject = "Confirmation d'emprunt";
        
        $dateRetour = date('Y-m-d', strtotime('+14 days'));
        $pdfLink = $idExemplaire ? "<p><a href='generate_pdf.php?id=$idExemplaire'>Télécharger le reçu en PDF</a></p>" : "";
        
        $mail->Body = "
            <h3>Bonjour $nom,</h3>
            <p>Vous avez emprunté le livre <strong>$titreLivre</strong> le <strong>" . date('Y-m-d') . "</strong>.</p>
            <p>Date de retour prévue : <strong>$dateRetour</strong>.</p>
            <p>Vous avez actuellement <strong>$nbEmprunts</strong> emprunts en cours. La limite est de 3 emprunts.</p>
            <p>Merci de respecter la date de retour.</p>
            $pdfLink
            <p>Cordialement,<br>L'équipe de la bibliothèque.</p>
        ";

        // Envoi du mail
        $mail->send();
        return true;
    } catch (Exception $e) {
        throw new Exception("Erreur lors de l'envoi de l'email de confirmation d'emprunt: " . $mail->ErrorInfo);
    }
}

function notifierGestionnaireNouvelEmprunt($emprunt_id, $connexion, $admin_email = null) {
    // 1. Récupérer les informations de l'emprunt
    $sql = "SELECT e.id, l.titre, u.nom, u.prenom, u.email AS email_adherent,
                   e.date_debut, e.date_fin
            FROM emprunt e
            JOIN user u ON e.id_adherent = u.id
            JOIN exemplaire ex ON e.id_exemplaire = ex.id
            JOIN livre l ON ex.id_l = l.id
            WHERE e.id = ?";
    
    $stmt = mysqli_prepare($connexion, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $emprunt_id);
    mysqli_stmt_execute($stmt);
    $emprunt = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if (!$emprunt) {
        throw new Exception("Emprunt introuvable");
    }

    // 2. Récupérer l'email du gestionnaire si non spécifié
    if (!$admin_email) {
        $sql_admin = "SELECT email FROM user WHERE id_r = 1 LIMIT 1"; // 1 = gestionnaire
        $admin_email = mysqli_fetch_assoc(mysqli_query($connexion, $sql_admin))['email'];
    }

    // 3. Préparer l'email
    $mail = configurerMailer();
    try {
        $mail->addAddress($admin_email);
        $mail->Subject = "[Bibliothèque] Nouvelle demande d'emprunt #".$emprunt['id'];

        $mail->isHTML(true);
        $mail->Body = "
            <h3>Nouvelle demande d'emprunt</h3>
            <p><strong>Livre:</strong> ".htmlspecialchars($emprunt['titre'])."</p>
            <p><strong>Adhérent:</strong> ".htmlspecialchars($emprunt['prenom'].' '.$emprunt['nom'])."</p>
            <p><strong>Email:</strong> ".htmlspecialchars($emprunt['email_adherent'])."</p>
            <p><strong>Demandé le:</strong> ".date('d/m/Y', strtotime($emprunt['date_debut']))."</p>
            <p><strong>À rendre avant:</strong> ".date('d/m/Y', strtotime($emprunt['date_fin']))."</p>
            
            <div style='margin-top: 20px;'>
                <a href='".getenv('APP_URL')."/pages/gestionnaire/valider_emprunts.php'
                   style='background-color: #4361ee; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;'>
                   Gérer les demandes
                </a>
            </div>
            
            <p style='margin-top: 20px; font-size: 0.9em; color: #666;'>
                Cet email a été envoyé automatiquement, merci de ne pas y répondre.
            </p>
        ";

        return $mail->send();
    } catch (Exception $e) {
        error_log("Erreur notification gestionnaire: ".$e->getMessage());
        return false;
    }
}

// Fonction utilitaire pour afficher les messages
function afficherMessage($type, $message) {
    echo "<div class='alert alert-$type'>$message</div>";
}

// Gestion des actions (uniquement si le fichier est appelé directement)
if (basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
    session_start();
    
    // Vérifier si la connexion à la base de données est disponible
    if (!isset($connexion)) {
        require_once __DIR__ . '/../uploads/database.php'; // Adaptez le chemin selon votre structure
    }

    $action = $_GET['action'] ?? null;

    try {
        if ($action === 'inscription') {
            $email = $_POST['email'] ?? $_GET['email'] ?? null;
            $nom = $_POST['nom'] ?? $_GET['nom'] ?? null;
            
            if (!$email || !$nom) {
                throw new Exception("Email et nom sont requis");
            }
            
            envoyerEmailInscription($email, $nom);
            afficherMessage('success', 'Email d\'inscription envoyé avec succès.');
            
        } elseif ($action === 'emprunt') {
            if (!isset($_SESSION['id'])) {
                throw new Exception("Vous devez être connecté");
            }
            
            $id_exemplaire = $_GET['id'] ?? null;
            $id_adherent = $_SESSION['id'];
            
            if (!$id_exemplaire) {
                throw new Exception("ID exemplaire manquant");
            }

            // Récupérer les informations de l'adhérent
            $sql_adherent = "SELECT nom, prenom, email FROM user WHERE id = ?";
            $stmt_adherent = mysqli_prepare($connexion, $sql_adherent);
            mysqli_stmt_bind_param($stmt_adherent, 'i', $id_adherent);
            mysqli_stmt_execute($stmt_adherent);
            $result_adherent = mysqli_stmt_get_result($stmt_adherent);
            $adherent = mysqli_fetch_assoc($result_adherent);

            if (!$adherent) {
                throw new Exception("Adhérent non trouvé");
            }

            // Récupérer les informations du livre
            $sql_livre = "SELECT l.titre FROM livre l INNER JOIN exemplaire e ON l.id = e.id_l WHERE e.id = ?";
            $stmt_livre = mysqli_prepare($connexion, $sql_livre);
            mysqli_stmt_bind_param($stmt_livre, 'i', $id_exemplaire);
            mysqli_stmt_execute($stmt_livre);
            $result_livre = mysqli_stmt_get_result($stmt_livre);
            $livre = mysqli_fetch_assoc($result_livre);

            if (!$livre) {
                throw new Exception("Livre non trouvé");
            }

            // Récupérer le nombre d'emprunts en cours
            $sql_count_emprunts = "SELECT COUNT(*) AS nb_emprunts FROM emprunt WHERE id_adherent = ? AND date_fin IS NULL";
            $stmt_count = mysqli_prepare($connexion, $sql_count_emprunts);
            mysqli_stmt_bind_param($stmt_count, 'i', $id_adherent);
            mysqli_stmt_execute($stmt_count);
            $result_count = mysqli_stmt_get_result($stmt_count);
            $row_count = mysqli_fetch_assoc($result_count);
            $nb_emprunts = $row_count['nb_emprunts'];

            // Envoyer l'email
            envoyerEmailEmprunt(
                $adherent['email'], 
                $adherent['prenom'] . ' ' . $adherent['nom'], 
                $livre['titre'], 
                $nb_emprunts + 1,
                $id_exemplaire
            );
            
            afficherMessage('success', 'Email de confirmation d\'emprunt envoyé avec succès.');
        } else {
            afficherMessage('warning', 'Action non spécifiée.');
        }
    } catch (Exception $e) {
        afficherMessage('danger', 'Erreur: ' . $e->getMessage());
    }
}
?>