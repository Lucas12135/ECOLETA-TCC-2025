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
    <link rel="stylesheet" href="../CSS/acessibilidade.css">
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
                    <form class="settings-form">
                        <div class="form-group">
                            <label for="email">E-mail</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($gerador['email'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="phone">Telefone</label>
                            <input type="tel" id="phone" name="telefone" value="<?php echo htmlspecialchars($gerador['telefone'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>Senha</label>
                            <button type="button" class="change-password-btn" id="changePasswordBtn">
                                <i class="ri-lock-line"></i> Alterar Senha
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Endereço -->
                <div class="settings-section">
                    <h2>Seu Endereço</h2>
                    <form class="settings-form">
                        <div class="form-group">
                            <label for="cep">CEP</label>
                            <input type="text" id="cep" placeholder="00000-000" value="<?php echo htmlspecialchars($gerador['cep'] ?? ''); ?>" maxlength="9">
                        </div>
                        <div class="form-group">
                            <label for="rua">Rua</label>
                            <input type="text" id="rua" placeholder="Rua das Flores" value="<?php echo htmlspecialchars($gerador['rua'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="numero">Número</label>
                            <input type="text" id="numero" placeholder="123" value="<?php echo htmlspecialchars($gerador['numero'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="complemento">Complemento</label>
                            <input type="text" id="complemento" placeholder="Apto 456, Fundos" value="<?php echo htmlspecialchars($gerador['complemento'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="bairro">Bairro</label>
                            <input type="text" id="bairro" placeholder="Centro" value="<?php echo htmlspecialchars($gerador['bairro'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="cidade">Cidade</label>
                            <input type="text" id="cidade" placeholder="São Paulo" value="<?php echo htmlspecialchars($gerador['cidade'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="estado">Estado</label>
                            <input type="text" id="estado" placeholder="SP" value="<?php echo htmlspecialchars($gerador['estado'] ?? ''); ?>" maxlength="2">
                        </div>
                    </form>
                </div>
                <!-- Botões de Ação -->
                <div class="action-buttons">
                    <button class="save-btn">Salvar Alterações</button>
                    <button class="cancel-btn">Cancelar</button>
                    <button class="logout-btn" onclick="window.location.href='../logout.php'"><i class="ri-logout-box-line"></i> Sair</button>
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
            <!-- Tamanho de Texto -->
            <div class="accessibility-group">
                <div class="accessibility-group-title">Tamanho de Texto</div>
                <div class="size-control">
                    <span class="size-label">A</span>
                    <input type="range" class="size-slider" min="50" max="150" value="100">
                    <span class="size-label" style="font-weight: bold;">A</span>
                    <span class="size-value">100%</span>
                </div>
            </div>
            <!-- Opções de Visão -->
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
            <!-- Opções de Fonte -->
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
            <!-- Opções de Espaçamento -->
            <div class="accessibility-group">
                <div class="accessibility-group-title">Espaçamento</div>
                <div class="accessibility-options">
                    <label class="accessibility-option">
                        <input type="checkbox" id="increased-spacing">
                        <span>Aumentar Espaçamento</span>
                    </label>
                </div>
            </div>
            <!-- Opções de Foco e Cursor -->
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
            <!-- Botão de Reset -->
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
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Modal de Alterar Senha
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

                // Função para salvar configurações
                async function handleSaveSettings() {
                    try {
                        // Coletar dados de conta
                        const email = document.getElementById('email')?.value || '';
                        const phone = document.getElementById('phone')?.value || '';

                        // Coletar dados de endereço
                        const endereco = {
                            cep: document.getElementById('cep')?.value || '',
                            rua: document.getElementById('rua')?.value || '',
                            numero: document.getElementById('numero')?.value || '',
                            complemento: document.getElementById('complemento')?.value || '',
                            bairro: document.getElementById('bairro')?.value || '',
                            cidade: document.getElementById('cidade')?.value || '',
                            estado: document.getElementById('estado')?.value || '',
                        };

                        // Preparar dados para envio
                        const payload = {
                            email,
                            phone,
                            endereco,
                        };

                        // Enviar para o servidor
                        const response = await fetch('../BANCO/update_gerador_config.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify(payload),
                        });

                        const data = await response.json();

                        if (data.success) {
                            showNotification('Configurações salvas com sucesso!', 'sucesso');
                        } else {
                            showNotification(data.message || 'Erro ao salvar configurações', 'erro');
                        }
                    } catch (error) {
                        console.error('Erro ao salvar:', error);
                        showNotification('Erro ao processar a solicitação', 'erro');
                    }
                }

                // Botão Salvar Alterações
                const saveBtn = document.querySelector('.save-btn');
                if (saveBtn) {
                    saveBtn.addEventListener('click', handleSaveSettings);
                }

                // Botão Cancelar
                const cancelBtn = document.querySelector('.cancel-btn');
                if (cancelBtn) {
                    cancelBtn.addEventListener('click', function() {
                        location.reload();
                    });
                }

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
        <script src="../JS/acessibilidade.js"></script><script src="../JS/libras.js"></script>
</body>

</html>


