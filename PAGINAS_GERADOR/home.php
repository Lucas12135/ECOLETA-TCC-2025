<?php
session_start();
require_once '../BANCO/conexao.php';

// Extrai primeiro e último nome
$nomeCompleto = $_SESSION['nome_usuario'] ?? 'Produtor de Óleo usado';
$nomePartes = explode(' ', trim($nomeCompleto));
$primeiroNome = $nomePartes[0] ?? 'Produtor';
$ultimoNome = end($nomePartes);

// Obter ID do gerador logado
$idGerador = $_SESSION['id_usuario'] ?? null;

if (!$idGerador) {
    header('Location: ../logins.php');
    exit;
}

// Calcular total de óleo reciclado (coletas concluídas)
$sqlTotalOleo = "SELECT COALESCE(SUM(hc.quantidade_coletada), 0) as total_oleo
                 FROM historico_coletas hc
                 WHERE hc.id_gerador = :id_gerador AND hc.status = 'concluida'";
$stmtTotalOleo = $conn->prepare($sqlTotalOleo);
$stmtTotalOleo->bindParam(':id_gerador', $idGerador, PDO::PARAM_INT);
$stmtTotalOleo->execute();
$totalOleo = $stmtTotalOleo->fetch(PDO::FETCH_ASSOC)['total_oleo'];

// Buscar coleta atual (em andamento ou agendada)
$sqlColetaAtual = "SELECT c.id, c.quantidade_oleo, c.data_agendada, c.periodo, c.status,
                          c.rua, c.numero, c.bairro, c.cidade, c.estado,
                          co.id as id_coletor, co.nome_completo as nome_coletor,
                          hc.id as id_historico, hc.status as status_historico,
                          hc.data_inicio, hc.data_conclusao
                   FROM coletas c
                   LEFT JOIN coletores co ON c.id_coletor = co.id
                   LEFT JOIN historico_coletas hc ON c.id = hc.id_coleta
                   WHERE c.id_gerador = :id_gerador 
                   AND c.status IN ('agendada', 'em_andamento', 'pendente', 'solicitada')
                   ORDER BY c.data_agendada DESC LIMIT 1";
$stmtColetaAtual = $conn->prepare($sqlColetaAtual);
$stmtColetaAtual->bindParam(':id_gerador', $idGerador, PDO::PARAM_INT);
$stmtColetaAtual->execute();
$coletaAtual = $stmtColetaAtual->fetch(PDO::FETCH_ASSOC);

// Buscar histórico recente (últimas 2 coletas concluídas)
$sqlHistorico = "SELECT hc.id, hc.data_conclusao, hc.quantidade_coletada, hc.status,
                         c.id as id_coleta
                  FROM historico_coletas hc
                  JOIN coletas c ON hc.id_coleta = c.id
                  WHERE hc.id_gerador = :id_gerador AND hc.status = 'concluida'
                  ORDER BY hc.data_conclusao DESC LIMIT 2";
$stmtHistorico = $conn->prepare($sqlHistorico);
$stmtHistorico->bindParam(':id_gerador', $idGerador, PDO::PARAM_INT);
$stmtHistorico->execute();
$historico = $stmtHistorico->fetchAll(PDO::FETCH_ASSOC);

// Função auxiliar para formatar data
function formatarData($data) {
    if (!$data) return 'N/A';
    try {
        $dt = new DateTime($data);
        return $dt->format('d/m/Y');
    } catch (Exception $e) {
        return 'N/A';
    }
}

function formatarDataHora($data) {
    if (!$data) return 'N/A';
    try {
        $dt = new DateTime($data);
        return $dt->format('d/m - H:i');
    } catch (Exception $e) {
        return 'N/A';
    }
}

// Determinar status visual para a coleta atual
$statusClasses = [
    'solicitada' => 'active',
    'pendente' => 'active',
    'agendada' => 'active',
    'em_andamento' => 'active',
    'concluida' => 'completed',
    'cancelada' => 'canceled'
];
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Gerador</title>
    <link rel="icon" href="../img/logo.png" type="image/png">
    <link rel="stylesheet" href="../CSS/home-gerador.css">
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
                    <p>Gerencie suas solicitações de coleta de óleo</p>
                </div>
            </header>

            <!-- Cards de Informações -->
            <div class="info-cards">

                <div class="card oil-stats">
                    <h3>Estatísticas de Reciclagem</h3>
                    <div class="card-content">
                        <div class="stat-item">
                            <span class="number"><?php echo number_format($totalOleo, 1, ',', '.'); ?>L</span>
                            <span class="label">Óleo Reciclado</span>
                        </div>
                        <div class="eco-impact">
                            <span class="impact-text">Você ajudou a evitar a poluição de aproximadamente <?php echo number_format($totalOleo * 25000, 0, ',', '.'); ?>L de água!</span>
                        </div>
                    </div>
                </div>

                <div class="card quick-actions">
                    <h3>Ações Rápidas</h3>
                    <div class="card-content">
                        <a href="solicitar_coleta.php">
                        <button class="action-btn request-collection">
                            <i class="ri-oil-line"></i>
                            Nova Solicitação
                        </button>
                        </a>
                        <a href="historico.php">
                        <button class="action-btn view-history">
                            <i class="ri-history-line"></i>
                            Ver Histórico
                        </button>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Status da Coleta Atual -->
            <div class="collection-status-container">
                <div class="status-tracking">
                    <h3>Situação da Coleta Atual</h3>
                    <div class="status-timeline">
                        <?php if ($coletaAtual): ?>
                            <div class="status-step completed">
                                <div class="step-icon">
                                    <i class="ri-check-line"></i>
                                </div>
                                <div class="step-info">
                                    <h4>Solicitação Enviada</h4>
                                    <span><?php echo formatarDataHora($coletaAtual['data_inicio'] ?? $coletaAtual['data_agendada']); ?></span>
                                </div>
                            </div>
                            
                            <?php if (!empty($coletaAtual['id_coletor'])): ?>
                                <div class="status-step completed">
                                    <div class="step-icon">
                                        <i class="ri-check-line"></i>
                                    </div>
                                    <div class="step-info">
                                        <h4>Coleta Aceita</h4>
                                        <span>Coletor confirmado</span>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <div class="status-step <?php echo $statusClasses[$coletaAtual['status']] ?? 'active'; ?>">
                                <div class="step-icon">
                                    <i class="ri-<?php 
                                        echo match($coletaAtual['status']) {
                                            'concluida' => 'check-line',
                                            default => 'time-line'
                                        };
                                    ?>"></i>
                                </div>
                                <div class="step-info">
                                    <h4>
                                        <?php
                                            echo match($coletaAtual['status']) {
                                                'concluida' => 'Coleta Concluída',
                                                'em_andamento' => 'Coleta em Andamento',
                                                'agendada' => 'Aguardando Coleta',
                                                'pendente' => 'Aguardando Confirmação',
                                                default => 'Processando'
                                            };
                                        ?>
                                    </h4>
                                    <span>
                                        <?php 
                                            if ($coletaAtual['status'] === 'concluida') {
                                                echo formatarData($coletaAtual['data_conclusao']);
                                            } else if ($coletaAtual['status'] === 'agendada') {
                                                echo 'Agendada para ' . formatarData($coletaAtual['data_agendada']);
                                            } else {
                                                echo 'Processando...';
                                            }
                                        ?>
                                    </span>
                                </div>
                            </div>
                            
                            <?php if ($coletaAtual['status'] !== 'concluida'): ?>
                                <div class="status-step">
                                    <div class="step-icon">
                                        <i class="ri-checkbox-blank-circle-line"></i>
                                    </div>
                                    <div class="step-info">
                                        <h4>Coleta Concluída</h4>
                                        <span>Pendente</span>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div style="text-align: center; padding: 20px; color: #999;">
                                <p>Nenhuma coleta em andamento no momento</p>
                                <a href="solicitar_coleta.php" style="color: #4CAF50; text-decoration: none;">Solicitar uma coleta</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="collection-details">
                    <?php if ($coletaAtual): ?>
                        <div class="details-card">
                            <h4>Detalhes da Coleta</h4>
                            <div class="detail-item">
                                <i class="ri-oil-line"></i>
                                <span>Volume: <?php echo $coletaAtual['quantidade_oleo'] ?? 'N/A'; ?>L</span>
                            </div>
                            <div class="detail-item">
                                <i class="ri-map-pin-line"></i>
                                <span>Local: <?php echo htmlspecialchars($coletaAtual['rua'] . ', ' . $coletaAtual['numero'] . ' - ' . $coletaAtual['bairro'] . ', ' . $coletaAtual['cidade'] . ' - ' . $coletaAtual['estado']); ?></span>
                            </div>
                            <div class="detail-item">
                                <i class="ri-user-line"></i>
                                <span>Coletor: <?php echo htmlspecialchars($coletaAtual['nome_coletor'] ?? 'A confirmar'); ?></span>
                            </div>
                            <?php if (!empty($coletaAtual['id_coletor'])): ?>
                                <button class="btn-ver-perfil" onclick="abrirPerfilColetor(<?php echo $coletaAtual['id_coletor']; ?>)" style="margin-top: 15px; width: 100%; padding: 10px; background-color: #3b82f6; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: 600; transition: all 0.3s;">
                                    <i class="ri-eye-line"></i> Ver Perfil do Coletor
                                </button>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="details-card">
                            <h4>Detalhes da Coleta</h4>
                            <p style="color: #999;">Nenhuma coleta disponível</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Histórico Recente -->
            <div class="recent-history">
                <h3>Histórico Recente</h3>
                <div class="history-items">
                    <?php if (!empty($historico)): ?>
                        <?php foreach ($historico as $item): ?>
                            <div class="history-item">
                                <div class="history-content">
                                    <div class="history-info">
                                        <span class="date"><?php echo formatarData($item['data_conclusao']); ?></span>
                                        <span class="volume"><?php echo number_format($item['quantidade_coletada'], 1, ',', '.'); ?>L</span>
                                    </div>
                                    <span class="status completed">Concluída</span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div style="text-align: center; padding: 20px; color: #999;">
                            <p>Nenhuma coleta concluída ainda</p>
                        </div>
                    <?php endif; ?>
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
    
    <!-- Modal de Perfil do Coletor -->
    <div id="modalPerfilColetor" class="modal-perfil-coletor">
        <div class="modal-perfil-content">
            <button class="modal-perfil-close">&times;</button>
            <div id="perfilColetorConteudo" class="perfil-coletor-conteudo">
                <div style="text-align: center; padding: 40px;">
                    <div style="display: inline-block; width: 40px; height: 40px; border: 4px solid #3b82f6; border-radius: 50%; border-top: 4px solid transparent; animation: spin 1s linear infinite;"></div>
                    <p style="margin-top: 15px; color: #666;">Carregando perfil...</p>
                </div>
            </div>
        </div>
    </div>

    <style>
        .modal-perfil-coletor {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.3s;
        }

        .modal-perfil-coletor.show {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .modal-perfil-content {
            background-color: white;
            padding: 30px;
            border-radius: 12px;
            max-width: 600px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            position: relative;
        }

        .modal-perfil-close {
            position: absolute;
            right: 15px;
            top: 15px;
            background: none;
            border: none;
            font-size: 28px;
            cursor: pointer;
            color: #999;
        }

        .modal-perfil-close:hover {
            color: #333;
        }

        .perfil-coletor-conteudo {
            padding-top: 20px;
        }

        .perfil-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
        }

        .perfil-foto {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
            border: 4px solid #3b82f6;
        }

        .perfil-nome {
            font-size: 24px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 5px;
        }

        .perfil-tipo {
            font-size: 14px;
            color: #64748b;
            background-color: #e0f2fe;
            padding: 5px 15px;
            border-radius: 20px;
            display: inline-block;
        }

        .perfil-info-section {
            margin-bottom: 25px;
        }

        .perfil-info-title {
            font-size: 14px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .perfil-info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px;
            background-color: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 8px;
        }

        .perfil-info-label {
            font-weight: 500;
            color: #475569;
        }

        .perfil-info-value {
            color: #1e293b;
            font-weight: 600;
        }

        .perfil-avaliacao {
            background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 8px;
        }

        .perfil-stars {
            font-size: 18px;
            color: #ffc107;
        }

        .perfil-transporte {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }

        .perfil-transporte-icon {
            font-size: 24px;
        }

        .perfil-transporte-info {
            flex: 1;
        }

        .perfil-transporte-label {
            font-size: 12px;
            color: #64748b;
        }

        .perfil-transporte-valor {
            font-weight: 600;
            color: #1e293b;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .btn-ver-perfil:hover {
            background-color: #2563eb;
            transform: translateY(-2px);
        }
    </style>

    <script>
        function abrirPerfilColetor(idColetor) {
            const modal = document.getElementById('modalPerfilColetor');
            const conteudo = document.getElementById('perfilColetorConteudo');
            
            modal.classList.add('show');

            fetch(`../api/get_perfil_coletor.php?id=${idColetor}`)
                .then(response => response.json())
                .then(data => {
                    if (data.sucesso) {
                        const coletor = data.coletor;
                        const estrelas = Array(5).fill('<i class="ri-star-fill"></i>').slice(0, Math.round(coletor.avaliacao_media)).join('');
                        const estrelasBrancas = Array(5 - Math.round(coletor.avaliacao_media)).fill('<i class="ri-star-line"></i>').join('');

                        const tipoTransporte = {
                            'carro': '🚗 Carro',
                            'moto': '🏍️ Motocicleta',
                            'bicicleta': '🚴 Bicicleta',
                            'van': '🚐 Van',
                            'caminhao': '🚚 Caminhão'
                        };

                        conteudo.innerHTML = `
                            <div class="perfil-coletor-conteudo">
                                <div class="perfil-header">
                                    <img src="${coletor.foto_url ? '../' + coletor.foto_url : '../img/avatar-default.png'}" alt="${coletor.nome_completo}" class="perfil-foto" onerror="this.src='../img/avatar-default.png'">
                                    <div class="perfil-nome">${coletor.nome_completo}</div>
                                    <div class="perfil-tipo">${coletor.tipo_coletor === 'pessoa_fisica' ? 'Pessoa Física' : 'Pessoa Jurídica'}</div>
                                </div>

                                <div class="perfil-info-section">
                                    <div class="perfil-info-title">
                                        <i class="ri-phone-line"></i> Contato
                                    </div>
                                    <div class="perfil-info-item">
                                        <span class="perfil-info-label">Telefone</span>
                                        <span class="perfil-info-value">${coletor.telefone}</span>
                                    </div>
                                    <div class="perfil-info-item">
                                        <span class="perfil-info-label">Email</span>
                                        <span class="perfil-info-value">${coletor.email}</span>
                                    </div>
                                </div>

                                <div class="perfil-info-section">
                                    <div class="perfil-info-title">
                                        <i class="ri-star-line"></i> Avaliação
                                    </div>
                                    <div class="perfil-avaliacao">
                                        <div class="perfil-stars">${estrelas}${estrelasBrancas}</div>
                                        <div style="margin-top: 8px; color: #333; font-weight: 600;">
                                            ${parseFloat(coletor.avaliacao_media).toFixed(1)} / 5.0 (${coletor.total_avaliacoes} avaliações)
                                        </div>
                                    </div>
                                </div>

                                <div class="perfil-info-section">
                                    <div class="perfil-info-title">
                                        <i class="ri-truck-line"></i> Meio de Transporte
                                    </div>
                                    <div class="perfil-transporte">
                                        <div class="perfil-transporte-icon">${tipoTransporte[coletor.meio_transporte]?.split(' ')[0] || '🚗'}</div>
                                        <div class="perfil-transporte-info">
                                            <div class="perfil-transporte-label">Transporta com</div>
                                            <div class="perfil-transporte-valor">${tipoTransporte[coletor.meio_transporte] || coletor.meio_transporte}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="perfil-info-section">
                                    <div class="perfil-info-title">
                                        <i class="ri-history-line"></i> Estatísticas
                                    </div>
                                    <div class="perfil-info-item">
                                        <span class="perfil-info-label">Total de Coletas</span>
                                        <span class="perfil-info-value">${coletor.coletas}</span>
                                    </div>
                                    <div class="perfil-info-item">
                                        <span class="perfil-info-label">Óleo Total Coletado</span>
                                        <span class="perfil-info-value">${parseFloat(coletor.total_oleo).toFixed(1)}L</span>
                                    </div>
                                </div>
                            </div>
                        `;
                    } else {
                        conteudo.innerHTML = `<div style="text-align: center; padding: 40px; color: #e74c3c;"><i class="ri-error-warning-line" style="font-size: 48px; display: block; margin-bottom: 15px;"></i><p>${data.mensagem}</p></div>`;
                    }
                })
                .catch(error => {
                    conteudo.innerHTML = `<div style="text-align: center; padding: 40px; color: #e74c3c;"><i class="ri-error-warning-line" style="font-size: 48px; display: block; margin-bottom: 15px;"></i><p>Erro ao carregar perfil</p></div>`;
                });
        }

        document.querySelector('.modal-perfil-close').addEventListener('click', () => {
            document.getElementById('modalPerfilColetor').classList.remove('show');
        });

        document.getElementById('modalPerfilColetor').addEventListener('click', (e) => {
            if (e.target === document.getElementById('modalPerfilColetor')) {
                document.getElementById('modalPerfilColetor').classList.remove('show');
            }
        });
    </script>

    <script src="../JS/home-gerador.js"></script>
    <script src="../JS/navbar.js"></script>
    <script src="../JS/acessibilidade.js"></script><script src="../JS/libras.js"></script>
</body>

</html>


