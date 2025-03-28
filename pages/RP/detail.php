<?php

// Vérification des droits d'accès (RP seulement - id_r = 3)
if (!isset($_SESSION['id_r']) || $_SESSION['id_r'] != 3) {
    header('Location: ../../index.php');
    exit;
}

if (!isset($_GET['livre_id'])) {
    header('Location: index.php?action=listExemplaire');
    exit;
}

$livre_id = intval($_GET['livre_id']);

// Récupérer les détails du livre
$sql_livre = "SELECT l.*, r.libelle AS rayon 
              FROM livre l 
              JOIN rayon r ON l.id_r = r.id 
              WHERE l.id = $livre_id";
$livre = mysqli_fetch_assoc(mysqli_query($connexion, $sql_livre));

// Récupérer les exemplaires de ce livre
$sql_exemplaires = "SELECT * FROM exemplaire WHERE id_l = $livre_id ORDER BY statut, date_enregistre";
$exemplaires = mysqli_query($connexion, $sql_exemplaires);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails des exemplaires</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .exemplaire-card {
            border-left: 4px solid #4361ee;
            margin-bottom: 1rem;
        }
        .status-badge {
            font-size: 0.8rem;
            padding: 5px 10px;
            border-radius: 10px;
        }
        .book-cover {
            width: 100px;
            height: 150px;
            object-fit: cover;
        }
    </style>
</head>
<body>

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-info-circle me-2"></i>Détails des exemplaires</h2>
            <a href="index.php?action=listExemplaire" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Retour
            </a>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="card mb-4">
                    <?php if (!empty($livre['photo'])): ?>
                        <img src="../../uploads/<?= htmlspecialchars($livre['photo']) ?>" class="card-img-top book-cover mx-auto mt-3" alt="Couverture">
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-book fa-5x text-secondary"></i>
                        </div>
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($livre['titre']) ?></h5>
                        <p class="card-text"><?= htmlspecialchars($livre['auteur']) ?></p>
                        <p class="card-text">
                            <small class="text-muted">
                                <?= htmlspecialchars($livre['rayon']) ?> - 
                                <?= date('d/m/Y', strtotime($livre['date_edition'])) ?>
                            </small>
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-8">
                <h4 class="mb-3">Exemplaires (<?= mysqli_num_rows($exemplaires) ?>)</h4>
                
                <?php if (mysqli_num_rows($exemplaires) > 0): ?>
                    <div class="list-group">
                        <?php while ($ex = mysqli_fetch_assoc($exemplaires)): ?>
                            <div class="list-group-item exemplaire-card">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>ID: <?= $ex['id'] ?></strong>
                                        <div class="mt-1">
                                            <span class="status-badge bg-<?= 
                                                $ex['statut'] == 'disponible' ? 'success' : 
                                                ($ex['statut'] == 'emprunté' ? 'warning' : 'danger')
                                            ?>">
                                                <?= ucfirst($ex['statut']) ?>
                                            </span>
                                            <span class="badge bg-secondary ms-2"><?= ucfirst($ex['etat']) ?></span>
                                        </div>
                                        <small class="text-muted">
                                            Enregistré le: <?= date('d/m/Y', strtotime($ex['date_enregistre'])) ?>
                                        </small>
                                    </div>
                                    <div>
                                        <a href="index.php?action=editExemplaire&id=<?= $ex['id'] ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Aucun exemplaire trouvé pour ce livre.
                    </div>
                <?php endif; ?>
                
                <div class="mt-3">
                    <a href="index.php?action=addExemplaire&livre_id=<?= $livre_id ?>" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Ajouter un exemplaire
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>