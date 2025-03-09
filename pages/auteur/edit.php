

<div class="container w-50 mt-5 p-4 shadow rounded bg-light">
    <form action="?action=updateAuteur" method="POST">
        <input type="text" name="code" value="<?= $auteur['code'] ?>" hidden>
        <div class="mb-4">
            <label for="nom" class="form-label fw-bold">Nom :</label>
            <input type="text" name="nom" class="form-control p-2" value="<?= $auteur['nom'] ?>" required>
        </div>
        <div class="mb-4">
            <label for="prenom" class="form-label fw-bold">Prénom :</label>
            <input type="text" name="prenom" class="form-control p-2" value="<?= $auteur['prenom'] ?>" required>
        </div>
        <div class="mb-4">
            <label for="profession" class="form-label fw-bold">Profession :</label>
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