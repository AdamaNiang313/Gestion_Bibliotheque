<?php 
session_start();

$message1 = '';
$message2 = '';
$message3 = '';
$message4 = '';



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $id = $_SESSION['id']; // Récupérer l'ID utilisateur depuis la session

    $file_path = null; // Initialisation de la variable

    // Vérifier si un fichier a été uploadé
    if (isset($_FILES['profile']) && $_FILES['profile']['error'] == 0) {
        $file_name = basename($_FILES['profile']['name']);
        $file_path = "uploads/" . $file_name;
        
        // Déplacement du fichier vers le dossier uploads/
        if (move_uploaded_file($_FILES['profile']['tmp_name'], $file_path)) {
            // Mettre à jour l'utilisateur dans la base de données
            $sql = "UPDATE users SET password = '$new_password', profile = '$file_path', rang = 1 WHERE id = '$id'";
            mysqli_query($connexion, $sql);
            
            // Mettre à jour la session et rediriger
            $_SESSION['rang'] = 0;
            header("Location: index.php?action=listeUser");
            exit();
        } else {
            echo "<div class='alert alert-danger'>Erreur lors du téléchargement du fichier.</div>";
        }
    } else {
        echo "<div class='alert alert-warning'>Veuillez sélectionner une image.</div>";
}
}
?>

<div class="container col-md-4 mt-5">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title"><i class="fas fa-sign-in-alt"></i> Changer votre mot de passe</h5>
        </div>
        <form action="#" method="post" class="card-body">
            <?php if ($message3): ?>
                <div class="alert alert-danger"><?= $message3 ?></div>
            <?php endif; ?>
            <div class="form-group">
                <label for="login">New Login</label>
                <input type="text" name="login" class="form-control" >
                <span class="text-danger"> <?php if(isset($message1)) echo $message1; ?></span>
            </div>
            <div class="form-group">
                <label for="password">New Password</label>
                <div class="input-group">
                    <input type="password" name="password" id="password" class="form-control" >
                    <div class="input-group-append mt-1">
                        <span class="input-group-text" onclick="togglePasswordVisibility()">
                            <i class="fa fa-eye" id="togglePasswordIcon"></i>
                        </span>
                    </div>
                </div>
                <span class="text-danger"> <?php if(isset($message2)) echo $message2; ?></span>
            </div>
            <div class="form-group">
                <label for="login">Photo</label>
                <input type="file" name="photo" class="form-control" >
                <span class="text-danger"> <?php if(isset($message4)) echo $message4; ?></span>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-primary" name="seConnecter"><i class="fas fa-sign-in-alt"></i> Se connecter</button>
            </div>
        </form>
    </div>
</div>

<script>
function togglePasswordVisibility() {
    var passwordField = document.getElementById("password");
    var togglePasswordIcon = document.getElementById("togglePasswordIcon");
    if (passwordField.type === "password") {
        passwordField.type = "text";
        togglePasswordIcon.classList.remove("fa-eye");
        togglePasswordIcon.classList.add("fa-eye-slash");
    } else {
        passwordField.type = "password";
        togglePasswordIcon.classList.remove("fa-eye-slash");
        togglePasswordIcon.classList.add("fa-eye");
    }
}
</script>

<!-- Assurez-vous d'inclure Font Awesome pour les icônes -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">