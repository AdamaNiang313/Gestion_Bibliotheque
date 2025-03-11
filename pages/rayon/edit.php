<?php
require_once 'database.php';

// Récupérer les informations du rayon à modifier
if (isset($_GET['code'])) {
    $code = $_GET['code'];
    $sql = "SELECT * FROM rayon WHERE code = $code";
    $result = mysqli_query($connexion, $sql);
    $rayon = mysqli_fetch_assoc($result);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $code = $_POST['code'];
    $libelle = htmlspecialchars($_POST['libelle']);

    // Mise à jour du rayon dans la base de données
    $sql = "UPDATE rayon SET libelle = '$libelle' WHERE code = $code";
    if (mysqli_query($connexion, $sql)) {
        header('location:index.php?action=listRayon');
        exit;
    } else {
        echo "<div class='alert alert-danger'>Erreur lors de la mise à jour du rayon.</div>";
    }
}
?>

<div class="container">
    <h2 class="mt-5">Modifier un rayon</h2>
    <form action="?action=editRayon" method="POST">
        <input type="hidden" name="code" value="<?= $rayon['code'] ?>">
        <div class="form-group">
            <label for="libelle">Libellé:</label>
            <input type="text" id="libelle" name="libelle" class="form-control" value="<?= $rayon['libelle'] ?>" required>
        </div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Enregistrer</button>
    </form>
</div>