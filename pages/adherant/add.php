<?php
require_once 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date_debut = $_POST["date_debut"];
    $date_fin = $_POST["date_fin"];
    $id_livre = $_POST["id_livre"];

    // Insertion de l'emprunt dans la base de données
    $sql = "INSERT INTO emprunt (date_debut, date_fin, id_livre) VALUES ('$date_debut', '$date_fin', '$id_livre')";
    if (mysqli_query($connexion, $sql)) {
        // Mettre à jour le statut du livre
        $sql_update = "UPDATE exemplaire SET statut = 'emprunté' WHERE id = '$id_livre'";
        mysqli_query($connexion, $sql_update);

        header('location:index.php?action=listEmprunt');
        exit;
    } else {
        echo "<div class='alert alert-danger'>Erreur lors de l'ajout de l'emprunt.</div>";
    }
}
?>

<div class="container w-50 mt-5 p-4 shadow rounded bg-light">
    <h2 class="mb-4"><i class="fas fa-plus"></i> Entrer les deux dates</h2>
    <form action="?action=addEmprunt" method="POST">
        <div class="mb-4">
            <label for="date_debut" class="form-label fw-bold"><i class="fas fa-tag"></i> Date début :</label>
            <input type="date" id="date_debut" name="date_debut" class="form-control p-2" required>
        </div>
        <div class="mb-4">
            <label for="date_fin" class="form-label fw-bold"><i class="fas fa-tag"></i> Date fin :</label>
            <input type="date" id="date_fin" name="date_fin" class="form-control p-2" required>
        </div>
        <div class="mb-4">
            <label for="id_livre" class="form-label fw-bold"><i class="fas fa-book"></i> id du livre :</label>
            <input type="text" id="id_livre" name="id_livre" class="form-control p-2" required>
        </div>
        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
            <button type="submit" class="btn btn-primary me-md-2">
                <i class="fas fa-save"></i> Enregistrer
            </button>
            <a href="?action=listEmprunt" class="btn btn-danger">
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