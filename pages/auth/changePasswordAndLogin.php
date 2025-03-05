<?php
/*
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $newPassword = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
    $confirmPassword = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
    $login = $_SESSION['login'];

    if (isset($_FILES['file'])) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $fileExtension = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
        if (!in_array($fileExtension, $allowed)) {
            $message = "Le fichier n'est pas une image";
        } else {
            $fileName = $_FILES['file']['name'];
            $fileTmpName = $_FILES['file']['tmp_name'];
            $fileSize = $_FILES['file']['size'];
            $fileError = $_FILES['file']['error'];
            $fileType = $_FILES['file']['type'];

            $fileDestination = 'uploads/' . $fileName;
            move_uploaded_file($fileTmpName, $fileDestination);
        }
        
    }

        # code...
    }
    # code...

*/
?>


<div class="container col-md-4 mt-5">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title"><i class="fas fa-sign-in-alt"></i> Changer votre mot de passe</h5>
        </div>
        <form action="#" method="post" class="card-body">
            <div class="form-group">
                <label for="login">new login</label>
                <input type="text" name="new_login" class="form-control" >
            </div>
            <div class="form-group">
                <label for="login">New Password</label>
                <input type="text" name="new_password" class="form-control">
            </div>
            <div class="form-group">
                <label for="login">Photo</label>
                <input type="file" name="photo" class="form-control">
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-primary" name="seConnecter"><i class="fas fa-sign-in-alt"></i> Se connecter</button>
            </div>
        </form>
    </div>
</div>