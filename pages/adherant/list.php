<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'database.php'; // Inclure la connexion à la base de données

$id_adherent = $_SESSION['id']; 

// Récupérer la liste des livres disponibles
$sql = "SELECT l.id AS id_livre, l.titre, l.date_edition, COUNT(e.id) AS disponible_count, MIN(e.photo) AS photo
        FROM livre l
        LEFT JOIN exemplaire e ON l.id = e.id_l AND e.statut = 'disponible'
        GROUP BY l.id, l.titre, l.date_edition";
$livres = mysqli_query($connexion, $sql);

if (!$livres) {
    die("Erreur dans la requête SQL : " . mysqli_error($connexion));
}

$tabLivres = [];
while ($row = mysqli_fetch_assoc($livres)) {
    $tabLivres[] = $row;
}

// Gestion de l'emprunt
if (isset($_GET['action']) && $_GET['action'] === 'emprunter' && isset($_GET['id'])) {
    $id_livre = $_GET['id'];

    // Sélectionner un exemplaire disponible
    $sql_exemplaire = "SELECT id FROM exemplaire WHERE id_l = ? AND statut = 'disponible' LIMIT 1";
    $stmt_exemplaire = mysqli_prepare($connexion, $sql_exemplaire);
    mysqli_stmt_bind_param($stmt_exemplaire, 'i', $id_livre);
    mysqli_stmt_execute($stmt_exemplaire);
    $result_exemplaire = mysqli_stmt_get_result($stmt_exemplaire);
    $exemplaire = mysqli_fetch_assoc($result_exemplaire);
    
    if ($exemplaire) {
        $id_exemplaire = $exemplaire['id'];
        $date_emprunt = date('Y-m-d');

        // Insérer l'emprunt dans la base de données
        $sql_emprunt = "INSERT INTO emprunt (id_adherent, date_debut, id_exemplaire) 
                        VALUES (?, ?, ?)";
        $stmt_emprunt = mysqli_prepare($connexion, $sql_emprunt);
        mysqli_stmt_bind_param($stmt_emprunt, 'isi', $id_adherent, $date_emprunt, $id_exemplaire);
        
        if (mysqli_stmt_execute($stmt_emprunt)) {
            // Mettre à jour le statut de l'exemplaire
            $sql_update = "UPDATE exemplaire SET statut = 'emprunté' WHERE id = ?";
            $stmt_update = mysqli_prepare($connexion, $sql_update);
            mysqli_stmt_bind_param($stmt_update, 'i', $id_exemplaire);
            mysqli_stmt_execute($stmt_update);
            
            // Redirection vers mail2.php pour envoyer l'e-mail
            header("Location: ./../mail2.php?id=$id_exemplaire");
            exit;
        } else {
            echo "<div class='alert alert-danger'>Erreur lors de l'emprunt du livre.</div>";
        }
    } else {
        echo "<div class='alert alert-warning'>Aucun exemplaire disponible pour ce livre.</div>";
    }
}
?>

<!-- Affichage des livres -->
<div class="container mt-4">
    <div class="row g-3">
        <?php foreach ($tabLivres as $livre) { ?>
            <div class="col-md-3 mb-4">
                <div class="card h-100">
                    <?php if (!empty($livre['photo'])) { ?>
                        <img src="uploads/<?= htmlspecialchars($livre['photo']) ?>" class="card-img-top" alt="Couverture du livre" style="height: 150px; object-fit: cover;">
                    <?php } else { ?>
                        <div class="text-center py-4 bg-light">
                            <span>Aucune photo disponible</span>
                        </div>
                    <?php } ?>
                    <div class="card-body text-center">
                        <span class="badge bg-primary mb-2"><i class="fas fa-calendar-alt"></i> Édition: <?= htmlspecialchars($livre['date_edition']) ?></span>
                        <h5 class="card-title mt-2"><i class="fas fa-book"></i> <?= htmlspecialchars($livre['titre']) ?></h5>
                        <p class="card-text"><i class="fas fa-check-circle"></i> Disponibles: <?= htmlspecialchars($livre['disponible_count']) ?></p>
                        <?php if ($livre['disponible_count'] > 0) { ?>
                            <a class="btn btn-danger" href="#" onclick="confirmerEmprunt('<?= $livre['id_livre'] ?>')">
                            <i class="fas fa-plus"></i> Emprunter
                            </a>
                        <?php } else { ?>
                            <button class="btn btn-secondary" disabled>Indisponible</button>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<script>
function confirmerEmprunt(idLivre) {
    if (confirm("Êtes-vous sûr de vouloir emprunter ce livre ?")) {
        window.location.href = "?action=emprunter&id=" + idLivre;
    }
}
</script>