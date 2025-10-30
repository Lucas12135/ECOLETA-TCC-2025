<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendamentos - Coletor</title>
    <link rel="icon" href="../img/logo.png" type="image/png">
    <link rel="stylesheet" href="../CSS/agendamentos.css">
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
                    <li class="active">
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
                    <h1>Página de agendamentos</h1>
                    <p>Gerencie suas solicitações e agendamentos de coleta</p>
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
            <div class="agendamento-content">
                <!-- Seção de Solicitações Pendentes -->
                <div class="section-agendamentos">
                    <div class="section-header">
                        <h2>
                            <i class="ri-time-line"></i>
                            Solicitações Pendentes
                        </h2>
                    </div>
                    <div class="agendamento-list">
                        <div class="agendamento-item">
                            <div class="agendamento-header">
                                <div class="agendamento-info">
                                    <span class="agendamento-id">#12345</span>
                                    <span class="agendamento-quantidade">
                                        <i class="ri-oil-line"></i>
                                        5 litros
                                    </span>
                                    <span class="agendamento-data">Solicitado em: 28/10/2025</span>
                                    <span class="agendamento-solicitante">João da Silva</span>
                                </div>
                                <div class="agendamento-actions">
                                    <button class="btn-aceitar">
                                        <i class="ri-check-line"></i>
                                        Aceitar
                                    </button>
                                    <button class="btn-recusar">
                                        <i class="ri-close-line"></i>
                                        Recusar
                                    </button>
                                    <button class="btn-ver-mapa" title="Ver no Mapa">
                                        <i class="ri-map-pin-line"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="agendamento-details">
                                <div class="details-grid">
                                    <div class="detail-group">
                                        <span class="detail-label">Endereço</span>
                                        <span class="detail-value">Rua das Flores, 123 - Jardim Primavera</span>
                                    </div>
                                    <div class="detail-group">
                                        <span class="detail-label">Telefone</span>
                                        <span class="detail-value">(11) 98765-4321</span>
                                    </div>
                                    <div class="map-container" id="map-12345">
                                        <!-- Mapa será carregado aqui -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Mais itens pendentes aqui -->
                    </div>
                </div>

                <!-- Seção de Agendamentos Aceitos -->
                <div class="section-agendamentos">
                    <div class="section-header">
                        <h2>
                            <i class="ri-calendar-check-line"></i>
                            Agendamentos Aceitos
                        </h2>
                    </div>
                    <div class="agendamento-list">
                        <div class="agendamento-item">
                            <div class="agendamento-header">
                                <div class="agendamento-info">
                                    <span class="agendamento-id">#12344</span>
                                    <span class="agendamento-quantidade">
                                        <i class="ri-oil-line"></i>
                                        3 litros
                                    </span>
                                    <span class="agendamento-data">
                                        Solicitado em: 27/10/2025<br>
                                        Aceito em: 28/10/2025
                                    </span>
                                    <span class="agendamento-solicitante">Maria Santos</span>
                                </div>
                                <div class="agendamento-actions">
                                    <button class="btn-cancelar">
                                        <i class="ri-close-line"></i>
                                        Cancelar
                                    </button>
                                    <button class="btn-ver-mapa" title="Ver no Mapa">
                                        <i class="ri-map-pin-line"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="agendamento-details">
                                <div class="details-grid">
                                    <div class="detail-group">
                                        <span class="detail-label">Endereço</span>
                                        <span class="detail-value">Av. das Palmeiras, 456 - Centro</span>
                                    </div>
                                    <div class="detail-group">
                                        <span class="detail-label">Telefone</span>
                                        <span class="detail-value">(11) 99876-5432</span>
                                    </div>
                                    <div class="map-container" id="map-12344">
                                        <!-- Mapa será carregado aqui -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Mais itens aceitos aqui -->
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAe884hZ7UbSCJDuS4hkEWrR-ls0XVBe_U"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Gerenciar expansão dos itens de agendamento
            const agendamentoItems = document.querySelectorAll('.agendamento-item');
            const maps = {};

            agendamentoItems.forEach(item => {
                const header = item.querySelector('.agendamento-header');
                const mapContainer = item.querySelector('.map-container');
                const mapId = mapContainer?.id;

                header.addEventListener('click', (e) => {
                    if (!e.target.closest('button')) {
                        item.classList.toggle('expanded');

                        if (mapId && !maps[mapId] && item.classList.contains('expanded')) {
                            // Inicializar mapa quando expandido pela primeira vez
                            const map = new google.maps.Map(mapContainer, {
                                center: {
                                    lat: -23.550520,
                                    lng: -46.633308
                                }, // Coordenadas exemplo
                                zoom: 15
                            });

                            const marker = new google.maps.Marker({
                                position: {
                                    lat: -23.550520,
                                    lng: -46.633308
                                },
                                map: map,
                                title: 'Local da Coleta'
                            });

                            maps[mapId] = map;
                        }
                    }
                });
            });

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

            // Gerenciar botões de ação
            document.querySelectorAll('.btn-aceitar').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const item = e.target.closest('.agendamento-item');
                    // Aqui você pode adicionar a lógica para aceitar o agendamento
                    alert('Agendamento aceito!');
                });
            });

            document.querySelectorAll('.btn-cancelar').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const item = e.target.closest('.agendamento-item');
                    // Aqui você pode adicionar a lógica para cancelar o agendamento
                    if (confirm('Tem certeza que deseja cancelar este agendamento?')) {
                        alert('Agendamento cancelado!');
                    }
                });
            });

            document.querySelectorAll('.btn-ver-mapa').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const item = e.target.closest('.agendamento-item');
                    item.classList.add('expanded');
                    // Forçar a expansão do item para mostrar o mapa
                });
            });
        });
    </script>
</body>

</html>