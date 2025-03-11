<?php
require_once 'database.php';

$verif = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = htmlspecialchars($_POST['login']);
    $password = htmlspecialchars($_POST['password']);

    if (empty($login) || empty($password)) {
        $verif = true;
    } else {
        // Utilisation d'une requête préparée pour éviter les injections SQL
        $sql = "SELECT * FROM user WHERE login = ?";
        $stmt = $connexion->prepare($sql);
        $stmt->bind_param("s", $login);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            // Stocker les informations de l'utilisateur dans la session
            $_SESSION['nom'] = $user['nom'];
            $_SESSION['prenom'] = $user['prenom'];
            $_SESSION['photo'] = $user['photo'];
            $_SESSION['id_r'] = $user['id_r'];
            $_SESSION['count'] = $user['count'];
            $_SESSION['id'] = $user['id'];

            // Redirection en fonction du rôle et du compteur de changement de mot de passe
            if ($user['count'] == 0) {
                header('location:index.php?action=changePasswordAndLogin');
                exit;
            } else {
                if ($user['id_r'] == 1) {
                    header('location:index.php?action=listUser');
                    exit;
                } elseif ($user['id_r'] == 2 || $user['id_r'] == 3) {
                    header('location:pointage.php?action=listLivre');
                    exit;
                }
            }
        } else {
            $verif = true;
        }
    }
}
?>

<div class="container">
    <h2 class="mt-5">Login</h2>
    <form action="#" method="post" class="mt-3">
        <div class="form-group">
            <label for="login">Login:</label>
            <input type="text" id="login" name="login" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>
        <?php if ($verif) { ?>
            <div class="alert alert-danger text-center">Login ou mot de passe incorrect</div>
        <?php } ?>
        <button type="submit" class="btn btn-primary">Login</button>
    </form>
</div>