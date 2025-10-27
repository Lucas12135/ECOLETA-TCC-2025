<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Coletor</title>
    <link rel="icon" href="../img/logo.png" type="image/png">
    <link rel="stylesheet" href="../CSS/home-coletor.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Biblioteca de ícones -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <!-- Barra Lateral -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo-placeholder">
                    <img src="../img/logo.png" alt="Logo Ecoleta" class="logo">
                </div>
                <span class="logo-text">Ecoleta</span>
            </div>

            <nav class="sidebar-nav">
                <ul>
                    <li class="active">
                        <a href="#" class="nav-link">
                            <i class="ri-home-4-line"></i>
                            <span>Home</span>
                        </a>
                    </li>
                    <li>
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
        </aside>

        <!-- Conteúdo Principal -->
        <main class="main-content">
            <header class="content-header">
                <div class="welcome-message">
                    <h1>Olá, <?php echo isset($_SESSION['nome']) ? $_SESSION['nome'] : 'Coletor'; ?>!</h1>
                    <p>Confira suas coletas e atualizações de hoje</p>
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

            <!-- Cards de Informações -->
            <div class="info-cards">
                <div class="card next-collection">
                    <h3>Próxima Coleta</h3>
                    <div class="card-content">
                        <div class="time">
                            <i class="ri-time-line"></i>
                            <span>14:30</span>
                        </div>
                        <div class="location">
                            <i class="ri-map-pin-line"></i>
                            <span>Rua das Flores, 123</span>
                        </div>
                        <button class="view-map-btn">Ver no Mapa</button>
                    </div>
                </div>

                <div class="card today-collections">
                    <h3>Coletas Hoje</h3>
                    <div class="card-content">
                        <div class="collection-count">
                            <span class="number">5</span>
                            <span class="label">agendadas</span>
                        </div>
                        <div class="collection-progress">
                            <span class="completed">2</span>
                            <span class="separator">/</span>
                            <span class="total">5</span>
                            <span class="label">completadas</span>
                        </div>
                    </div>
                </div>

                <div class="card pending-requests">
                    <h3>Solicitações Pendentes</h3>
                    <div class="card-content">
                        <div class="request-count">
                            <span class="number">3</span>
                            <span class="label">novas Solicitações</span>
                        </div>
                        <button class="view-requests-btn">Ver Solicitações</button>
                    </div>
                </div>
            </div>

            <!-- Mapa e Lista de Coletas -->
            <div class="collections-container">
                <div class="collections-list">
                    <h3>Coletas de Hoje</h3>
                    <div class="collection-items">
                        <!-- Item de Coleta -->
                        <div class="collection-item">
                            <div class="time-location">
                                <span class="time">14:30</span>
                                <span class="location">Rua das Flores, 123</span>
                            </div>
                            <div class="details">
                                <span class="quantity">5L</span>
                                <span class="status pending">Pendente</span>
                                <button class="view-map-btn" title="Ver no mapa">
                                    <i class="ri-map-pin-line"></i>
                                </button>
                            </div>
                        </div>

                        <div class="collection-item">
                            <div class="time-location">
                                <span class="time">16:00</span>
                                <span class="location">Av. das Palmeiras, 789</span>
                            </div>
                            <div class="details">
                                <span class="quantity">3L</span>
                                <span class="status pending">Pendente</span>
                                <button class="view-map-btn" title="Ver no mapa">
                                    <i class="ri-map-pin-line"></i>
                                </button>
                            </div>
                        </div>

                        <div class="collection-item">
                            <div class="time-location">
                                <span class="time">17:30</span>
                                <span class="location">Rua dos Ipês, 456</span>
                            </div>
                            <div class="details">
                                <span class="quantity">7L</span>
                                <span class="status pending">Pendente</span>
                                <button class="view-map-btn" title="Ver no mapa">
                                    <i class="ri-map-pin-line"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="map-container">
                    <div id="map">
                        <!-- Aqui será carregado o mapa via JavaScript -->
                    </div>
                </div>
            </div>

            <!-- Próximos Dias -->
            <div class="upcoming-collections">
                <h3>Próximos Dias</h3>
                <div class="calendar-view">
                    <!-- Componente de calendário será implementado aqui -->
                </div>
            </div>
        </main>
    </div>

    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAe884hZ7UbSCJDuS4hkEWrR-ls0XVBe_U"></script>
    <script src="../JS/home-coletor.js"></script>
</body>

</html>