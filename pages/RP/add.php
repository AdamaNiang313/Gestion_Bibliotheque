<?php
// Vérification des droits d'accès (RP seulement - id_r = 3)
if (!isset($_SESSION['id_r'])) {
    header('Location: ../../index.php');
    exit;
}

if ($_SESSION['id_r'] != 3) {
    $_SESSION['error'] = "Accès non autorisé";
    header('Location: ../../index.php');
    exit;
}

// Configuration
define('UPLOAD_DIR', '../../uploads/');
$allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
$max_size = 5 * 1024 * 1024; // 5 Mo

// Initialisation des variables
$error = '';
$livre_id = isset($_GET['livre_id']) ? intval($_GET['livre_id']) : null;

// Récupération de la liste des livres
$sql_livres = "SELECT id, titre FROM livre ORDER BY titre";
$result_livres = mysqli_query($connexion, $sql_livres);

if (!$result_livres) {
    die("Erreur SQL : " . mysqli_error($connexion));
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validation des données
    $date_enregistre = date('Y-m-d'); // Utilise la date actuelle
    $statut = mysqli_real_escape_string($connexion, $_POST['statut']);
    $id_l = intval($_POST['id_l']);
    
    // Traitement de l'image
    $photo = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $file_name = $_FILES['photo']['name'];
        $file_tmp = $_FILES['photo']['tmp_name'];
        $file_size = $_FILES['photo']['size'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        // Vérifications
        if (!in_array($file_ext, $allowed_types)) {
            $error = "Format de fichier non autorisé. Formats acceptés: " . implode(', ', $allowed_types);
        } elseif ($file_size > $max_size) {
            $error = "Le fichier est trop volumineux (> 5Mo)";
        } else {
            // Création du dossier uploads si inexistant
            if (!is_dir(UPLOAD_DIR)) {
                mkdir(UPLOAD_DIR, 0755, true);
            }
            
            // Génération d'un nom unique
            $new_filename = 'exemplaire_' . uniqid() . '.' . $file_ext;
            $target_path = UPLOAD_DIR . $new_filename;
            
            // Déplacement du fichier
            if (move_uploaded_file($file_tmp, $target_path)) {
                $photo = $new_filename;
            } else {
                $error = "Erreur lors de l'enregistrement du fichier";
            }
        }
    }

    // Insertion en base si pas d'erreur
    if (empty($error)) {
        $sql = "INSERT INTO exemplaire (date_enregistre, id_l, statut, photo) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($connexion, $sql);
        
        if ($stmt) {
            // Gestion spéciale pour NULL si pas de photo
            $photo_param = ($photo !== null) ? $photo : null;
            mysqli_stmt_bind_param($stmt, 'siss', $date_enregistre, $id_l, $statut, $photo_param);
            
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['success'] = "L'exemplaire a été ajouté avec succès";
                header('Location: index.php?action=listExemplaire');
                exit;
            } else {
                $error = "Erreur lors de l'ajout : " . mysqli_error($connexion);
            }
            
            mysqli_stmt_close($stmt);
        } else {
            $error = "Erreur de préparation de la requête";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un exemplaire</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .form-container {
            max-width: 600px;
            margin: 2rem auto;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            overflow: hidden;
            background: white;
        }
        .form-header {
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            color: white;
            padding: 1.5rem;
            text-align: center;
        }
        .form-body {
            padding: 2rem;
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
            <div class="form-header">
                <h3><i class="fas fa-book-medical me-2"></i>Ajouter un exemplaire</h3>
            </div>
            
            <div class="form-body">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <?= htmlspecialchars($error) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <form method="POST" enctype="multipart/form-data" id="exemplaireForm">
                    <!-- Sélection du livre -->
                    <div class="mb-4">
                        <label for="id_l" class="form-label fw-bold">Livre *</label>
                        <select class="form-select" id="id_l" name="id_l" required>
                            <option value="">-- Sélectionnez un livre --</option>
                            <?php while ($livre = mysqli_fetch_assoc($result_livres)): ?>
                                <option value="<?= $livre['id'] ?>" 
                                    <?= ($livre_id && $livre['id'] == $livre_id) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($livre['titre']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <!-- Date d'enregistrement (automatique) -->
                    <input type="hidden" name="date_enregistre" value="<?= date('Y-m-d') ?>">
                    
                    <!-- Statut -->
                    <div class="mb-4">
                        <label for="statut" class="form-label fw-bold">Statut *</label>
                        <select class="form-select" id="statut" name="statut" required>
                            <option value="disponible">Disponible</option>
                            <option value="réservé">Réservé</option>
                            <option value="emprunté">Emprunté</option>
                            <option value="perdu">Perdu</option>
                            <option value="en réparation">En réparation</option>
                        </select>
                    </div>
                    
                    <!-- Upload de photo -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Photo de l'exemplaire</label>
                        <div class="file-upload" onclick="document.getElementById('photo').click()">
                            <div>
                                <i class="fas fa-cloud-upload-alt fa-3x mb-3 text-primary"></i>
                                <p class="mb-1">Cliquez pour télécharger une image</p>
                                <small class="text-muted">Formats: JPG, PNG, GIF (max 5Mo)</small>
                            </div>
                            <input type="file" id="photo" name="photo" class="file-upload-input" accept="image/*">
                        </div>
                        <div class="preview-container">
                            <img id="preview" class="preview-img" alt="Aperçu de l'image">
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
        document.getElementById('exemplaireForm').addEventListener('submit', function(e) {
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
                const allowedExts = <?= json_encode($allowed_types) ?>;
                
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