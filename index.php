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
            $image = null;
            // Handle image upload
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $image = basename($_FILES['photo']['name']);
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
    // Insertion du livre dans la base de données
            $sql = "UPDATE user SET password = '$new_password',photo = $image, count = 1 WHERE id = '$id'";
            mysqli_query($connexion, $sql);
            header('location:index.php?action=listUser');
        }
        if ($_GET['action'] == "changePasswordAndLogin") {
            require_once './pages/auth/changePasswordAndLogin.php';
        }

        // Gestion des livres
        if ($_GET['action'] == "addLivre") {
            require_once './pages/livres/add.php';
        }
        if ($_GET['action'] == "listLivre") {
            require_once './pages/livres/list.php';
        }
        if ($_GET['action'] == "deleteLivre") {
            $id = $_GET['id'];
            $sql = "DELETE FROM livre WHERE id = $id";
            mysqli_query($connexion, $sql);
            header('location:index.php?action=listLivre');
        }
        if ($_GET['action'] == "editLivre") {
            $id = $_GET['id'];
            $sql = "SELECT * FROM livre WHERE id = $id";
            $result = mysqli_query($connexion, $sql);
            if ($result) {
                $livre = mysqli_fetch_assoc($result);
            } else {
                echo "Erreur: " . mysqli_error($connexion);
            }
            require_once './pages/livres/edit.php';
        }
        if ($_GET['action'] == "updateLivre") {
            $id = $_POST['id'];
            $titre = htmlspecialchars($_POST['titre']);
            $auteur = htmlspecialchars($_POST['auteur']);
            $description = htmlspecialchars($_POST['description']);
            $date_publication = $_POST['date_publication'];
            $image = $livre['image']; // Conserver l'image actuelle par défaut

            // Gestion de l'upload de l'image
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $file_name = $_FILES['image']['name'];
                $temp_name = $_FILES['image']['tmp_name'];
                $file_path = "./auth/uploads/" . $file_name;

                $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
                if (in_array($_FILES['image']['type'], $allowed_types)) {
                    if (move_uploaded_file($temp_name, $file_path)) {
                        $image = $file_name;
                    } else {
                        echo "<div class='alert alert-danger'>Erreur lors du déplacement du fichier.</div>";
                    }
                } else {
                    echo "<div class='alert alert-warning'>Type de fichier non autorisé.</div>";
                }
            }

            // Mise à jour du livre dans la base de données
            $sql = "UPDATE livre 
                    SET titre = '$titre', auteur = '$auteur', description = '$description', date_publication = '$date_publication', image = '$image' 
                    WHERE id = $id";
            if (mysqli_query($connexion, $sql)) {
                header('location:livre.php?action=listLivre');
                exit;
            } else {
                echo "<div class='alert alert-danger'>Erreur lors de la mise à jour du livre.</div>";
            }
        }

        // Gestion des rayons
        if ($_GET['action'] == "addRayon") {
            require_once './pages/rayon/add.php';
        }
        if ($_GET['action'] == "listRayon") {
            require_once './pages/rayon/list.php';
        }
        if ($_GET['action'] == "deleteRayon") {
            $id = $_GET['code'];
            $sql = "DELETE FROM rayon WHERE code = $code";
            mysqli_query($connexion, $sql);
            header('location:index.php?action=listRayon');
        }
        if ($_GET['action'] == "editRayon") {
            $code = $_GET['code'];
            $sql = "SELECT * FROM rayon WHERE code = $code";
            $result = mysqli_query($connexion, $sql);
            if ($result) {
                $rayon = mysqli_fetch_assoc($result);
            } else {
                echo "Erreur: ". mysqli_error($connexion);
            }
            require_once './pages/rayon/edit.php';
        }
        if ($_GET['action'] == "updateRayon") {
            $code = $_POST['code'];
            $libelle = htmlspecialchars($_POST['libelle']);
            $sql = "UPDATE rayon SET libelle = '$libelle' WHERE code = $code";
            if (mysqli_query($connexion, $sql)) {
                header('Location: index.php?action=listRayon');
            }
        }
        if ($_GET['action'] == "listRayon") {
            require_once './pages/rayon/list.php';
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
