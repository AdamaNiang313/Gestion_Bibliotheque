<div class="container w-50 mt-5 p-4 shadow rounded bg-light">
    <form action="?action=updateAuteur" method="POST">
        <input type="text" name="id" value="<?= $auteur['id'] ?>" hidden>
        <div class="mb-4">
            <label for="nom" class="form-label fw-bold"><i class="fas fa-user"></i> Nom :</label>
            <input type="text" name="nom" class="form-control p-2" value="<?= $auteur['nom'] ?>" required>
        </div>
        <div class="mb-4">
            <label for="prenom" class="form-label fw-bold"><i class="fas fa-user"></i> Prénom :</label>
            <input type="text" name="prenom" class="form-control p-2" value="<?= $auteur['prenom'] ?>" required>
        </div>
        <div class="mb-4">
            <label for="profession" class="form-label fw-bold"><i class="fas fa-briefcase"></i> Profession :</label>
            <input type="text" name="profession" class="form-control p-2" value="<?= $auteur['profession'] ?>" required>
        </div>

        <!-- Boutons -->
        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
            <button type="submit" class="btn btn-primary me-md-2">
                <i class="fas fa-edit"></i> Modifier
            </button>
            <a href="?action=listAuteur" class="btn btn-danger">
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