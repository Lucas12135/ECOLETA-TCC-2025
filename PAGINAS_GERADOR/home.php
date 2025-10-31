<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Gerador</title>
    <link rel="icon" href="../img/logo.png" type="image/png">
    <link rel="stylesheet" href="../CSS/home-gerador.css">
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
                    <li class="nav-link">
                        <a href="../index.php" class="nav-link">
                            <i class="ri-arrow-left-line"></i>
                            <span>Voltar</span>
                        </a>
                    </li>
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
                        <a href="solicitar_coleta.php" class="nav-link">
                            <i class="ri-add-circle-line"></i>
                            <span>Solicitar Coleta</span>
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
                    <h1>Olá, <?php echo isset($_SESSION['nome']) ? $_SESSION['nome'] : 'Gerador'; ?>!</h1>
                    <p>Gerencie suas solicitações de coleta de óleo</p>
                </div>
                <div class="header-actions">
                    <div class="action-buttons">
                        <button class="notification-btn" title="Notificações">
                            <i class="ri-notification-3-line"></i>
                            <span class="notification-badge">2</span>
                        </button>
                        <!-- Popup de Notificações -->
                        <div class="notifications-popup">
                            <div class="notifications-header">
                                <h3>Notificações</h3>
                            </div>
                            <div class="notification-list">
                                <div class="notification-item">
                                    <div class="notification-content">
                                        <i class="ri-check-line notification-icon"></i>
                                        <div class="notification-text">
                                            <p>Sua solicitação de coleta foi aceita!</p>
                                            <span class="notification-time">Há 1 hora</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="notification-item">
                                    <div class="notification-content">
                                        <i class="ri-time-line notification-icon"></i>
                                        <div class="notification-text">
                                            <p>Coleta agendada para amanhã às 14:00</p>
                                            <span class="notification-time">Há 2 horas</span>
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

                <div class="card oil-stats">
                    <h3>Estatísticas de Reciclagem</h3>
                    <div class="card-content">
                        <div class="stat-item">
                            <span class="number">25L</span>
                            <span class="label">Óleo Reciclado</span>
                        </div>
                        <div class="eco-impact">
                            <span class="impact-text">Você ajudou a evitar a poluição de aproximadamente 500.000L de água!</span>
                        </div>
                    </div>
                </div>

                <div class="card quick-actions">
                    <h3>Ações Rápidas</h3>
                    <div class="card-content">
                        <a href="solicitar_coleta.php">
                        <button class="action-btn request-collection">
                            <i class="ri-oil-line"></i>
                            Nova Solicitação
                        </button>
                        </a>
                        <a href="historico.php">
                        <button class="action-btn view-history">
                            <i class="ri-history-line"></i>
                            Ver Histórico
                        </button>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Status da Coleta Atual -->
            <div class="collection-status-container">
                <div class="status-tracking">
                    <h3>Status da Coleta Atual</h3>
                    <div class="status-timeline">
                        <div class="status-step completed">
                            <div class="step-icon">
                                <i class="ri-check-line"></i>
                            </div>
                            <div class="step-info">
                                <h4>Solicitação Enviada</h4>
                                <span>27/10 - 10:30</span>
                            </div>
                        </div>
                        <div class="status-step completed">
                            <div class="step-icon">
                                <i class="ri-check-line"></i>
                            </div>
                            <div class="step-info">
                                <h4>Coleta Aceita</h4>
                                <span>27/10 - 11:15</span>
                            </div>
                        </div>
                        <div class="status-step active">
                            <div class="step-icon">
                                <i class="ri-time-line"></i>
                            </div>
                            <div class="step-info">
                                <h4>Aguardando Coleta</h4>
                                <span>Agendada para 29/10</span>
                            </div>
                        </div>
                        <div class="status-step">
                            <div class="step-icon">
                                <i class="ri-checkbox-blank-circle-line"></i>
                            </div>
                            <div class="step-info">
                                <h4>Coleta Concluída</h4>
                                <span>Pendente</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="collection-details">
                    <div class="details-card">
                        <h4>Detalhes da Coleta</h4>
                        <div class="detail-item">
                            <i class="ri-oil-line"></i>
                            <span>Volume: 5L</span>
                        </div>
                        <div class="detail-item">
                            <i class="ri-map-pin-line"></i>
                            <span>Local: Rua das Flores, 123</span>
                        </div>
                        <div class="detail-item">
                            <i class="ri-user-line"></i>
                            <span>Coletor: João Silva</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Histórico Recente -->
            <div class="recent-history">
                <h3>Histórico Recente</h3>
                <div class="history-items">
                    <div class="history-item">
                        <div class="history-content">
                            <div class="history-info">
                                <span class="date">15/10/2025</span>
                                <span class="volume">3L</span>
                            </div>
                            <span class="status completed">Concluída</span>
                        </div>
                    </div>
                    <div class="history-item">
                        <div class="history-content">
                            <div class="history-info">
                                <span class="date">01/10/2025</span>
                                <span class="volume">4L</span>
                            </div>
                            <span class="status completed">Concluída</span>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="../JS/home-gerador.js"></script>
    <script src="../JS/navbar.js"></script>
</body>

</html>