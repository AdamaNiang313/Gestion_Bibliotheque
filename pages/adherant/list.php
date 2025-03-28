<?php
// Vérification de l'authentification et du rôle (2 = adhérent)

$id_adherent = $_SESSION['id'];

// Compter les emprunts validés en cours
$sql_count = "SELECT COUNT(*) AS nb FROM emprunt 
             WHERE id_adherent = ? AND statut = 'valide' AND date_fin IS NULL";
$stmt_count = mysqli_prepare($connexion, $sql_count);
mysqli_stmt_bind_param($stmt_count, 'i', $id_adherent);
mysqli_stmt_execute($stmt_count);
$result_count = mysqli_stmt_get_result($stmt_count);
$nb_emprunts = mysqli_fetch_assoc($result_count)['nb'];

// Gestion de la demande d'emprunt
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['emprunter'])) {
    $livre_id = intval($_GET['emprunter']);
    
    if ($nb_emprunts >= 3) {
        $_SESSION['message'] = 'Vous avez atteint la limite de 3 emprunts';
        $_SESSION['message_type'] = 'warning';
    } else {
        mysqli_begin_transaction($connexion);
        try {
            // Trouver un exemplaire disponible
            $sql_ex = "SELECT id FROM exemplaire 
                      WHERE id_l = ? AND statut = 'disponible' 
                      LIMIT 1 FOR UPDATE";
            $stmt_ex = mysqli_prepare($connexion, $sql_ex);
            mysqli_stmt_bind_param($stmt_ex, 'i', $livre_id);
            mysqli_stmt_execute($stmt_ex);
            $exemplaire = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_ex));
            
            if ($exemplaire) {
                // Créer la demande
                $sql_emp = "INSERT INTO emprunt 
                           (id_adherent, id_exemplaire, date_debut, date_fin, statut) 
                           VALUES (?, ?, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 14 DAY), 'en_attente')";
                $stmt_emp = mysqli_prepare($connexion, $sql_emp);
                mysqli_stmt_bind_param($stmt_emp, 'ii', $id_adherent, $exemplaire['id']);
                
                if (mysqli_stmt_execute($stmt_emp)) {
                    // Réserver l'exemplaire
                    $sql_upd = "UPDATE exemplaire SET statut = 'réservé' WHERE id = ?";
                    $stmt_upd = mysqli_prepare($connexion, $sql_upd);
                    mysqli_stmt_bind_param($stmt_upd, 'i', $exemplaire['id']);
                    mysqli_stmt_execute($stmt_upd);
                    
                    mysqli_commit($connexion);
                    $_SESSION['message'] = 'Demande envoyée au gestionnaire';
                    $_SESSION['message_type'] = 'success';
                    
                    // Envoyer notification au gestionnaire
                    require_once __DIR__.'/../../mail.php';
                    $emprunt_id = mysqli_insert_id($connexion);
                    notifierGestionnaireNouvelEmprunt($emprunt_id, $connexion);
                }
            } else {
                throw new Exception("Aucun exemplaire disponible");
            }
        } catch (Exception $e) {
            mysqli_rollback($connexion);
            $_SESSION['message'] = 'Erreur: ' . $e->getMessage();
            $_SESSION['message_type'] = 'danger';
        }
    }
    header("Location: index.php?action=listExemplaire");
    exit();
}

// Récupérer les livres disponibles avec leurs exemplaires
$sql = "SELECT l.id, l.titre, a.nom AS auteur, r.libelle AS rayon,
       (SELECT COUNT(*) FROM exemplaire WHERE id_l = l.id AND statut = 'disponible') AS disponible,
       (SELECT photo FROM exemplaire WHERE id_l = l.id AND photo IS NOT NULL LIMIT 1) AS photo
       FROM livre l
       JOIN auteur a ON l.id_a = a.id
       JOIN rayon r ON l.id_r = r.id
       GROUP BY l.id
       HAVING disponible > 0
       ORDER BY l.titre";
$livres = mysqli_query($connexion, $sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catalogue des livres</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
        }
        .card-book {
            transition: transform 0.3s ease;
            border-radius: 10px;
            overflow: hidden;
            height: 100%;
        }
        .card-book:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .book-cover {
            height: 250px;
            object-fit: cover;
            width: 100%;
        }
        .badge-available {
            background-color: var(--primary-color);
        }
    </style>
</head>
<body>
    <main class="container py-4 mt-5">
        <!-- Messages d'alerte -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?= $_SESSION['message_type'] ?> alert-dismissible fade show">
                <?= $_SESSION['message'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">
                <i class="fas fa-book-open me-2"></i>Catalogue des livres
            </h2>
            <div>
                <span class="badge bg-primary me-2">
                    <i class="fas fa-bookmark me-1"></i>
                    <?= $nb_emprunts ?> / 3 emprunts
                </span>
                <a href="mes_emprunts.php" class="btn btn-outline-primary">
                    <i class="fas fa-list me-1"></i>Mes emprunts
                </a>
            </div>
        </div>

        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-4">
            <?php while ($livre = mysqli_fetch_assoc($livres)): ?>
            <div class="col">
                <div class="card card-book h-100">
                    <!-- Couverture du livre - MODIFICATION ICI -->
                    <?php if (!empty($livre['photo'])): ?>
                        <img src="/uploads/<?= htmlspecialchars($livre['photo']) ?>" 
                             class="book-cover" 
                             alt="Couverture de <?= htmlspecialchars($livre['titre']) ?>">
                    <?php else: ?>
                        <div class="book-cover bg-light d-flex align-items-center justify-content-center">
                            <i class="fas fa-book fa-4x text-secondary"></i>
                        </div>
                    <?php endif; ?>
                    
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($livre['titre']) ?></h5>
                        <p class="card-text text-muted small mb-2">
                            <i class="fas fa-user-edit me-1"></i>
                            <?= htmlspecialchars($livre['auteur']) ?>
                        </p>
                        
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge bg-secondary">
                                <?= htmlspecialchars($livre['rayon']) ?>
                            </span>
                            <span class="badge bg-success">
                                <?= $livre['disponible'] ?> disponible(s)
                            </span>
                        </div>
                        
                        <!-- Bouton d'emprunt -->
                        <div class="d-grid">
                            <?php if ($nb_emprunts < 3): ?>
                                <a href="index.php?action=emprunter&id=<?= $livre['id'] ?>" 
                                    class="btn btn-primary"
                                    onclick="return confirm('Voulez-vous vraiment emprunter <?= htmlspecialchars($livre['titre']) ?> ?')">
                                    <i class="fas fa-hand-paper me-1"></i>Demander
                                </a>
                            <?php else: ?>
                                <button class="btn btn-outline-secondary" disabled>
                                    <i class="fas fa-ban me-1"></i>Quota atteint
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Animation au chargement
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.card-book');
            cards.forEach((card, index) => {
                card.style.animationDelay = `${index * 0.1}s`;
                card.classList.add('animate__animated', 'animate__fadeInUp');
            });
        });
    </script>
</body>
</html>