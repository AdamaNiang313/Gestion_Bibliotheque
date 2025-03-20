<?php
// Connexion à la base de données (assure-toi que $connexion est déjà défini)
$livre = "SELECT * FROM livre";
$sql_livre = mysqli_query($connexion, $livre);

// Gestion de l'upload de la photo
$error_message = '';
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date_enregistre = $_POST['date_enregistre'];
    $statut = $_POST['statut'];
    $id_l = $_POST['id_l'];
    $photo = null;

    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $file_name = basename($_FILES['photo']['name']);
        $temp_name = $_FILES['photo']['tmp_name'];
        $target_dir = 'uploads/';
        $file_path = $target_dir . $file_name;

        // Vérifie le type de fichier
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = mime_content_type($temp_name);

        if (in_array($file_type, $allowed_types)) {
            // Vérifie la taille du fichier (5 Mo maximum)
            if ($_FILES['photo']['size'] <= 5 * 1024 * 1024) {
                // Déplace le fichier uploadé
                if (move_uploaded_file($temp_name, $file_path)) {
                    $photo = $file_name;
                } else {
                    $error_message = "Erreur lors du déplacement du fichier.";
                }
            } else {
                $error_message = "Le fichier est trop volumineux. Taille maximale : 5 Mo.";
            }
        } else {
            $error_message = "Type de fichier non autorisé. Formats acceptés : JPEG, PNG, GIF.";
        }
    }

    if (empty($error_message)) {
        $sql = "INSERT INTO exemplaire (date_enregistre, id_l, statut, photo) VALUES ('$date_enregistre', '$id_l', '$statut', '$photo')";

        if (mysqli_query($connexion, $sql)) {
            $success_message = "L'exemplaire a été ajouté avec succès.";
            header('Location: index.php?action=listExemplaire');
            exit;
        } else {
            $error_message = "Erreur lors de l'ajout de l'exemplaire.";
        }
    }
}
?>

<div class="container w-50 mt-5 p-4 shadow rounded bg-light">
    <h2 class="mb-4"><i class="fas fa-user-plus"></i> Ajouter un exemplaire</h2>
    
    <!-- Affichage des messages d'erreur ou de succès -->
    <?php if (!empty($error_message)) { ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
    <?php } ?>
    <?php if (!empty($success_message)) { ?>
        <div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div>
    <?php } ?>

    <form action="#" method="POST" enctype="multipart/form-data">
        <div class="mb-4">
            <label for="date_enregistre" class="form-label fw-bold"><i class="fas fa-calendar"></i> Date :</label>
            <input type="date" name="date_enregistre" class="form-control p-2" required>
        </div>
        <div class="mb-4">
            <label for="statut" class="form-label fw-bold"><i class="fas fa-info-circle"></i> Statut :</label>
            <input type="text" name="statut" class="form-control p-2" required>
        </div>
        <div class="mb-4">
            <label for="id_l" class="form-label fw-bold"><i class="fas fa-book"></i> Livre :</label>
            <select name="id_l" class="form-control p-2" required>
                <?php while ($livre = mysqli_fetch_assoc($sql_livre)) { ?>
                    <option value="<?= htmlspecialchars($livre['id']) ?>"><?= htmlspecialchars($livre['titre']) ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="mb-4">
            <label for="photo" class="form-label fw-bold"><i class="fas fa-camera"></i> Photo :</label>
            <input type="file" id="photo" name="photo" class="form-control" accept="image/*">
            <small class="form-text text-muted">Formats acceptés : JPEG, PNG, GIF. Taille maximale : 5 Mo.</small>
        </div>
        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
            <button type="submit" class="btn btn-success me-md-2">
                <i class="fas fa-check-circle"></i> Valider
            </button>
            <a href="?action=listExemplaire" class="btn btn-danger">
                <i class="fas fa-times-circle"></i> Annuler
            </a>
        </div>
    </form>
</div>

<!-- Styles for form and buttons -->
<style>
    .form-label {
        font-weight: bold;
        color: #495057;
    }

    .form-control {
        border-radius: 5px;
        border: 1px solid #ced4da;
        padding: 10px;
    }

    .form-control:focus {
        border-color: #80bdff;
        box-shadow: 0 0 5px rgba(128, 189, 255, 0.5);
    }

    .btn-success {
        background-color: #28a745;
        border: none;
        padding: 10px 20px;
        font-size: 16px;
        border-radius: 5px;
    }

    .btn-danger {
        background-color: #dc3545;
        border: none;
        padding: 10px 20px;
        font-size: 16px;
        border-radius: 5px;
    }

    .btn i {
        margin-right: 5px;
    }
</style>