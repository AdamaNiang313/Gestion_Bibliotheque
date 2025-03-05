<?php

if (!empty($_POST)) {
    $libelle = $_POST['libelle'];

    // Utilisation de requêtes préparées pour éviter les injections SQL
    $sql = "INSERT INTO role (libelle) VALUES ('$libelle')";
    if (mysqli_query($connexion, $sql)) {
        header('Location: index.php?action=listRole');
    }
}
?>

<div class="container">
    <form action="#" method="POST">
        <label for="">Libelle</label>
        <input type="text" name="libelle" class="form-control">
        <div class="mt-5">
            <button type="submit" class="btn btn-success"><i class="fas fa-plus"></i>Créer</button>
            <button type="reset" class="btn btn-danger"><i class="fas fa-times"></i>Annuler</button>
        </div>
    </form>
</div>