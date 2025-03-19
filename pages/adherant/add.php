<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start(); // Ajout de session_start()
require_once 'database.php'; // Inclure la connexion à la base de données

$id_adherent = $_SESSION['id']; 

$sql = "SELECT l.titre, l.date_edition, COUNT(e.id) AS disponible_count, MIN(e.photo) AS photo, l.id AS id_livre
        FROM livre l
        LEFT JOIN exemplaire e ON l.id = e.id_l AND e.statut = 'disponible'
        GROUP BY l.titre, l.date_edition, l.id";
$livres = mysqli_query($connexion, $sql);

if (!$livres) {
    die("Erreur dans la requête SQL : " . mysqli_error($connexion));
}

$tabLivres = [];
while ($row = mysqli_fetch_assoc($livres)) {
    $tabLivres[] = $row;
}

if (isset($_GET['action']) && $_GET['action'] === 'emprunter' && isset($_GET['id'])) {
    $id_livre = $_GET['id'];
    
    $sql_find_exemplaire = "SELECT id FROM exemplaire WHERE id_l = ? AND statut = 'disponible' LIMIT 1";
    $stmt_find = mysqli_prepare($connexion, $sql_find_exemplaire);
    mysqli_stmt_bind_param($stmt_find, "i", $id_livre);
    mysqli_stmt_execute($stmt_find);
    $result_find = mysqli_stmt_get_result($stmt_find);
    
    if ($row = mysqli_fetch_assoc($result_find)) {
        $id_exemplaire = $row['id'];
        $date_emprunt = date('Y-m-d');  
        $date_fin = date('Y-m-d', strtotime('+2 weeks'));

        $sql_emprunt = "INSERT INTO emprunt (id_adherent, date_debut, date_fin, id_exemplaire) VALUES (?, ?, ?, ?)";
        $stmt_emprunt = mysqli_prepare($connexion, $sql_emprunt);
        mysqli_stmt_bind_param($stmt_emprunt, "issi", $id_adherent, $date_emprunt, $date_fin, $id_exemplaire);

        if (mysqli_stmt_execute($stmt_emprunt)) {
            $sql_update = "UPDATE exemplaire SET statut = 'emprunté' WHERE id = ?";
            $stmt_update = mysqli_prepare($connexion, $sql_update);
            mysqli_stmt_bind_param($stmt_update, "i", $id_exemplaire);
            mysqli_stmt_execute($stmt_update);
            
            header('Location: index.php?action=listEmprunt');
            exit;
        } else {
            echo "<div class='alert alert-danger'>Erreur lors de l'emprunt du livre.</div>";
        }
    } else {
        echo "<div class='alert alert-warning'>Aucun exemplaire disponible pour ce livre.</div>";
    }
}
?>