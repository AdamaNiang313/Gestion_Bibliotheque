<?php
// Vérification des droits d'accès (Gestionnaire seulement - id_r = 1)
if (!isset($_SESSION['id_r']) || $_SESSION['id_r'] != 1) {
    $_SESSION['error'] = "Accès non autorisé";
    header('Location: ../../index.php');
    exit;
}

require_once __DIR__ . '/../../mail.php';

// Configuration des chemins
define('UPLOAD_PATH', '../../uploads/');

// Traitement de la validation d'emprunt
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['valider_emprunt'])) {
    $id_emprunt = intval($_POST['id_emprunt']);
    $id_exemplaire = intval($_POST['id_exemplaire']);
    $id_adherent = intval($_POST['id_adherent']);

    try {
        // Début de transaction
        mysqli_begin_transaction($connexion);

        // 1. Validation de l'emprunt
        $sql_update = "UPDATE emprunt SET statut = 'valide', date_debut = NOW() WHERE id = ?";
        $stmt_update = mysqli_prepare($connexion, $sql_update);
        mysqli_stmt_bind_param($stmt_update, 'i', $id_emprunt);
        mysqli_stmt_execute($stmt_update);

        // 2. Mise à jour du statut de l'exemplaire
        $sql_exemplaire = "UPDATE exemplaire SET statut = 'emprunte' WHERE id = ?";
        $stmt_exemplaire = mysqli_prepare($connexion, $sql_exemplaire);
        mysqli_stmt_bind_param($stmt_exemplaire, 'i', $id_exemplaire);
        mysqli_stmt_execute($stmt_exemplaire);

        // 3. Récupération des infos pour l'email
        $sql_info = "SELECT u.email, u.prenom, u.nom, l.titre 
                    FROM emprunt e
                    JOIN user u ON e.id_adherent = u.id
                    JOIN exemplaire ex ON e.id_exemplaire = ex.id
                    JOIN livre l ON ex.id_l = l.id
                    WHERE e.id = ?";
        $stmt_info = mysqli_prepare($connexion, $sql_info);
        mysqli_stmt_bind_param($stmt_info, 'i', $id_emprunt);
        mysqli_stmt_execute($stmt_info);
        $result_info = mysqli_stmt_get_result($stmt_info);
        $info = mysqli_fetch_assoc($result_info);

        if (!$info) {
            throw new Exception("Impossible de récupérer les informations pour l'email");
        }

        // 4. Comptage des emprunts en cours
        $sql_count = "SELECT COUNT(*) AS nb FROM emprunt 
                     WHERE id_adherent = ? AND statut = 'valide' AND date_fin IS NULL";
        $stmt_count = mysqli_prepare($connexion, $sql_count);
        mysqli_stmt_bind_param($stmt_count, 'i', $id_adherent);
        mysqli_stmt_execute($stmt_count);
        $result_count = mysqli_stmt_get_result($stmt_count);
        $count = mysqli_fetch_assoc($result_count);

        // Validation de la transaction
        mysqli_commit($connexion);

        // Envoi de l'email de confirmation
        envoyerEmailEmprunt(
            $info['email'],
            $info['prenom'] . ' ' . $info['nom'],
            $info['titre'],
            $count['nb'],
            $id_exemplaire
        );

        $_SESSION['message'] = [
            'type' => 'success',
            'text' => "Emprunt validé avec succès et email envoyé."
        ];
        
    } catch (Exception $e) {
        mysqli_rollback($connexion);
        $_SESSION['message'] = [
            'type' => 'danger',
            'text' => "Erreur: " . $e->getMessage()
        ];
    }

    header("Location: index.php?action=listEmprunt");
    exit();
}

// Récupération des emprunts en attente
$sql = "SELECT e.id, e.date_debut, e.date_fin, e.id_adherent,
               l.titre, l.id as livre_id,
               u.nom, u.prenom, u.email, u.photo as photo_adherent,
               ex.photo as photo_exemplaire, ex.id as exemplaire_id,
               r.libelle as rayon,
               (SELECT COUNT(*) FROM exemplaire WHERE id_l = l.id AND statut = 'disponible') as disponible_count
        FROM emprunt e
        JOIN exemplaire ex ON e.id_exemplaire = ex.id
        JOIN livre l ON ex.id_l = l.id
        JOIN user u ON e.id_adherent = u.id
        JOIN rayon r ON l.id_r = r.id
        WHERE e.statut = 'en_attente'
        ORDER BY e.date_debut ASC";

$emprunts = mysqli_query($connexion, $sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validation des emprunts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .main-content {
            margin-left: 280px;
            padding-top: 70px;
        }
        @media (max-width: 992px) {
            .main-content {
                margin-left: 0;
            }
        }
        .card-emprunt {
            transition: all 0.3s ease;
            border-left: 4px solid #FFC107;
            margin-bottom: 20px;
        }
        .card-emprunt:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #dee2e6;
        }
        .book-cover {
            width: 60px;
            height: 80px;
            object-fit: cover;
            border: 1px solid #dee2e6;
        }
        .badge-pending {
            background-color: #FFC107;
            color: #212529;
        }
        .empty-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: #dee2e6;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
        }
        .empty-cover {
            width: 60px;
            height: 80px;
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="main-content">
        <div class="container py-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-clipboard-check me-2"></i>Demandes d'emprunt</h2>
                <span class="badge bg-primary">
                    <?= mysqli_num_rows($emprunts) ?> demande(s) en attente
                </span>
            </div>

            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-<?= $_SESSION['message']['type'] ?> alert-dismissible fade show">
                    <?= $_SESSION['message']['text'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['message']); ?>
            <?php endif; ?>

            <?php if (mysqli_num_rows($emprunts) > 0): ?>
                <div class="row">
                    <?php while ($emprunt = mysqli_fetch_assoc($emprunts)): ?>
                    <div class="col-md-6">
                        <div class="card card-emprunt h-100">
                            <div class="card-body">
                                <!-- En-tête avec info adhérent -->
                                <div class="d-flex justify-content-between mb-3">
                                    <div class="d-flex align-items-center">
                                        <?php if (!empty($emprunt['photo_adherent']) && file_exists(UPLOAD_PATH . $emprunt['photo_adherent'])): ?>
                                            <img src="<?= UPLOAD_PATH . htmlspecialchars($emprunt['photo_adherent']) ?>" 
                                                 class="user-avatar me-3" 
                                                 alt="Photo de <?= htmlspecialchars($emprunt['prenom']) ?>">
                                        <?php else: ?>
                                            <div class="empty-avatar me-3">
                                                <i class="fas fa-user"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <h5 class="mb-0"><?= htmlspecialchars($emprunt['prenom'].' '.$emprunt['nom']) ?></h5>
                                            <small class="text-muted"><?= htmlspecialchars($emprunt['email']) ?></small>
                                        </div>
                                    </div>
                                    <span class="badge badge-pending">
                                        <i class="fas fa-clock me-1"></i> En attente
                                    </span>
                                </div>
                                
                                <!-- Détails du livre -->
                                <div class="d-flex mb-3">
                                    <?php if (!empty($emprunt['photo_exemplaire']) && file_exists(UPLOAD_PATH . $emprunt['photo_exemplaire'])): ?>
                                        <img src="<?= UPLOAD_PATH . htmlspecialchars($emprunt['photo_exemplaire']) ?>" 
                                             class="book-cover me-3"
                                             alt="Couverture de <?= htmlspecialchars($emprunt['titre']) ?>">
                                    <?php else: ?>
                                        <div class="empty-cover me-3">
                                            <i class="fas fa-book"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <h6><?= htmlspecialchars($emprunt['titre']) ?></h6>
                                        <small class="text-muted"><?= htmlspecialchars($emprunt['rayon']) ?></small>
                                        <div class="mt-2">
                                            <span class="badge bg-<?= $emprunt['disponible_count'] > 0 ? 'success' : 'secondary' ?>">
                                                <?= $emprunt['disponible_count'] ?> exemplaire(s) disponible(s)
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Dates et actions -->
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="text-muted">
                                            <i class="far fa-calendar-alt me-1"></i>
                                            <?= date('d/m/Y', strtotime($emprunt['date_debut'])) ?>
                                        </small>
                                        <small class="text-muted ms-3">
                                            <i class="far fa-calendar-check me-1"></i>
                                            <?= date('d/m/Y', strtotime($emprunt['date_fin'])) ?>
                                        </small>
                                    </div>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="valider_emprunt" value="1">
                                        <input type="hidden" name="id_emprunt" value="<?= $emprunt['id'] ?>">
                                        <input type="hidden" name="id_exemplaire" value="<?= $emprunt['exemplaire_id'] ?>">
                                        <input type="hidden" name="id_adherent" value="<?= $emprunt['id_adherent'] ?>">
                                        <button type="submit" class="btn btn-sm btn-success"
                                                onclick="return confirm('Valider cet emprunt ?')">
                                            <i class="fas fa-check me-1"></i> Valider
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info text-center py-4">
                    <i class="fas fa-check-circle fa-3x mb-3 text-primary"></i>
                    <h4>Aucune demande en attente</h4>
                    <p class="mb-0">Toutes les demandes d'emprunt ont été traitées</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>