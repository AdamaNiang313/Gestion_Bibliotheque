<?php
// Vérification de l'authentification et du rôle (2 = adhérent)

$id_adherent = $_SESSION['id'];

// Récupérer les emprunts en cours avec jointures correctes
$sql = "SELECT 
        e.id, 
        l.titre, 
        IFNULL(l.photo, '') AS photo, 
        e.date_debut, 
        e.date_fin,
        e.statut AS statut_emprunt,
        ex.statut AS statut_exemplaire
    FROM emprunt e
    JOIN exemplaire ex ON e.id_exemplaire = ex.id
    JOIN livre l ON ex.id_l = l.id
    WHERE e.id_adherent = ? 
    AND e.statut = 'valide'
    AND (e.date_fin IS NULL OR e.date_fin > NOW())
    ORDER BY e.date_debut DESC";

$stmt = mysqli_prepare($connexion, $sql);
mysqli_stmt_bind_param($stmt, 'i', $id_adherent);
mysqli_stmt_execute($stmt);
$emprunts = mysqli_stmt_get_result($stmt);

// Debug temporaire - à enlever en production
// echo "<pre>"; print_r(mysqli_fetch_all($emprunts, MYSQLI_ASSOC)); echo "</pre>";
// mysqli_data_seek($emprunts, 0);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Emprunts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .emprunt-card {
            transition: all 0.3s ease;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            height: 100%;
        }
        .emprunt-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
        }
        .book-img-container {
            height: 180px;
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .book-img {
            max-height: 100%;
            max-width: 100%;
            object-fit: contain;
        }
        .status-badge {
            font-size: 0.8rem;
            padding: 5px 10px;
            border-radius: 10px;
        }
        .empty-icon {
            font-size: 3rem;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container py-4 mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">
                <i class="fas fa-bookmark me-2"></i>Mes Emprunts en cours
            </h2>
            <a href="historique.php" class="btn btn-outline-secondary">
                <i class="fas fa-history me-1"></i> Voir l'historique
            </a>
        </div>

        <?php if (mysqli_num_rows($emprunts) > 0): ?>
            <div class="row row-cols-1 row-cols-md-2 g-4">
                <?php while ($emprunt = mysqli_fetch_assoc($emprunts)): ?>
                    <div class="col">
                        <div class="card emprunt-card">
                            <div class="row g-0">
                                <div class="col-md-4">
                                    <div class="book-img-container">
                                        <?php if (!empty($emprunt['photo'])): ?>
                                            <img src="../uploads/<?= htmlspecialchars($emprunt['photo']) ?>" 
                                                 class="book-img" 
                                                 alt="<?= htmlspecialchars($emprunt['titre']) ?>">
                                        <?php else: ?>
                                            <i class="fas fa-book empty-icon"></i>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="card-body">
                                        <h5 class="card-title"><?= htmlspecialchars($emprunt['titre']) ?></h5>
                                        
                                        <div class="d-flex flex-wrap gap-2 mb-3">
                                            <span class="status-badge bg-<?= $emprunt['statut_emprunt'] == 'valide' ? 'success' : 'warning' ?>">
                                                <?= ucfirst($emprunt['statut_emprunt']) ?>
                                            </span>
                                            <span class="status-badge bg-info">
                                                <?= ucfirst($emprunt['statut_exemplaire']) ?>
                                            </span>
                                        </div>
                                        
                                        <ul class="list-unstyled">
                                            <li class="mb-2">
                                                <i class="fas fa-calendar-alt text-muted me-2"></i>
                                                <small>Emprunté le: <?= date('d/m/Y', strtotime($emprunt['date_debut'])) ?></small>
                                            </li>
                                            <li class="mb-2">
                                                <i class="fas fa-calendar-check text-muted me-2"></i>
                                                <small>Retour avant: <?= date('d/m/Y', strtotime($emprunt['date_fin'])) ?></small>
                                            </li>
                                            <li>
                                                <?php 
                                                $jours_restants = floor((strtotime($emprunt['date_fin']) - time()) / (60 * 60 * 24));
                                                $couleur = $jours_restants <= 3 ? 'danger' : ($jours_restants <= 7 ? 'warning' : 'success');
                                                ?>
                                                <i class="fas fa-clock text-<?= $couleur ?> me-2"></i>
                                                <small class="text-<?= $couleur ?>">
                                                    <?= $jours_restants > 0 ? "$jours_restants jours restants" : "En retard de ".abs($jours_restants)." jours" ?>
                                                </small>
                                            </li>
                                        </ul>
                                        
                                        <div class="d-flex gap-2 mt-3">
                                            <button class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-sync-alt me-1"></i> Prolonger
                                            </button>
                                            <button class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-info-circle me-1"></i> Détails
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center py-5">
                <i class="fas fa-book-open fa-3x mb-3 text-muted"></i>
                <h4>Aucun emprunt en cours</h4>
                <p class="mb-0">Vous n'avez actuellement aucun livre emprunté.</p>
                <a href="list.php" class="btn btn-primary mt-3">
                    <i class="fas fa-book me-1"></i> Voir le catalogue
                </a>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>