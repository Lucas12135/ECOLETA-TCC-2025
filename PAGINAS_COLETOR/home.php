<?php
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../index.php');
    exit;
}

// Extrai primeiro e último nome
$nomeCompleto = $_SESSION['nome_usuario'] ?? 'Coletor';
$nomePartes = explode(' ', trim($nomeCompleto));
$primeiroNome = $nomePartes[0] ?? 'Coletor';
$ultimoNome = end($nomePartes);

// Definir fuso horário de Brasília
date_default_timezone_set('America/Sao_Paulo');

include_once('../BANCO/conexao.php');

// Variáveis padrão
$proximaColeta = null;
$coletasHoje = [];
$coletasHojeTotal = 0;
$coletasHojeCompletar = 0;
$coletasPendentes = 0;

try {
    if ($conn) {
        // Buscar próxima coleta agendada
        $stmt = $conn->prepare("
            SELECT c.*, g.nome_completo
            FROM coletas c
            JOIN geradores g ON c.id_gerador = g.id
            WHERE c.id_coletor = :id_coletor 
            AND c.status IN ('agendada', 'em_andamento')
            ORDER BY c.data_agendada ASC
            LIMIT 1
        ");
        $stmt->bindParam(':id_coletor', $_SESSION['id_usuario']);
        $stmt->execute();
        $proximaColeta = $stmt->fetch(PDO::FETCH_ASSOC);

        // Buscar coletas de hoje
        $hoje = date('Y-m-d');
        $stmt = $conn->prepare("
            SELECT c.*, g.nome_completo
            FROM coletas c
            JOIN geradores g ON c.id_gerador = g.id
            WHERE c.id_coletor = :id_coletor 
            AND DATE(c.data_agendada) = :hoje
            AND c.status IN ('agendada', 'em_andamento', 'concluida')
            ORDER BY c.data_agendada ASC
        ");
        $stmt->bindParam(':id_coletor', $_SESSION['id_usuario']);
        $stmt->bindParam(':hoje', $hoje);
        $stmt->execute();
        $coletasHoje = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $coletasHojeTotal = count($coletasHoje);
        $coletasHojeCompletar = count(array_filter($coletasHoje, function($c) { return $c['status'] !== 'concluida'; }));

        // Buscar solicitações pendentes (não aceitas ainda)
        $stmt = $conn->prepare("
            SELECT COUNT(*) as total
            FROM coletas c
            WHERE (c.id_coletor IS NULL OR c.id_coletor = :id_coletor) 
            AND c.status IN ('solicitada', 'pendente')
        ");
        $stmt->bindParam(':id_coletor', $_SESSION['id_usuario']);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $coletasPendentes = $result['total'] ?? 0;
    }
} catch (PDOException $e) {
    // Silenciosamente ignorar erro
}

// Função auxiliar para formatar período
function formatarPeriodo($periodo) {
    $periodos = [
        'manha' => 'Manhã (8h - 12h)',
        'tarde' => 'Tarde (13h - 17h)'
    ];
    return $periodos[$periodo] ?? $periodo;
}

// Função auxiliar para formatar data
function formatarData($data) {
    return date('d/m/Y', strtotime($data));
}

// Função auxiliar para formatar hora
function formatarHora($data) {
    return date('H:i', strtotime($data));
}

// Função auxiliar para obter nome do dia da semana
function obterDiaSemana($data) {
    $dias = ['Domingo', 'Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado'];
    return $dias[date('w', strtotime($data))];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Coletor</title>
    <link rel="icon" href="../img/logo.png" type="image/png">
    <link rel="stylesheet" href="../CSS/home-coletor.css">
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
        </header>

        <!-- Conteúdo Principal -->
        <main class="main-content">
            <div style="margin-bottom: 20px;">
                <a href="../index.php"><button class="back-button" style="background-color: #ff6b6b; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-size: 14px; display: flex; align-items: center; gap: 8px;"><i class="ri-arrow-left-line"></i>Voltar</button></a>
            </div>
            <header class="content-header">
                <div class="welcome-message">
                    <h1>Olá, <?php echo htmlspecialchars($primeiroNome) . ' ' . htmlspecialchars($ultimoNome); ?>!</h1>
                    <p>Confira suas coletas e atualizações de hoje</p>
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

            <!-- Cards de Informações -->
            <div class="info-cards">
                <div class="card next-collection">
                    <h3>Próxima Coleta</h3>
                    <div class="card-content">
                        <?php if ($proximaColeta): ?>
                            <div class="time">
                                <i class="ri-time-line"></i>
                                <span><?php echo formatarHora($proximaColeta['data_agendada']); ?> - <?php echo formatarPeriodo($proximaColeta['periodo']); ?></span>
                            </div>
                            <div class="location">
                                <i class="ri-map-pin-line"></i>
                                <span><?php echo htmlspecialchars($proximaColeta['rua']); ?>, <?php echo htmlspecialchars($proximaColeta['numero']); ?> - <?php echo htmlspecialchars($proximaColeta['bairro']); ?></span>
                            </div>
                            <button class="view-map-btn">Ver no Mapa</button>
                        <?php else: ?>
                            <p style="color: #999; text-align: center;">Nenhuma coleta agendada no momento</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card today-collections">
                    <h3>Coletas Hoje</h3>
                    <div class="card-content">
                        <div class="collection-count">
                            <span class="number"><?php echo $coletasHojeTotal; ?></span>
                            <span class="label">agendadas</span>
                        </div>
                        <div class="collection-progress">
                            <span class="completed"><?php echo ($coletasHojeTotal - $coletasHojeCompletar); ?></span>
                            <span class="separator">/</span>
                            <span class="total"><?php echo $coletasHojeTotal; ?></span>
                            <span class="label">completadas</span>
                        </div>
                    </div>
                </div>

                <div class="card pending-requests">
                    <h3>Solicitações Pendentes</h3>
                    <div class="card-content">
                        <div class="request-count">
                            <span class="number"><?php echo $coletasPendentes; ?></span>
                            <span class="label">novas Solicitações</span>
                        </div>
                        <a href="agendamentos.php"><button class="view-requests-btn">Ver Solicitações</button></a>
                    </div>
                </div>
            </div>

            <!-- Mapa e Lista de Coletas -->
            <div class="collections-container">
                <div class="collections-list">
                    <h3>Coletas de Hoje</h3>
                    <div class="collection-items">
                        <?php if (empty($coletasHoje)): ?>
                            <div style="text-align: center; padding: 40px; color: #999;">
                                <i class="ri-inbox-line" style="font-size: 48px; margin-bottom: 10px;"></i>
                                <p>Nenhuma coleta agendada para hoje</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($coletasHoje as $coleta): ?>
                                <!-- Item de Coleta -->
                                <div class="collection-item">
                                    <div class="time-location">
                                        <span class="time"><?php echo formatarHora($coleta['data_agendada']); ?></span>
                                        <span class="period"><?php echo formatarPeriodo($coleta['periodo']); ?></span>
                                        <span class="location"><?php echo htmlspecialchars($coleta['rua']); ?>, <?php echo htmlspecialchars($coleta['numero']); ?> - <?php echo htmlspecialchars($coleta['bairro']); ?></span>
                                    </div>
                                    <div class="details">
                                        <span class="quantity"><?php echo $coleta['quantidade_oleo']; ?>L</span>
                                        <span class="status <?php echo strtolower($coleta['status']); ?>"><?php 
                                            $statusTexto = [
                                                'agendada' => 'Agendada',
                                                'em_andamento' => 'Em andamento',
                                                'concluida' => 'Concluída'
                                            ];
                                            echo $statusTexto[$coleta['status']] ?? ucfirst($coleta['status']); 
                                        ?></span>
                                        <button class="view-map-btn" title="Ver no mapa">
                                            <i class="ri-map-pin-line"></i>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="map-container">
                    <div id="map">
                        <!-- Aqui será carregado o mapa via JavaScript -->
                    </div>
                </div>
            </div>

            <!-- Próximos Dias -->
            <div class="upcoming-collections">
                <h3>Próximos Dias</h3>
                <div class="calendar-view">
                    <?php
                    try {
                        // Buscar coletas dos próximos 7 dias
                        $stmt = $conn->prepare("
                            SELECT c.*, g.nome_completo,
                                   DATE(c.data_agendada) as data_coleta
                            FROM coletas c
                            JOIN geradores g ON c.id_gerador = g.id
                            WHERE c.id_coletor = :id_coletor 
                            AND DATE(c.data_agendada) > :hoje
                            AND DATE(c.data_agendada) <= DATE_ADD(:hoje, INTERVAL 7 DAY)
                            AND c.status IN ('agendada', 'em_andamento')
                            ORDER BY c.data_agendada ASC
                        ");
                        $stmt->bindParam(':id_coletor', $_SESSION['id_usuario']);
                        $stmt->bindParam(':hoje', $hoje);
                        $stmt->execute();
                        $coletasProximos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        if (empty($coletasProximos)):
                    ?>
                        <div style="text-align: center; padding: 40px; color: #999;">
                            <i class="ri-calendar-blank-line" style="font-size: 48px; margin-bottom: 10px;"></i>
                            <p>Nenhuma coleta agendada para os próximos dias</p>
                        </div>
                    <?php
                        else:
                            // Agrupar coletas por data
                            $coletasPorData = [];
                            foreach ($coletasProximos as $coleta) {
                                $data = $coleta['data_coleta'];
                                if (!isset($coletasPorData[$data])) {
                                    $coletasPorData[$data] = [];
                                }
                                $coletasPorData[$data][] = $coleta;
                            }
                    ?>
                        <div class="upcoming-days">
                            <?php foreach ($coletasPorData as $data => $coletas): ?>
                                <div class="upcoming-day">
                                    <div class="day-header">
                                        <h4><?php echo date('d/m/Y', strtotime($data)); ?></h4>
                                        <span class="day-name"><?php echo obterDiaSemana($data); ?></span>
                                    </div>
                                    <div class="day-collections">
                                        <?php foreach ($coletas as $coleta): ?>
                                            <div class="upcoming-collection-item">
                                                <div class="collection-time">
                                                    <i class="ri-time-line"></i>
                                                    <span><?php echo formatarHora($coleta['data_agendada']); ?></span>
                                                </div>
                                                <div class="collection-info">
                                                    <p class="generator"><?php echo htmlspecialchars($coleta['nome_completo']); ?></p>
                                                    <p class="location"><?php echo htmlspecialchars($coleta['bairro']); ?>, <?php echo htmlspecialchars($coleta['cidade']); ?></p>
                                                </div>
                                                <div class="collection-amount">
                                                    <span><?php echo $coleta['quantidade_oleo']; ?>L</span>
                                                    <span class="period"><?php echo formatarPeriodo($coleta['periodo']); ?></span>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php
                        endif;
                    } catch (PDOException $e) {
                        // Silenciosamente ignorar erro
                    }
                    ?>
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
        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAe884hZ7UbSCJDuS4hkEWrR-ls0XVBe_U"></script>
        <script src="../JS/home-coletor.js"></script>
        <script src="../JS/libras.js"></script>
</body>

</html>