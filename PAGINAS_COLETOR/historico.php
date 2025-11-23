<?php
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../index.php');
    exit;
}

// Definir fuso horário de Brasília
date_default_timezone_set('America/Sao_Paulo');

include_once('../BANCO/conexao.php');

// Buscar histórico de coletas do coletor
$historico_coletas = [];

try {
    if ($conn) {
        $stmt_historico = $conn->prepare("
            SELECT 
                hc.id,
                hc.id_coleta,
                hc.id_gerador,
                hc.data_inicio,
                hc.data_conclusao,
                hc.quantidade_coletada,
                hc.observacoes,
                hc.status,
                g.nome_completo as nome_gerador,
                g.telefone,
                g.email,
                gc.valor_ganho,
                gc.status_pagamento
            FROM historico_coletas hc
            LEFT JOIN geradores g ON hc.id_gerador = g.id
            LEFT JOIN ganhos_coletores gc ON hc.id = gc.id_historico_coleta
            WHERE hc.id_coletor = :id_coletor
            ORDER BY hc.data_conclusao DESC
        ");
        $stmt_historico->bindParam(':id_coletor', $_SESSION['id_usuario']);
        $stmt_historico->execute();
        $historico_coletas = $stmt_historico->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    // Silenciosamente ignorar erro
}

// Função auxiliar para formatar data
function formatarData($data)
{
    return date('d/m/Y', strtotime($data));
}

// Função auxiliar para formatar hora
function formatarHora($data)
{
    return date('H:i', strtotime($data));
}

// Função auxiliar para formatar status
function formatarStatus($status)
{
    $statusMap = [
        'concluida' => ['label' => 'Concluída', 'class' => 'status-concluida'],
        'em_andamento' => ['label' => 'Em Andamento', 'class' => 'status-andamento'],
        'cancelada' => ['label' => 'Cancelada', 'class' => 'status-cancelada']
    ];
    return $statusMap[$status] ?? ['label' => $status, 'class' => 'status-padrao'];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico - Coletor</title>
    <link rel="icon" href="../img/logo.png" type="image/png">
    <link rel="stylesheet" href="../CSS/coletor-historico.css">
    <link rel="stylesheet" href="../CSS/navbar.css">
    <link rel="stylesheet" href="../CSS/libras.css">
    <link rel="stylesheet" href="../CSS/acessibilidade.css">
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
        </header>

        <!-- Conteúdo Principal -->
        <main class="main-content">
            <header class="content-header">
                <div class="welcome-message">
                    <h1>Página de Histórico</h1>
                    <p>Confira seu histórico de coletas</p>
                </div>
                <div class="header-actions">
                </div>
            </header>

            <!-- Lista de Histórico -->
            <div class="history-list">
                <?php if (empty($historico_coletas)): ?>
                    <div style="text-align: center; padding: 60px 20px; color: #999;">
                        <i class="ri-history-line" style="font-size: 64px; margin-bottom: 20px; display: block;"></i>
                        <h3>Nenhuma coleta realizada ainda</h3>
                        <p>Suas coletas concluídas aparecerão aqui</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($historico_coletas as $coleta): ?>
                        <?php $status_info = formatarStatus($coleta['status']); ?>
                        <div class="history-item">
                            <div class="history-item-header">
                                <div class="history-main-info">
                                    <span class="collection-id">ID: #<?php echo $coleta['id_coleta']; ?></span>
                                    <span class="collection-quantity">
                                        <i class="ri-oil-line"></i>
                                        <?php echo number_format($coleta['quantidade_coletada'], 2); ?> litros
                                    </span>
                                    <span class="collection-date"><?php echo formatarData($coleta['data_conclusao']); ?></span>
                                </div>
                                <div class="history-actions">
                                    <span class="collection-status <?php echo $status_info['class']; ?>">
                                        <?php echo $status_info['label']; ?>
                                    </span>
                                    <button class="expand-button">
                                        Mais detalhes
                                        <i class="ri-arrow-down-s-line"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="history-details">
                                <div class="detail-row">
                                    <span class="detail-label">Solicitante:</span>
                                    <span class="detail-value"><?php echo htmlspecialchars($coleta['nome_gerador']); ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Contato:</span>
                                    <span class="detail-value"><?php echo htmlspecialchars($coleta['telefone']); ?> / <?php echo htmlspecialchars($coleta['email']); ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Data da Conclusão:</span>
                                    <span class="detail-value"><?php echo formatarData($coleta['data_conclusao']); ?> às <?php echo formatarHora($coleta['data_conclusao']); ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Quantidade Coletada:</span>
                                    <span class="detail-value"><?php echo number_format($coleta['quantidade_coletada'], 2); ?> litros</span>
                                </div>
                                <?php if ($coleta['valor_ganho']): ?>
                                    <div class="detail-row">
                                        <span class="detail-label">Ganho:</span>
                                        <span class="detail-value" style="color: #4CAF50; font-weight: bold;">
                                            R$ <?php echo number_format($coleta['valor_ganho'], 2, ',', '.'); ?>
                                        </span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label">Status do Pagamento:</span>
                                        <span class="detail-value">
                                            <?php
                                            $status_pag = $coleta['status_pagamento'] ?? 'pendente';
                                            echo $status_pag === 'pago' ? '✓ Pago' : 'Pendente';
                                            ?>
                                        </span>
                                    </div>
                                <?php endif; ?>
                                <?php if ($coleta['observacoes']): ?>
                                    <div class="detail-row">
                                        <span class="detail-label">Observações:</span>
                                        <span class="detail-value"><?php echo htmlspecialchars($coleta['observacoes']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
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
        <div class="libras-button" id="librasButton" onclick="toggleLibras(event)" title="Libras">
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
                // Gerenciar expansão dos itens do histórico
                const historyItems = document.querySelectorAll('.history-item');

                historyItems.forEach(item => {
                    const expandButton = item.querySelector('.expand-button');

                    expandButton.addEventListener('click', () => {
                        item.classList.toggle('expanded');
                    });
                });

                // Fim de gerenciamento de eventos
            });
        </script>
        <script src="../JS/navbar.js"></script>
        <script src="../JS/acessibilidade.js"></script><script src="../JS/libras.js"></script>
</body>

</html>


