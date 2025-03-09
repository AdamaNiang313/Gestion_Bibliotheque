<?php
//session_start();
?>

<nav class="navbar navbar-expand-lg navbar-light px-4 text-light shadow-sm fixed-top" style="background-color: rgb(0, 174, 255);">
  <a class="navbar-brand text-light fw-bold" href="?action=acceuil">
    <i class="fas fa-school"></i> ECOLE IIBS
  </a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  
  <div class="collapse navbar-collapse justify-content-between" id="navbarNav">
    <!-- Liens à gauche -->
    <ul class="navbar-nav gap-3">
      <li class="nav-item">
        <a href="?action=listUser" class="btn btn-outline-light btn-hover-animation" role="button" data-bs-toggle="button">
          <i class="fas fa-list"></i> Liste users
        </a>
      </li>
      <li class="nav-item">
        <a href="?action=listRole" class="btn btn-outline-light btn-hover-animation" role="button" data-bs-toggle="button">
          <i class="fas fa-book"></i> Liste rôles
        </a>
      </li>
      <li class="nav-item">
        <a href="?action=listAuteur" class="btn btn-outline-light btn-hover-animation" role="button" data-bs-toggle="button">
          <i class="fas fa-book"></i> Liste Acteur
        </a>
      </li>
    </ul>

    <!-- Liens à droite -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link text-light fw-bold" href="" name="nom" value="">
          <i class="fas fa-user"></i> <?= $_SESSION['photo'] . " " .  $_SESSION['prenom'] . " " . $_SESSION['nom']; ?>
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

<!-- Styles pour les effets de survol -->
<style>
  .btn-hover-animation:hover {
    transform: scale(1.05);
    transition: transform 0.2s ease-in-out;
  }

  /* Ajouter un padding au body pour éviter que le contenu ne soit caché sous la navbar */
  body {
    padding-top: 70px; /* Ajustez cette valeur en fonction de la hauteur de votre navbar */
  }
</style>