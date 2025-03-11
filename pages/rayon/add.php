<?php
require_once 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $libelle = htmlspecialchars($_POST['libelle']);

    // Insertion du rayon dans la base de données
    $sql = "INSERT INTO rayon (libelle) VALUES ('$libelle')";
    if (mysqli_query($connexion, $sql)) {
        header('location:index.php?action=listRayon');
        exit;
    } else {
        echo "<div class='alert alert-danger'>Erreur lors de l'ajout du rayon.</div>";
    }
}
?>

<div class="container">
    <h2 class="mt-5">Ajouter un rayon</h2>
    <form action="?action=addRayon" method="POST">
        <div class="form-group">
            <label for="libelle">Libellé:</label>
            <input type="text" id="libelle" name="libelle" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Enregistrer</button>
    </form>
</div>