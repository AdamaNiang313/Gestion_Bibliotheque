<?php
require_once './mail.php';

$sql = "SELECT * FROM role";
$roles = mysqli_query($connexion, $sql);

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $id_r = $_POST['id_r'];
    $login = $_POST['login'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO user (nom, prenom, email, id_r, login, password ) VALUES ('$nom', '$prenom', '$email', '$id_r', '$login', '$password')";
    
    if (mysqli_query($connexion, $sql)) {
        envoyerEmailInscription($email, $_POST['password']); // Appel de la fonction pour envoyer l'email
        header('Location: index.php?action=listUser');
        exit();
    }
}
?>

<div class="container w-50 mt-5 p-4 shadow rounded bg-light">
    <h2 class="mb-4"><i class="fas fa-user-plus"></i> Ajouter un utilisateur</h2>
    <form action="#" method="POST">
        <?php if ($message): ?>
            <div class="alert alert-danger"><?= $message ?></div>
        <?php endif; ?>
        <div class="mb-4">
            <label for="nom" class="form-label fw-bold"><i class="fas fa-user"></i> Nom :</label>
            <input type="text" name="nom" class="form-control p-2" required>
        </div>
        <div class="mb-4">
            <label for="prenom" class="form-label fw-bold"><i class="fas fa-user"></i> Prénom :</label>
            <input type="text" name="prenom" class="form-control p-2" required>
        </div>
        <div class="mb-4">
            <label for="email" class="form-label fw-bold"><i class="fas fa-envelope"></i> Email :</label>
            <input type="text" name="email" class="form-control p-2" required>
        </div>
        <div class="mb-4">
            <label for="login" class="form-label fw-bold"><i class="fas fa-sign-in-alt"></i> Login :</label>
            <input type="text" name="login" class="form-control p-2" required>
        </div>
        <div class="mb-4">
            <label for="password" class="form-label fw-bold"><i class="fas fa-lock"></i> Password :</label>
            <input type="password" name="password" class="form-control p-2" required>
        </div>
        <div class="mb-4">
            <label for="id_r" class="form-label fw-bold"><i class="fas fa-user-tag"></i> Rôle :</label>
            <select name="id_r" class="form-control p-2" required>
                <?php while ($role = mysqli_fetch_assoc($roles)) { ?>
                    <option value="<?php echo $role['id']; ?>"><?php echo $role['libelle']; ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
            <button type="submit" class="btn btn-success me-md-2">
                <i class="fas fa-check-circle"></i> Valider
            </button>
            <a href="?action=listUser" class="btn btn-danger">
                <i class="fas fa-times-circle"></i> Annuler
            </a>
        </div>
    </form>
</div>

<!-- Styles for form and buttons -->
<style>
    .form-label {
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

    .btn-success {
        background-color: #28a745;
        border: none;
        padding: 10px 20px;
        font-size: 16px;
        border-radius: 5px;
    }

    .btn-danger {
        background-color: #dc3545;
        border: none;
        padding: 10px 20px;
        font-size: 16px;
        border-radius: 5px;
    }

    .btn i {
        margin-right: 5px;
    }
</style>