<?php
require_once 'database.php';

// Récupérer la liste des livres avec leurs auteurs et rayons
$sql = "SELECT l.id, l.titre, l.date_edition, l.photo, a.nom AS auteur_nom, a.prenom AS auteur_prenom, r.libelle AS rayon_libelle
        FROM livre l
        JOIN auteur a ON l.id_a = a.code
        JOIN rayon r ON l.id_r = r.code";
$livres = mysqli_query($connexion, $sql);
?>

<div class="container mt-5">
    <a class="btn btn-success mb-3" href="?action=addLivre"><i class="fas fa-plus"></i> Ajouter un livre</a>
    <table class="table table-bordered">
        <thead class="thead-dark">
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Titre</th>
                <th scope="col">Auteur</th>
                <th scope="col">Rayon</th>
                <th scope="col">Date d'édition</th>
                <th scope="col">Photo</th>
                <th scope="col">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($livres)) { ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['titre'] ?></td>
                    <td><?= $row['auteur_nom'] ?> <?= $row['auteur_prenom'] ?></td>
                    <td><?= $row['rayon_libelle'] ?></td>
                    <td><?= $row['date_edition'] ?></td>
                    <td>
                        <?php if (!empty($row['photo'])) { ?>
                            <img src="uploads/<?= $row['photo'] ?>" alt="Couverture du livre" style="width: 50px; height: 50px;">
                        <?php } else { ?>
                            <span>Aucune photo</span>
                        <?php } ?>
                    </td>
                    <td>
                        <a class="btn btn-warning" href="?action=editLivre&&id=<?= $row['id'] ?>"><i class="fas fa-edit"></i> Modifier</a>
                        <a class="btn btn-danger" href="?action=deleteLivre&&id=<?= $row['id'] ?>"><i class="fas fa-trash-alt"></i> Supprimer</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>