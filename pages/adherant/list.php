<?php
require_once 'database.php';

$id_adherent = $_SESSION['id']; 

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

if (isset($_GET['action']) && $_GET['action'] === 'emprunter' && isset($_GET['id'])) {
    $id_livre = $_GET['id'];

    // Sélectionner un exemplaire disponible
    $sql_exemplaire = "SELECT id FROM exemplaire WHERE id_l = '$id_livre' AND statut = 'disponible' LIMIT 1";
    $result_exemplaire = mysqli_query($connexion, $sql_exemplaire);
    $exemplaire = mysqli_fetch_assoc($result_exemplaire);
    
    if ($exemplaire) {
        $id_exemplaire = $exemplaire['id'];
        $date_emprunt = date('Y-m-d');

        $sql_emprunt = "INSERT INTO emprunt (id_adherent, date_debut, id_exemplaire) 
                        VALUES ('$id_adherent', '$date_emprunt', '$id_exemplaire')";
        if (mysqli_query($connexion, $sql_emprunt)) {
            $sql_update = "UPDATE exemplaire SET statut = 'emprunté' WHERE id = '$id_exemplaire'";
            mysqli_query($connexion, $sql_update);
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