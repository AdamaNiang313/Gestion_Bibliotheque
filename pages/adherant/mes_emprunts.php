<?php
// Vérifier que l'utilisateur est bien un adhérent (id_r = 2)

$id_adherent = $_SESSION['id'];

// Récupérer les emprunts en cours
$sql = "SELECT e.id, l.titre, l.photo, e.date_debut, e.date_fin, ex.statut
        FROM emprunt e
        JOIN exemplaire ex ON e.id_exemplaire = ex.id
        JOIN livre l ON ex.id_l = l.id
        WHERE e.id_adherent = ? AND e.date_fin IS NULL
        ORDER BY e.date_debut DESC";
$stmt = mysqli_prepare($connexion, $sql);
mysqli_stmt_bind_param($stmt, 'i', $id_adherent);
mysqli_stmt_execute($stmt);
$emprunts = mysqli_stmt_get_result($stmt);
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
        }
        .emprunt-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
        }
        .book-img {
            height: 150px;
            object-fit: cover;
        }
        .status-badge {
            font-size: 0.8rem;
            padding: 5px 10px;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <h2 class="mb-4"><i class="fas fa-bookmark me-2"></i>Mes Emprunts en cours</h2>
        
        <?php if (mysqli_num_rows($emprunts) > 0): ?>
            <div class="row g-4">
                <?php while ($emprunt = mysqli_fetch_assoc($emprunts)): ?>
                    <div class="col-md-6">
                        <div class="card emprunt-card h-100">
                            <div class="row g-0">
                                <div class="col-md-4">
                                    <?php if (!empty($emprunt['photo'])): ?>
                                        <img src="../uploads/<?= htmlspecialchars($emprunt['photo']) ?>" class="img-fluid rounded-start book-img" alt="Couverture">
                                    <?php else: ?>
                                        <div class="bg-light d-flex align-items-center justify-content-center h-100">
                                            <i class="fas fa-book fa-3x text-secondary"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-8">
                                    <div class="card-body">
                                        <h5 class="card-title"><?= htmlspecialchars($emprunt['titre']) ?></h5>
                                        <div class="mb-2">
                                            <span class="status-badge bg-<?= $emprunt['statut'] == 'emprunté' ? 'success' : 'warning' ?>">
                                                <?= ucfirst($emprunt['statut']) ?>
                                            </span>
                                        </div>
                                        <p class="card-text">
                                            <small class="text-muted">
                                                <i class="fas fa-calendar-alt me-1"></i>
                                                Emprunté le: <?= date('d/m/Y', strtotime($emprunt['date_debut'])) ?>
                                            </small>
                                        </p>
                                        <p class="card-text">
                                            <small class="text-muted">
                                                <i class="fas fa-calendar-check me-1"></i>
                                                À retourner avant: <?= date('d/m/Y', strtotime($emprunt['date_fin'])) ?>
                                            </small>
                                        </p>
                                        <button class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-sync-alt me-1"></i> Prolonger
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                Vous n'avez aucun emprunt en cours.
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>