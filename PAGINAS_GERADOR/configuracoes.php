<?php
session_start();
require_once '../BANCO/conexao.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../index.php');
    exit;
}

$id_gerador = $_SESSION['id_usuario'];
$mensagem = '';
$tipo_mensagem = '';

// Processar salvamento de configurações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Atualizar dados do gerador
        if (!empty($_POST['email']) || !empty($_POST['telefone'])) {
            $email = $_POST['email'] ?? '';
            $telefone = $_POST['telefone'] ?? '';
            
            $sql_update = "UPDATE geradores SET email = :email, telefone = :telefone WHERE id = :id";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bindParam(':email', $email);
            $stmt_update->bindParam(':telefone', $telefone);
            $stmt_update->bindParam(':id', $id_gerador, PDO::PARAM_INT);
            $stmt_update->execute();
        }

        // Atualizar endereço
        if (!empty($_POST['cep']) && $gerador['id_endereco']) {
            $sql_endereco = "UPDATE enderecos SET 
                            cep = :cep, 
                            rua = :rua, 
                            numero = :numero, 
                            complemento = :complemento, 
                            bairro = :bairro, 
                            cidade = :cidade 
                            WHERE id = :id";
            $stmt_endereco = $conn->prepare($sql_endereco);
            $stmt_endereco->bindParam(':cep', $_POST['cep']);
            $stmt_endereco->bindParam(':rua', $_POST['rua']);
            $stmt_endereco->bindParam(':numero', $_POST['numero']);
            $stmt_endereco->bindParam(':complemento', $_POST['complemento']);
            $stmt_endereco->bindParam(':bairro', $_POST['bairro']);
            $stmt_endereco->bindParam(':cidade', $_POST['cidade']);
            $stmt_endereco->bindParam(':id', $gerador['id_endereco'], PDO::PARAM_INT);
            $stmt_endereco->execute();
        }

        // Atualizar configurações de notificação
        $notif_email = isset($_POST['notif_email']) ? 1 : 0;
        $notif_push = isset($_POST['notif_push']) ? 1 : 0;

        // Verificar se já existe configuração
        $sql_check = "SELECT id FROM configuracoes_usuario WHERE id_gerador = :id";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bindParam(':id', $id_gerador, PDO::PARAM_INT);
        $stmt_check->execute();
        $existe_config = $stmt_check->fetch();

        if ($existe_config) {
            // Atualizar
            $sql_config_update = "UPDATE configuracoes_usuario SET 
                                 notificacoes_email = :email, 
                                 notificacoes_push = :push 
                                 WHERE id_gerador = :id";
            $stmt_config = $conn->prepare($sql_config_update);
        } else {
            // Inserir
            $sql_config_update = "INSERT INTO configuracoes_usuario 
                                 (id_gerador, notificacoes_email, notificacoes_push) 
                                 VALUES (:id, :email, :push)";
            $stmt_config = $conn->prepare($sql_config_update);
        }
        
        $stmt_config->bindParam(':id', $id_gerador, PDO::PARAM_INT);
        $stmt_config->bindParam(':email', $notif_email, PDO::PARAM_INT);
        $stmt_config->bindParam(':push', $notif_push, PDO::PARAM_INT);
        $stmt_config->execute();

        $mensagem = 'Configurações salvas com sucesso!';
        $tipo_mensagem = 'sucesso';
    } catch (Exception $e) {
        $mensagem = 'Erro ao salvar: ' . $e->getMessage();
        $tipo_mensagem = 'erro';
    }
}

// Buscar dados do gerador
$sql = "SELECT g.id, g.email, g.nome_completo, g.cpf, g.telefone, g.data_nasc, g.foto_perfil, g.id_endereco,
                e.rua, e.numero, e.complemento, e.bairro, e.cidade, e.estado, e.cep
        FROM geradores g
        LEFT JOIN enderecos e ON g.id_endereco = e.id
        WHERE g.id = :id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':id', $id_gerador, PDO::PARAM_INT);
$stmt->execute();
$gerador = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$gerador) {
    header('Location: ../index.php');
    exit;
}

// Buscar configurações de notificação
$sql_config = "SELECT notificacoes_email, notificacoes_push FROM configuracoes_usuario WHERE id_gerador = :id";
$stmt_config = $conn->prepare($sql_config);
$stmt_config->bindParam(':id', $id_gerador, PDO::PARAM_INT);
$stmt_config->execute();
$config = $stmt_config->fetch(PDO::FETCH_ASSOC);

// Se não existir configuração, criar padrão
if (!$config) {
    $config = ['notificacoes_email' => 1, 'notificacoes_push' => 1];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações - Gerador</title>
    <link rel="icon" href="../img/logo.png" type="image/png">
    <link rel="stylesheet" href="../CSS/gerador-configuracoes.css">
    <link rel="stylesheet" href="../CSS/navbar.css">
    <link rel="stylesheet" href="../CSS/libras.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Biblioteca de ícones -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <!-- Barra Lateral -->
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
                <!-- Mensagem de Feedback -->
                <?php if ($mensagem): ?>
                <div class="feedback-message <?php echo $tipo_mensagem; ?>">
                    <i class="ri-<?php echo $tipo_mensagem === 'sucesso' ? 'check-line' : 'alert-line'; ?>"></i>
                    <span><?php echo $mensagem; ?></span>
                </div>
                <?php endif; ?>

                <!-- Informações da Conta -->
                <div class="settings-section">
                    <h2>Informações da Conta</h2>
                    <form class="settings-form" method="POST" action="">
                        <div class="form-group">
                            <label for="email">E-mail</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($gerador['email'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="phone">Telefone</label>
                            <input type="tel" id="phone" name="telefone" value="<?php echo htmlspecialchars($gerador['telefone'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="new-password">Nova Senha</label>
                            <input type="password" id="new-password" name="new-password">
                        </div>
                        <div class="form-group">
                            <label for="confirm-password">Confirmar Nova Senha</label>
                            <input type="password" id="confirm-password" name="confirm-password">
                        </div>
                    </form>
                </div>

                <!-- Endereço de Coleta Padrão -->
                <div class="settings-section">
                    <h2>Endereço de Coleta Padrão</h2>
                    <form class="settings-form" method="POST" action="">
                        <div class="form-group">
                            <label for="cep">CEP</label>
                            <input type="text" id="cep" name="cep" placeholder="00000-000" value="<?php echo htmlspecialchars($gerador['cep'] ?? ''); ?>">
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="rua">Rua</label>
                                <input type="text" id="rua" name="rua" placeholder="Nome da rua" value="<?php echo htmlspecialchars($gerador['rua'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label for="numero">Número</label>
                                <input type="text" id="numero" name="numero" placeholder="Nº" value="<?php echo htmlspecialchars($gerador['numero'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="complemento">Complemento</label>
                            <input type="text" id="complemento" name="complemento" placeholder="Apto, bloco, etc (opcional)" value="<?php echo htmlspecialchars($gerador['complemento'] ?? ''); ?>">
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="bairro">Bairro</label>
                                <input type="text" id="bairro" name="bairro" placeholder="Bairro" value="<?php echo htmlspecialchars($gerador['bairro'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label for="cidade">Cidade</label>
                                <input type="text" id="cidade" name="cidade" placeholder="Cidade" value="<?php echo htmlspecialchars($gerador['cidade'] ?? ''); ?>">
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Preferências de Notificação -->
                <div class="settings-section">
                    <h2>Preferências de Notificação</h2>
                    <form method="POST" action="">
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
                                    <input type="checkbox" id="notif_email" name="notif_email" <?php echo (!empty($config) && $config['notificacoes_email']) ? 'checked' : ''; ?>>
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
                                    <input type="checkbox" id="notif_push" name="notif_push" <?php echo (!empty($config) && $config['notificacoes_push']) ? 'checked' : ''; ?>>
                                    <span class="slider"></span>
                                </label>
                            </div>
                        </div>
                    </form>
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
                    <form method="POST" action="" style="display: contents;">
                        <button type="submit" class="save-btn">Salvar Alterações</button>
                    </form>
                    <button class="cancel-btn" onclick="location.reload()">Cancelar</button>
                    <button class="logout-btn" onclick="window.location.href='../logout.php'"><i class="ri-logout-box-line"></i> Sair</button>
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
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-hide feedback message after 5 seconds
            const feedbackMessage = document.querySelector('.feedback-message');
            if (feedbackMessage) {
                setTimeout(() => {
                    feedbackMessage.style.opacity = '0';
                    feedbackMessage.style.transition = 'opacity 0.3s ease-out';
                    setTimeout(() => feedbackMessage.remove(), 300);
                }, 5000);
            }

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
    <script src="../JS/navbar.js"></script>
    <script src="../JS/libras.js"></script>
</body>

</html>