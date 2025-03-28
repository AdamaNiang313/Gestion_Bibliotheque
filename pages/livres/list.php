<?php
// Vérification des droits d'accès (Gestionnaire seulement - id_r = 1)
if (!isset($_SESSION['id_r']) || $_SESSION['id_r'] != 1) {
    $_SESSION['error'] = "Accès non autorisé";
    header('Location: ../index.php');
    exit;
}

// Configuration du chemin des uploads
define('UPLOAD_PATH', '../uploads/');

// Requête optimisée pour récupérer les livres groupés par rayon
$sql = "SELECT r.id AS rayon_id, r.libelle AS rayon_libelle,
               l.id AS livre_id, l.titre, l.date_edition, l.photo,
               a.id AS auteur_id, CONCAT(a.prenom, ' ', a.nom) AS auteur_nom_complet
        FROM rayon r
        LEFT JOIN livre l ON r.id = l.id_r
        LEFT JOIN auteur a ON l.id_a = a.id
        ORDER BY r.libelle, l.titre";

$result = mysqli_query($connexion, $sql);

if (!$result) {
    die("Erreur SQL : " . mysqli_error($connexion));
}

// Organisation des données par rayon
$livres_par_rayon = [];
while ($row = mysqli_fetch_assoc($result)) {
    if (!isset($livres_par_rayon[$row['rayon_id']])) {
        $livres_par_rayon[$row['rayon_id']] = [
            'libelle' => $row['rayon_libelle'],
            'livres' => []
        ];
    }
    
    if ($row['livre_id']) { // Vérifie qu'il y a bien un livre
        $livres_par_rayon[$row['rayon_id']]['livres'][] = [
            'id' => $row['livre_id'],
            'titre' => $row['titre'],
            'date_edition' => $row['date_edition'],
            'photo' => $row['photo'],
            'auteur' => $row['auteur_nom_complet']
        ];
    }
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
    <title>Gestion des livres par rayon</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .rayon-header {
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            color: white;
            padding: 1rem;
            border-radius: 8px 8px 0 0;
            margin-top: 2rem;
        }
        .livre-card {
            transition: all 0.3s ease;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            height: 100%;
        }
        .livre-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
        }
        .livre-img-container {
            height: 200px;
            overflow: hidden;
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .livre-img {
            max-height: 100%;
            max-width: 100%;
            object-fit: contain;
        }
        .empty-img {
            color: #6c757d;
            font-size: 3rem;
        }
        .action-btn {
            min-width: 80px;
        }
        .no-livre {
            padding: 2rem;
            text-align: center;
            color: #6c757d;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="container py-4 mt-5">
        <!-- En-tête avec bouton d'ajout -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-book me-2"></i>Gestion des livres</h2>
            <a href="?action=addLivre" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i> Ajouter un livre
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

        <!-- Affichage par rayon -->
        <?php foreach ($livres_par_rayon as $rayon_id => $rayon): ?>
            <div class="rayon-section mb-5">
                <div class="rayon-header">
                    <h3><i class="fas fa-archive me-2"></i><?= htmlspecialchars($rayon['libelle']) ?></h3>
                </div>
                
                <div class="bg-white p-3 rounded-bottom">
                    <?php if (empty($rayon['livres'])): ?>
                        <div class="no-livre">
                            <i class="fas fa-book-open fa-3x mb-3"></i>
                            <h5>Aucun livre dans ce rayon</h5>
                        </div>
                    <?php else: ?>
                        <div class="row g-4">
                            <?php foreach ($rayon['livres'] as $livre): ?>
                                <div class="col-md-6 col-lg-4 col-xl-3">
                                    <div class="card livre-card">
                                        <!-- Image du livre -->
                                        <div class="livre-img-container">
                                            <?php if (!empty($livre['photo']) && file_exists(UPLOAD_PATH . $livre['photo'])): ?>
                                                <img src="<?= UPLOAD_PATH . htmlspecialchars($livre['photo']) ?>" 
                                                     class="livre-img" 
                                                     alt="Couverture de <?= htmlspecialchars($livre['titre']) ?>">
                                            <?php else: ?>
                                                <div class="empty-img">
                                                    <i class="fas fa-book"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="card-body">
                                            <h5 class="card-title"><?= htmlspecialchars($livre['titre']) ?></h5>
                                            <p class="card-text text-muted mb-2">
                                                <i class="fas fa-user-edit me-1"></i>
                                                <?= htmlspecialchars($livre['auteur']) ?>
                                            </p>
                                            <p class="card-text text-muted">
                                                <i class="fas fa-calendar-alt me-1"></i>
                                                <?= date('d/m/Y', strtotime($livre['date_edition'])) ?>
                                            </p>
                                            
                                            <div class="d-flex justify-content-between mt-3">
                                                <a href="?action=editLivre&id=<?= $livre['id'] ?>" 
                                                   class="btn btn-sm btn-warning action-btn">
                                                    <i class="fas fa-edit me-1"></i> Modifier
                                                </a>
                                                <a href="?action=deleteLivre&id=<?= $livre['id'] ?>" 
                                                   class="btn btn-sm btn-danger action-btn"
                                                   onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce livre ?')">
                                                    <i class="fas fa-trash-alt me-1"></i> Supprimer
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>