<?php
// Vérification des droits d'accès (RP seulement - id_r = 3)
if (!isset($_SESSION['id_r'])) {
    header('Location: ../../index.php');
    exit;
}

// Vérification spécifique du rôle
if ($_SESSION['id_r'] != 3) {
    $_SESSION['error'] = "Accès non autorisé";
    header('Location: ../../index.php');
    exit;
}

// Configuration du chemin des uploads
define('UPLOAD_PATH', '../../uploads/');

// Requête optimisée avec jointures et statistiques
$sql = "SELECT l.id, l.titre, l.date_edition, 
               COUNT(CASE WHEN e.statut = 'disponible' THEN 1 END) AS disponible_count,
               COUNT(CASE WHEN e.statut = 'emprunté' THEN 1 END) AS emprunte_count,
               COUNT(CASE WHEN e.statut = 'réservé' THEN 1 END) AS reserve_count,
               COUNT(e.id) AS total_count,
               (SELECT photo FROM exemplaire WHERE id_l = l.id AND photo IS NOT NULL LIMIT 1) AS photo_exemplaire,
               r.libelle AS rayon
        FROM livre l
        LEFT JOIN exemplaire e ON l.id = e.id_l
        LEFT JOIN rayon r ON l.id_r = r.id
        GROUP BY l.id, l.titre, l.date_edition, r.libelle
        ORDER BY r.libelle, l.titre";

$result = mysqli_query($connexion, $sql);

if (!$result) {
    die("Erreur SQL : " . mysqli_error($connexion));
}

// Gestion des messages
$success = $_SESSION['success'] ?? null;
$error = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des exemplaires</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .book-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            height: 100%;
        }
        .book-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
        }
        .book-img-container {
            height: 200px;
            overflow: hidden;
            background-color: #f8f9fa;
        }
        .book-img {
            height: 100%;
            width: 100%;
            object-fit: cover;
        }
        .empty-img {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: #6c757d;
        }
        .stat-badge {
            font-size: 0.8rem;
            padding: 5px 10px;
            border-radius: 10px;
            margin-right: 5px;
            margin-bottom: 5px;
            display: inline-block;
        }
        .badge-disponible { background-color: #28a745; }
        .badge-emprunte { background-color: #17a2b8; }
        .badge-reserve { background-color: #ffc107; color: #212529; }
        .action-btn { min-width: 100px; }
    </style>
</head>
<body>
    <div class="container py-4 mt-5">
        <!-- En-tête avec bouton d'ajout -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-copy me-2"></i>Gestion des exemplaires</h2>
            <a href="?action=addExemplaire" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Ajouter un exemplaire
            </a>
        </div>

        <!-- Messages d'alerte -->
        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= htmlspecialchars($success) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?= htmlspecialchars($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Liste des livres -->
        <div class="row g-4">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($livre = mysqli_fetch_assoc($result)): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card book-card">
                            <div class="book-img-container">
                                <?php if (!empty($livre['photo_exemplaire']) && file_exists(UPLOAD_PATH . $livre['photo_exemplaire'])): ?>
                                    <img src="<?= UPLOAD_PATH . htmlspecialchars($livre['photo_exemplaire']) ?>" 
                                         class="book-img" 
                                         alt="Couverture de <?= htmlspecialchars($livre['titre']) ?>">
                                <?php else: ?>
                                    <div class="empty-img">
                                        <i class="fas fa-book fa-4x"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="card-body d-flex flex-column">
                                <div class="mb-2">
                                    <h5 class="card-title"><?= htmlspecialchars($livre['titre']) ?></h5>
                                    <small class="text-muted"><?= htmlspecialchars($livre['rayon']) ?></small>
                                </div>
                                
                                <div class="mt-auto">
                                    <!-- Statistiques -->
                                    <div class="mb-3">
                                        <span class="stat-badge badge-disponible">
                                            <i class="fas fa-check-circle me-1"></i>
                                            <?= $livre['disponible_count'] ?> Disponibles
                                        </span>
                                        <span class="stat-badge badge-emprunte">
                                            <i class="fas fa-book-reader me-1"></i>
                                            <?= $livre['emprunte_count'] ?> Empruntés
                                        </span>
                                        <span class="stat-badge badgereserve">
                                            <i class="fas fa-bookmark me-1"></i>
                                            <?= $livre['reserve_count'] ?> Réservés
                                        </span>
                                        <span class="stat-badge bg-secondary">
                                            <i class="fas fa-copy me-1"></i>
                                            <?= $livre['total_count'] ?> Total
                                        </span>
                                    </div>
                                    
                                    <!-- Boutons d'action -->
                                    <div class="d-flex justify-content-between">
                                        <a href="?action=detailExemplaire&livre_id=<?= $livre['id'] ?>" 
                                           class="btn btn-sm btn-outline-primary action-btn">
                                            <i class="fas fa-list me-1"></i> Détails
                                        </a>
                                        <a href="?action=addExemplaire&livre_id=<?= $livre['id'] ?>" 
                                           class="btn btn-sm btn-success action-btn">
                                            <i class="fas fa-plus me-1"></i> Ajouter
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info text-center py-4">
                        <i class="fas fa-info-circle fa-3x mb-3 text-primary"></i>
                        <h4>Aucun livre trouvé dans le catalogue</h4>
                        <p class="mb-3">Vous pouvez commencer par ajouter des livres</p>
                        <a href="../livres/add.php" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i> Ajouter un livre
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>