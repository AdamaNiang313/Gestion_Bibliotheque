<?php
?>

<!-- Inclure Font Awesome via CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<nav class="navbar navbar-expand-lg navbar-light px-4 text-light shadow-sm fixed-top" style="background-color: rgb(0, 174, 255);">
  <a class="navbar-brand text-light fw-bold" href="#">
    <i class="fas fa-school"></i> ECOLE IIBS
  </a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  
  <div class="collapse navbar-collapse justify-content-between" id="navbarNav">
    <!-- Liens à gauche -->
    <ul class="navbar-nav gap-3">
      <?php if($_SESSION['id_r'] == 1){ //RB ?>
      <li class="nav-item">
        <a href="?action=listUser" class="btn btn-outline-light btn-hover-animation" role="button" data-bs-toggle="button">
          <i class="fas fa-users"></i> Liste users
        </a>
      </li>
      <li class="nav-item">
        <a href="?action=listRole" class="btn btn-outline-light btn-hover-animation" role="button" data-bs-toggle="button">
          <i class="fas fa-user-tag"></i> Liste rôles
        </a>
      </li>
      <li class="nav-item">
        <a href="?action=listAuteur" class="btn btn-outline-light btn-hover-animation" role="button" data-bs-toggle="button">
          <i class="fas fa-theater-masks"></i> Liste Acteurs
        </a>
      </li>
      <li class="nav-item">
        <a href="?action=listLivre" class="btn btn-outline-light btn-hover-animation" role="button" data-bs-toggle="button">
          <i class="fas fa-book-open"></i> Liste Livres
        </a>
      </li>
      <li class="nav-item">
        <a href="?action=listRayon" class="btn btn-outline-light btn-hover-animation" role="button" data-bs-toggle="button">
          <i class="fas fa-archive"></i> Liste Rayons
        </a>
      </li>
      <?php }else if($_SESSION['id_r'] == 2){ //RP ?>
      <li class="nav-item">
        <a href="?action=listExemplaire" class="btn btn-outline-light btn-hover-animation" role="button" data-bs-toggle="button">
          <i class="fas fa-copy"></i> Liste exemplaires
        </a>
      </li>
      <?php }else{ //Adherant ?>
        <a href="?action=listEmprunt" class="btn btn-outline-light btn-hover-animation" role="button" data-bs-toggle="button">
          <i class="fas fa-book-open"></i> Mes emprunts
        </a>
      <?php }?>
    </ul>

    <!-- Liens à droite -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link text-light fw-bold" href="" name="nom" value="">
          <img src="<?= $_SESSION['photo'] ?>" alt="" style="width: 30px; height: 30px; border-radius: 50%; border: 2px solid white;">
          <?= $_SESSION['prenom'] . " " . $_SESSION['nom']; ?>
        </a>
      </li>
      <li class="nav-item">
        <a class="btn btn-outline-light btn-sm btn-hover-animation" href="?action=deconnexion">
          <i class="fas fa-sign-out-alt"></i> Déconnexion
        </a>
      </li>
    </ul>
  </div>
</nav>

<!-- Styles pour les effets de survol et animations -->
<style>
  .btn-hover-animation:hover {
    transform: scale(1.05);
    transition: transform 0.2s ease-in-out;
    background-color: rgba(255, 255, 255, 0.1); /* Fond légèrement blanc au survol */
  }

  .nav-link img {
    transition: transform 0.2s ease-in-out;
  }

  .nav-link img:hover {
    transform: scale(1.1); /* Effet de zoom au survol */
  }

  /* Ajouter un padding au body pour éviter que le contenu ne soit caché sous la navbar */
  body {
    padding-top: 70px; /* Ajustez cette valeur en fonction de la hauteur de votre navbar */
  }

  @media (max-width: 768px) {
    .navbar-nav {
      gap: 10px; /* Réduire l'espace entre les boutons */
    }

    .btn {
      padding: 5px 10px; /* Réduire la taille des boutons */
      font-size: 14px; /* Réduire la taille du texte */
    }
  }
</style>