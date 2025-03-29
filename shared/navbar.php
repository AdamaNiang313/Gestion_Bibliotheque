<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Inclure Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
    :root {
        --primary-color: #4361ee;
        --secondary-color: #3f37c9;
        --accent-color: #4cc9f0;
        --dark-color: #3a0ca3;
        --light-color: #f8f9fa;
        --text-light: #ffffff;
        --text-dark: #212529;
        --shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    }

    body {
        font-family: 'Poppins', sans-serif;
        padding-left: 280px;
        background-color: #f8fafc;
    }

    /* Sidebar - Version améliorée */
    .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        width: 280px;
        height: 100vh;
        background: linear-gradient(180deg, var(--dark-color), var(--primary-color));
        color: var(--text-light);
        box-shadow: var(--shadow);
        z-index: 1000;
        overflow-y: auto;
        transition: var(--transition);
        border-right: 1px solid rgba(255, 255, 255, 0.1);
    }

    .sidebar-header {
        padding: 1.5rem;
        text-align: center;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        background: rgba(0, 0, 0, 0.1);
        backdrop-filter: blur(5px);
    }

    .brand {
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        font-weight: 600;
        color: white;
        text-decoration: none;
        transition: var(--transition);
    }

    .brand:hover {
        transform: scale(1.05);
    }

    .brand-icon {
        font-size: 2rem;
        margin-right: 10px;
        color: var(--accent-color);
        text-shadow: 0 0 10px rgba(76, 201, 240, 0.5);
    }

    .nav-menu {
        padding: 1rem 0;
    }

    .nav-title {
        padding: 0.7rem 1.5rem;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: rgba(255, 255, 255, 0.6);
        margin-top: 0.5rem;
        font-weight: 600;
    }

    .nav-item {
        margin: 0.3rem 1rem;
        position: relative;
    }

    .nav-link {
        display: flex;
        align-items: center;
        padding: 0.9rem 1.5rem;
        color: rgba(255, 255, 255, 0.9);
        border-radius: 8px;
        transition: var(--transition);
        text-decoration: none;
        font-weight: 500;
        background: transparent;
        backdrop-filter: blur(5px);
    }

    .nav-link i {
        width: 24px;
        text-align: center;
        margin-right: 12px;
        font-size: 1.1rem;
        color: var(--accent-color);
    }

    .nav-link:hover {
        background: rgba(255, 255, 255, 0.15);
        transform: translateX(5px);
        color: white;
    }

    .nav-link.active {
        background: linear-gradient(90deg, rgba(76, 201, 240, 0.2), transparent);
        color: white;
        border-left: 3px solid var(--accent-color);
    }

    .nav-link.active::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: 3px;
        background: var(--accent-color);
    }

    .sidebar-footer {
        position: absolute;
        bottom: 0;
        width: 100%;
        padding: 1.2rem;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        background: rgba(0, 0, 0, 0.1);
        backdrop-filter: blur(5px);
    }

    .logout-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        padding: 0.7rem;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: var(--text-light);
        border-radius: 8px;
        transition: var(--transition);
        font-weight: 500;
        cursor: pointer;
    }

    .logout-btn:hover {
        background: rgba(255, 255, 255, 0.2);
        border-color: var(--accent-color);
        transform: translateY(-2px);
    }

    .logout-btn i {
        margin-right: 8px;
    }

    /* Header amélioré */
    .main-header {
        position: fixed;
        top: 0;
        left: 280px;
        right: 0;
        height: 50px;
        background: rgba(234, 233, 240, 0.95);
        box-shadow: var(--shadow);
        display: flex;
        align-items: center;
        justify-content: flex-end;
        padding: 0 2.5rem;
        z-index: 900;
        backdrop-filter: blur(5px);
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    .user-profile {
        display: flex;
        align-items: center;
        gap: 1.2rem;
        padding: 0.5rem 1rem;
        border-radius: 50px;
        transition: var(--transition);
    }

    .user-profile:hover {
        background: rgba(67, 97, 238, 0.1);
    }

    .user-info {
        text-align: right;
    }

    .user-name {
        font-weight: 600;
        margin-bottom: 0.2rem;
        color: var(--text-dark);
        font-size: 0.95rem;
    }

    .user-role {
        font-size: 0.75rem;
        color: #6c757d;
        letter-spacing: 0.5px;
    }

    .user-avatar {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid var(--accent-color);
        transition: var(--transition);
        box-shadow: 0 0 0 3px rgba(76, 201, 240, 0.2);
    }

    .user-avatar:hover {
        transform: scale(1.1);
        box-shadow: 0 0 0 3px var(--accent-color);
    }

    /* Animation améliorée */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .nav-item {
        animation: fadeIn 0.4s cubic-bezier(0.39, 0.575, 0.565, 1) forwards;
        opacity: 0;
    }

    .nav-item:nth-child(1) { animation-delay: 0.1s; }
    .nav-item:nth-child(2) { animation-delay: 0.2s; }
    .nav-item:nth-child(3) { animation-delay: 0.3s; }
    .nav-item:nth-child(4) { animation-delay: 0.4s; }
    .nav-item:nth-child(5) { animation-delay: 0.5s; }
    .nav-item:nth-child(6) { animation-delay: 0.6s; }

    /* Effet de vague au survol */
    .nav-link::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: radial-gradient(circle at center, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0) 70%);
        opacity: 0;
        transition: var(--transition);
    }

    .nav-link:hover::after {
        opacity: 1;
    }

    /* Responsive */
    @media (max-width: 992px) {
        body {
            padding-left: 0;
        }

        .sidebar {
            transform: translateX(-280px);
        }

        .sidebar.active {
            transform: translateX(0);
        }

        .main-header {
            left: 0;
        }
    }
</style>
</head>
<body>
    <!-- Sidebar Navigation -->
    <nav class="sidebar">
        <div class="sidebar-header">
            <a href="index.php" class="brand">
                <i class="fas fa-book-open brand-icon"></i>
                <span>IIBS Library</span>
            </a>
        </div>

        <div class="nav-menu">
            <?php if($_SESSION['id_r'] == 1): // Gestionnaire ?>
                <div class="nav-title">Gestion</div>
                <ul>
                    <li class="nav-item">
                        <a href="?action=listUser" class="nav-link">
                            <i class="fas fa-users"></i>
                            <span>Utilisateurs</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="?action=listRole" class="nav-link">
                            <i class="fas fa-user-tag"></i>
                            <span>Rôles</span>
                        </a>
                    </li>
                </ul>
                
                <div class="nav-title">Catalogue</div>
                <ul>
                    <li class="nav-item">
                        <a href="?action=listAuteur" class="nav-link">
                            <i class="fas fa-theater-masks"></i>
                            <span>Auteurs</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="?action=listLivre" class="nav-link">
                            <i class="fas fa-book"></i>
                            <span>Livres</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="?action=listStatistiques" class="nav-link">
                            <i class="fas fa-book"></i>
                            <span>ChartJS</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="?action=listRayon" class="nav-link">
                            <i class="fas fa-archive"></i>
                            <span>Rayons</span>
                        </a>
                    </li>
                </ul>
                
                <div class="nav-title">Transactions</div>
                <ul>
                <li class="nav-item">
                  <a href="index.php?action=validerEmprunts" class="nav-link">
                      <i class="fas fa-exchange-alt"></i>
                      <span>Valider Emprunts</span>
                  </a>
                </li>
                </ul>

                <?php elseif($_SESSION['id_r'] == 2): // Adhérent ?>
                  <div class="nav-title">Mon Espace</div>
                  <ul>
                    <li class="nav-item">
                        <a href="?action=catalogue" class="nav-link"> <!-- J'ai changé listcatalogues en catalogue -->
                            <i class="fas fa-search"></i>
                            <span>Catalogue</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="?action=mesEmprunt" class="nav-link">
                            <i class="fas fa-list-ul"></i>
                            <span>Mes Emprunts</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="?action=historique" class="nav-link">
                            <i class="fas fa-history"></i>
                            <span>Historique</span>
                        </a>
                    </li>
                  </ul>
              <?php else: // RP ?>
                  <div class="nav-title">Gestion Exemplaires</div>
                  <ul>
                      <li class="nav-item">
                          <a href="?action=listExemplaire" class="nav-link">
                              <i class="fas fa-copy"></i>
                              <span>Liste exemplaires</span>
                          </a>
                      </li>
                     
                  </ul>
              <?php endif; ?>
        </div>

        <div class="sidebar-footer mt-5">
            <button class="logout-btn" onclick="window.location.href='?action=deconnexion'">
                <i class="fas fa-sign-out-alt"></i>
                <span>Déconnexion</span>
            </button>
        </div>
    </nav>

    <!-- Main Header -->
    <header class="main-header">
        <div class="user-profile">
            <div class="user-info">
                <div class="user-name"><?= htmlspecialchars($_SESSION['prenom'] . ' ' . $_SESSION['nom']) ?></div>
                <div class="user-role">
                    <?php 
                    if($_SESSION['id_r'] == 1) echo 'Gestionnaire';
                    elseif($_SESSION['id_r'] == 2) echo 'Adhérent';
                    else echo 'Responsable Pédagogique ';
                    ?>
                </div>
            </div>
            <img src="../../uploads/image.jpg ?>" class="user-avatar" alt="Photo de profil">
        </div>
    </header>

    <script>
        // Ajouter une classe active au lien courant
        document.addEventListener('DOMContentLoaded', function() {
            const currentUrl = window.location.href;
            const navLinks = document.querySelectorAll('.nav-link');
            
            navLinks.forEach(link => {
                if (currentUrl.includes(link.getAttribute('href'))) {
                    link.classList.add('active');
                }
            });
            
            // Animation au chargement
            const navItems = document.querySelectorAll('.nav-item');
            navItems.forEach((item, index) => {
                item.style.animationDelay = `${index * 0.1}s`;
            });
        });
    </script>
</body>
</html>