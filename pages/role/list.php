<?php
    $sql="SELECT * FROM role ";
    $roles = mysqli_query($connexion,$sql);


?>
<div class="container">
    <a class="btn btn-success mt-5" href="?action=addRole"> <i class="fas fa-plus"></i>Nouveau</a>
    <table class="table table-bordered mt-3">
    <thead>
        <tr>
        <th scope="col">ID</th>
        <th scope="col">Libelle</th>
        <th scope="col">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while($row = mysqli_fetch_assoc($roles)){ ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['libelle'] ?></td>
                <td>
                    <a class="btn btn-warning" href="?action=editRole&&id=<?= $row['id'] ?>" > <i class="fas fa-edit"></i> Modifier</a>
                    <a class="btn btn-danger" href="?action=deleteRole&&id=<?= $row['id'] ?>"><i class="fas fa-trash-alt"></i> Supprimer</a>
                </td>
            </tr>
        <?php } ?>
    </tbody>
    </table>
</div>
