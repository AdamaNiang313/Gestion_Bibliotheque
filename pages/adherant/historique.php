<?php


// Vérification de l'authentification et du rôle (2 = adhérent)

$id_adherent = $_SESSION['user_id'];

// Récupérer l'historique complet
$sql = "SELECT e.id, l.titre, a.nom AS auteur, 
               e.date_debut, e.date_fin, e.statut,
               ex.photo AS photo_exemplaire,
               DATEDIFF(e.date_fin, e.date_debut) AS duree,
               r.libelle AS rayon
        FROM emprunt e
        JOIN exemplaire ex ON e.id_exemplaire = ex.id
        JOIN livre l ON ex.id_l = l.id
        JOIN auteur a ON l.id_a = a.id
        JOIN rayon r ON l.id_r = r.id
        WHERE e.id_adherent = ?
        ORDER BY e.date_fin DESC";
$stmt = mysqli_prepare($connexion, $sql);
mysqli_stmt_bind_param($stmt, 'i', $id_adherent);
mysqli_stmt_execute($stmt);
$historique = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique des emprunts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
        }
        .history-item {
            transition: all 0.3s ease;
            border-left: 3px solid var(--primary-color);
        }
        .history-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        .book-thumbnail {
            width: 60px;
            height: 80px;
            object-fit: cover;
            border-radius: 4px;
        }
        .badge-returned {
            background-color: #6c757d;
        }
    </style>
</head>
<body>
    
    <main class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">
                <i class="fas fa-history me-2"></i>Historique des emprunts
            </h2>
            <a href="mes_emprunts.php" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-1"></i>Retour aux emprunts
            </a>
        </div>

        <?php if (mysqli_num_rows($historique) > 0): ?>
            <div class="row g-3">
                <?php while ($item = mysqli_fetch_assoc($historique)): ?>
                <div class="col-md-6">
                    <div class="card history-item h-100">
                        <div class="card-body">
                            <div class="d-flex">
                                <!-- Photo du livre -->
                                <?php if (!empty($item['photo_exemplaire'])): ?>
                                    <img src="/uploads/<?= htmlspecialchars($item['photo_exemplaire']) ?>" 
                                         class="book-thumbnail me-3"
                                         alt="<?= htmlspecialchars($item['titre']) ?>">
                                <?php else: ?>
                                    <div class="book-thumbnail bg-light me-3 d-flex align-items-center justify-content-center">
                                        <i class="fas fa-book fa-2x text-secondary"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="flex-grow-1">
                                    <h5 class="card-title mb-1"><?= htmlspecialchars($item['titre']) ?></h5>
                                    <p class="text-muted small mb-2">
                                        <i class="fas fa-user-edit me-1"></i>
                                        <?= htmlspecialchars($item['auteur']) ?>
                                    </p>
                                    
                                    <div class="d-flex flex-wrap gap-2 mb-2">
                                        <span class="badge bg-secondary">
                                            <?= htmlspecialchars($item['rayon']) ?>
                                        </span>
                                        <span class="badge bg-<?= 
                                            $item['statut'] == 'validé' ? 'success' : 'warning' 
                                        ?>">
                                            <?= ucfirst($item['statut']) ?>
                                        </span>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <small class="text-muted">
                                                <i class="far fa-calendar-alt me-1"></i>
                                                <?= date('d/m/Y', strtotime($item['date_debut'])) ?> - 
                                                <?= date('d/m/Y', strtotime($item['date_fin'])) ?>
                                            </small>
                                            <small class="text-muted d-block">
                                                (<?= $item['duree'] ?> jours)
                                            </small>
                                        </div>
                                        <?php if ($item['statut'] == 'validé'): ?>
                                            <a href="/generate_pdf.php?id=<?= $item['id'] ?>" 
                                               class="btn btn-sm btn-outline-primary"
                                               target="_blank">
                                               <i class="fas fa-file-pdf me-1"></i>Reçu
                                            </a>
                                        <?php endif; ?>
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
                <i class="fas fa-book-open fa-3x mb-3 text-primary"></i>
                <h4>Aucun emprunt dans l'historique</h4>
                <p class="mb-0">Vos emprunts passés apparaîtront ici</p>
                <a href="list.php" class="btn btn-primary mt-3">
                    <i class="fas fa-search me-1"></i>Explorer le catalogue
                </a>
            </div>
        <?php endif; ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>