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

<div class="container w-50 mt-5 p-4 shadow rounded bg-light">
    <h2 class="mb-4"><i class="fas fa-plus"></i> Ajouter un rayon</h2>
    <form action="?action=addRayon" method="POST">
        <div class="mb-4">
            <label for="libelle" class="form-label fw-bold"><i class="fas fa-tag"></i> Libellé :</label>
            <input type="text" id="libelle" name="libelle" class="form-control p-2" required>
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