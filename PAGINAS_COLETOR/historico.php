<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico - Coletor</title>
    <link rel="icon" href="../img/logo.png" type="image/png">
    <link rel="stylesheet" href="../CSS/historico.css">
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
                    <li>
                        <a href="home.php" class="nav-link">
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
                    <li class="active">
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
                    <h1>Página de Histórico</h1>
                    <p>Confira seu histórico de coletas</p>
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

            <!-- Lista de Histórico -->
            <div class="history-list">
                <!-- Item de Histórico -->
                <div class="history-item">
                    <div class="history-item-header">
                        <div class="history-main-info">
                            <span class="collection-id">ID: #12345</span>
                            <span class="collection-quantity">5 litros</span>
                            <span class="collection-date">28/10/2025</span>
                        </div>
                        <div class="history-actions">
                            <span class="collection-status status-concluida">Concluída</span>
                            <button class="expand-button">
                                Mais detalhes
                                <i class="ri-arrow-down-s-line"></i>
                            </button>
                        </div>
                    </div>
                    <div class="history-details">
                        <div class="detail-row">
                            <span class="detail-label">Solicitante:</span>
                            <span class="detail-value">João da Silva</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Endereço:</span>
                            <span class="detail-value">Rua das Flores, 123 - Jardim Primavera</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Data da solicitação:</span>
                            <span class="detail-value">25/10/2025</span>
                        </div>
                    </div>
                </div>

                <!-- Item de Histórico -->
                <div class="history-item">
                    <div class="history-item-header">
                        <div class="history-main-info">
                            <span class="collection-id">ID: #12344</span>
                            <span class="collection-quantity">3 litros</span>
                            <span class="collection-date">27/10/2025</span>
                        </div>
                        <div class="history-actions">
                            <span class="collection-status status-cancelada">Cancelada</span>
                            <button class="expand-button">
                                Mais detalhes
                                <i class="ri-arrow-down-s-line"></i>
                            </button>
                        </div>
                    </div>
                    <div class="history-details">
                        <div class="detail-row">
                            <span class="detail-label">Solicitante:</span>
                            <span class="detail-value">Maria Santos</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Endereço:</span>
                            <span class="detail-value">Av. das Palmeiras, 456 - Centro</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Data da solicitação:</span>
                            <span class="detail-value">24/10/2025</span>
                        </div>
                    </div>
                </div>

                <!-- Item de Histórico -->
                <div class="history-item">
                    <div class="history-item-header">
                        <div class="history-main-info">
                            <span class="collection-id">ID: #12343</span>
                            <span class="collection-quantity">7 litros</span>
                            <span class="collection-date">26/10/2025</span>
                        </div>
                        <div class="history-actions">
                            <span class="collection-status status-concluida">Concluída</span>
                            <button class="expand-button">
                                Mais detalhes
                                <i class="ri-arrow-down-s-line"></i>
                            </button>
                        </div>
                    </div>
                    <div class="history-details">
                        <div class="detail-row">
                            <span class="detail-label">Solicitante:</span>
                            <span class="detail-value">Pedro Oliveira</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Endereço:</span>
                            <span class="detail-value">Rua dos Ipês, 789 - Jardim América</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Data da solicitação:</span>
                            <span class="detail-value">23/10/2025</span>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Gerenciar expansão dos itens do histórico
            const historyItems = document.querySelectorAll('.history-item');

            historyItems.forEach(item => {
                const expandButton = item.querySelector('.expand-button');

                expandButton.addEventListener('click', () => {
                    item.classList.toggle('expanded');
                });
            });

            // Gerenciar notificações (reaproveitado do home-coletor.js)
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
        });
    </script>
</body>

</html>
</body>

</html>