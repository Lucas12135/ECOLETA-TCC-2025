<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil - Coletor</title>
    <link rel="icon" href="../img/logo.png" type="image/png">
    <link rel="stylesheet" href="../CSS/perfil.css">
    <link rel="stylesheet" href="../CSS/navbar.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Biblioteca de ícones -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <!-- Navbar -->
        <header class="sidebar">
            <div class="sidebar-header">
                <div class="logo-placeholder">
                    <img src="../img/logo.png" alt="Logo Ecoleta" class="logo">
                </div>
                <span class="logo-text">Ecoleta</span>
            </div>

            <button class="menu-mobile-button" onclick="toggleMobileMenu()">
                <i class="ri-menu-line"></i>
            </button>

            <nav class="sidebar-nav">
                <ul>
                    <li>
                        <a href="home.php" class="nav-link">
                            <i class="ri-home-4-line"></i>
                            <span>Home</span>
                        </a>
                    </li>
                    <li class="active">
                        <a href="perfil.php" class="nav-link">
                            <i class="ri-user-line"></i>
                            <span>Perfil</span>
                        </a>
                    </li>
                    <li>
                        <a href="agendamentos.php" class="nav-link">
                            <i class="ri-calendar-line"></i>
                            <span>Agendamentos</span>
                        </a>
                    </li>
                    <li>
                        <a href="historico.php" class="nav-link">
                            <i class="ri-history-line"></i>
                            <span>Histórico</span>
                        </a>
                    </li>
                    <li>
                        <a href="configuracoes.php" class="nav-link">
                            <i class="ri-settings-3-line"></i>
                            <span>Configurações</span>
                        </a>
                    </li>
                    <li>
                        <a href="suporte.php" class="nav-link">
                            <i class="ri-customer-service-2-line"></i>
                            <span>Suporte</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </header>

        <!-- Conteúdo Principal -->
        <main class="main-content">
            <header class="content-header">
                <div class="welcome-message">
                    <h1>Página do perfil</h1>
                    <p>Confira suas informações e configurações do seu perfil.</p>
                </div>
                <div class="header-actions">
                    <div class="action-buttons">
                        <button class="notification-btn" title="Notificações">
                            <i class="ri-notification-3-line"></i>
                            <span class="notification-badge">3</span>
                        </button>
                        <!-- Popup de Notificações -->
                        <div class="notifications-popup">
                            <div class="notifications-header">
                                <h3>Notificações</h3>
                            </div>
                            <div class="notification-list">
                                <div class="notification-item">
                                    <div class="notification-content">
                                        <i class="ri-calendar-check-line notification-icon"></i>
                                        <div class="notification-text">
                                            <p>Nova coleta agendada para hoje às 14:30</p>
                                            <span class="notification-time">Há 5 minutos</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="notification-item">
                                    <div class="notification-content">
                                        <i class="ri-map-pin-line notification-icon"></i>
                                        <div class="notification-text">
                                            <p>Alteração no endereço de coleta - Rua das Palmeiras, 789</p>
                                            <span class="notification-time">Há 30 minutos</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="notification-item">
                                    <div class="notification-content">
                                        <i class="ri-message-3-line notification-icon"></i>
                                        <div class="notification-text">
                                            <p>Mensagem do gerador sobre a coleta #123</p>
                                            <span class="notification-time">Há 1 hora</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            <div class="profile-content">

                </header>

                <div class="profile-content">
                    <!-- Cabeçalho do Perfil -->
                    <div class="profile-header">
                        <div class="profile-info">
                            <div class="profile-photo">
                                <img src="../img/profile-placeholder.jpg" alt="Foto do perfil">
                            </div>
                            <div class="profile-text">
                                <h2 class="profile-name"><?php echo isset($_SESSION['nome']) ? $_SESSION['nome'] : 'Nome do Coletor'; ?></h2>
                                <div class="rating">
                                    <i class="ri-star-fill star"></i>
                                    <i class="ri-star-fill star"></i>
                                    <i class="ri-star-fill star"></i>
                                    <i class="ri-star-fill star"></i>
                                    <i class="ri-star-half-fill star"></i>
                                    <span style="color: var(--cor-branco); margin-left: 0.5rem;">4.5</span>
                                </div>
                            </div>
                        </div>
                        <button class="btn-edit-profile">
                            <i class="ri-edit-line"></i>
                            Editar Perfil
                        </button>
                    </div>

                    <!-- Estatísticas do Perfil -->
                    <div class="profile-stats">
                        <div class="stat-card">
                            <div class="stat-header">
                                <i class="ri-oil-line"></i>
                                Total de Óleo Coletado
                            </div>
                            <div class="stat-value">1.250</div>
                            <div class="stat-label">litros</div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-header">
                                <i class="ri-calendar-check-line"></i>
                                Coletas Realizadas
                            </div>
                            <div class="stat-value">85</div>
                            <div class="stat-label">coletas</div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-header">
                                <i class="ri-star-smile-line"></i>
                                Avaliação Média
                            </div>
                            <div class="rating" style="margin-top: 0.5rem;">
                                <i class="ri-star-fill star"></i>
                                <i class="ri-star-fill star"></i>
                                <i class="ri-star-fill star"></i>
                                <i class="ri-star-fill star"></i>
                                <i class="ri-star-half-fill star"></i>
                            </div>
                            <div class="stat-label">baseado em 45 avaliações</div>
                        </div>
                    </div>
                </div>
        </main>
    </div>

    <script src="../JS/navbar.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Gerenciar notificações
            const notificationBtn = document.querySelector('.notification-btn');
            const notificationsPopup = document.querySelector('.notifications-popup');

            document.addEventListener('click', function(event) {
                const isClickInsidePopup = notificationsPopup.contains(event.target);
                const isClickOnButton = notificationBtn.contains(event.target);

                if (!isClickInsidePopup && !isClickOnButton) {
                    notificationsPopup.classList.remove('show');
                }
            });

            notificationBtn.addEventListener('click', function(event) {
                event.stopPropagation();
                notificationsPopup.classList.toggle('show');
            });

            // Botão de editar perfil
            const editBtn = document.querySelector('.btn-edit-profile');
            editBtn.addEventListener('click', function() {
                // Aqui você pode adicionar a lógica para editar o perfil
                alert('Funcionalidade de edição será implementada!');
            });
        });
    </script>
    <script src="../JS/navbar.js"></script>
</body>

</html>