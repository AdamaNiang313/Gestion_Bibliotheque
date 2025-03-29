<?php
// pages/gestionnaire/statistiques.php

require_once __DIR__.'/../../database.php';

// Vérification des droits
if (!isset($_SESSION['id_r']) || $_SESSION['id_r'] != 1) {
    $_SESSION['error'] = "Accès non autorisé";
    header('Location: ../../index.php');
    exit;
}

// 1. Statistiques des emprunts par mois (pour l'utilisateur connecté)
$sql_emprunts_mois = "SELECT 
    DATE_FORMAT(date_debut, '%Y-%m') AS mois, 
    COUNT(*) AS total 
    FROM emprunt 
    WHERE id_adherent = ? 
    AND statut = 'valide'
    GROUP BY mois 
    ORDER BY mois DESC 
    LIMIT 12";

$stmt = mysqli_prepare($connexion, $sql_emprunts_mois);
mysqli_stmt_bind_param($stmt, 'i', $_SESSION['id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$mois = [];
$emprunts_mois = [];
while ($row = mysqli_fetch_assoc($result)) {
    $mois[] = $row['mois'];
    $emprunts_mois[] = $row['total'];
}

// 2. Top 10 des livres les plus empruntés
$sql_top_livres = "SELECT 
    l.titre, 
    COUNT(*) AS total_emprunts
    FROM emprunt e
    JOIN exemplaire ex ON e.id_exemplaire = ex.id
    JOIN livre l ON ex.id_l = l.id
    WHERE e.statut = 'valide'
    GROUP BY l.id
    ORDER BY total_emprunts DESC
    LIMIT 10";

$result = mysqli_query($connexion, $sql_top_livres);

$livres = [];
$emprunts_livres = [];
$couleurs = [];
while ($row = mysqli_fetch_assoc($result)) {
    $livres[] = substr($row['titre'], 0, 20) . (strlen($row['titre']) > 20 ? '...' : '');
    $emprunts_livres[] = $row['total_emprunts'];
    $couleurs[] = sprintf('rgba(%d, %d, %d, 0.7)', rand(50, 200), rand(50, 200), rand(50, 200));
}

// 3. Statistiques globales (pour le footer)
$sql_stats = "SELECT
    (SELECT COUNT(*) FROM emprunt WHERE statut = 'valide') AS total_emprunts,
    (SELECT COUNT(*) FROM exemplaire WHERE statut = 'disponible') AS exemplaires_disponibles,
    (SELECT COUNT(*) FROM user WHERE id_r = 2) AS adherents";

$stats = mysqli_fetch_assoc(mysqli_query($connexion, $sql_stats));
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques complètes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --accent: #4cc9f0;
        }
        .stat-card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s;
            height: 100%;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px rgba(0,0,0,0.1);
        }
        .chart-container {
            position: relative;
            height: 300px;
            padding: 15px;
        }
        .stat-header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border-radius: 10px 10px 0 0 !important;
        }
        .stat-badge {
            background-color: var(--accent);
            color: #fff;
        }
        .info-box {
            background-color: #f8f9fa;
            border-left: 4px solid var(--primary);
            padding: 10px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container-fluid py-4 mt-5">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="display-5 fw-bold">
                    <i class="fas fa-chart-line me-2"></i>Tableau de bord statistique
                </h1>
                <p class="lead">Visualisation des données clés de la bibliothèque</p>
            </div>
        </div>

        <!-- Section 1: Mes emprunts -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card stat-card">
                    <div class="card-header stat-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Mes emprunts par mois</h5>
                        <span class="badge stat-badge">12 mois</span>
                    </div>
                    <div class="card-body">
                        <div class="info-box">
                            <i class="fas fa-info-circle me-2"></i>
                            Evolution de vos emprunts validés au cours de l'année
                        </div>
                        <div class="chart-container">
                            <canvas id="chartEmpruntsMois"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Top livres -->
            <div class="col-md-6">
                <div class="card stat-card">
                    <div class="card-header stat-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-book me-2"></i>Top 10 des livres</h5>
                        <span class="badge stat-badge">Les plus empruntés</span>
                    </div>
                    <div class="card-body">
                        <div class="info-box">
                            <i class="fas fa-info-circle me-2"></i>
                            Répartition des emprunts par livre (en nombre)
                        </div>
                        <div class="chart-container">
                            <canvas id="chartTopLivres"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 3: Statistiques globales -->
        <div class="row">
            <div class="col-md-4">
                <div class="card stat-card">
                    <div class="card-body text-center py-4">
                        <h3><i class="fas fa-book-open text-primary"></i></h3>
                        <h2 class="fw-bold"><?= $stats['total_emprunts'] ?></h2>
                        <p class="mb-0">Emprunts total</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card">
                    <div class="card-body text-center py-4">
                        <h3><i class="fas fa-book text-success"></i></h3>
                        <h2 class="fw-bold"><?= $stats['exemplaires_disponibles'] ?></h2>
                        <p class="mb-0">Exemplaires disponibles</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card">
                    <div class="card-body text-center py-4">
                        <h3><i class="fas fa-users text-info"></i></h3>
                        <h2 class="fw-bold"><?= $stats['adherents'] ?></h2>
                        <p class="mb-0">Adhérents actifs</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Initialisation après chargement de la page
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Graphique des emprunts par mois
        const ctx1 = document.getElementById('chartEmpruntsMois').getContext('2d');
        new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: <?= json_encode($mois) ?>,
                datasets: [{
                    label: 'Emprunts',
                    data: <?= json_encode($emprunts_mois) ?>,
                    backgroundColor: 'rgba(67, 97, 238, 0.7)',
                    borderColor: 'rgba(58, 12, 163, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // 2. Graphique des top livres
        const ctx2 = document.getElementById('chartTopLivres').getContext('2d');
        new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: <?= json_encode($livres) ?>,
                datasets: [{
                    data: <?= json_encode($emprunts_livres) ?>,
                    backgroundColor: <?= json_encode($couleurs) ?>,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right'
                    }
                }
            }
        });
    });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


</script>
</body>
</html>