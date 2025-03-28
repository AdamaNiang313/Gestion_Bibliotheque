<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['password'];
    $id = $_SESSION['id'];
    $id_r = $_SESSION['id_r'];
    $photo = $_SESSION['photo'];

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

<!-- Inclure Font Awesome via CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<style>
    .container {
        max-width: 600px;
        margin-top: 50px;
    }
    h2 {
        text-align: center;
        margin-bottom: 30px;
        color: #343a40;
        font-size: 2rem;
    }
    .form-group label {
        font-weight: bold;
        color: #495057;
        margin-bottom: 10px;
    }
    .form-control {
        border-radius: 5px;
        border: 1px solid #ced4da;
        padding: 10px;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }
    .form-control:focus {
        border-color: #80bdff;
        box-shadow: 0 0 8px rgba(128, 189, 255, 0.6);
    }
    .btn-primary {
        background-color: #007bff;
        border: none;
        padding: 12px 20px;
        font-size: 16px;
        border-radius: 5px;
        width: 100%;
        margin-top: 20px;
        transition: background-color 0.3s ease, transform 0.2s ease;
    }
    .btn-primary:hover {
        background-color: #0056b3;
        transform: scale(1.02);
    }
    .fa-lock, .fa-camera, .fa-save {
        margin-right: 10px;
    }
    .alert {
        border-radius: 5px;
        padding: 15px;
        margin-bottom: 20px;
    }
    .alert-danger {
        background-color: #f8d7da;
        border-color: #f5c6cb;
        color: #721c24;
    }
    .alert-warning {
        background-color: #fff3cd;
        border-color: #ffeeba;
        color: #856404;
    }
    .form-text {
        font-size: 0.875rem;
        color: #6c757d;
    }
</style>

<div class="container">
    <h2><i class="fas fa-key"></i> Changement de mot de passe et photo de profil</h2>
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