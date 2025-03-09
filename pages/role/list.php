<?php
    $sql="SELECT * FROM role ";
    $role = mysqli_query($connexion,$sql);


?>
<div class="container mt-5">
    <a class="btn btn-success mb-3" href="?action=addAuteur"><i class="fas fa-user-plus"></i> Nouveau</a>
    <table class="table table-bordered">
            <thead class="header-dark">
                <tr>
                    <th scope="col" class="text-center align-middle">Id</th>
                    <th scope="col" class="text-center align-middle">Libelle</th>
                    <th scope="col" class="text-center align-middle">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($role)) { ?>
                    <tr>
                        <td class="text-center align-middle"><?= $row['id'] ?></td>
                        <td class="text-center align-middle"><?= $row['libelle'] ?></td>
                        <td class="text-center align-middle">
                            <a class="btn btn-warning btn-sm" href="?action=editRole&&id=<?= $row['id'] ?>">
                                <i class="fas fa-edit"></i> Modifier
                            </a>
                            <a class="btn btn-danger btn-sm" href="?action=deleteRole&&id=<?= $row['id'] ?>">
                                <i class="fas fa-trash-alt"></i> Supprimer
                            </a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
</div>
