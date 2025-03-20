<?php
require('./fpdf/fpdf.php'); 
require_once 'database.php'; 

session_start();

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

    // Récupérer les informations du livre
    $sql_livre = "SELECT l.titre FROM livre l INNER JOIN exemplaire e ON l.id = e.id_l WHERE e.id = ?";
    $stmt_livre = mysqli_prepare($connexion, $sql_livre);
    mysqli_stmt_bind_param($stmt_livre, 'i', $id_exemplaire);
    mysqli_stmt_execute($stmt_livre);
    $result_livre = mysqli_stmt_get_result($stmt_livre);
    $livre = mysqli_fetch_assoc($result_livre);

    if ($adherent && $livre) {
        // Créer un nouveau PDF
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);

        // Titre du PDF
        $pdf->Cell(40, 10, 'Confirmation d\'emprunt');
        $pdf->Ln(20);

        // Informations de l'adhérent
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(40, 10, 'Nom : ' . $adherent['nom']);
        $pdf->Ln();
        $pdf->Cell(40, 10, 'Prénom : ' . $adherent['prenom']);
        $pdf->Ln();
        $pdf->Cell(40, 10, 'Email : ' . $adherent['email']);
        $pdf->Ln(20);

        // Informations du livre
        $pdf->Cell(40, 10, 'Livre emprunté : ' . $livre['titre']);
        $pdf->Ln();
        $pdf->Cell(40, 10, 'Date d\'emprunt : ' . date('Y-m-d'));
        $pdf->Ln();
        $pdf->Cell(40, 10, 'Date de retour prévue : ' . date('Y-m-d', strtotime('+14 days')));
        $pdf->Ln(20);

        // Enregistrer le PDF dans un fichier
        $filename = 'emprunt_' . $id_exemplaire . '.pdf';
        $pdf->Output('F', $filename);

        // Télécharger le PDF
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        readfile($filename);

        // Supprimer le fichier après téléchargement
        unlink($filename);
        exit;
    } else {
        die("Erreur : Informations manquantes pour générer le PDF.");
    }
} else {
    die("Erreur : ID exemplaire manquant.");
}
?>