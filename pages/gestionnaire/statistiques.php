<?php
// pages/gestionnaire/statistiques.php
require_once __DIR__ . '/../../database.php';

// Vérification des droits

// 1. Statistiques des emprunts par mois (pour l'utilisateur connecté)
$mois = [];
$emprunts_mois = [];
if (isset($_SESSION['id'])) {
    $sql_emprunts_mois = "SELECT 
        DATE_FORMAT(date_debut, '%Y-%m') AS mois, 
        COUNT(*) AS total 
        FROM emprunt 
        WHERE id_adherent = ? 
        AND statut = 'valide'
        GROUP BY mois 
        ORDER BY mois ASC 
        LIMIT 12";

    $stmt = mysqli_prepare($connexion, $sql_emprunts_mois);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'i', $_SESSION['id']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                // Formater le mois en français (ex: "Jan 2023")
                $date = DateTime::createFromFormat('Y-m', $row['mois']);
                $mois[] = $date->format('M Y');
                $emprunts_mois[] = $row['total'];
            }
        }
        mysqli_stmt_close($stmt);
    }
}

// 2. Top 10 des livres les plus empruntés
$sql_top_livres = "SELECT 
    l.titre, 
    COUNT(*) AS total_emprunts,
    a.nom AS auteur
    FROM emprunt e
    JOIN exemplaire ex ON e.id_exemplaire = ex.id
    JOIN livre l ON ex.id_l = l.id
    JOIN auteur a ON l.id_a = a.id
    WHERE e.statut = 'valide'
    GROUP BY l.id
    ORDER BY total_emprunts DESC
    LIMIT 10";

$result = mysqli_query($connexion, $sql_top_livres);

$livres = [];
$emprunts_livres = [];
$auteurs = [];
$couleurs = [];
$couleurs_dark = [];
while ($row = mysqli_fetch_assoc($result)) {
    $livres[] = $row['titre'];
    $auteurs[] = $row['auteur'];
    $emprunts_livres[] = $row['total_emprunts'];
    
    // Générer des couleurs harmonieuses
    $hue = rand(0, 360);
    $couleurs[] = "hsla($hue, 70%, 60%, 0.7)";
    $couleurs_dark[] = "hsla($hue, 70%, 40%, 1)";
}

// 3. Statistiques globales (pour le footer)
$sql_stats = "SELECT
    (SELECT COUNT(*) FROM emprunt WHERE statut = 'valide') AS total_emprunts,
    (SELECT COUNT(*) FROM exemplaire WHERE statut = 'disponible') AS exemplaires_disponibles,
    (SELECT COUNT(DISTINCT id_adherent) FROM emprunt WHERE date_debut >= DATE_SUB(NOW(), INTERVAL 1 YEAR)) AS adherents_actifs,
    (SELECT COUNT(*) FROM user WHERE id_r = 2) AS adherents_total";

$result_stats = mysqli_query($connexion, $sql_stats);
$stats = mysqli_fetch_assoc($result_stats) ?: ["total_emprunts"=>0, "exemplaires_disponibles"=>0, "adherents_actifs"=>0, "adherents_total"=>0];

// 4. Statistiques par rayon
$sql_rayons = "SELECT 
    r.libelle AS rayon,
    COUNT(DISTINCT l.id) AS nb_livres,
    COUNT(e.id) AS nb_emprunts
    FROM rayon r
    LEFT JOIN livre l ON r.id = l.id_r
    LEFT JOIN exemplaire ex ON l.id = ex.id_l
    LEFT JOIN emprunt e ON ex.id = e.id_exemplaire AND e.statut = 'valide'
    GROUP BY r.id
    ORDER BY nb_emprunts DESC";

$result_rayons = mysqli_query($connexion, $sql_rayons);

$rayons = [];
$livres_rayon = [];
$emprunts_rayon = [];
while ($row = mysqli_fetch_assoc($result_rayons)) {
    $rayons[] = $row['rayon'];
    $livres_rayon[] = $row['nb_livres'];
    $emprunts_rayon[] = $row['nb_emprunts'];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques de la bibliothèque</title>
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
            border-radius: 15px;
            box-shadow: 0 6px 10px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border: none;
            height: 100%;
            overflow: hidden;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 20px rgba(0,0,0,0.12);
        }
        .chart-container {
            position: relative;
            height: 300px;
            padding: 15px;
        }
        .stat-header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 1rem 1.5rem;
        }
        .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary);
        }
        .stat-label {
            font-size: 1rem;
            color: #6c757d;
        }
        .info-box {
            background-color: #f8f9fa;
            border-left: 4px solid var(--primary);
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .card-title {
            font-weight: 600;
        }
        .progress {
            height: 8px;
            border-radius: 4px;
        }
        .table-stat {
            font-size: 0.9rem;
        }
        .table-stat th {
            border-top: none;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="container-fluid py-4 mt-5">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="display-5 fw-bold text-primary">
                            <i class="fas fa-chart-pie me-2"></i>Statistiques
                        </h1>
                        <p class="lead text-muted">Analyse des données de la bibliothèque</p>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-outline-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown">
                            <i class="fas fa-calendar-alt me-1"></i> Année <?= date('Y') ?>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">2023</a></li>
                            <li><a class="dropdown-item" href="#">2022</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cartes de synthèse -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card border-start border-primary border-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h5 class="card-title text-muted mb-2">Emprunts total</h5>
                                <h2 class="stat-value"><?= number_format($stats['total_emprunts'], 0, ',', ' ') ?></h2>
                            </div>
                            <div class="icon-shape bg-primary text-white rounded-circle p-3">
                                <i class="fas fa-book-open fa-2x"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Cette année</span>
                                <span><?= array_sum($emprunts_mois) ?></span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-primary" style="width: <?= min(100, array_sum($emprunts_mois)/max(1,$stats['total_emprunts'])*100) ?>%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card border-start border-success border-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h5 class="card-title text-muted mb-2">Exemplaires</h5>
                                <h2 class="stat-value text-success"><?= number_format($stats['exemplaires_disponibles'], 0, ',', ' ') ?></h2>
                                <small class="text-muted">disponibles</small>
                            </div>
                            <div class="icon-shape bg-success text-white rounded-circle p-3">
                                <i class="fas fa-book fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card border-start border-info border-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h5 class="card-title text-muted mb-2">Adhérents</h5>
                                <h2 class="stat-value text-info"><?= number_format($stats['adherents_actifs'], 0, ',', ' ') ?></h2>
                                <small class="text-muted">actifs sur <?= $stats['adherents_total'] ?> total</small>
                            </div>
                            <div class="icon-shape bg-info text-white rounded-circle p-3">
                                <i class="fas fa-users fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card border-start border-warning border-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h5 class="card-title text-muted mb-2">Taux d'emprunt</h5>
                                <h2 class="stat-value text-warning">
                                    <?= $stats['exemplaires_disponibles'] > 0 ? 
                                        round($stats['total_emprunts']/($stats['total_emprunts']+$stats['exemplaires_disponibles'])*100, 1) : 0 ?>%
                                </h2>
                                <small class="text-muted">des exemplaires</small>
                            </div>
                            <div class="icon-shape bg-warning text-white rounded-circle p-3">
                                <i class="fas fa-percentage fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Première ligne de graphiques -->
        <div class="row mb-4">
            <div class="col-lg-6 mb-4">
                <div class="card stat-card h-100">
                    <div class="card-header stat-header">
                        <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Mes emprunts par mois</h5>
                    </div>
                    <div class="card-body">
                        <div class="info-box">
                            <i class="fas fa-info-circle me-2"></i>
                            Evolution de vos emprunts validés au cours des 12 derniers mois
                        </div>
                        <div class="chart-container">
                            <canvas id="chartEmpruntsMois"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="card stat-card h-100">
                    <div class="card-header stat-header">
                        <h5 class="mb-0"><i class="fas fa-book me-2"></i>Top 10 des livres</h5>
                    </div>
                    <div class="card-body">
                        <div class="info-box">
                            <i class="fas fa-info-circle me-2"></i>
                            Les livres les plus empruntés avec leur auteur
                        </div>
                        <div class="chart-container">
                            <canvas id="chartTopLivres"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Deuxième ligne de graphiques -->
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card stat-card h-100">
                    <div class="card-header stat-header">
                        <h5 class="mb-0"><i class="fas fa-tags me-2"></i>Activité par rayon</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="chartRayons"></canvas>
                        </div>
                        <div class="table-responsive mt-3">
                            <table class="table table-stat">
                                <thead>
                                    <tr>
                                        <th>Rayon</th>
                                        <th>Livres</th>
                                        <th>Emprunts</th>
                                        <th>Taux</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($rayons as $index => $rayon): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($rayon) ?></td>
                                        <td><?= $livres_rayon[$index] ?></td>
                                        <td><?= $emprunts_rayon[$index] ?></td>
                                        <td>
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar bg-primary" 
                                                     style="width: <?= $livres_rayon[$index] > 0 ? ($emprunts_rayon[$index]/$livres_rayon[$index]*100) : 0 ?>%">
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="card stat-card h-100">
                    <div class="card-header stat-header">
                        <h5 class="mb-0"><i class="fas fa-star me-2"></i>Détails top livres</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-stat">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Livre</th>
                                        <th>Auteur</th>
                                        <th>Emprunts</th>
                                        <th>Part</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($livres as $index => $livre): ?>
                                    <tr>
                                        <td><?= $index+1 ?></td>
                                        <td><?= htmlspecialchars($livre) ?></td>
                                        <td><?= htmlspecialchars($auteurs[$index]) ?></td>
                                        <td><?= $emprunts_livres[$index] ?></td>
                                        <td>
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar" 
                                                     style="width: <?= $emprunts_livres[$index]/max(1,array_sum($emprunts_livres))*100 ?>%;
                                                     background-color: <?= $couleurs[$index] ?>">
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Graphique des emprunts par mois
            const ctx1 = document.getElementById('chartEmpruntsMois');
            if (ctx1) {
                new Chart(ctx1, {
                    type: 'line',
                    data: {
                        labels: <?= json_encode($mois) ?>,
                        datasets: [{
                            label: 'Emprunts',
                            data: <?= json_encode($emprunts_mois) ?>,
                            backgroundColor: 'rgba(67, 97, 238, 0.1)',
                            borderColor: 'rgba(67, 97, 238, 1)',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: true,
                            pointBackgroundColor: 'rgba(67, 97, 238, 1)',
                            pointRadius: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.parsed.y + ' emprunt' + (context.parsed.y > 1 ? 's' : '');
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                }
                            }
                        }
                    }
                });
            }

            // 2. Graphique des top livres
            const ctx2 = document.getElementById('chartTopLivres');
            if (ctx2) {
                new Chart(ctx2, {
                    type: 'bar',
                    data: {
                        labels: <?= json_encode($livres) ?>,
                        datasets: [{
                            label: 'Emprunts',
                            data: <?= json_encode($emprunts_livres) ?>,
                            backgroundColor: <?= json_encode($couleurs) ?>,
                            borderColor: <?= json_encode($couleurs_dark) ?>,
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        indexAxis: 'y',
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    afterLabel: function(context) {
                                        const index = context.dataIndex;
                                        return 'Auteur: ' + <?= json_encode($auteurs) ?>[index];
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }

            // 3. Graphique des rayons
            const ctx3 = document.getElementById('chartRayons');
            if (ctx3) {
                new Chart(ctx3, {
                    type: 'radar',
                    data: {
                        labels: <?= json_encode($rayons) ?>,
                        datasets: [
                            {
                                label: 'Livres',
                                data: <?= json_encode($livres_rayon) ?>,
                                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                borderColor: 'rgba(75, 192, 192, 1)',
                                borderWidth: 2
                            },
                            {
                                label: 'Emprunts',
                                data: <?= json_encode($emprunts_rayon) ?>,
                                backgroundColor: 'rgba(153, 102, 255, 0.2)',
                                borderColor: 'rgba(153, 102, 255, 1)',
                                borderWidth: 2
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            r: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>