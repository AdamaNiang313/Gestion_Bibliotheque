<?php
require_once 'database.php';

// Récupérer les informations du rayon à modifier
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM rayon WHERE id = $id";
    $result = mysqli_query($connexion, $sql);
    $rayon = mysqli_fetch_assoc($result);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $libelle = htmlspecialchars($_POST['libelle']);

    // Mise à jour du rayon dans la base de données
    $sql = "UPDATE rayon SET libelle = '$libelle' WHERE id = $id";
    if (mysqli_query($connexion, $sql)) {
        header('location:index.php?action=listRayon');
        exit;
    } else {
        echo "<div class='alert alert-danger'>Erreur lors de la mise à jour du rayon.</div>";
    }
}
?>

<div class="container w-50 mt-5 p-4 shadow rounded bg-light">
    <h2 class="mb-4"><i class="fas fa-edit"></i> Modifier un rayon</h2>
    <form action="?action=editRayon" method="POST">
        <input type="hidden" name="id" value="<?= $rayon['id'] ?>">
        <div class="mb-4">
            <label for="libelle" class="form-label fw-bold"><i class="fas fa-tag"></i> Libellé :</label>
            <input type="text" id="libelle" name="libelle" class="form-control p-2" value="<?= $rayon['libelle'] ?>" required>
        </div>
        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
            <button type="submit" class="btn btn-primary me-md-2">
                <i class="fas fa-save"></i> Enregistrer
            </button>
            <a href="?action=listRayon" class="btn btn-danger">
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

    .btn-primary {
        background-color: #007bff;
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