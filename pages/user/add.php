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

<div class="container">
    <form action="#" method="POST">
        <?php if ($message): ?>
            <div class="alert alert-danger"><?= $message ?></div>
        <?php endif; ?>
        <label for="nom">Nom</label>
        <input type="text" name="nom" class="form-control" required>
        <label for="prenom">Prénom</label>
        <input type="text" name="prenom" class="form-control" required>
        <label for="email">Email</label>
        <input type="text" name="email" class="form-control" required>
        <label for="login">Login</label>
        <input type="text" name="login" class="form-control" required>
        <label for="password">Password</label>
        <input type="password" name="password" class="form-control" required>
        <label for="id_r">Role</label>
        <select name="id_r" class="form-control" required>
            <?php while ($role = mysqli_fetch_assoc($roles)) { ?>
                <option value="<?php echo $role['id']; ?>"><?php echo $role['libelle']; ?></option>
            <?php } ?>
        </select>
        <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i>Submit</button>
    </form>
</div>