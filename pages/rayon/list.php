<?php
require_once 'database.php';

// Récupérer la liste des rayons
$sql = "SELECT * FROM rayon";
$rayons = mysqli_query($connexion, $sql);
?>

<div class="container mt-5">
    <a class="btn btn-success mb-3" href="?action=addRayon"><i class="fas fa-plus"></i> Ajouter un rayon</a>
    <table class="table table-bordered">
        <thead class="thead-dark">
            <tr>
                <th scope="col">Code</th>
                <th scope="col">Libellé</th>
                <th scope="col">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($rayons)) { ?>
                <tr>
                    <td><?= $row['code'] ?></td>
                    <td><?= $row['libelle'] ?></td>
                    <td>
                        <a class="btn btn-warning" href="?action=editRayon&&code=<?= $row['code'] ?>"><i class="fas fa-edit"></i> Modifier</a>
                        <a class="btn btn-danger" href="?action=deleteRayon&&code=<?= $row['code'] ?>"><i class="fas fa-trash-alt"></i> Supprimer</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>