<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['password'];
    $id = $_SESSION['id'];
    $id_r = $_SESSION['id_r'];
    $photo = null;

    if ($new_password !== $confirm_password) {
        echo "<div class='alert alert-danger'>Les mots de passe ne correspondent pas.</div>";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

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
                        echo "<div class='alert alert-danger'>Erreur lors du déplacement du fichier.</div>";
                    }
                } else {
                    echo "<div class='alert alert-warning'>Le fichier est trop volumineux. Taille maximale : 5 Mo.</div>";
                }
            } else {
                echo "<div class='alert alert-warning'>Type de fichier non autorisé. Formats acceptés : JPEG, PNG, GIF.</div>";
            }
        }
    }
}
?>

<style>
    .container {
        max-width: 600px;
        margin-top: 50px;
    }
    h2 {
        text-align: center;
        margin-bottom: 30px;
        color: #343a40;
    }
    .form-group label {
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
    .btn-primary {
        background-color: #007bff;
        border: none;
        padding: 10px 20px;
        font-size: 16px;
        border-radius: 5px;
        width: 100%;
        margin-top: 20px;
    }
    .btn-primary:hover {
        background-color: #0056b3;
    }
    .fa-lock, .fa-camera {
        margin-right: 10px;
    }
</style>

<div class="container">
    <h2>Changement de mot de passe et photo de profil</h2>
    <form action="?action=validPassword" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="new_password"><i class="fas fa-lock"></i> Nouveau mot de passe:</label>
            <input type="password" id="new_password" name="new_password" class="form-control" placeholder="Entrez votre nouveau mot de passe" required>
        </div>
        <div class="form-group">
            <label for="password"><i class="fas fa-lock"></i> Confirmer le mot de passe:</label>
            <input type="password" id="password" name="password" class="form-control" placeholder="Confirmez votre nouveau mot de passe" required>
        </div>
        <div class="form-group">
            <label for="photo"><i class="fas fa-camera"></i> Photo de profil:</label>
            <input type="file" id="photo" name="photo" class="form-control" accept="image/*">
            <small class="form-text text-muted">Formats acceptés : JPEG, PNG, GIF. Taille maximale : 5 Mo.</small>
        </div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Enregistrer</button>
    </form>
</div>