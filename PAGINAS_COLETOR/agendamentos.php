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

// Buscar solicitações pendentes para este coletor (coletas onde id_coletor é NULL ou igual ao id do coletor)
$solicitacoes_pendentes = [];
$agendamentos_aceitos = [];

try {
    if ($conn) {
        // Solicitações pendentes - coletas não aceitas por nenhum coletor específico ou para este coletor
        $stmt_pendentes = $conn->prepare("
            SELECT c.*, g.nome_completo, g.telefone, g.email
            FROM coletas c
            JOIN geradores g ON c.id_gerador = g.id
            WHERE (c.id_coletor IS NULL OR c.id_coletor = :id_coletor) 
            AND c.status IN ('solicitada', 'pendente')
            ORDER BY c.data_solicitacao DESC
        ");
        $stmt_pendentes->bindParam(':id_coletor', $_SESSION['id_usuario']);
        $stmt_pendentes->execute();
        $solicitacoes_pendentes = $stmt_pendentes->fetchAll(PDO::FETCH_ASSOC);

        // Agendamentos aceitos por este coletor
        $stmt_aceitos = $conn->prepare("
            SELECT c.*, g.nome_completo, g.telefone, g.email
            FROM coletas c
            JOIN geradores g ON c.id_gerador = g.id
            WHERE c.id_coletor = :id_coletor 
            AND c.status IN ('agendada', 'em_andamento')
            ORDER BY c.data_agendada ASC
        ");
        $stmt_aceitos->bindParam(':id_coletor', $_SESSION['id_usuario']);
        $stmt_aceitos->execute();
        $agendamentos_aceitos = $stmt_aceitos->fetchAll(PDO::FETCH_ASSOC);
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
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendamentos - Coletor</title>
    <link rel="icon" href="../img/logo.png" type="image/png">
    <link rel="stylesheet" href="../CSS/coletor-agendamentos.css">
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
                    <li class="active">
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
            <header class="content-header">
                <div class="welcome-message">
                    <h1>Página de agendamentos</h1>
                    <p>Gerencie suas solicitações e agendamentos de coleta</p>
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
            <div class="agendamento-content">
                <!-- Seção de Solicitações Pendentes -->
                <div class="section-agendamentos">
                    <div class="section-header">
                        <h2>
                            <i class="ri-time-line"></i>
                            Solicitações Pendentes
                        </h2>
                    </div>
                    <div class="agendamento-list">
                        <?php if (empty($solicitacoes_pendentes)): ?>
                            <div style="text-align: center; padding: 40px; color: #999;">
                                <i class="ri-inbox-line" style="font-size: 48px; margin-bottom: 10px;"></i>
                                <p>Nenhuma solicitação pendente no momento</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($solicitacoes_pendentes as $coleta): ?>
                                <div class="agendamento-item">
                                    <div class="agendamento-header">
                                        <div class="agendamento-info">
                                            <span class="agendamento-id">#<?php echo $coleta['id']; ?></span>
                                            <span class="agendamento-quantidade">
                                                <i class="ri-oil-line"></i>
                                                <?php echo $coleta['quantidade_oleo']; ?> litros
                                            </span>
                                            <span class="agendamento-data">Solicitado em: <?php echo formatarData($coleta['data_solicitacao']); ?></span>
                                            <span class="agendamento-solicitante"><?php echo htmlspecialchars($coleta['nome_completo']); ?></span>
                                        </div>
                                        <div class="agendamento-actions">
                                            <button class="btn-aceitar" data-coleta-id="<?php echo $coleta['id']; ?>">
                                                <i class="ri-check-line"></i>
                                                Aceitar
                                            </button>
                                            <button class="btn-recusar" data-coleta-id="<?php echo $coleta['id']; ?>">
                                                <i class="ri-close-line"></i>
                                                Recusar
                                            </button>
                                            <button class="btn-ver-mapa" title="Ver no Mapa">
                                                <i class="ri-map-pin-line"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="agendamento-details">
                                        <div class="details-grid">
                                            <div class="detail-group">
                                                <span class="detail-label">Endereço</span>
                                                <span class="detail-value"><?php echo htmlspecialchars($coleta['rua']); ?>, <?php echo htmlspecialchars($coleta['numero']); ?> - <?php echo htmlspecialchars($coleta['bairro']); ?></span>
                                            </div>
                                            <div class="detail-group">
                                                <span class="detail-label">Complemento</span>
                                                <span class="detail-value"><?php echo htmlspecialchars($coleta['complemento'] ?? 'N/A'); ?></span>
                                            </div>
                                            <div class="detail-group">
                                                <span class="detail-label">Cidade/UF</span>
                                                <span class="detail-value"><?php echo htmlspecialchars($coleta['cidade']); ?> - <?php echo htmlspecialchars($coleta['estado']); ?></span>
                                            </div>
                                            <div class="detail-group">
                                                <span class="detail-label">CEP</span>
                                                <span class="detail-value"><?php echo htmlspecialchars($coleta['cep']); ?></span>
                                            </div>
                                            <div class="detail-group">
                                                <span class="detail-label">Telefone</span>
                                                <span class="detail-value"><?php echo htmlspecialchars($coleta['telefone']); ?></span>
                                            </div>
                                            <div class="detail-group">
                                                <span class="detail-label">Email</span>
                                                <span class="detail-value"><?php echo htmlspecialchars($coleta['email']); ?></span>
                                            </div>
                                            <div class="detail-group">
                                                <span class="detail-label">Data Preferencial</span>
                                                <span class="detail-value"><?php echo formatarData($coleta['data_agendada']); ?></span>
                                            </div>
                                            <div class="detail-group">
                                                <span class="detail-label">Período</span>
                                                <span class="detail-value"><?php echo formatarPeriodo($coleta['periodo']); ?></span>
                                            </div>
                                            <?php if ($coleta['observacoes']): ?>
                                                <div class="detail-group" style="grid-column: 1 / -1;">
                                                    <span class="detail-label">Observações</span>
                                                    <span class="detail-value"><?php echo htmlspecialchars($coleta['observacoes']); ?></span>
                                                </div>
                                            <?php endif; ?>
                                            <div class="map-container" id="map-<?php echo $coleta['id']; ?>" data-lat="" data-lng="">
                                                <!-- Mapa será carregado aqui -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Seção de Agendamentos Aceitos -->
                <div class="section-agendamentos">
                    <div class="section-header">
                        <h2>
                            <i class="ri-calendar-check-line"></i>
                            Agendamentos Aceitos
                        </h2>
                    </div>
                    <div class="agendamento-list">
                        <?php if (empty($agendamentos_aceitos)): ?>
                            <div style="text-align: center; padding: 40px; color: #999;">
                                <i class="ri-calendar-line" style="font-size: 48px; margin-bottom: 10px;"></i>
                                <p>Nenhum agendamento aceito no momento</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($agendamentos_aceitos as $coleta): ?>
                                <div class="agendamento-item">
                                    <div class="agendamento-header">
                                        <div class="agendamento-info">
                                            <span class="agendamento-id">#<?php echo $coleta['id']; ?></span>
                                            <span class="agendamento-quantidade">
                                                <i class="ri-oil-line"></i>
                                                <?php echo $coleta['quantidade_oleo']; ?> litros
                                            </span>
                                            <span class="agendamento-data">
                                                Solicitado em: <?php echo formatarData($coleta['data_solicitacao']); ?><br>
                                                Agendado para: <?php echo formatarData($coleta['data_agendada']); ?>
                                            </span>
                                            <span class="agendamento-solicitante"><?php echo htmlspecialchars($coleta['nome_completo']); ?></span>
                                        </div>
                                        <div class="agendamento-actions">
                                            <button class="btn-concluir" data-coleta-id="<?php echo $coleta['id']; ?>" data-quantidade="<?php echo $coleta['quantidade_oleo']; ?>">
                                                <i class="ri-check-double-line"></i>
                                                Concluir
                                            </button>
                                            <button class="btn-cancelar" data-coleta-id="<?php echo $coleta['id']; ?>">
                                                <i class="ri-close-line"></i>
                                                Cancelar
                                            </button>
                                            <button class="btn-ver-mapa" title="Ver no Mapa">
                                                <i class="ri-map-pin-line"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="agendamento-details">
                                        <div class="details-grid">
                                            <div class="detail-group">
                                                <span class="detail-label">Endereço</span>
                                                <span class="detail-value"><?php echo htmlspecialchars($coleta['rua']); ?>, <?php echo htmlspecialchars($coleta['numero']); ?> - <?php echo htmlspecialchars($coleta['bairro']); ?></span>
                                            </div>
                                            <div class="detail-group">
                                                <span class="detail-label">Complemento</span>
                                                <span class="detail-value"><?php echo htmlspecialchars($coleta['complemento'] ?? 'N/A'); ?></span>
                                            </div>
                                            <div class="detail-group">
                                                <span class="detail-label">Cidade/UF</span>
                                                <span class="detail-value"><?php echo htmlspecialchars($coleta['cidade']); ?> - <?php echo htmlspecialchars($coleta['estado']); ?></span>
                                            </div>
                                            <div class="detail-group">
                                                <span class="detail-label">CEP</span>
                                                <span class="detail-value"><?php echo htmlspecialchars($coleta['cep']); ?></span>
                                            </div>
                                            <div class="detail-group">
                                                <span class="detail-label">Período</span>
                                                <span class="detail-value"><?php echo formatarPeriodo($coleta['periodo']); ?></span>
                                            </div>
                                            <div class="detail-group">
                                                <span class="detail-label">Telefone</span>
                                                <span class="detail-value"><?php echo htmlspecialchars($coleta['telefone']); ?></span>
                                            </div>
                                            <?php if ($coleta['observacoes']): ?>
                                                <div class="detail-group" style="grid-column: 1 / -1;">
                                                    <span class="detail-label">Observações</span>
                                                    <span class="detail-value"><?php echo htmlspecialchars($coleta['observacoes']); ?></span>
                                                </div>
                                            <?php endif; ?>
                                            <div class="map-container" id="map-<?php echo $coleta['id']; ?>" data-lat="" data-lng="">
                                                <!-- Mapa será carregado aqui -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAe884hZ7UbSCJDuS4hkEWrR-ls0XVBe_U"></script>
    <script src="../JS/agendamentos.js"></script>
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

    <script src="../JS/libras.js"></script>   
    <script src="../JS/navbar.js"></script>
    
    <!-- Modal para Concluir Coleta -->
    <div id="modalConcluir" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Concluir Coleta</h2>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <form id="formConcluirColeta">
                    <div class="form-group">
                        <label for="coleta_id_input">ID da Coleta</label>
                        <input type="text" id="coleta_id_input" disabled>
                    </div>
                    
                    <div class="form-group">
                        <label for="quantidade_coletada">Quantidade de Óleo Coletada (litros)</label>
                        <input type="number" id="quantidade_coletada" name="quantidade_coletada" min="0" step="0.5" required placeholder="Digite a quantidade coletada" max="999">
                    </div>
                    
                    <div class="form-group">
                        <label for="observacoes_coleta">Observações da Coleta (opcional)</label>
                        <textarea id="observacoes_coleta" name="observacoes_coleta" rows="3" placeholder="Ex.: Local de armazenamento, condições do óleo, etc."></textarea>
                    </div>
                    
                    <input type="hidden" id="hidden_coleta_id" name="id_coleta">
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" id="btnCancelarModal">Cancelar</button>
                <button class="btn btn-primary" id="btnConfirmarConclusao">Concluir Coleta</button>
            </div>
        </div>
    </div>

    <!-- Modal para Confirmação Geral -->
    <div id="modalConfirmacao" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalConfirmacaoTitulo">Confirmação</h2>
                <button class="modal-close" data-modal="modalConfirmacao">&times;</button>
            </div>
            <div class="modal-body">
                <p id="modalConfirmacaoMensagem"></p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" id="btnCancelarConfirmacao">Cancelar</button>
                <button class="btn btn-primary" id="btnConfirmarAcao">Confirmar</button>
            </div>
        </div>
    </div>

    <!-- Modal para Sucesso/Erro -->
    <div id="modalResultado" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalResultadoTitulo">Sucesso</h2>
                <button class="modal-close" data-modal="modalResultado">&times;</button>
            </div>
            <div class="modal-body">
                <div id="modalResultadoConteudo"></div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="btnFecharResultado">Fechar</button>
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
            animation: fadeIn 0.3s ease;
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
            max-width: 500px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
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
            transition: color 0.3s;
        }

        .modal-close:hover {
            color: #333;
        }

        .modal-body {
            padding: 20px;
        }

        .modal-body .form-group {
            margin-bottom: 15px;
        }

        .modal-body label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
            font-size: 14px;
        }

        .modal-body input[type="text"],
        .modal-body input[type="number"],
        .modal-body textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-family: inherit;
            font-size: 14px;
            box-sizing: border-box;
        }

        .modal-body input[type="text"]:disabled {
            background-color: #f5f5f5;
            cursor: not-allowed;
        }

        .modal-body input[type="text"]:focus,
        .modal-body input[type="number"]:focus,
        .modal-body textarea:focus {
            outline: none;
            border-color: #4CAF50;
            box-shadow: 0 0 5px rgba(76, 175, 80, 0.3);
        }

        .modal-body p {
            margin: 0;
            color: #333;
            line-height: 1.6;
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

        .modal-resultado-sucesso {
            background-color: #f1f8f4;
            border-left: 4px solid #4CAF50;
            padding: 15px;
            border-radius: 5px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .modal-resultado-sucesso i {
            color: #4CAF50;
            font-size: 24px;
        }

        .modal-resultado-erro {
            background-color: #fef1f1;
            border-left: 4px solid #f44336;
            padding: 15px;
            border-radius: 5px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .modal-resultado-erro i {
            color: #f44336;
            font-size: 24px;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
    </style>
</body>

</html>