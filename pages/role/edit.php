<?php
if (!isset($role)) {
    echo "Erreur: rôle non trouvé.";
}
?>

<div class="container">
    <form action="?action=updateRole" method="POST">
        <input type="hidden" name="id" value="<?= $role['id'] ?>">
        <label for="libelle">Libellé</label>
        <input type="text" name="libelle" class="form-control" value="<?= $role['libelle'] ?>" required>
        <div class="mt-5">
            <button type="submit" class="btn btn-primary">Modifier</button>
            <button type="reset" class="btn btn-danger">Annuler</button>
        </div>
    </form>
</div>