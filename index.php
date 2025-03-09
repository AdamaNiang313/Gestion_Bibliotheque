<!doctype html>
<html lang="en">
    <head>
        <title>Title</title>
        <!-- Required meta tags -->
        <meta charset="utf-8" />
        <meta
            name="viewport"
            content="width=device-width, initial-scale=1, shrink-to-fit=no"
        />

        <!-- Bootstrap CSS v5.2.1 -->
        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
            rel="stylesheet"
            integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN"
            crossorigin="anonymous"
        />
        <script src="https://kit.fontawesome.com/a076d05399.js"></script>

    </head>

    <body>
    <?php
    session_start(); // Démarre la session
    require_once 'database.php';

    if (isset($_GET['action']) && !empty($_SESSION)) {
        require_once 'shared/navbar.php';

        // Gestion des rôles
        if ($_GET['action'] == 'addRole') {
            require_once 'pages/role/add.php';
        }
        if ($_GET['action'] == "listRole") {
            require_once './pages/role/list.php';
        }
        if ($_GET['action'] == "deleteRole") {
            $id = $_GET['id'];
            $sql = "DELETE FROM role WHERE id = $id";
            mysqli_query($connexion, $sql);
            header('location:index.php?action=listRole');
        }
        if ($_GET['action'] == "editRole") {
            $id = $_GET['id'];
            $sql = "SELECT * FROM role WHERE id = $id";
            $result = mysqli_query($connexion, $sql);
            if ($result) {
                $role = mysqli_fetch_assoc($result);
            }
            require_once './pages/role/edit.php';
        }
        if ($_GET['action'] == "updateRole") {
            $id = $_POST['id'];
            $libelle = $_POST['libelle'];
            $sql = "UPDATE role SET libelle = '$libelle' WHERE id = $id";
            if (mysqli_query($connexion, $sql)) {
                header('Location: index.php?action=listRole');
            }
        }

        // Gestion des utilisateurs
        if ($_GET['action'] == "listUser") {
            require_once './pages/user/list.php';
        }
        if ($_GET['action'] == "deleteUser") {
            $id = $_GET['id'];
            $sql = "DELETE FROM user WHERE id = $id";
            mysqli_query($connexion, $sql);
            header('location:index.php?action=listUser');
        }
        if ($_GET['action'] == "addUser") {
            require_once './pages/user/add.php';
        }
        if ($_GET['action'] == "editUser") {
            $id = $_GET['id'];
            $sql = "SELECT * FROM user WHERE id = $id";
            $result = mysqli_query($connexion, $sql);
            if ($result) {
                $user = mysqli_fetch_assoc($result);
            } else {
                echo "Erreur: " . mysqli_error($connexion);
            }
            $sql = "SELECT * FROM role";
            $roles = mysqli_query($connexion, $sql);
            require_once './pages/user/edit.php';
        }
        if ($_GET['action'] == "updateUser") {
            extract($_POST);
            $sql = "UPDATE user SET nom ='$nom', prenom='$prenom', age=$age WHERE id = $id";
            mysqli_query($connexion, $sql);
            header('location:index.php?action=listUser');
        }

        // Gestion des auteurs
        if ($_GET['action'] == "addAuteur") {
            require_once './pages/auteur/add.php';
        }
        if ($_GET['action'] == "listAuteur") {
            require_once './pages/auteur/list.php';
        }
        if ($_GET['action'] == "deleteAuteur") {
            $code = $_GET['code'];
            $sql = "DELETE FROM auteur WHERE code = $code";
            mysqli_query($connexion, $sql);
            header('location:index.php?action=listAuteur');
        }
        if ($_GET['action'] == "editAuteur") {
            $code = $_GET['code'];
            $sql = "SELECT * FROM auteur WHERE code = $code";
            $result = mysqli_query($connexion, $sql);
            if ($result) {
                $auteur = mysqli_fetch_assoc($result);
            } else {
                echo "Erreur: " . mysqli_error($connexion);
            }
            require_once './pages/auteur/edit.php';
        }
        if ($_GET['action'] == "updateAuteur") {
            $code = $_POST['code'];
            $nom = $_POST['nom'];
            $prenom = $_POST['prenom'];
            $profession = $_POST['profession'];
            $sql = "UPDATE auteur SET nom = '$nom', prenom = '$prenom', profession = '$profession' WHERE code = $code";
            if (mysqli_query($connexion, $sql)) {
                header('Location: index.php?action=listAuteur');
            }
        }

        // Changer le mot de passe
        if ($_GET['action'] == "validPassword") {
            $id = $_SESSION['id'];
            $count = $_SESSION['count'];
            $password = $_POST['password'];
            $new_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $sql = "UPDATE user SET password = '$new_password', count = 1 WHERE id = '$id'";
            mysqli_query($connexion, $sql);
            header('location:index.php?action=listUser');
        }
        if ($_GET['action'] == "changePasswordAndLogin") {
            require_once './pages/auth/changePasswordAndLogin.php';
        }

        // Déconnexion
        if ($_GET['action'] == "deconnexion") {
            session_destroy();
            header('location:index.php');
        }
    } else {
        require_once './pages/auth/login.php';
    }
?>
    </body>
</html>
