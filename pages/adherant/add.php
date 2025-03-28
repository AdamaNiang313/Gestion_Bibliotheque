<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);



$id_adherent = $_SESSION['id'];

// Requête pour les livres disponibles
$sql = "SELECT l.id, l.titre, l.auteur, l.date_edition, 
               COUNT(CASE WHEN e.statut = 'disponible' THEN 1 END) AS disponible_count,
               MIN(e.photo) AS photo
        FROM livre l
        LEFT JOIN exemplaire e ON l.id = e.id_l
        GROUP BY l.id
        HAVING disponible_count > 0
        ORDER BY l.titre";

$result = mysqli_query($connexion, $sql);

if (!$result) {
    die("Erreur SQL : " . mysqli_error($connexion));
}

$livres = [];
while ($row = mysqli_fetch_assoc($result)) {
    $livres[] = $row;
}

// Traitement de l'emprunt
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'emprunter' && isset($_GET['id'])) {
    $id_livre = intval($_GET['id']);
    
    // Vérifier le nombre d'emprunts en cours
    $sql_count = "SELECT COUNT(*) AS nb FROM emprunt 
                 WHERE id_adherent = ? AND statut = 'valide' AND date_fin >= CURDATE()";
    $stmt = mysqli_prepare($connexion, $sql_count);
    mysqli_stmt_bind_param($stmt, 'i', $id_adherent);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $count = mysqli_fetch_assoc($result)['nb'];
    
    if ($count >= 3) {
        $_SESSION['alert'] = ['type' => 'warning', 'message' => 'Vous avez déjà 3 emprunts en cours'];
        header('Location: index.php?action=listExemplaire');
        exit;
    }
    
    // Trouver un exemplaire disponible
    $sql_exemplaire = "SELECT id FROM exemplaire 
                      WHERE id_l = ? AND statut = 'disponible' 
                      LIMIT 1 FOR UPDATE";
    $stmt = mysqli_prepare($connexion, $sql_exemplaire);
    mysqli_stmt_bind_param($stmt, 'i', $id_livre);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $exemplaire = mysqli_fetch_assoc($result);
    
    if ($exemplaire) {
        $id_exemplaire = $exemplaire['id'];
        $date_debut = date('Y-m-d');
        $date_fin = date('Y-m-d', strtotime('+14 days'));
        
        // Démarrer une transaction
        mysqli_begin_transaction($connexion);
        
        try {
            // Créer la demande d'emprunt
            $sql_emprunt = "INSERT INTO emprunt 
                          (id_adherent, id_exemplaire, date_debut, date_fin, statut) 
                          VALUES (?, ?, ?, ?, 'en_attente')";
            $stmt = mysqli_prepare($connexion, $sql_emprunt);
            mysqli_stmt_bind_param($stmt, 'iiss', $id_adherent, $id_exemplaire, $date_debut, $date_fin);
            mysqli_stmt_execute($stmt);
            
            // Mettre à jour le statut de l'exemplaire
            $sql_update = "UPDATE exemplaire SET statut = 'reserve' WHERE id = ?";
            $stmt = mysqli_prepare($connexion, $sql_update);
            mysqli_stmt_bind_param($stmt, 'i', $id_exemplaire);
            mysqli_stmt_execute($stmt);
            
            mysqli_commit($connexion);
            
            $_SESSION['alert'] = ['type' => 'success', 'message' => 'Demande d\'emprunt envoyée'];
            header('Location: index.php?action=listExemplaire');
            exit;
            
        } catch (Exception $e) {
            mysqli_rollback($connexion);
            $_SESSION['alert'] = ['type' => 'danger', 'message' => 'Erreur lors de la demande'];
            header('Location: index.php?action=listExemplaire');
            exit;
        }
    } else {
        $_SESSION['alert'] = ['type' => 'warning', 'message' => 'Aucun exemplaire disponible'];
        header('Location: index.php?action=listExemplaire');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emprunter un livre</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-4">
        <h2 class="mb-4">Emprunter un livre</h2>
        
        <?php if (isset($_SESSION['alert'])): ?>
            <div class="alert alert-<?= $_SESSION['alert']['type'] ?>">
                <?= $_SESSION['alert']['message'] ?>
            </div>
            <?php unset($_SESSION['alert']); ?>
        <?php endif; ?>
        
        <div class="row">
            <?php foreach ($livres as $livre): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <?php if ($livre['photo']): ?>
                            <img src="../uploads/<?= htmlspecialchars($livre['photo']) ?>" class="card-img-top" alt="Couverture du livre">
                        <?php else: ?>
                            <div class="text-center py-4 bg-light">
                                <i class="fas fa-book fa-5x text-muted"></i>
                            </div>
                        <?php endif; ?>
                        
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($livre['titre']) ?></h5>
                            <p class="card-text"><?= htmlspecialchars($livre['auteur']) ?></p>
                            <p class="card-text">
                                <small class="text-muted">Publié le : <?= date('d/m/Y', strtotime($livre['date_edition'])) ?></small>
                            </p>
                            <p class="card-text">
                                <span class="badge bg-success">
                                    <?= $livre['disponible_count'] ?> exemplaire(s) disponible(s)
                                </span>
                            </p>
                        </div>
                        
                        <div class="card-footer bg-white">
                            <a href="?action=emprunter&id=<?= $livre['id'] ?>" class="btn btn-primary w-100">
                                Emprunter
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>