<?php

    if(!empty($_POST)){
        $libelle = $_POST['libelle'];
        $sql="INSERT INTO role (libelle) values ('$libelle')";
        mysqli_query($connexion,$sql);
        header('location:index.php?action=listRole');
    }


?>
<div class="container w-50 mt-5 p-4 shadow rounded bg-light">
    <form action="" method="POST">
        <!-- Champ Libellé -->
        <div class="mb-4">
            <label for="libelle" class="form-label fw-bold"><i class="fas fa-tag"></i> Libellé :</label>
            <input type="text" name="libelle" class="form-control p-2" placeholder="Entrez le libellé" required>
        </div>

        <!-- Boutons -->
        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
            <button type="submit" class="btn btn-success me-md-2">
                <i class="fas fa-check-circle"></i> Valider
            </button>
            <a href="?action=listRole" class="btn btn-danger">
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