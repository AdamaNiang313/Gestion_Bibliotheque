<?php
    $sql="SELECT * FROM auteur ";
    $auteur = mysqli_query($connexion,$sql);


?>
<div class="container mt-5">
    <a class="btn btn-success mb-3" href="?action=addAuteur"><i class="fas fa-user-plus"></i> Nouveau</a>
    <table class="table table-bordered">
    <thead class="thead-dark">
        <tr>
        <th scope="col">Code</th>
        <th scope="col">Nom</th>
        <th scope="col">Prénom</th>
        <th scope="col">Profession</th>
        <th scope="col">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while($row = mysqli_fetch_assoc($auteur)){ ?>
            <tr>
                <td><?= $row['code'] ?></td>
                <td><?= $row['nom'] ?></td>
                <td><?= $row['prenom'] ?></td>
                <td><?= $row['profession'] ?></td>
                <td>
                    <a class="btn btn-warning" href="?action=editAuteur&&code=<?= $row['code'] ?>"><i class="fas fa-edit"></i> Modifier</a>
                    <a class="btn btn-danger" href="?action=deleteAuteur&&code=<?= $row['code'] ?>"><i class="fas fa-trash-alt"></i> Supprimer</a>
                </td>
            </tr>
        <?php } ?>
    </tbody>
    </table>
</div>
