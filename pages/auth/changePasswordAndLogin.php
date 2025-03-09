<?php
require_once 'database.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    extract($_POST);
    $new_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $id = $_SESSION['id']; 
    $id_r = $_SESSION['id_r'];
    $count = $_SESSION['count'];
    $file_path = null; 
    
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $file_name = basename($_FILES['photo']['name']);
        $file_path = "uploads/" . $file_name;
        
       
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $file_path)) {
            
            $sql = "UPDATE user SET password = '$new_password', photo = '$file_name', count = 1 WHERE id = '$id'";
            mysqli_query($connexion, $sql);
            
            $_SESSION['count'] = 0;
            
            if ($id_r == 1) {
                header('location:index.php?action=listUser');
                exit;
            }elseif ($user['id_r'] == 2) {
                header('location:pointage.php?action=listLivre');
                exit;
            }elseif ($user['id_r'] == 3) {
                header('location:pointage.php?action=listLivre');
                exit;
            }
            exit();
        } else {
            echo "<div class='alert alert-danger'>Erreur lors du téléchargement du fichier.</div>";
        }
    } else {
        echo "<div class='alert alert-warning'>Veuillez sélectionner une image.</div>";
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
</head>
<body>
    <div class="container">
        <h2>Changement de mot de passe et photo de profil</h2>
        <form action="?action=validPassword" method="POST" enctype="multipart/form-data"> <!-- Ajoutez enctype pour l'upload de fichiers -->
            <!-- Champ pour le nouveau mot de passe -->
            <div class="form-group">
                <label for="new_password">
                    <i class="fas fa-lock"></i> Nouveau mot de passe:
                </label>
                <input type="password" id="new_password" name="new_password" class="form-control" placeholder="Entrez votre nouveau mot de passe" required>
            </div>

            <!-- Champ pour confirmer le nouveau mot de passe -->
            <div class="form-group">
                <label for="password">
                    <i class="fas fa-lock"></i> Confirmer le mot de passe:
                </label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Confirmez votre nouveau mot de passe" required>
            </div>

            <!-- Champ pour uploader la photo de profil -->
            <div class="form-group">
                <label for="photo">
                    <i class="fas fa-camera"></i> Photo de profil:
                </label>
                <input type="file" id="photo" name="photo" class="form-control" accept="image/*">
                <small class="form-text text-muted">Formats acceptés : JPEG, PNG, GIF. Taille maximale : 5 Mo.</small>
            </div>

            <!-- Bouton pour soumettre le formulaire -->
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Enregistrer
            </button>
        </form>
    </div>
