<?php
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] !== 'gerador') {
    header('Location: ../index.php');
    exit;
}

// Definir fuso horário de Brasília
date_default_timezone_set('America/Sao_Paulo');

include_once('../BANCO/conexao.php');

// Buscar histórico de coletas do gerador
$historico_coletas = [];

try {
    if ($conn) {
        $stmt_historico = $conn->prepare("
            SELECT 
                c.id as coleta_id,
                c.status,
                c.quantidade_oleo,
                c.data_solicitacao,
                c.data_agendada,
                c.periodo,
                c.rua,
                c.numero,
                c.bairro,
                c.cidade,
                col.id as id_coletor,
                col.nome_completo as nome_coletor,
                col.telefone as telefone_coletor,
                col.email as email_coletor,
                hc.id as id_historico,
                hc.data_conclusao,
                hc.quantidade_coletada,
                ac.id as id_avaliacao,
                ac.nota,
                ac.comentario,
                ac.pontualidade,
                ac.profissionalismo,
                ac.qualidade_servico
            FROM coletas c
            LEFT JOIN coletores col ON c.id_coletor = col.id
            LEFT JOIN historico_coletas hc ON c.id = hc.id_coleta
            LEFT JOIN avaliacoes_coletores ac ON hc.id = ac.id_historico_coleta
            WHERE c.id_gerador = :id_gerador
            ORDER BY c.data_solicitacao DESC
        ");
        $stmt_historico->bindParam(':id_gerador', $_SESSION['id_usuario']);
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

// Função auxiliar para formatar período
function formatarPeriodo($periodo)
{
    $periodos = [
        'manha' => 'Manhã (8h - 12h)',
        'tarde' => 'Tarde (13h - 17h)'
    ];
    return $periodos[$periodo] ?? $periodo;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico - Gerador</title>
    <link rel="icon" href="../img/logo.png" type="image/png">
    <link rel="stylesheet" href="../CSS/gerador-historico.css">
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
                        <a href="solicitar_coleta.php" class="nav-link">
                            <i class="ri-add-circle-line"></i>
                            <span>Solicitar Coleta</span>
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
                    <h1>Histórico de Solicitações</h1>
                    <p>Acompanhe suas solicitações de coleta</p>
                </div>
                <div class="header-actions">
                </div>
            </header>

            <!-- Lista de Histórico -->
            <div class="history-list">
                <?php if (empty($historico_coletas)): ?>
                    <div style="text-align: center; padding: 60px 20px; color: #999;">
                        <i class="ri-history-line" style="font-size: 64px; margin-bottom: 20px; display: block;"></i>
                        <h3>Nenhuma solicitação realizada ainda</h3>
                        <p>Suas solicitações de coleta aparecerão aqui</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($historico_coletas as $coleta): ?>
                        <div class="history-item" data-coleta-id="<?php echo $coleta['coleta_id']; ?>">
                            <div class="history-item-header">
                                <div class="history-main-info">
                                    <span class="collection-id">ID: #<?php echo $coleta['coleta_id']; ?></span>
                                    <span class="collection-quantity">
                                        <i class="ri-oil-line"></i>
                                        <?php echo number_format($coleta['quantidade_oleo'], 2); ?> litros
                                    </span>
                                    <span class="collection-date"><?php echo formatarData($coleta['data_solicitacao']); ?></span>
                                </div>
                                <div class="history-actions">
                                    <span class="collection-status status-<?php echo $coleta['status']; ?>">
                                        <?php
                                        $status_labels = [
                                            'solicitada' => 'Solicitada',
                                            'pendente' => 'Pendente',
                                            'agendada' => 'Agendada',
                                            'em_andamento' => 'Em Andamento',
                                            'concluida' => 'Concluída',
                                            'cancelada' => 'Cancelada'
                                        ];
                                        echo $status_labels[$coleta['status']] ?? $coleta['status'];
                                        ?>
                                    </span>
                                    <button class="expand-button">
                                        <i class="ri-arrow-down-s-line"></i>
                                        Detalhes
                                    </button>
                                </div>
                            </div>
                            <div class="history-details">
                                <div class="detail-row">
                                    <span class="detail-label">Coletor:</span>
                                    <span class="detail-value">
                                        <?php
                                        if ($coleta['nome_coletor']) {
                                            echo htmlspecialchars($coleta['nome_coletor']);
                                        } else {
                                            echo 'Aguardando confirmação';
                                        }
                                        ?>
                                    </span>
                                </div>
                                <?php if ($coleta['telefone_coletor']): ?>
                                    <div class="detail-row">
                                        <span class="detail-label">Contato:</span>
                                        <span class="detail-value">
                                            <?php echo htmlspecialchars($coleta['telefone_coletor']); ?> / <?php echo htmlspecialchars($coleta['email_coletor']); ?>
                                        </span>
                                    </div>
                                <?php endif; ?>
                                <div class="detail-row">
                                    <span class="detail-label">Local de Coleta:</span>
                                    <span class="detail-value">
                                        <?php echo htmlspecialchars($coleta['rua']); ?>, <?php echo htmlspecialchars($coleta['numero']); ?> - <?php echo htmlspecialchars($coleta['bairro']); ?>, <?php echo htmlspecialchars($coleta['cidade']); ?>
                                    </span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Data Preferencial:</span>
                                    <span class="detail-value">
                                        <?php echo formatarData($coleta['data_agendada']); ?> - <?php echo formatarPeriodo($coleta['periodo']); ?>
                                    </span>
                                </div>

                                <?php if ($coleta['status'] === 'concluida' && $coleta['id_historico']): ?>
                                    <div style="border-top: 1px solid #e0e0e0; margin-top: 15px; padding-top: 15px;">
                                        <h4 style="margin: 0 0 10px 0; color: #333;">Detalhes da Coleta Realizada:</h4>
                                        <div class="detail-row">
                                            <span class="detail-label">Data da Conclusão:</span>
                                            <span class="detail-value"><?php echo formatarData($coleta['data_conclusao']); ?></span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label">Quantidade Coletada:</span>
                                            <span class="detail-value"><?php echo number_format($coleta['quantidade_coletada'], 2); ?> litros</span>
                                        </div>

                                        <?php if ($coleta['id_avaliacao']): ?>
                                            <div style="border-top: 1px solid #f0f0f0; margin-top: 15px; padding-top: 15px;">
                                                <h4 style="margin: 0 0 10px 0; color: #333;">Sua Avaliação:</h4>
                                                <div class="detail-row">
                                                    <span class="detail-label">Nota Geral:</span>
                                                    <span class="detail-value">
                                                        <?php
                                                        for ($i = 0; $i < $coleta['nota']; $i++) {
                                                            echo '<i class="ri-star-fill" style="color: #FFB800;"></i>';
                                                        }
                                                        for ($i = $coleta['nota']; $i < 5; $i++) {
                                                            echo '<i class="ri-star-line" style="color: #ddd;"></i>';
                                                        }
                                                        ?>
                                                    </span>
                                                </div>
                                                <div class="detail-row">
                                                    <span class="detail-label">Comentário:</span>
                                                    <span class="detail-value"><?php echo htmlspecialchars($coleta['comentario']); ?></span>
                                                </div>
                                                <button class="btn-editar-avaliacao" data-historico-id="<?php echo $coleta['id_historico']; ?>" data-coletor-id="<?php echo $coleta['id_coletor']; ?>">
                                                    <i class="ri-edit-line"></i>
                                                    Editar Avaliação
                                                </button>
                                            </div>
                                        <?php else: ?>
                                            <button class="btn-avaliar" data-historico-id="<?php echo $coleta['id_historico']; ?>" data-coletor-id="<?php echo $coleta['id_coletor']; ?>" data-coletor-nome="<?php echo htmlspecialchars($coleta['nome_coletor']); ?>">
                                                <i class="ri-star-line"></i>
                                                Avaliar Coletor
                                            </button>
                                        <?php endif; ?>
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

        <!-- Modal de Avaliação -->
        <div id="modalAvaliacao" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Avaliar Coletor</h2>
                    <button class="modal-close">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="coletor-info">
                        <p><strong>Coletor:</strong> <span id="nome-coletor-avaliacao">-</span></p>
                    </div>

                    <form id="formAvaliacao">
                        <div class="form-group">
                            <label>Avaliação Geral:</label>
                            <div class="star-rating" id="star-rating">
                                <i class="ri-star-line star" data-value="1"></i>
                                <i class="ri-star-line star" data-value="2"></i>
                                <i class="ri-star-line star" data-value="3"></i>
                                <i class="ri-star-line star" data-value="4"></i>
                                <i class="ri-star-line star" data-value="5"></i>
                            </div>
                            <input type="hidden" id="nota" name="nota" value="0">
                        </div>

                        <div class="form-group">
                            <label for="pontualidade">Pontualidade:</label>
                            <div class="rating-scale">
                                <label class="radio-option">
                                    <input type="radio" name="pontualidade" value="1"> Muito Ruim
                                </label>
                                <label class="radio-option">
                                    <input type="radio" name="pontualidade" value="2"> Ruim
                                </label>
                                <label class="radio-option">
                                    <input type="radio" name="pontualidade" value="3"> Neutro
                                </label>
                                <label class="radio-option">
                                    <input type="radio" name="pontualidade" value="4"> Bom
                                </label>
                                <label class="radio-option">
                                    <input type="radio" name="pontualidade" value="5"> Excelente
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="profissionalismo">Profissionalismo:</label>
                            <div class="rating-scale">
                                <label class="radio-option">
                                    <input type="radio" name="profissionalismo" value="1"> Muito Ruim
                                </label>
                                <label class="radio-option">
                                    <input type="radio" name="profissionalismo" value="2"> Ruim
                                </label>
                                <label class="radio-option">
                                    <input type="radio" name="profissionalismo" value="3"> Neutro
                                </label>
                                <label class="radio-option">
                                    <input type="radio" name="profissionalismo" value="4"> Bom
                                </label>
                                <label class="radio-option">
                                    <input type="radio" name="profissionalismo" value="5"> Excelente
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="qualidade_servico">Qualidade do Serviço:</label>
                            <div class="rating-scale">
                                <label class="radio-option">
                                    <input type="radio" name="qualidade_servico" value="1"> Muito Ruim
                                </label>
                                <label class="radio-option">
                                    <input type="radio" name="qualidade_servico" value="2"> Ruim
                                </label>
                                <label class="radio-option">
                                    <input type="radio" name="qualidade_servico" value="3"> Neutro
                                </label>
                                <label class="radio-option">
                                    <input type="radio" name="qualidade_servico" value="4"> Bom
                                </label>
                                <label class="radio-option">
                                    <input type="radio" name="qualidade_servico" value="5"> Excelente
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="comentario">Comentário (opcional):</label>
                            <textarea id="comentario" name="comentario" rows="4" placeholder="Deixe seu comentário sobre a coleta..."></textarea>
                        </div>

                        <input type="hidden" id="id_historico" name="id_historico">
                        <input type="hidden" id="id_coletor" name="id_coletor">
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" id="btnCancelarAvaliacao">Cancelar</button>
                    <button class="btn btn-primary" id="btnConfirmarAvaliacao">Enviar Avaliação</button>
                </div>
            </div>
        </div>

        <style>
            .modal {
                display: none;
                position: fixed;
                z-index: 1000;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.4);
            }

            .modal.show {
                display: flex;
                justify-content: center;
                align-items: center;
            }

            .modal-content {
                background-color: #fefefe;
                padding: 0;
                border-radius: 10px;
                width: 90%;
                max-width: 600px;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
                max-height: 90vh;
                overflow-y: auto;
            }

            .modal-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 20px;
                border-bottom: 1px solid #e0e0e0;
            }

            .modal-header h2 {
                margin: 0;
                font-size: 20px;
                color: #333;
            }

            .modal-close {
                background: none;
                border: none;
                font-size: 28px;
                cursor: pointer;
                color: #999;
            }

            .modal-body {
                padding: 20px;
            }

            .coletor-info {
                background-color: #f9f9f9;
                padding: 15px;
                border-radius: 5px;
                margin-bottom: 20px;
            }

            .form-group {
                margin-bottom: 20px;
            }

            .form-group label {
                display: block;
                margin-bottom: 10px;
                font-weight: 600;
                color: #333;
            }

            .star-rating {
                display: flex;
                gap: 10px;
                margin-bottom: 10px;
            }

            .star-rating .star {
                font-size: 30px;
                cursor: pointer;
                color: #ddd;
                transition: color 0.2s;
            }

            .star-rating .star:hover,
            .star-rating .star.active {
                color: #FFB800;
            }

            .rating-scale {
                display: flex;
                flex-direction: column;
                gap: 8px;
            }

            .radio-option {
                display: flex;
                align-items: center;
                padding: 8px;
                cursor: pointer;
                border-radius: 5px;
                transition: background-color 0.2s;
            }

            .radio-option:hover {
                background-color: #f0f0f0;
            }

            .radio-option input[type="radio"] {
                margin-right: 10px;
            }

            .form-group textarea {
                width: 100%;
                padding: 10px;
                border: 1px solid #ddd;
                border-radius: 5px;
                font-family: inherit;
                box-sizing: border-box;
            }

            .form-group textarea:focus {
                outline: none;
                border-color: #4CAF50;
                box-shadow: 0 0 5px rgba(76, 175, 80, 0.3);
            }

            .modal-footer {
                padding: 20px;
                border-top: 1px solid #e0e0e0;
                display: flex;
                gap: 10px;
                justify-content: flex-end;
            }

            .modal-footer .btn {
                padding: 10px 20px;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                font-size: 14px;
                transition: all 0.3s;
            }

            .modal-footer .btn-secondary {
                background-color: #f0f0f0;
                color: #333;
            }

            .modal-footer .btn-secondary:hover {
                background-color: #e0e0e0;
            }

            .modal-footer .btn-primary {
                background-color: #4CAF50;
                color: white;
            }

            .modal-footer .btn-primary:hover {
                background-color: #45a049;
            }

            .btn-avaliar,
            .btn-editar-avaliacao {
                background-color: #4CAF50;
                color: white;
                border: none;
                padding: 10px 15px;
                border-radius: 5px;
                cursor: pointer;
                font-weight: 600;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                transition: all 0.3s;
                margin-top: 10px;
            }

            .btn-avaliar:hover,
            .btn-editar-avaliacao:hover {
                background-color: #45a049;
                transform: translateY(-2px);
            }
        </style>

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

                // Modal de avaliação
                const modal = document.getElementById('modalAvaliacao');
                const starRating = document.getElementById('star-rating');
                const notaInput = document.getElementById('nota');
                const stars = starRating.querySelectorAll('.star');

                stars.forEach(star => {
                    star.addEventListener('click', () => {
                        const value = star.getAttribute('data-value');
                        notaInput.value = value;

                        stars.forEach(s => {
                            s.classList.remove('active');
                            if (s.getAttribute('data-value') <= value) {
                                s.classList.add('active');
                            }
                        });
                    });

                    star.addEventListener('mouseover', () => {
                        const value = star.getAttribute('data-value');
                        stars.forEach(s => {
                            s.style.color = s.getAttribute('data-value') <= value ? '#FFB800' : '#ddd';
                        });
                    });
                });

                starRating.addEventListener('mouseleave', () => {
                    stars.forEach(s => {
                        s.style.color = s.classList.contains('active') ? '#FFB800' : '#ddd';
                    });
                });

                // Abrir modal de avaliação (novo)
                document.querySelectorAll('.btn-avaliar').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        e.preventDefault();
                        const historicoId = btn.getAttribute('data-historico-id');
                        const coletorId = btn.getAttribute('data-coletor-id');
                        const coletorNome = btn.getAttribute('data-coletor-nome');

                        document.getElementById('id_historico').value = historicoId;
                        document.getElementById('id_coletor').value = coletorId;
                        document.getElementById('nome-coletor-avaliacao').textContent = coletorNome;

                        // Limpar formulário
                        document.getElementById('formAvaliacao').reset();
                        notaInput.value = 0;
                        stars.forEach(s => s.classList.remove('active'));

                        modal.classList.add('show');
                    });
                });

                // Abrir modal de avaliação (editar existente)
                document.querySelectorAll('.btn-editar-avaliacao').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        e.preventDefault();
                        const historicoId = btn.getAttribute('data-historico-id');
                        const coletorId = btn.getAttribute('data-coletor-id');
                        const coletorNome = btn.parentElement.parentElement.parentElement.querySelector('.detail-label + .detail-value')?.textContent || 'Coletor';

                        // Buscar dados da avaliação existente
                        fetch('../api/buscar_avaliacao.php?id_historico=' + historicoId)
                            .then(response => response.json())
                            .then(data => {
                                if (data.success && data.avaliacao) {
                                    const av = data.avaliacao;

                                    // Preencher form com dados existentes
                                    document.getElementById('id_historico').value = historicoId;
                                    document.getElementById('id_coletor').value = coletorId;
                                    document.getElementById('nome-coletor-avaliacao').textContent = coletorNome;

                                    // Restaurar nota
                                    notaInput.value = av.nota;
                                    stars.forEach(s => {
                                        s.classList.remove('active');
                                        if (s.getAttribute('data-value') <= av.nota) {
                                            s.classList.add('active');
                                        }
                                    });

                                    // Restaurar outros campos
                                    document.querySelector('input[name="pontualidade"][value="' + av.pontualidade + '"]').checked = true;
                                    document.querySelector('input[name="profissionalismo"][value="' + av.profissionalismo + '"]').checked = true;
                                    document.querySelector('input[name="qualidade_servico"][value="' + av.qualidade_servico + '"]').checked = true;
                                    document.getElementById('comentario').value = av.comentario || '';

                                    modal.classList.add('show');
                                }
                            })
                            .catch(error => {
                                console.error('Erro ao buscar avaliação:', error);
                                alert('Erro ao carregar avaliação');
                            });
                    });
                });

                // Fechar modal
                document.querySelector('.modal-close').addEventListener('click', () => {
                    modal.classList.remove('show');
                });

                document.getElementById('btnCancelarAvaliacao').addEventListener('click', () => {
                    modal.classList.remove('show');
                });

                modal.addEventListener('click', (e) => {
                    if (e.target === modal) {
                        modal.classList.remove('show');
                    }
                });

                // Enviar avaliação
                document.getElementById('btnConfirmarAvaliacao').addEventListener('click', () => {
                    const nota = notaInput.value;
                    const pontualidade = document.querySelector('input[name="pontualidade"]:checked')?.value;
                    const profissionalismo = document.querySelector('input[name="profissionalismo"]:checked')?.value;
                    const qualidadeServico = document.querySelector('input[name="qualidade_servico"]:checked')?.value;
                    const comentario = document.getElementById('comentario').value;
                    const historicoId = document.getElementById('id_historico').value;
                    const coletorId = document.getElementById('id_coletor').value;

                    if (!nota || nota == 0) {
                        alert('Por favor, selecione uma avaliação geral');
                        return;
                    }

                    if (!pontualidade || !profissionalismo || !qualidadeServico) {
                        alert('Por favor, complete todos os critérios de avaliação');
                        return;
                    }

                    fetch('../api/avaliar_coletor.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                id_historico: historicoId,
                                id_coletor: coletorId,
                                nota: nota,
                                pontualidade: pontualidade,
                                profissionalismo: profissionalismo,
                                qualidade_servico: qualidadeServico,
                                comentario: comentario
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Avaliação enviada com sucesso!');
                                modal.classList.remove('show');
                                location.reload();
                            } else {
                                alert('Erro ao enviar avaliação: ' + (data.message || 'Tente novamente'));
                            }
                        })
                        .catch(error => {
                            console.error('Erro:', error);
                            alert('Erro ao processar a solicitação');
                        });
                });

            });
        </script>
        <script src="../JS/navbar.js"></script>
        <script src="../JS/acessibilidade.js"></script><script src="../JS/libras.js"></script>
</body>

</html>


