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

        if ($user) {
            // Stocker les informations de l'utilisateur dans la session
            $_SESSION['nom'] = $user['nom'];
            $_SESSION['prenom'] = $user['prenom'];
            $_SESSION['photo'] = $user['photo'];
            $_SESSION['id_r'] = $user['id_r'];
            $_SESSION['count'] = $user['count'];
            $_SESSION['id'] = $user['id'];

            // Redirection en fonction du rôle et du compteur de changement de mot de passe
            if ($user['count'] == 0) {
                header('location:index.php?action=listUser');
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

<!-- Inclure Font Awesome via CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4 mt-5">
            <div class="card shadow">
                <div class="card-body">
                    <h2 class="card-title text-center mb-4">
                        <i class="fas fa-sign-in-alt"></i> Connexion
                    </h2>
                    <form action="#" method="post">
                        <div class="form-group mb-3">
                            <label for="login" class="form-label">
                                <i class="fas fa-user"></i> Login:
                            </label>
                            <input type="text" id="login" name="login" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock"></i> Password:
                            </label>
                            <input type="password" id="password" name="password" class="form-control" required>
                        </div>
                        <?php if ($verif) { ?>
                            <div class="alert alert-danger text-center">
                                <i class="fas fa-exclamation-circle"></i> Login ou mot de passe incorrect
                            </div>
                        <?php } ?>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-sign-in-alt"></i> Se connecter
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Styles pour améliorer l'apparence -->
<style>
    .container {
        max-width: 100%;
        margin-top: 90px;
    }

    .card {
        border: none;
        border-radius: 10px;
    }

    .card-title {
        font-size: 1.5rem;
        color: #333;
    }

    .form-label {
        font-weight: 500;
    }

    .btn-primary {
        background-color: #007bff;
        border: none;
        transition: background-color 0.3s ease;
    }

    .btn-primary:hover {
        background-color: #0056b3;
    }

    .alert-danger {
        background-color: #f8d7da;
        border-color: #f5c6cb;
        color: #721c24;
    }

    .fa-sign-in-alt, .fa-user, .fa-lock, .fa-exclamation-circle {
        margin-right: 8px;
    }
</style>