<?php
?>

<!-- Inclure Font Awesome via CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<!-- Sidebar -->
<nav class="sidebar bg-primary text-light shadow-sm fixed-left" style="width: 250px; height: 100vh;">
  <div class="sidebar-header p-4 text-center">
    <a class="navbar-brand text-light fw-bold d-flex align-items-center justify-content-center" href="#">
      <i class="fas fa-school me-2"></i> ECOLE IIBS
    </a>
  </div>

  <ul class="nav flex-column p-3 gap-2">
    <?php if($_SESSION['id_r'] == 1){ //RB ?>
    <li class="nav-item">
      <a href="?action=listUser" class="nav-link text-light btn-hover-animation d-flex align-items-center">
        <i class="fas fa-users me-2"></i> Liste users
      </a>
    </li>
    <li class="nav-item">
      <a href="?action=listRole" class="nav-link text-light btn-hover-animation d-flex align-items-center">
        <i class="fas fa-user-tag me-2"></i> Liste rôles
      </a>
    </li>
    <li class="nav-item">
      <a href="?action=listAuteur" class="nav-link text-light btn-hover-animation d-flex align-items-center">
        <i class="fas fa-theater-masks me-2"></i> Liste Acteurs
      </a>
    </li>
    <li class="nav-item">
      <a href="?action=listLivre" class="nav-link text-light btn-hover-animation d-flex align-items-center">
        <i class="fas fa-book-open me-2"></i> Liste Livres
      </a>
    </li>
    <li class="nav-item">
      <a href="?action=listRayon" class="nav-link text-light btn-hover-animation d-flex align-items-center">
        <i class="fas fa-archive me-2"></i> Liste Rayons
      </a>
    </li>
    <?php }else if($_SESSION['id_r'] == 2){ //RP ?>
    <li class="nav-item">
      <a href="?action=listExemplaire" class="nav-link text-light btn-hover-animation d-flex align-items-center">
        <i class="fas fa-copy me-2"></i> Liste exemplaires
      </a>
    </li>
    <?php }else{ //Adherant ?>
    <li class="nav-item">
      <a href="?action=listEmprunt" class="nav-link text-light btn-hover-animation d-flex align-items-center">
        <i class="fas fa-book-open me-2"></i> Mes emprunts
      </a>
    </li>
    <?php }?>
  </ul>

  <!-- Section utilisateur et déconnexion -->
  <div class="sidebar-footer p-3 mt-auto">
    <a class="btn btn-outline-light btn-sm btn-hover-animation w-100" href="?action=deconnexion">
      <i class="fas fa-sign-out-alt me-2"></i> Déconnexion
    </a>
  </div>
</nav>

<!-- Header -->
<header class="navbar navbar-light px-4 text-light shadow-sm fixed-top" style="background: linear-gradient(90deg, rgb(0, 174, 255), rgb(0, 123, 255)); margin-left: 250px;">
  <!-- Espace vide à gauche pour aligner le contenu avec le sidebar -->
  <div class="flex-grow-1"></div>

  <!-- Section utilisateur à droite -->
  <div class="d-flex align-items-center gap-3">
    <span class="text-light fw-bold"><?= $_SESSION['prenom'] . " " . $_SESSION['nom']; ?></span>
    <img src="<?= $_SESSION['photo'] ?>" alt="" style="width: 30px; height: 30px; border-radius: 50%; border: 2px solid white;">
  </div>
</header>

<!-- Styles pour le sidebar, le header et les effets de survol -->
<style>
  /* Sidebar */
  .sidebar {
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1000;
    overflow-y: auto;
  }

  .sidebar-header {
    background-color: rgba(0, 0, 0, 0.1);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  }

  .nav-link {
    padding: 10px 15px;
    border-radius: 5px;
    transition: all 0.3s ease;
  }

  .btn-hover-animation:hover {
    background-color: rgba(255, 255, 255, 0.1);
    transform: translateX(5px);
  }

  .sidebar-footer {
    background-color: rgba(0, 0, 0, 0.1);
    border-top: 1px solid rgba(255, 255, 255, 0.1);
  }

  .btn-outline-light {
    border-color: rgba(255, 255, 255, 0.5);
    color: white;
  }

  .btn-outline-light:hover {
    background-color: rgba(255, 255, 255, 0.1);
    border-color: white;
  }

  /* Header */
  header {
    background: linear-gradient(90deg, rgb(0, 174, 255), rgb(0, 123, 255));
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    height: 60px;
    display: flex;
    align-items: center;
    padding: 0 20px;
  }

  header img {
    transition: transform 0.3s ease;
  }

  header img:hover {
    transform: scale(1.1);
  }

  /* Ajustements pour le contenu principal */
  body {
    padding-left: 250px;
    padding-top: 60px;
  }

  @media (max-width: 768px) {
    .sidebar {
      width: 100%;
      height: auto;
      position: relative;
    }

    header {
      margin-left: 0;
    }

    body {
      padding-left: 0;
      padding-top: 120px; /* Ajustement pour le header et le sidebar en mode mobile */
    }

    .sidebar-footer {
      text-align: center;
    }
  }
</style>