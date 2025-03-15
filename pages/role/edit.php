<div class="container w-50 mt-5 p-4 shadow rounded bg-light">
    <form action="?action=updateRole" method="POST">
        <!-- Champ caché pour l'ID -->
        <input type="text" name="id" value="<?= $role['id'] ?>" hidden>

        <!-- Champ Libellé -->
        <div class="mb-4">
            <label for="libelle" class="form-label fw-bold"><i class="fas fa-tag"></i> Nom :</label>
            <input type="text" name="libelle" class="form-control p-2" value="<?= $role['libelle'] ?>" required>
        </div>

        <!-- Boutons -->
        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
            <button type="submit" class="btn btn-primary me-md-2">
                <i class="fas fa-edit"></i> Modifier
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