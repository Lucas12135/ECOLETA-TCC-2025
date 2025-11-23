<?php
session_start();
require_once '../BANCO/conexao.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../index.php');
    exit;
}

// Buscar dados do coletor
$id_coletor = $_SESSION['id_usuario'];
$sql = "SELECT 
    c.email, c.cpf_cnpj, c.telefone, 
    cc.raio_atuacao, cc.meio_transporte, cc.disponibilidade,
    e.rua, e.numero, e.complemento, e.bairro, e.cidade, e.estado, e.cep
FROM coletores c
LEFT JOIN enderecos e ON c.id_endereco = e.id
LEFT JOIN coletores_config cc ON cc.id_coletor = c.id
WHERE c.id = :id;
";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':id', $id_coletor, PDO::PARAM_INT);
$stmt->execute();
$coletor = $stmt->fetch(PDO::FETCH_ASSOC);

// Buscar disponibilidade
$sql_disp = "SELECT * FROM horarios_funcionamento WHERE id_coletor = :id";
$stmt_disp = $conn->prepare($sql_disp);
$stmt_disp->bindParam(':id', $id_coletor, PDO::PARAM_INT);
$stmt_disp->execute();
$horarios = $stmt_disp->fetchAll(PDO::FETCH_ASSOC);

// Mapa de dias da semana
$dias_semana = [
    'segunda',
    'terca',
    'quarta',
    'quinta',
    'sexta',
    'sabado',
    'domingo'
];

// Função para buscar horarios de um dia
function getDisponibilidadeDia($horarios, $dia)
{
    foreach ($horarios as $d) {
        if ($d['dia_semana'] === $dia) {
            return $d;
        }
    }
    return null;
}
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
    <link rel="stylesheet" href="../CSS/acessibilidade.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Biblioteca de ícones -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #f5f5f5;
            margin: 10% auto;
            padding: 30px;
            border-radius: 10px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            position: relative;
        }

        .modal-content h2 {
            margin-top: 0;
            margin-bottom: 20px;
            color: #333;
            font-size: 1.5rem;
        }

        .close-modal-btn {
            position: absolute;
            right: 15px;
            top: 15px;
            font-size: 28px;
            font-weight: bold;
            background: none;
            border: none;
            cursor: pointer;
            color: #333;
        }

        .close-modal-btn:hover {
            color: #2ecc71;
        }

        .modal .form-group {
            margin-bottom: 20px;
        }

        .modal .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
            font-size: 0.95rem;
        }

        .modal .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            box-sizing: border-box;
        }

        .modal .form-group input:focus {
            outline: none;
            border-color: #2ecc71;
            box-shadow: 0 0 5px rgba(46, 204, 113, 0.3);
        }

        .password-hint {
            display: block;
            margin-top: 5px;
            font-size: 0.85rem;
            color: #666;
        }

        .error-message {
            color: #d32f2f;
            font-size: 0.9rem;
            margin-top: 10px;
            padding: 10px;
            background-color: #ffebee;
            border-radius: 5px;
            border-left: 3px solid #d32f2f;
        }

        .modal-buttons {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 25px;
        }

        .btn-cancel,
        .btn-save {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .btn-cancel {
            background-color: #ddd;
            color: #333;
        }

        .btn-cancel:hover {
            background-color: #ccc;
        }

        .btn-save {
            background-color: #2ecc71;
            color: white;
        }

        .btn-save:hover {
            background-color: #27ae60;
        }

        .change-password-btn {
            background-color: #2ecc71;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .change-password-btn:hover {
            background-color: #27ae60;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(46, 204, 113, 0.3);
        }

        .change-password-btn i {
            font-size: 1.1rem;
        }
    </style>


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
                </div>
            </header>
            <div class="settings-content">
                <!-- Mensagem de Feedback -->
                <div id="feedbackContainer"></div>

                <!-- Status do Usuário -->
                <div class="settings-section">
                    <h2>Disponibilidade</h2>
                    <div class="status-toggle">
                        <?php $disponibilidade = $coletor['disponibilidade'] ?? 'disponivel'; ?>
                        <div class="status-option <?= $disponibilidade === 'disponivel' ? 'active' : '' ?>">
                            <i class="ri-check-line"></i>
                            Disponível
                        </div>
                        <div class="status-option <?= $disponibilidade === 'indisponivel' ? 'active' : '' ?>">
                            <i class="ri-time-line"></i>
                            Indisponível
                        </div>
                    </div>
                </div>

                <!-- Raio de Atuação -->
                <div class="settings-section">
                    <h2>Raio de Atuação</h2>
                    <div class="range-slider">
                        <input type="range" min="1" max="50" value="<?= htmlspecialchars($coletor['raio_atuacao'] ?? 10) ?>" id="radius-slider">
                        <div class="range-value">
                            Raio atual: <span id="radius-value"><?= htmlspecialchars($coletor['raio_atuacao'] ?? 10) ?></span> km
                        </div>
                    </div>
                </div>

                <!-- Informações da Conta -->
                <div class="settings-section">
                    <h2>Informações da Conta</h2>
                    <form class="settings-form">
                        <div class="form-group">
                            <label for="email">E-mail</label>
                            <input type="email" id="email" value="<?php echo $coletor['email'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label for="phone">Telefone</label>
                            <input type="tel" id="phone" value="<?php echo $coletor['telefone'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Senha</label>
                            <button type="button" class="change-password-btn" id="changePasswordBtn">
                                <i class="ri-lock-line"></i> Alterar Senha
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Horários de Funcionamento -->
                <div class="settings-section">
                    <h2>Horários de Funcionamento</h2>
                    <div class="schedule-container">
                        <?php
                        foreach ($dias_semana as $dia_semana) :
                            $dia = getDisponibilidadeDia($horarios, $dia_semana);
                        ?>
                            <div class="day-schedule">
                                <!-- Checkbox ativado ou não -->
                                <input type="checkbox" class="day-toggle" name="days[<?= $dia_semana ?>][active]"
                                    <?= ($dia && $dia['ativo']) ? 'checked' : '' ?>>
                                <span class="day-name"><?= $dia_semana ?></span>
                                <div class="time-inputs">
                                    <!-- Hora de abertura -->
                                    <input type="time" min="08:00" max="17:00" name="days[<?= $dia_semana ?>][open]" value="<?= $dia['hora_abertura'] ?? '08:00' ?>">
                                    <span>até</span>
                                    <!-- Hora de fechamento -->
                                    <input type="time" min="08:00" max="17:00" name="days[<?= $dia_semana ?>][close]" value="<?= $dia['hora_fechamento'] ?? '17:00' ?>">
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Endereço -->
                <div class="settings-section">
                    <h2>Seu Endereço</h2>
                    <form class="settings-form">
                        <div class="form-group">
                            <label for="cep">CEP</label>
                            <input type="text" id="cep" value="<?= htmlspecialchars($coletor['cep'] ?? '') ?>" placeholder="00000-000" maxlength="9">
                        </div>
                        <div class="form-group">
                            <label for="rua">Rua</label>
                            <input type="text" id="rua" value="<?= htmlspecialchars($coletor['rua'] ?? '') ?>" placeholder="Rua das Flores">
                        </div>
                        <div class="form-group">
                            <label for="numero">Número</label>
                            <input type="text" id="numero" value="<?= htmlspecialchars($coletor['numero'] ?? '') ?>" placeholder="123">
                        </div>
                        <div class="form-group">
                            <label for="complemento">Complemento</label>
                            <input type="text" id="complemento" value="<?= htmlspecialchars($coletor['complemento'] ?? '') ?>" placeholder="Apto 456, Fundos">
                        </div>
                        <div class="form-group">
                            <label for="bairro">Bairro</label>
                            <input type="text" id="bairro" value="<?= htmlspecialchars($coletor['bairro'] ?? '') ?>" placeholder="Centro">
                        </div>
                        <div class="form-group">
                            <label for="cidade">Cidade</label>
                            <input type="text" id="cidade" value="<?= htmlspecialchars($coletor['cidade'] ?? '') ?>" placeholder="São Paulo">
                        </div>
                        <div class="form-group">
                            <label for="estado">Estado</label>
                            <input type="text" id="estado" value="<?= htmlspecialchars($coletor['estado'] ?? '') ?>" placeholder="SP" maxlength="2">
                        </div>
                    </form>
                </div>

                <!-- Meio de Transporte -->
                <div class="settings-section">
                    <h2>Meio de Transporte</h2>
                    <form class="settings-form">
                        <div class="form-group">
                            <label for="transport">Altere seu meio de transporte</label>
                            <select id="transport" name="transport">
                                <option value="carro" <?= ($coletor['meio_transporte'] ?? '') === 'carro' ? 'selected' : '' ?>>🚗 Carro</option>
                                <option value="bicicleta" <?= ($coletor['meio_transporte'] ?? '') === 'bicicleta' ? 'selected' : '' ?>>🚴 Bicicleta</option>
                                <option value="motocicleta" <?= ($coletor['meio_transporte'] ?? '') === 'motocicleta' ? 'selected' : '' ?>>🏍️ Motocicleta</option>
                                <option value="carroca" <?= ($coletor['meio_transporte'] ?? '') === 'carroca' ? 'selected' : '' ?>>🛒 Carroça</option>
                                <option value="ape" <?= ($coletor['meio_transporte'] ?? '') === 'ape' ? 'selected' : '' ?>>🚶 À Pé</option>
                            </select>
                        </div>
                    </form>
                </div>

                <!-- Botões de Ação -->
                <div class="action-buttons">
                    <button class="save-btn">Salvar Alterações</button>
                    <button class="cancel-btn">Cancelar</button>
                    <button class="logout-btn" href="../logout.php"><i class="ri-logout-box-line"></i> Sair</button>
                </div>
            </div>
        </main>

        <!-- Modal de Alterar Senha -->
        <div id="changePasswordModal" class="modal">
            <div class="modal-content">
                <button class="close-modal-btn">&times;</button>
                <h2>Alterar Senha</h2>

                <form id="changePasswordForm">
                    <div class="form-group">
                        <label for="newPassword">Nova Senha</label>
                        <input type="password" id="newPassword" name="newPassword" placeholder="Digite sua nova senha" required>
                        <small class="password-hint">Mínimo 8 caracteres, incluindo maiúscula, minúscula, número e caractere especial</small>
                    </div>

                    <div class="form-group">
                        <label for="confirmPassword">Confirmar Nova Senha</label>
                        <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirme sua nova senha" required>
                    </div>

                    <div id="passwordError" class="error-message" style="display: none;"></div>

                    <div class="modal-buttons">
                        <button type="button" class="btn-cancel" onclick="document.getElementById('changePasswordModal').style.display='none'">Cancelar</button>
                        <button type="submit" class="btn-save">Alterar Senha</button>
                    </div>
                </form>
            </div>
        </div>
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
        <!-- Painel de Acessibilidade -->
        <div class="accessibility-overlay"></div>
        <div class="accessibility-panel">
            <div class="accessibility-header">
                <h3>Acessibilidade</h3>
                <button class="accessibility-close">×</button>
            </div>
            <div class="accessibility-group">
                <div class="accessibility-group-title">Tamanho de Texto</div>
                <div class="size-control">
                    <span class="size-label">A</span>
                    <input type="range" class="size-slider" min="50" max="150" value="100">
                    <span class="size-label" style="font-weight: bold;">A</span>
                    <span class="size-value">100%</span>
                </div>
            </div>
            <div class="accessibility-group">
                <div class="accessibility-group-title">Visão</div>
                <div class="accessibility-options">
                    <label class="accessibility-option">
                        <select id="contrast-level">
                            <option value="none">Sem Contraste</option>
                            <option value="wcag-aa">Contraste WCAG AA</option>
                        </select>
                    </label>
                    <label class="accessibility-option">
                        <input type="checkbox" id="inverted-mode">
                        <span>Modo Invertido</span>
                    </label>
                    <label class="accessibility-option">
                        <input type="checkbox" id="reading-guide">
                        <span>Linha Guia de Leitura</span>
                    </label>
                </div>
            </div>
            <div class="accessibility-group">
                <div class="accessibility-group-title">Fonte</div>
                <div class="accessibility-options">
                    <label class="accessibility-option">
                        <input type="checkbox" id="sans-serif">
                        <span>Fonte Sem Serifa</span>
                    </label>
                    <label class="accessibility-option">
                        <input type="checkbox" id="dyslexia-font">
                        <span>Fonte Dislexia</span>
                    </label>
                    <label class="accessibility-option">
                        <input type="checkbox" id="monospace-font">
                        <span>Fonte Monoespacida</span>
                    </label>
                </div>
            </div>
            <div class="accessibility-group">
                <div class="accessibility-group-title">Espaçamento</div>
                <div class="accessibility-options">
                    <label class="accessibility-option">
                        <input type="checkbox" id="increased-spacing">
                        <span>Aumentar Espaçamento</span>
                    </label>
                </div>
            </div>
            <div class="accessibility-group">
                <div class="accessibility-group-title">Navegação</div>
                <div class="accessibility-options">
                    <label class="accessibility-option">
                        <input type="checkbox" id="expanded-focus">
                        <span>Foco Expandido</span>
                    </label>
                    <label class="accessibility-option">
                        <input type="checkbox" id="large-cursor">
                        <span>Cursor Maior</span>
                    </label>
                </div>
            </div>
            <button class="accessibility-reset-btn">Restaurar Padrões</button>
        </div>
        <!-- Botão de Libras Separado -->
        <div class="libras-button" id="librasButton" onclick="toggleAccessibility(event)" title="Libras">
            👋
        </div>
        <div vw class="enabled">
            <div vw-access-button class="active"></div>
            <div vw-plugin-wrapper>
                <div class="vw-plugin-top-wrapper"></div>
            </div>
        </div>


        <script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
        <script src="../JS/configuracoes.js"></script>
        <script src="../JS/navbar.js"></script>
        <script src="../JS/acessibilidade.js"></script><script src="../JS/libras.js"></script>
        <script>
            // Modal de Alterar Senha
            document.addEventListener('DOMContentLoaded', function() {
                const changePasswordBtn = document.getElementById('changePasswordBtn');
                const changePasswordModal = document.getElementById('changePasswordModal');
                const closeModalBtn = document.querySelector('.close-modal-btn');
                const changePasswordForm = document.getElementById('changePasswordForm');
                const passwordError = document.getElementById('passwordError');
                const newPasswordInput = document.getElementById('newPassword');
                const confirmPasswordInput = document.getElementById('confirmPassword');

                // Abrir modal
                if (changePasswordBtn) {
                    changePasswordBtn.addEventListener('click', function() {
                        changePasswordModal.style.display = 'block';
                        newPasswordInput.focus();
                    });
                }

                // Fechar modal ao clicar no X
                if (closeModalBtn) {
                    closeModalBtn.addEventListener('click', function() {
                        changePasswordModal.style.display = 'none';
                        passwordError.style.display = 'none';
                    });
                }

                // Fechar modal ao clicar fora
                window.addEventListener('click', function(event) {
                    if (event.target === changePasswordModal) {
                        changePasswordModal.style.display = 'none';
                        passwordError.style.display = 'none';
                    }
                });

                // Validar e enviar senha
                if (changePasswordForm) {
                    changePasswordForm.addEventListener('submit', async function(e) {
                        e.preventDefault();

                        const newPassword = newPasswordInput.value.trim();
                        const confirmPassword = confirmPasswordInput.value.trim();

                        // Limpar mensagem de erro anterior
                        passwordError.style.display = 'none';
                        passwordError.textContent = '';

                        // Validações
                        if (newPassword.length < 8) {
                            showError('A senha deve ter no mínimo 8 caracteres.');
                            return;
                        }

                        if (!/[A-Z]/.test(newPassword)) {
                            showError('A senha deve conter pelo menos uma letra maiúscula.');
                            return;
                        }

                        if (!/[a-z]/.test(newPassword)) {
                            showError('A senha deve conter pelo menos uma letra minúscula.');
                            return;
                        }

                        if (!/[0-9]/.test(newPassword)) {
                            showError('A senha deve conter pelo menos um número.');
                            return;
                        }

                        if (!/[^A-Za-z0-9]/.test(newPassword)) {
                            showError('A senha deve conter pelo menos um caractere especial.');
                            return;
                        }

                        if (newPassword !== confirmPassword) {
                            showError('As senhas não coincidem. Tente novamente.');
                            return;
                        }

                        // Enviar para backend
                        try {
                            const response = await fetch('../BANCO/change_password.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded',
                                },
                                body: new URLSearchParams({
                                    nova_senha: newPassword
                                })
                            });

                            const data = await response.json();

                            if (data.success) {
                                alert('Senha alterada com sucesso!');
                                changePasswordModal.style.display = 'none';
                                changePasswordForm.reset();
                            } else {
                                showError(data.message || 'Erro ao alterar a senha.');
                            }
                        } catch (error) {
                            showError('Erro de conexão ao alterar a senha.');
                            console.error('Erro:', error);
                        }
                    });
                }

                function showError(message) {
                    passwordError.textContent = message;
                    passwordError.style.display = 'block';
                }

                // Função para mostrar notificação de feedback
                function showNotification(message, type) {
                    // Remover notificação anterior se existir
                    const existingMessage = document.querySelector('.feedback-message');
                    if (existingMessage) {
                        existingMessage.remove();
                    }

                    // Criar novo elemento de notificação
                    const notification = document.createElement('div');
                    notification.className = `feedback-message ${type}`;
                    notification.innerHTML = `
                        <i class="ri-${type === 'sucesso' ? 'check-line' : 'alert-line'}"></i>
                        <span>${message}</span>
                    `;

                    // Inserir após o header
                    const header = document.querySelector('.content-header');
                    header.parentElement.insertBefore(notification, header.nextSibling);

                    // Auto-hide após 5 segundos
                    setTimeout(() => {
                        notification.style.opacity = '0';
                        notification.style.transition = 'opacity 0.3s ease-out';
                        setTimeout(() => notification.remove(), 300);
                    }, 5000);
                }
            });
        </script>
</body>

</html>


