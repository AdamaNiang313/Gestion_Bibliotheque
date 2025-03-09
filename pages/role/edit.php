

<div class="container w-50 mt-5 p-4 shadow rounded bg-light">
    <form action="?action=updateRole" method="POST">
        <!-- Champ caché pour l'ID -->
        <input type="text" name="id" value="<?= $role['id'] ?>" hidden>

        <!-- Champ Libellé -->
        <div class="mb-4">
            <label for="libelle" class="form-label fw-bold">Nom :</label>
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