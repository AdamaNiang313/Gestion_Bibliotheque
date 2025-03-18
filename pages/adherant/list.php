<?php
// Connexion à la base de données (assure-toi que $connexion est déjà défini)
$sql = "SELECT l.titre, l.date_edition, 
               COUNT(e.id) AS disponible_count,
               MIN(e.photo) AS photo,
               e.id
        FROM livre l
        LEFT JOIN exemplaire e ON l.id = e.id_l AND e.statut = 'disponible'
        GROUP BY l.titre, l.date_edition, e.id";
$livres = mysqli_query($connexion, $sql);

// Vérifie si la requête a réussi
if (!$livres) {
    die("Erreur dans la requête SQL : " . mysqli_error($connexion));
}

// Initialise le tableau pour stocker les résultats
$livresArray = [];

// Récupère les résultats de la requête
while ($row = mysqli_fetch_assoc($livres)) {
    $livresArray[] = $row;
}

// Fonction pour formater la date en français
function formatDateFrench($date) {
    setlocale(LC_TIME, 'fr_FR.UTF-8');
    return strftime('%d %B %Y', strtotime($date));
}

// Gestion de l'emprunt
if (isset($_GET['action']) && $_GET['action'] === 'emprunter' && isset($_GET['id'])) {
    $id = $_GET['id'];

    // Mettre à jour le statut de l'exemplaire
    $sql_update = "UPDATE exemplaire SET statut = 'emprunté' WHERE id = ?";
    $stmt = $connexion->prepare($sql_update);
    $stmt->bind_param("s", $id);

    if ($stmt->execute()) {
        // Rediriger vers la liste après l'emprunt
        header('Location: index.php?action=listEmprunt');
        exit;
    } else {
        echo "<div class='alert alert-danger'>Erreur lors de l'emprunt du livre.</div>";
    }
}
?>


    <style>
        body {
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            color: #333;
            font-family: 'Arial', sans-serif;
        }

        .container {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            padding: 20px;
            margin-top: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background: linear-gradient(135deg, #f9f9f9, #eaeaea);
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.2);
        }

        .card-img-top {
            border-radius: 15px 15px 0 0;
        }

        .card-body {
            padding: 20px;
            background: white;
            border-radius: 0 0 15px 15px;
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: bold;
            color: #333;
        }

        .card-text {
            font-size: 0.9rem;
            color: #666;
        }

        .btn {
            border-radius: 25px;
            padding: 8px 20px;
            font-size: 0.9rem;
            transition: background-color 0.3s ease;
        }

        .badge {
            font-size: 0.9rem;
            padding: 8px 12px;
            border-radius: 20px;
        }
    </style>

    <div class="container mt-4">    
        <!-- Liste des livres -->
        <div class="row g-3">
            <?php for ($i = 0; $i < count($livresArray); $i++) { ?>
                <div class="col-md-3 mb-4">
                    <div class="card h-100">
                        <?php if (!empty($livresArray[$i]['photo'])) { ?>
                            <img src="uploads/<?= htmlspecialchars($livresArray[$i]['photo']) ?>" class="card-img-top" alt="Couverture du livre" style="height: 150px; object-fit: cover;">
                        <?php } else { ?>
                            <div class="text-center py-4 bg-light">
                                <span>Aucune photo disponible</span>
                            </div>
                        <?php } ?>
                        <div class="card-body text-center">
                            <span class="badge bg-primary mb-2"><i class="fas fa-calendar-alt"></i> Édition: <?= formatDateFrench($livresArray[$i]['date_edition']) ?></span>
                            <h5 class="card-title mt-2"><i class="fas fa-book"></i> <?= htmlspecialchars($livresArray[$i]['titre']) ?></h5>
                            <p class="card-text"><i class="fas fa-check-circle"></i> Disponibles: <?= htmlspecialchars($livresArray[$i]['disponible_count']) ?></p>
                            <a class="btn btn-danger" href="?action=emprunter&id=<?= $livresArray[$i]['id'] ?>">
                                <i class="fas fa-plus"></i> Emprunter
                            </a>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

  