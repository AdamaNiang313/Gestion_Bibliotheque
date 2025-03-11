<?php
$sql = "SELECT u.id, u.nom, u.prenom, u.login, u.email, u.photo, r.libelle 
        FROM user u, role r 
        WHERE u.id_r = r.id";
$users = mysqli_query($connexion, $sql);
?>

<div class="container mt-5">
    <a class="btn btn-success mb-3" href="?action=addUser"><i class="fas fa-user-plus"></i> Nouveau</a>
    <table class="table table-bordered">
        <thead class="thead-dark">
            <tr>
                <th scope="col">Id</th>
                <th scope="col">Nom</th>
                <th scope="col">Prénom</th>
                <th scope="col">Email</th>
                <th scope="col">Rôle</th>
                <th scope="col">Login</th>
                <th scope="col">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($users)) { ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['nom'] ?></td>
                    <td><?= $row['prenom'] ?></td>
                    <td><?= $row['email'] ?></td>
                    <td><?= $row['libelle'] ?></td>
                    <td><?= $row['login'] ?></td>
                    <td>
                        <a class="btn btn-warning" href="?action=editUser&&id=<?= $row['id'] ?>"><i class="fas fa-edit"></i> Modifier</a>
                        <a class="btn btn-danger" href="?action=deleteUser&&id=<?= $row['id'] ?>"><i class="fas fa-trash-alt"></i> Supprimer</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>