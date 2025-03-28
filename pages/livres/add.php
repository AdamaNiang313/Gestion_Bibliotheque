<?php
// Vérification des droits d'accès (Gestionnaire seulement - id_r = 1)
if (!isset($_SESSION['id_r'])) {
    header('Location: ../../index.php');
    exit;
}

if ($_SESSION['id_r'] != 1) {
    $_SESSION['error'] = "Accès non autorisé";
    header('Location: ../../index.php');
    exit;
}

// Configuration
define('UPLOAD_DIR', '../uploads/');
$allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
$max_size = 5 * 1024 * 1024; // 5 Mo

// Récupération des auteurs et rayons
$sql_auteurs = "SELECT id, CONCAT(prenom, ' ', nom) AS nom_complet FROM auteur ORDER BY nom";
$auteurs = mysqli_query($connexion, $sql_auteurs);

$sql_rayons = "SELECT id, libelle FROM rayon ORDER BY libelle";
$rayons = mysqli_query($connexion, $sql_rayons);

// Traitement du formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validation des données
    $titre = htmlspecialchars(trim($_POST['titre']));
    $date_edition = $_POST['date_edition'];
    $id_a = intval($_POST['id_a']);
    $id_r = intval($_POST['id_r']);
    $photo = null;

    // Gestion de l'upload
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $file_name = $_FILES['photo']['name'];
        $file_tmp = $_FILES['photo']['tmp_name'];
        $file_size = $_FILES['photo']['size'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        // Vérifications
        if (!in_array($file_ext, ['jpg', 'jpeg', 'png', 'gif'])) {
            $_SESSION['error'] = "Format de fichier non autorisé. Formats acceptés: JPG, PNG, GIF";
        } elseif ($file_size > $max_size) {
            $_SESSION['error'] = "Le fichier est trop volumineux (max 5Mo)";
        } else {
            // Création du dossier uploads si inexistant
            if (!is_dir(UPLOAD_DIR)) {
                mkdir(UPLOAD_DIR, 0755, true);
            }
            
            // Génération d'un nom unique
            $new_filename = 'livre_' . uniqid() . '.' . $file_ext;
            $target_path = UPLOAD_DIR . $new_filename;
            
            // Déplacement du fichier
            if (move_uploaded_file($file_tmp, $target_path)) {
                $photo = $new_filename;
            } else {
                $_SESSION['error'] = "Erreur lors de l'enregistrement de l'image";
            }
        }
    }

    // Insertion en base si pas d'erreur
    if (!isset($_SESSION['error'])) {
        $sql = "INSERT INTO livre (titre, date_edition, photo, id_a, id_r) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($connexion, $sql);
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'sssii', $titre, $date_edition, $photo, $id_a, $id_r);
            
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['success'] = "Le livre a été ajouté avec succès";
                header('Location: index.php?action=listLivre');
                exit;
            } else {
                $_SESSION['error'] = "Erreur lors de l'ajout: " . mysqli_error($connexion);
            }
            
            mysqli_stmt_close($stmt);
        } else {
            $_SESSION['error'] = "Erreur de préparation de la requête";
        }
    }
    
    // Redirection en cas d'erreur pour éviter la resoumission
    if (isset($_SESSION['error'])) {
        header('Location: add.php');
        exit;
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
    <title>Ajouter un livre</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .form-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            background: white;
        }
        .form-header {
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            color: white;
            padding: 1.5rem;
            border-radius: 8px 8px 0 0;
            margin-bottom: 2rem;
        }
        .file-upload {
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 2rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        .file-upload:hover {
            border-color: #0d6efd;
            background: #f8f9fa;
        }
        .file-upload-input {
            display: none;
        }
        .preview-container {
            margin-top: 1rem;
            text-align: center;
        }
        .preview-img {
            max-width: 100%;
            max-height: 200px;
            display: none;
            border-radius: 5px;
        }
        .btn-submit {
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            border: none;
            padding: 10px 25px;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="form-container">
            <div class="form-header text-center">
                <h3><i class="fas fa-book-medical me-2"></i>Ajouter un nouveau livre</h3>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= htmlspecialchars($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data" id="livreForm">
                <!-- Titre -->
                <div class="mb-4">
                    <label for="titre" class="form-label fw-bold">Titre *</label>
                    <input type="text" class="form-control" id="titre" name="titre" required
                           value="<?= htmlspecialchars($_POST['titre'] ?? '') ?>">
                </div>
                
                <!-- Date d'édition -->
                <div class="mb-4">
                    <label for="date_edition" class="form-label fw-bold">Date d'édition *</label>
                    <input type="date" class="form-control" id="date_edition" name="date_edition" required
                           value="<?= htmlspecialchars($_POST['date_edition'] ?? date('Y-m-d')) ?>">
                </div>
                
                <!-- Auteur -->
                <div class="mb-4">
                    <label for="id_a" class="form-label fw-bold">Auteur *</label>
                    <select class="form-select" id="id_a" name="id_a" required>
                        <option value="">-- Sélectionnez un auteur --</option>
                        <?php while ($auteur = mysqli_fetch_assoc($auteurs)): ?>
                            <option value="<?= $auteur['id'] ?>" 
                                <?= (isset($_POST['id_a']) && $_POST['id_a'] == $auteur['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($auteur['nom_complet']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <!-- Rayon -->
                <div class="mb-4">
                    <label for="id_r" class="form-label fw-bold">Rayon *</label>
                    <select class="form-select" id="id_r" name="id_r" required>
                        <option value="">-- Sélectionnez un rayon --</option>
                        <?php while ($rayon = mysqli_fetch_assoc($rayons)): ?>
                            <option value="<?= $rayon['id'] ?>" 
                                <?= (isset($_POST['id_r']) && $_POST['id_r'] == $rayon['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($rayon['libelle']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <!-- Photo de couverture -->
                <div class="mb-4">
                    <label class="form-label fw-bold">Photo de couverture</label>
                    <div class="file-upload" onclick="document.getElementById('photo').click()">
                        <div>
                            <i class="fas fa-cloud-upload-alt fa-3x mb-3 text-primary"></i>
                            <p class="mb-1">Cliquez pour télécharger une image</p>
                            <small class="text-muted">Formats: JPG, PNG, GIF (max 5Mo)</small>
                        </div>
                        <input type="file" id="photo" name="photo" class="file-upload-input" accept="image/*">
                    </div>
                    <div class="preview-container">
                        <img id="preview" class="preview-img" alt="Aperçu de la couverture">
                    </div>
                </div>
                
                <!-- Boutons -->
                <div class="d-flex justify-content-between mt-4">
                    <a href="list.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i> Retour
                    </a>
                    <button type="submit" class="btn btn-primary btn-submit">
                        <i class="fas fa-save me-2"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Aperçu de l'image
        document.getElementById('photo').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('preview');
            
            if (file) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                
                reader.readAsDataURL(file);
            }
        });
        
        // Validation avant soumission
        document.getElementById('livreForm').addEventListener('submit', function(e) {
            const fileInput = document.getElementById('photo');
            if (fileInput.files.length > 0) {
                const fileSize = fileInput.files[0].size;
                const maxSize = <?= $max_size ?>;
                
                if (fileSize > maxSize) {
                    alert('Le fichier est trop volumineux (max 5Mo)');
                    e.preventDefault();
                    return false;
                }
                
                const fileExt = fileInput.files[0].name.split('.').pop().toLowerCase();
                const allowedExts = ['jpg', 'jpeg', 'png', 'gif'];
                
                if (!allowedExts.includes(fileExt)) {
                    alert('Format de fichier non autorisé');
                    e.preventDefault();
                    return false;
                }
            }
            
            return true;
        });
    </script>
</body>
</html>