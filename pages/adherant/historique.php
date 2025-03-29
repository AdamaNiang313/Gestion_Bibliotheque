<?php
// Vérification de l'authentification et du rôle (2 = adhérent)

$id_adherent = $_SESSION['id']; // Correction: utiliser 'id' au lieu de 'user_id'

// Récupérer l'historique complet avec vérification des champs
$sql = "SELECT 
        e.id, 
        l.titre, 
        IFNULL(a.nom, 'Inconnu') AS auteur, 
        e.date_debut, 
        e.date_fin, 
        e.statut,
        IFNULL(ex.photo, '') AS photo_exemplaire,
        DATEDIFF(e.date_fin, e.date_debut) AS duree,
        IFNULL(r.libelle, 'Non classé') AS rayon
    FROM emprunt e
    JOIN exemplaire ex ON e.id_exemplaire = ex.id
    JOIN livre l ON ex.id_l = l.id
    LEFT JOIN auteur a ON l.id_a = a.id
    LEFT JOIN rayon r ON l.id_r = r.id
    WHERE e.id_adherent = ?
    ORDER BY e.date_fin DESC";

$stmt = mysqli_prepare($connexion, $sql);
mysqli_stmt_bind_param($stmt, 'i', $id_adherent);
mysqli_stmt_execute($stmt);
$historique = mysqli_stmt_get_result($stmt);

// Debug temporaire - à commenter en production
// echo "<pre>"; print_r(mysqli_fetch_all($historique, MYSQLI_ASSOC)); echo "</pre>";
// mysqli_data_seek($historique, 0);
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
        .history-card {
            transition: all 0.3s ease;
            border-left: 3px solid var(--primary-color);
            margin-bottom: 20px;
            height: 100%;
        }
        .history-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .book-thumbnail {
            width: 80px;
            height: 100px;
            object-fit: cover;
            border-radius: 4px;
        }
        .empty-thumbnail {
            width: 80px;
            height: 100px;
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
        }
        .status-badge {
            font-size: 0.8rem;
        }
    </style>
</head>
<body>
    <div class="container py-4 mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">
                <i class="fas fa-history me-2"></i>Historique complet
            </h2>
            <div>
                <a href="mes_emprunts.php" class="btn btn-outline-primary me-2">
                    <i class="fas fa-arrow-left me-1"></i>Emprunts en cours
                </a>
                <a href="list.php" class="btn btn-primary">
                    <i class="fas fa-book me-1"></i>Nouvel emprunt
                </a>
            </div>
        </div>

        <?php if (mysqli_num_rows($historique) > 0): ?>
            <div class="row row-cols-1 row-cols-md-2 g-4">
                <?php while ($item = mysqli_fetch_assoc($historique)): ?>
                <div class="col">
                    <div class="card history-card h-100">
                        <div class="card-body">
                            <div class="d-flex">
                                <!-- Couverture du livre -->
                                <div class="flex-shrink-0 me-3">
                                    <?php if (!empty($item['photo_exemplaire'])): ?>
                                        <img src="../uploads/<?= htmlspecialchars($item['photo_exemplaire']) ?>" 
                                             class="book-thumbnail"
                                             alt="Couverture de <?= htmlspecialchars($item['titre']) ?>">
                                    <?php else: ?>
                                        <div class="empty-thumbnail">
                                            <i class="fas fa-book fa-2x"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="flex-grow-1">
                                    <!-- Titre et auteur -->
                                    <h5 class="card-title"><?= htmlspecialchars($item['titre']) ?></h5>
                                    <p class="text-muted small mb-2">
                                        <i class="fas fa-user-edit me-1"></i>
                                        <?= htmlspecialchars($item['auteur']) ?>
                                    </p>
                                    
                                    <!-- Badges -->
                                    <div class="d-flex flex-wrap gap-2 mb-3">
                                        <span class="badge bg-secondary">
                                            <?= htmlspecialchars($item['rayon']) ?>
                                        </span>
                                        <span class="badge status-badge bg-<?= 
                                            $item['statut'] == 'valide' ? 'success' : 
                                            ($item['statut'] == 'en_attente' ? 'warning' : 'danger')
                                        ?>">
                                            <?= ucfirst($item['statut']) ?>
                                        </span>
                                    </div>
                                    
                                    <!-- Dates -->
                                    <div class="d-flex justify-content-between align-items-end">
                                        <div>
                                            <p class="small mb-1">
                                                <i class="far fa-calendar-alt text-muted me-2"></i>
                                                <span class="text-muted">Emprunt:</span> 
                                                <?= date('d/m/Y', strtotime($item['date_debut'])) ?>
                                            </p>
                                            <p class="small mb-1">
                                                <i class="far fa-calendar-check text-muted me-2"></i>
                                                <span class="text-muted">Retour:</span> 
                                                <?= date('d/m/Y', strtotime($item['date_fin'])) ?>
                                                <span class="text-muted">(<?= $item['duree'] ?> jours)</span>
                                            </p>
                                        </div>
                                        
                                        <!-- Bouton PDF si emprunt validé -->
                                        <?php if ($item['statut'] == 'valide'): ?>
                                            <a href="../generate_pdf.php?id=<?= $item['id'] ?>" 
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
                <i class="fas fa-book-open fa-3x mb-3 text-muted"></i>
                <h4>Aucun emprunt dans l'historique</h4>
                <p class="mb-0">Vos emprunts passés apparaîtront ici</p>
                <a href="list.php" class="btn btn-primary mt-3">
                    <i class="fas fa-book me-1"></i>Explorer le catalogue
                </a>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>