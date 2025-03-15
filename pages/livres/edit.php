<?php
require_once 'database.php';

// Récupérer les informations du livre à modifier
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM livre WHERE id = $id";
    $result = mysqli_query($connexion, $sql);
    $livre = mysqli_fetch_assoc($result);
}

// Récupérer la liste des auteurs et des rayons pour les menus déroulants
$sql_auteurs = "SELECT * FROM auteur";
$auteurs = mysqli_query($connexion, $sql_auteurs);

$sql_rayons = "SELECT * FROM rayon";
$rayons = mysqli_query($connexion, $sql_rayons);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $titre = htmlspecialchars($_POST['titre']);
    $date_edition = $_POST['date_edition'];
    $id_a = $_POST['id_a'];
    $id_r = $_POST['id_r'];
    $photo = $livre['photo']; // Conserver la photo actuelle par défaut

    // Gestion de l'upload de la photo
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $file_name = $_FILES['photo']['name'];
        $temp_name = $_FILES['photo']['tmp_name'];
        $file_path = "./auth/uploads/" . $file_name;

        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($_FILES['photo']['type'], $allowed_types)) {
            if (move_uploaded_file($temp_name, $file_path)) {
                $photo = $file_name;
            } else {
                echo "<div class='alert alert-danger'>Erreur lors du déplacement du fichier.</div>";
            }
        } else {
            echo "<div class='alert alert-warning'>Type de fichier non autorisé.</div>";
        }
    }

    // Mise à jour du livre dans la base de données
    $sql = "UPDATE livre 
            SET titre = '$titre', date_edition = '$date_edition', photo = '$photo', id_a = '$id_a', id_r = '$id_r' 
            WHERE id = $id";
    if (mysqli_query($connexion, $sql)) {
        header('location:index.php?action=listLivre');
        exit;
    } else {
        echo "<div class='alert alert-danger'>Erreur lors de la mise à jour du livre.</div>";
    }
}
?>

<div class="container w-50 mt-5 p-4 shadow rounded bg-light">
    <h2 class="mb-4"><i class="fas fa-edit"></i> Modifier un livre</h2>
    <form action="?action=editLivre" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $livre['id'] ?>">
        <div class="mb-4">
            <label for="titre" class="form-label fw-bold"><i class="fas fa-heading"></i> Titre :</label>
            <input type="text" id="titre" name="titre" class="form-control p-2" value="<?= $livre['titre'] ?>" required>
        </div>
        <div class="mb-4">
            <label for="date_edition" class="form-label fw-bold"><i class="fas fa-calendar-alt"></i> Date d'édition :</label>
            <input type="date" id="date_edition" name="date_edition" class="form-control p-2" value="<?= $livre['date_edition'] ?>" required>
        </div>
        <div class="mb-4">
            <label for="id_a" class="form-label fw-bold"><i class="fas fa-user"></i> Auteur :</label>
            <select id="id_a" name="id_a" class="form-control p-2" required>
                <?php while ($auteur = mysqli_fetch_assoc($auteurs)) { ?>
                    <option value="<?= $auteur['code'] ?>" <?= ($auteur['code'] == $livre['id_a']) ? 'selected' : '' ?>>
                        <?= $auteur['nom'] ?> <?= $auteur['prenom'] ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <div class="mb-4">
            <label for="id_r" class="form-label fw-bold"><i class="fas fa-archive"></i> Rayon :</label>
            <select id="id_r" name="id_r" class="form-control p-2" required>
                <?php while ($rayon = mysqli_fetch_assoc($rayons)) { ?>
                    <option value="<?= $rayon['code'] ?>" <?= ($rayon['code'] == $livre['id_r']) ? 'selected' : '' ?>>
                        <?= $rayon['libelle'] ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <div class="mb-4">
            <label for="photo" class="form-label fw-bold"><i class="fas fa-camera"></i> Photo de couverture :</label>
            <input type="file" id="photo" name="photo" class="form-control p-2" accept="image/*">
            <small class="form-text text-muted">Formats acceptés : JPEG, PNG, GIF. Taille maximale : 5 Mo.</small>
            <?php if (!empty($livre['photo'])) { ?>
                <p>Photo actuelle : <img src="uploads/<?= $livre['photo'] ?>" alt="Couverture du livre" style="width: 50px; height: 50px;"></p>
            <?php } ?>
        </div>
        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
            <button type="submit" class="btn btn-primary me-md-2">
                <i class="fas fa-save"></i> Enregistrer
            </button>
            <a href="?action=listLivre" class="btn btn-danger">
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