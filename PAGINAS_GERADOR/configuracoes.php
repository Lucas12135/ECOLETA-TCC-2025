<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações - Gerador</title>
    <link rel="icon" href="../img/logo.png" type="image/png">
    <link rel="stylesheet" href="../CSS/gerador-configuracoes.css">
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
        </aside>

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
                                        <i class="ri-check-double-line notification-icon"></i>
                                        <div class="notification-text">
                                            <p>Sua coleta foi confirmada para amanhã às 10:00</p>
                                            <span class="notification-time">Há 15 minutos</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="notification-item">
                                    <div class="notification-content">
                                        <i class="ri-truck-line notification-icon"></i>
                                        <div class="notification-text">
                                            <p>Coletor a caminho do seu endereço</p>
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

                <!-- Endereço de Coleta Padrão -->
                <div class="settings-section">
                    <h2>Endereço de Coleta Padrão</h2>
                    <form class="settings-form">
                        <div class="form-group">
                            <label for="cep">CEP</label>
                            <input type="text" id="cep" placeholder="00000-000">
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="rua">Rua</label>
                                <input type="text" id="rua" placeholder="Nome da rua">
                            </div>
                            <div class="form-group">
                                <label for="numero">Número</label>
                                <input type="text" id="numero" placeholder="Nº">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="complemento">Complemento</label>
                            <input type="text" id="complemento" placeholder="Apto, bloco, etc (opcional)">
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="bairro">Bairro</label>
                                <input type="text" id="bairro" placeholder="Bairro">
                            </div>
                            <div class="form-group">
                                <label for="cidade">Cidade</label>
                                <input type="text" id="cidade" placeholder="Cidade">
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Preferências de Notificação -->
                <div class="settings-section">
                    <h2>Preferências de Notificação</h2>
                    <div class="notification-preferences">
                        <div class="preference-item">
                            <div class="preference-info">
                                <i class="ri-mail-line"></i>
                                <div>
                                    <h3>Notificações por E-mail</h3>
                                    <p>Receber atualizações sobre coletas por e-mail</p>
                                </div>
                            </div>
                            <label class="switch">
                                <input type="checkbox" checked>
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div class="preference-item">
                            <div class="preference-info">
                                <i class="ri-smartphone-line"></i>
                                <div>
                                    <h3>Notificações Push</h3>
                                    <p>Receber notificações no navegador</p>
                                </div>
                            </div>
                            <label class="switch">
                                <input type="checkbox" checked>
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div class="preference-item">
                            <div class="preference-info">
                                <i class="ri-message-3-line"></i>
                                <div>
                                    <h3>SMS</h3>
                                    <p>Receber mensagens de texto sobre coletas</p>
                                </div>
                            </div>
                            <label class="switch">
                                <input type="checkbox">
                                <span class="slider"></span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Horários Preferenciais -->
                <div class="settings-section">
                    <h2>Horários Preferenciais para Coleta</h2>
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

                <!-- Quantidade Média de Óleo -->
                <div class="settings-section">
                    <h2>Quantidade Média de Óleo por Coleta</h2>
                    <div class="oil-quantity">
                        <div class="quantity-options">
                            <div class="quantity-option active">
                                <i class="ri-drop-line"></i>
                                <span>Até 2L</span>
                            </div>
                            <div class="quantity-option">
                                <i class="ri-contrast-drop-line"></i>
                                <span>2L - 5L</span>
                            </div>
                            <div class="quantity-option">
                                <i class="ri-contrast-drop-2-line"></i>
                                <span>5L - 10L</span>
                            </div>
                            <div class="quantity-option">
                                <i class="ri-drop-fill"></i>
                                <span>Mais de 10L</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botões de Ação -->
                <div class="action-buttons">
                    <button class="save-btn">Salvar Alterações</button>
                    <button class="cancel-btn">Cancelar</button>
                    <button class="logout-btn"><i class="ri-logout-box-line"></i> Sair</button>
                </div>
            </div>
        </main>
    </div>

    <script>
        document.querySelector('.logout-btn').addEventListener('click', function() {
            window.location.href = '../logout.php';
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Quantity options
            const quantityOptions = document.querySelectorAll('.quantity-option');
            quantityOptions.forEach(option => {
                option.addEventListener('click', () => {
                    quantityOptions.forEach(opt => opt.classList.remove('active'));
                    option.classList.add('active');
                });
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

            // CEP Auto-complete (exemplo básico)
            const cepInput = document.getElementById('cep');
            cepInput.addEventListener('blur', function() {
                const cep = this.value.replace(/\D/g, '');
                if (cep.length === 8) {
                    // Aqui você pode adicionar integração com API de CEP
                    console.log('Buscar endereço para CEP:', cep);
                }
            });
        });
    </script>
</body>

</html>