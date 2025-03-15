<?php
    // Fetch the user data
    $id = $_GET['id'];
    $sql_role = "SELECT * FROM role";
    $sql = "SELECT * FROM user WHERE id = $id";
    $result = mysqli_query($connexion, $sql);
    $user = mysqli_fetch_assoc($result);

    // Fetch the role data
    $roles = mysqli_query($connexion, $sql_role);
?>

<div class="container w-50 mt-5 p-4 shadow rounded bg-light">
    <form action="?action=updateUser" method="POST">
        <input type="text" name="id" value="<?= $user['id'] ?>" hidden>
        <div class="mb-4">
            <label for="nom" class="form-label fw-bold"><i class="fas fa-user"></i> Nom :</label>
            <input type="text" name="nom" class="form-control p-2" value="<?= $user['nom'] ?>" required>
        </div>
        <div class="mb-4">
            <label for="prenom" class="form-label fw-bold"><i class="fas fa-user"></i> Prénom :</label>
            <input type="text" name="prenom" class="form-control p-2" value="<?= $user['prenom'] ?>" required>
        </div>
        <div class="mb-4">
            <label for="email" class="form-label fw-bold"><i class="fas fa-envelope"></i> Email :</label>
            <input type="text" name="email" class="form-control p-2" value="<?= $user['email'] ?>" required>
        </div>
        <div class="mb-4">
            <label for="login" class="form-label fw-bold"><i class="fas fa-sign-in-alt"></i> Login :</label>
            <input type="text" name="login" class="form-control p-2" value="<?= $user['login'] ?>" required>
        </div>
        <div class="mb-4">
            <label for="password" class="form-label fw-bold"><i class="fas fa-lock"></i> Password :</label>
            <input type="password" name="password" class="form-control p-2" value="<?= $user['password'] ?>" required>
        </div>
        <div class="mb-4">
            <label for="id_r" class="form-label fw-bold"><i class="fas fa-user-tag"></i> Rôle :</label>
            <select name="id_r" class="form-control p-2" required>
                <?php while ($row = mysqli_fetch_assoc($roles)) { ?>
                    <option value="<?= $row['id'] ?>" <?= $user['id_r'] == $row['id'] ? 'selected' : '' ?>><?= $row['libelle'] ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
            <button type="submit" class="btn btn-primary me-md-2">
                <i class="fas fa-edit"></i> Modifier
            </button>
            <a href="?action=listUser" class="btn btn-danger">
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