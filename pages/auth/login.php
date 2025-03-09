<?php 

$message1 = '';
$message2 = '';
$message3 = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = $_POST['login'];
    $password = $_POST['password'];

    if (empty($login)) {
        $message1 = "Le login est obligatoire";
    }
    if (empty($password)) {
        $message2 = "Le mot de passe est obligatoire";
    }

    if (!empty($login) && !empty($password)) {
        $login = mysqli_real_escape_string($connexion, $login);
        $password = mysqli_real_escape_string($connexion, $password);

        
        $sql = "SELECT * FROM user WHERE login='$login'";
        $result = mysqli_query($connexion, $sql);
        $user = mysqli_fetch_assoc($result);

        // if ($_SESSION['count'] == 0) {
        //     $sql = "UPDATE user SET count = 1 WHERE login='$login'";
        //     mysqli_query($connexion, $sql);
        //     header('Location: changePasswordAndLogin.php');
        //     exit();

        // }

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['nom'] = $user['nom'];
            $_SESSION['prenom'] = $user['prenom'];
            $_SESSION['id_r'] = $user['id_r'];

            if ($user['id_r'] == 1) {
                header('Location: index.php?action=listUser');
            } 

            if ($user['count'] == 0) {
                header('Location: index.php?action=changePasswordAndLogin');
                exit();
            }
            
            else {
                header('Location: index.php?action=listCours');
                exit();
            }
        } else {
            $message3 = "Login ou mot de passe incorrect";
        }
    }
}
?>

<div class="container col-md-4 mt-5">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title"><i class="fas fa-sign-in-alt"></i> Page de connexion</h5>
        </div>
        <form action="#" method="post" class="card-body">
            <?php if ($message3): ?>
                <div class="alert alert-danger"><?= $message3 ?></div>
            <?php endif; ?>
            <div class="form-group">
                <label for="login">Login</label>
                <input type="text" name="login" class="form-control" >
                <span class="text-danger"> <?php if(isset($message1)) echo $message1; ?></span>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
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