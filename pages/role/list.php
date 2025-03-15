<?php
    $sql="SELECT * FROM role ";
    $role = mysqli_query($connexion,$sql);


?>
<div class="container mt-5">
    <a class="btn btn-success mb-3" href="?action=addRole"><i class="fas fa-plus"></i> Nouveau</a>
    <table class="table table-bordered">
        <thead class="thead-dark">
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

<!-- Styles for table and buttons -->
<style>
    .table {
        border-collapse: collapse;
        width: 100%;
        margin: 20px 0;
        font-size: 1em;
        text-align: left;
    }

    .table th, .table td {
        padding: 12px 15px;
    }

    .table thead th {
        background-color: #343a40;
        color: #ffffff;
    }

    .table tbody tr {
        border-bottom: 1px solid #dddddd;
    }

    .table tbody tr:nth-of-type(even) {
        background-color: #f3f3f3;
    }

    .table tbody tr:last-of-type {
        border-bottom: 2px solid #343a40;
    }

    .btn {
        margin-right: 5px;
    }

    .btn-warning {
        background-color: #ffc107;
        border: none;
    }

    .btn-danger {
        background-color: #dc3545;
        border: none;
    }

    .btn-success {
        background-color: #28a745;
        border: none;
    }

    .btn i {
        margin-right: 5px;
    }
</style>
