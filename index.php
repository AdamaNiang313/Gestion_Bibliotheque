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
    </head>

    <body>
       <?php 
            require_once 'database.php';
            
            session_start();

            if (isset($_GET['action'])) {
                require_once 'shared/navbar.php';
                if ($_GET['action'] == 'addRole') {
                    require_once 'pages/role/add.php';
                }
                if($_GET['action']=="listRole" ){
                    require_once './pages/role/list.php';
                }
                if($_GET['action']=="deleteRole" ){
                    $id=$_GET['id'];
                    $sql="DELETE FROM role where id = $id";
                    mysqli_query($connexion,$sql);
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

                if($_GET['action']=="listUser"){
                    require_once './pages/user/list.php';
                }
                if($_GET['action']=="deleteUser"){
                    $id=$_GET['id'];
                    $sql="DELETE FROM user where id = $id";
                    mysqli_query($connexion,$sql);
                    header('location:index.php?action=listUser');
                }

                if($_GET['action']=="addUser"){
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
                if($_GET['action']=="updateUser"){
                    extract($_POST);
                    $sql="UPDATE user SET nom ='$nom', prenom='$prenom', age=$age where id = $id";
                    mysqli_query($connexion,$sql);
                    header('location:index.php?action=listUser');
                }

                if($_GET['action']=="deconnecter"){
                    session_destroy();
                    require_once './pages/auth/login.php';
                }
                
            }else
            {
                require_once './pages/auth/login.php';
            }
       ?>
    </body>
</html>
