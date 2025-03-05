<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <a class="navbar-brand" href="#"><i class="fas fa-user-clock"></i> Gestion Bibliothéque</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item">
        <a class="nav-link" href="?action=listUser"><i class="fas fa-book"></i> Liste des Users</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="?action=listRole"><i class="fas fa-book"></i> Liste des Rôles</a>
      </li>
      
    <ul class="navbar-nav position-absolute top-1 end-0" style="list-style-type: none;">
      <li class="nav-item">
        <span class="nav-link"><i class="fas fa-user"></i> <b><?php echo isset($_SESSION['nom']) ? $_SESSION['prenom'] .'-'. $_SESSION['nom'] : 'Utilisateur'; ?></b></span>
      </li>
      <li class="nav-item">
        <a class="nav-link btn btn-primary text-white" href="?action=deconnecter"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
      </li>
    </ul>
  </div>
</nav>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">