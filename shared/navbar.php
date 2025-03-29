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
            --accent-color: #4895ef;
            --dark-color: #3a0ca3;
            --light-color: #f8f9fa;
            --success-color: #4cc9f0;
            --text-light: #ffffff;
            --text-dark: #212529;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        body {
            font-family: 'Poppins', sans-serif;
            padding-left: 280px;
            background-color: #f5f7fa;
        }

        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 280px;
            height: 100vh;
            background: linear-gradient(180deg, var(--primary-color), var(--dark-color));
            color: var(--text-light);
            box-shadow: var(--shadow);
            z-index: 1000;
            overflow-y: auto;
            transition: var(--transition);
        }

        .sidebar-header {
            padding: 1.5rem;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            background-color: rgba(0, 0, 0, 0.1);
        }

        .brand {
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 600;
            color: white;
            text-decoration: none;
        }

        .brand-icon {
            font-size: 2rem;
            margin-right: 10px;
            color: var(--success-color);
        }

        .nav-menu {
            padding: 1rem 0;
        }

        .nav-title {
            padding: 0.5rem 1.5rem;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255, 255, 255, 0.7);
            margin-top: 1rem;
        }

        .nav-item {
            margin: 0.2rem 1rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.8rem 1.5rem;
            color: var(--text-light);
            border-radius: 8px;
            transition: var(--transition);
            text-decoration: none;
            font-weight: 500;
        }

        .nav-link:hover, .nav-link.active {
            background-color: rgba(255, 255, 255, 0.15);
            transform: translateX(5px);
        }

        .nav-link i {
            width: 24px;
            text-align: center;
            margin-right: 12px;
            font-size: 1.1rem;
        }

        .sidebar-footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            padding: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            background-color: rgba(0, 0, 0, 0.1);
        }

        .logout-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 0.6rem;
            background-color: transparent;
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: var(--text-light);
            border-radius: 8px;
            transition: var(--transition);
            font-weight: 500;
        }

        .logout-btn:hover {
            background-color: rgba(255, 255, 255, 0.1);
            border-color: var(--text-light);
        }

        /* Header Styles */
        .main-header {
            position: fixed;
            top: 0;
            left: 280px;
            right: 0;
            height: 70px;
            background: white;
            box-shadow: var(--shadow);
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding: 0 2rem;
            z-index: 900;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-info {
            text-align: right;
        }

        .user-name {
            font-weight: 600;
            margin-bottom: 0.2rem;
            color: var(--text-dark);
        }

        .user-role {
            font-size: 0.8rem;
            color: #6c757d;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--accent-color);
            transition: var(--transition);
        }

        .user-avatar:hover {
            transform: scale(1.1);
        }

        /* Badge Styles */
        .badge-notification {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: #f72585;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: bold;
        }

        /* Responsive Styles */
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

            .menu-toggle {
                display: block;
            }
        }

        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .nav-item {
            animation: fadeIn 0.3s ease forwards;
            opacity: 0;
        }

        .nav-item:nth-child(1) { animation-delay: 0.1s; }
        .nav-item:nth-child(2) { animation-delay: 0.2s; }
        .nav-item:nth-child(3) { animation-delay: 0.3s; }
        .nav-item:nth-child(4) { animation-delay: 0.4s; }
        .nav-item:nth-child(5) { animation-delay: 0.5s; }
        .nav-item:nth-child(6) { animation-delay: 0.6s; }
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