<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações - Ecoleta</title>
    <link rel="icon" href="../img/logo.png" type="image/png">
    <link rel="stylesheet" href="../CSS/coletor-configuracoes.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
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
                        <a href="./solicitar_coleta.php" class="nav-link">
                            <i class="ri-oil-line"></i>
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
                        <a href="#" class="nav-link">
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
                <h1>Configurações</h1>
                <p>Gerencie suas preferências e configurações da conta</p>
            </header>

            <div class="settings-container">
                <!-- Configurações de Conta -->
                <section class="settings-section">
                    <h2>Conta</h2>
                    <div class="settings-group">
                        <div class="setting-item">
                            <div class="setting-info">
                                <h3>Alterar Senha</h3>
                                <p>Atualize sua senha para manter sua conta segura</p>
                            </div>
                            <button class="btn-action" onclick="showChangePasswordModal()">
                                Alterar
                            </button>
                        </div>

                        <div class="setting-item">
                            <div class="setting-info">
                                <h3>E-mail de Recuperação</h3>
                                <p>E-mail alternativo para recuperação de conta</p>
                            </div>
                            <button class="btn-action" onclick="showEmailModal()">
                                Configurar
                            </button>
                        </div>

                        <div class="setting-item">
                            <div class="setting-info">
                                <h3>Autenticação em Duas Etapas</h3>
                                <p>Adicione uma camada extra de segurança</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" id="twoFactorAuth">
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                    </div>
                </section>

                <!-- Configurações de Notificações -->
                <section class="settings-section">
                    <h2>Notificações</h2>
                    <div class="settings-group">
                        <div class="setting-item">
                            <div class="setting-info">
                                <h3>E-mail</h3>
                                <p>Receba atualizações sobre suas coletas por e-mail</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" id="emailNotifications" checked>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>

                        <div class="setting-item">
                            <div class="setting-info">
                                <h3>SMS</h3>
                                <p>Receba lembretes por SMS</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" id="smsNotifications">
                                <span class="toggle-slider"></span>
                            </label>
                        </div>

                        <div class="setting-item">
                            <div class="setting-info">
                                <h3>Notificações Push</h3>
                                <p>Receba notificações no navegador</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" id="pushNotifications" checked>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                    </div>
                </section>

                <!-- Preferências de Coleta -->
                <section class="settings-section">
                    <h2>Preferências de Coleta</h2>
                    <div class="settings-group">
                        <div class="setting-item">
                            <div class="setting-info">
                                <h3>Horário Preferencial</h3>
                                <p>Defina seu horário preferido para coletas</p>
                            </div>
                            <select class="setting-select" id="preferredTime">
                                <option value="morning">Manhã (8h - 12h)</option>
                                <option value="afternoon">Tarde (13h - 17h)</option>
                            </select>
                        </div>

                        <div class="setting-item">
                            <div class="setting-info">
                                <h3>Frequência de Coleta</h3>
                                <p>Configure lembretes para solicitar coletas</p>
                            </div>
                            <select class="setting-select" id="collectionFrequency">
                                <option value="weekly">Semanal</option>
                                <option value="biweekly">Quinzenal</option>
                                <option value="monthly">Mensal</option>
                                <option value="none">Sem lembrete</option>
                            </select>
                        </div>
                    </div>
                </section>

                <!-- Privacidade -->
                <section class="settings-section">
                    <h2>Privacidade</h2>
                    <div class="settings-group">
                        <div class="setting-item">
                            <div class="setting-info">
                                <h3>Compartilhar Estatísticas</h3>
                                <p>Permita que outros usuários vejam suas estatísticas de reciclagem</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" id="shareStats">
                                <span class="toggle-slider"></span>
                            </label>
                        </div>

                        <div class="setting-item danger-zone">
                            <div class="setting-info">
                                <h3>Excluir Conta</h3>
                                <p>Apague permanentemente sua conta e todos os dados</p>
                            </div>
                            <button class="btn-danger" onclick="showDeleteAccountModal()">
                                Excluir Conta
                            </button>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>

    <!-- Modal de Alteração de Senha -->
    <div class="modal" id="changePasswordModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Alterar Senha</h2>
                <button class="btn-close" onclick="closeModal('changePasswordModal')">
                    <i class="ri-close-line"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="changePasswordForm">
                    <div class="form-group">
                        <label for="currentPassword">Senha Atual</label>
                        <input type="password" id="currentPassword" required>
                    </div>
                    <div class="form-group">
                        <label for="newPassword">Nova Senha</label>
                        <input type="password" id="newPassword" required>
                    </div>
                    <div class="form-group">
                        <label for="confirmPassword">Confirmar Nova Senha</label>
                        <input type="password" id="confirmPassword" required>
                    </div>
                    <div class="modal-actions">
                        <button type="button" class="btn-secondary" onclick="closeModal('changePasswordModal')">Cancelar</button>
                        <button type="submit" class="btn-primary">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmação de Exclusão -->
    <div class="modal" id="deleteAccountModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Excluir Conta</h2>
                <button class="btn-close" onclick="closeModal('deleteAccountModal')">
                    <i class="ri-close-line"></i>
                </button>
            </div>
            <div class="modal-body">
                <p class="warning-text">Esta ação é irreversível. Todos os seus dados serão permanentemente excluídos.</p>
                <form id="deleteAccountForm">
                    <div class="form-group">
                        <label for="deleteConfirmPassword">Digite sua senha para confirmar</label>
                        <input type="password" id="deleteConfirmPassword" required>
                    </div>
                    <div class="modal-actions">
                        <button type="button" class="btn-secondary" onclick="closeModal('deleteAccountModal')">Cancelar</button>
                        <button type="submit" class="btn-danger">Excluir Permanentemente</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="../JS/configuracoes.js"></script>
</body>

</html>