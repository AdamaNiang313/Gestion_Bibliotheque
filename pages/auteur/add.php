<?php

    if(!empty($_POST)){
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $profession = $_POST['profession'];
        $sql="INSERT INTO auteur(nom,prenom,profession) values ('$nom','$prenom','$profession')";
        mysqli_query($connexion,$sql);
        header('location:index.php?action=listAuteur');
    }


?>
<div class="container ">
    <form action="#" method="POST">
        <!-- Champ Libellé -->
        <div class="mb-4">
            <label for="nom" class="form-label">Nom :</label>
            <input type="text" name="nom" class="form-control" placeholder="Entrez le nom" required>
        </div>
        <div class="mb-4">
            <label for="prenom" class="form-label">Prénom :</label>
            <input type="text" name="prenom" class="form-control" placeholder="Entrez le prenom" required>
        </div>
        <div class="mb-4">
            <label for="profession" class="form-label">Profession :</label>
            <input type="text" name="profession" class="form-control" placeholder="Entrez le profession" required>
        </div>

        <!-- Boutons -->
        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
            <button type="submit" class="btn btn-success me-md-2">
                <i class="fas fa-check-circle"></i> Valider
            </button>
            <a href="?action=listAuteur" class="btn btn-danger">
                <i class="fas fa-times-circle"></i> Annuler
            </a>
        </div>
    </form>
</div>