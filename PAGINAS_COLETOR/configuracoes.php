<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações - Coletor</title>
    <link rel="icon" href="../img/logo.png" type="image/png">
    <link rel="stylesheet" href="../CSS/coletor-configuracoes.css">
    <link rel="stylesheet" href="../CSS/navbar.css">
    <link rel="stylesheet" href="../CSS/libras.css">
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
                    <li>
                        <a href="historico.php" class="nav-link">
                            <i class="ri-history-line"></i>
                            <span>Histórico</span>
                        </a>
                    </li>
                    <li class="active">
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
                    <h1>Página de Configurações</h1>
                    <p>Gerencie suas preferências e configurações aqui</p>
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
            <div class="settings-content">
                <!-- Status do Usuário -->
                <div class="settings-section">
                    <h2>Status de Disponibilidade</h2>
                    <div class="status-toggle">
                        <div class="status-option active">
                            <i class="ri-check-line"></i>
                            Disponível
                        </div>
                        <div class="status-option">
                            <i class="ri-time-line"></i>
                            Indisponível
                        </div>
                        <div class="status-option">
                            <i class="ri-close-circle-line"></i>
                            Offline
                        </div>
                    </div>
                </div>

                <!-- Raio de Atuação -->
                <div class="settings-section">
                    <h2>Raio de Atuação</h2>
                    <div class="range-slider">
                        <input type="range" min="1" max="50" value="10" id="radius-slider">
                        <div class="range-value">
                            Raio atual: <span id="radius-value">10</span> km
                        </div>
                    </div>
                </div>

                <!-- Informações da Conta -->
                <div class="settings-section">
                    <h2>Informações da Conta</h2>
                    <form class="settings-form">
                        <div class="form-group">
                            <label for="email">E-mail</label>
                            <input type="email" id="email" value="<?php echo isset($_SESSION['email']) ? $_SESSION['email'] : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label for="phone">Telefone</label>
                            <input type="tel" id="phone" value="<?php echo isset($_SESSION['telefone']) ? $_SESSION['telefone'] : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label for="current-password">Senha Atual</label>
                            <input type="password" id="current-password">
                        </div>
                        <div class="form-group">
                            <label for="new-password">Nova Senha</label>
                            <input type="password" id="new-password">
                        </div>
                        <div class="form-group">
                            <label for="confirm-password">Confirmar Nova Senha</label>
                            <input type="password" id="confirm-password">
                        </div>
                    </form>
                </div>

                <!-- Horários de Funcionamento -->
                <div class="settings-section">
                    <h2>Horários de Funcionamento</h2>
                    <div class="schedule-container">
                        <div class="day-schedule">
                            <input type="checkbox" class="day-toggle" id="monday" checked>
                            <span class="day-name">Segunda-feira</span>
                            <div class="time-inputs">
                                <input type="time" value="08:00">
                                <span>até</span>
                                <input type="time" value="18:00">
                            </div>
                        </div>
                        <div class="day-schedule">
                            <input type="checkbox" class="day-toggle" id="tuesday" checked>
                            <span class="day-name">Terça-feira</span>
                            <div class="time-inputs">
                                <input type="time" value="08:00">
                                <span>até</span>
                                <input type="time" value="18:00">
                            </div>
                        </div>
                        <div class="day-schedule">
                            <input type="checkbox" class="day-toggle" id="wednesday" checked>
                            <span class="day-name">Quarta-feira</span>
                            <div class="time-inputs">
                                <input type="time" value="08:00">
                                <span>até</span>
                                <input type="time" value="18:00">
                            </div>
                        </div>
                        <div class="day-schedule">
                            <input type="checkbox" class="day-toggle" id="thursday" checked>
                            <span class="day-name">Quinta-feira</span>
                            <div class="time-inputs">
                                <input type="time" value="08:00">
                                <span>até</span>
                                <input type="time" value="18:00">
                            </div>
                        </div>
                        <div class="day-schedule">
                            <input type="checkbox" class="day-toggle" id="friday" checked>
                            <span class="day-name">Sexta-feira</span>
                            <div class="time-inputs">
                                <input type="time" value="08:00">
                                <span>até</span>
                                <input type="time" value="18:00">
                            </div>
                        </div>
                        <div class="day-schedule">
                            <input type="checkbox" class="day-toggle" id="saturday">
                            <span class="day-name">Sábado</span>
                            <div class="time-inputs">
                                <input type="time" value="08:00">
                                <span>até</span>
                                <input type="time" value="12:00">
                            </div>
                        </div>
                        <div class="day-schedule">
                            <input type="checkbox" class="day-toggle" id="sunday">
                            <span class="day-name">Domingo</span>
                            <div class="time-inputs">
                                <input type="time" value="08:00">
                                <span>até</span>
                                <input type="time" value="12:00">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botões de Ação -->
                <div class="action-buttons">
                    <button class="save-btn">Salvar Alterações</button>
                    <button class="cancel-btn">Cancelar</button>
                    <button class="logout-btn" href="../logout.php"><i class="ri-logout-box-line"></i> Sair</button>
                </div>
            </div>
        </main>
    </div>
    <div class="right">
      <div class="accessibility-button" onclick="toggleAccessibility(event)" title="Ferramentas de Acessibilidade">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="25" height="25" fill="white">
          <title>accessibility</title>
          <g>
            <circle cx="24" cy="7" r="4" />
            <path d="M40,13H8a2,2,0,0,0,0,4H19.9V27L15.1,42.4a2,2,0,0,0,1.3,2.5H17a2,2,0,0,0,1.9-1.4L23.8,28h.4l4.9,15.6A2,2,0,0,0,31,45h.6a2,2,0,0,0,1.3-2.5L28.1,27V17H40a2,2,0,0,0,0-4Z" />
          </g>
        </svg>
      </div>
<div vw class="enabled">
        <div vw-access-button class="active"></div>
        <div vw-plugin-wrapper>
            <div class="vw-plugin-top-wrapper"></div>
        </div>
    </div>


    <script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
    <script>
        document.querySelector('.logout-btn').addEventListener('click', function() {
            window.location.href = '../logout.php';
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Status toggle
            const statusOptions = document.querySelectorAll('.status-option');
            statusOptions.forEach(option => {
                option.addEventListener('click', () => {
                    statusOptions.forEach(opt => opt.classList.remove('active'));
                    option.classList.add('active');
                });
            });

            // Radius slider
            const radiusSlider = document.getElementById('radius-slider');
            const radiusValue = document.getElementById('radius-value');
            radiusSlider.addEventListener('input', () => {
                radiusValue.textContent = radiusSlider.value;
            });

            // Day schedule toggles
            const dayToggles = document.querySelectorAll('.day-toggle');
            dayToggles.forEach(toggle => {
                toggle.addEventListener('change', (e) => {
                    const timeInputs = e.target.parentElement.querySelector('.time-inputs');
                    timeInputs.style.opacity = e.target.checked ? '1' : '0.5';
                    const inputs = timeInputs.querySelectorAll('input');
                    inputs.forEach(input => input.disabled = !e.target.checked);
                });
            });

            // Notificações
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
    <script src="../JS/navbar.js"></script>
    <script src="../JS/libras.js"></script>
</body>

</html>