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
        <script src="../JS/configuracoes.js"></script>
        <script src="../JS/navbar.js"></script>
        <script src="../JS/libras.js"></script>
</body>

</html>