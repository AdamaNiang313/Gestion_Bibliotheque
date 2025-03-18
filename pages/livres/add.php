<?php
// Récupérer la liste des auteurs et des rayons
$sql_auteurs = "SELECT * FROM auteur";
$auteurs = mysqli_query($connexion, $sql_auteurs);

$sql_rayons = "SELECT * FROM rayon";
$rayons = mysqli_query($connexion, $sql_rayons);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titre = htmlspecialchars($_POST['titre']);
    $date_edition = $_POST['date_edition'];
    $id_a = $_POST['id_a'];
    $id_r = $_POST['id_r'];
    $photo = null;

    // Gestion de l'upload de la photo
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $file_name = basename($_FILES['photo']['name']);
        $temp_name = $_FILES['photo']['tmp_name'];
        $target_dir = 'uploads/';
        $file_path = $target_dir . $file_name;

        // Vérifie le type de fichier
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = mime_content_type($temp_name);

        if (in_array($file_type, $allowed_types)) {
            // Vérifie la taille du fichier (5 Mo maximum)
            if ($_FILES['photo']['size'] <= 5 * 1024 * 1024) {
                // Déplace le fichier uploadé
                if (move_uploaded_file($temp_name, $file_path)) {
                    $photo = $file_name;
                } else {
                    echo "<div class='alert alert-danger'>Erreur lors du déplacement du fichier.</div>";
                }
            } else {
                echo "<div class='alert alert-warning'>Le fichier est trop volumineux. Taille maximale : 5 Mo.</div>";
            }
        } else {
            echo "<div class='alert alert-warning'>Type de fichier non autorisé. Formats acceptés : JPEG, PNG, GIF.</div>";
        }
    }

    // Insertion du livre dans la base de données
    $stmt = $connexion->prepare("INSERT INTO livre (titre, date_edition, photo, id_a, id_r) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssii", $titre, $date_edition, $photo, $id_a, $id_r);

    if ($stmt->execute()) {
        header('location:index.php?action=listLivre');
        exit;
    } else {
        echo "<div class='alert alert-danger'>Erreur lors de l'ajout du livre.</div>";
    }
}
?>

<div class="container">
    <h2 class="mt-5"><i class="fas fa-book"></i> Ajouter un livre</h2>
    <form action="?action=addLivre" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="titre"><i class="fas fa-heading"></i> Titre:</label>
            <input type="text" id="titre" name="titre" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="date_edition"><i class="fas fa-calendar-alt"></i> Date d'édition:</label>
            <input type="date" id="date_edition" name="date_edition" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="id_a"><i class="fas fa-user"></i> Auteur:</label>
            <select id="id_a" name="id_a" class="form-control" required>
                <?php while ($auteur = mysqli_fetch_assoc($auteurs)) { ?>
                    <option value="<?= $auteur['id'] ?>"><?= $auteur['nom'] ?> <?= $auteur['prenom'] ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="form-group">
            <label for="id_r"><i class="fas fa-archive"></i> Rayon:</label>
            <select id="id_r" name="id_r" class="form-control" required>
                <?php while ($rayon = mysqli_fetch_assoc($rayons)) { ?>
                    <option value="<?= $rayon['id'] ?>"><?= $rayon['libelle'] ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="form-group">
            <label for="photo"><i class="fas fa-camera"></i> Photo de couverture:</label>
            <input type="file" id="photo" name="photo" class="form-control" accept="image/*">
            <small class="form-text text-muted">Formats acceptés : JPEG, PNG, GIF. Taille maximale : 5 Mo.</small>
        </div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Enregistrer</button>
    </form>
</div>

<!-- Styles for form and buttons -->
<style>
    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
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
        width: 100%;
        margin-top: 20px;
    }

    .btn-primary:hover {
        background-color: #0056b3;
    }

    .btn i {
        margin-right: 5px;
    }
</style>