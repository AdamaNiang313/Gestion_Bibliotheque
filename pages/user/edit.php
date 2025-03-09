<?php
    // Fetch the user data
    $id = $_GET['id'];
    $sql_role = "SELECT * FROM role";
    $sql = "SELECT * FROM user WHERE id = $id";
    $result = mysqli_query($connexion, $sql);
    $user = mysqli_fetch_assoc($result);

    // Fetch the role data
    $roles = mysqli_query($connexion, $sql_role);
?>

<div class="container">
    <form action="?action=updateUser" method="POST">
        <input type="text" name="id" value="<?= $user['id'] ?>" hidden>
        <label for="nom">Nom</label>
        <input type="text" name="nom" class="form-control" value="<?= $user['nom'] ?>">
        <label for="prenom">Prénom</label>
        <input type="text" name="prenom" class="form-control" value="<?= $user['prenom'] ?>">
        <label for="email">Email</label>
        <input type="text" name="email" class="form-control" value="<?= $user['email'] ?>">
        <label for="login">Login</label>
        <input type="text" name="login" class="form-control" value="<?= $user['login'] ?>">
        <label for="password">Password</label>
        <input type="password" name="password" class="form-control" value="<?= $user['password'] ?>">
        <label for="id_r">Rôle</label>
        <select name="id_r" class="form-control">
            <?php while ($row = mysqli_fetch_assoc($roles)) { ?>
                <option value="<?= $row['id'] ?>" <?= $user['id_r'] == $row['id'] ? 'selected' : '' ?>><?= $row['libelle'] ?></option>
            <?php } ?>
        </select>
        <div class="mt-5">
            <button type="submit" class="btn btn-primary">Modifier</button>
            <button type="reset" class="btn btn-danger">Annuler</button>
        </div>
    </form>
</div>