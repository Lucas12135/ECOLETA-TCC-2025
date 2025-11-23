<?php
session_start();
require_once '../BANCO/conexao.php';

// Verificar se o usuário está logado e é coletor
if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] !== 'coletor') {
    header('Location: ../index.php');
    exit;
}

$id_coletor = $_SESSION['id_usuario'];

// Processar ações (aceitar/rejeitar solicitação)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_coleta = filter_input(INPUT_POST, 'id_coleta', FILTER_VALIDATE_INT);
    $acao = filter_input(INPUT_POST, 'acao', FILTER_SANITIZE_STRING);

    if ($id_coleta && $acao) {
        try {
            if ($acao === 'aceitar') {
                // Atualizar a coleta com o coletor e mudar status
                $sql_update = "UPDATE coletas 
                              SET id_coletor = :id_coletor, status = 'agendada'
                              WHERE id = :id_coleta AND id_coletor IS NULL";
                
                $stmt_update = $conn->prepare($sql_update);
                $stmt_update->bindParam(':id_coleta', $id_coleta, PDO::PARAM_INT);
                $stmt_update->bindParam(':id_coletor', $id_coletor, PDO::PARAM_INT);
                
                if ($stmt_update->execute() && $stmt_update->rowCount() > 0) {
                    $mensagem_sucesso = "Solicitação aceita com sucesso!";
                } else {
                    $mensagem_erro = "Não foi possível aceitar a solicitação.";
                }
            } elseif ($acao === 'rejeitar') {
                // Apenas remover o coletor se ele tinha aceito
                $sql_update = "UPDATE coletas 
                              SET id_coletor = NULL, status = 'pendente'
                              WHERE id = :id_coleta AND id_coletor = :id_coletor";
                
                $stmt_update = $conn->prepare($sql_update);
                $stmt_update->bindParam(':id_coleta', $id_coleta, PDO::PARAM_INT);
                $stmt_update->bindParam(':id_coletor', $id_coletor, PDO::PARAM_INT);
                
                if ($stmt_update->execute() && $stmt_update->rowCount() > 0) {
                    $mensagem_sucesso = "Solicitação rejeitada.";
                } else {
                    $mensagem_erro = "Não foi possível rejeitar a solicitação.";
                }
            }
        } catch (Exception $e) {
            $mensagem_erro = "Erro ao processar: " . $e->getMessage();
        }
    }
}

// Buscar solicitações pendentes e aceitas
$sql_coletas = "SELECT 
                    c.id,
                    c.data_solicitacao,
                    c.data_coleta,
                    c.quantidade_oleo,
                    c.observacoes,
                    c.status,
                    c.tipo_coleta,
                    c.periodo,
                    c.cep,
                    c.rua,
                    c.numero,
                    c.complemento,
                    c.bairro,
                    c.cidade,
                    c.latitude,
                    c.longitude,
                    g.nome_completo as gerador_nome,
                    g.telefone as gerador_telefone,
                    g.email as gerador_email
                FROM coletas c
                LEFT JOIN geradores g ON c.id_gerador = g.id
                WHERE (c.tipo_coleta = 'automatico' OR (c.tipo_coleta = 'especifico' AND c.id_coletor IS NULL))
                AND c.status = 'pendente'
                ORDER BY c.data_solicitacao DESC";

$stmt_coletas = $conn->prepare($sql_coletas);
$stmt_coletas->execute();
$solicitacoes_pendentes = $stmt_coletas->fetchAll(PDO::FETCH_ASSOC);

// Buscar coletas aceitas pelo coletor
$sql_aceitas = "SELECT 
                    c.id,
                    c.data_solicitacao,
                    c.data_coleta,
                    c.quantidade_oleo,
                    c.observacoes,
                    c.status,
                    c.periodo,
                    c.cep,
                    c.rua,
                    c.numero,
                    c.complemento,
                    c.bairro,
                    c.cidade,
                    g.nome_completo as gerador_nome,
                    g.telefone as gerador_telefone,
                    g.email as gerador_email
                FROM coletas c
                LEFT JOIN geradores g ON c.id_gerador = g.id
                WHERE c.id_coletor = :id_coletor
                ORDER BY c.data_coleta ASC";

$stmt_aceitas = $conn->prepare($sql_aceitas);
$stmt_aceitas->bindParam(':id_coletor', $id_coletor, PDO::PARAM_INT);
$stmt_aceitas->execute();
$coletas_aceitas = $stmt_aceitas->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitações de Coleta - Ecoleta</title>
    <link rel="icon" href="../img/logo.png" type="image/png">
    <link rel="stylesheet" href="../CSS/navbar.css">
    <link rel="stylesheet" href="../CSS/libras.css">
    <link rel="stylesheet" href="../CSS/acessibilidade.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        :root {
            --cor-primaria: linear-gradient(to right, #223e2a, #2d5238, #386043, #386043, #386043);
            --cor-secondaria: #ffce46;
            --cor-quaternaria: #47704a;
            --cor-texto-primaria: #4c4c4c;
            --background: #f8f4e7;
            --verde-escuro: #223e2a;
            --cor-amarelo-escuro: #f59e0b;
            --cor-borda: #e0e0e0;
            --cor-branco: #fff;
            --cor-erro: #dc3545;
            --cor-sucesso: #28a745;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        body {
            background: var(--cor-primaria);
            color: var(--cor-texto-primaria);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            background-color: var(--background);
            padding: 2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .header h1 {
            color: var(--verde-escuro);
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .header p {
            color: #666;
            font-size: 1rem;
        }

        .message {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .message.sucesso {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }

        .message.erro {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }

        .section-title {
            font-size: 1.5rem;
            color: var(--verde-escuro);
            margin-bottom: 1.5rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .section-title i {
            font-size: 1.5rem;
        }

        .card {
            background-color: var(--background);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-left: 4px solid var(--cor-secondaria);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 1rem;
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--verde-escuro);
        }

        .status-badge {
            display: inline-block;
            padding: 0.35rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-pendente {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-agendada {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .status-concluida {
            background-color: #d4edda;
            color: #155724;
        }

        .card-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .info-item {
            display: flex;
            flex-direction: column;
        }

        .info-label {
            color: #666;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 0.35rem;
        }

        .info-value {
            color: var(--cor-texto-primaria);
            font-size: 0.95rem;
            font-weight: 500;
        }

        .endereco-box {
            background-color: var(--cor-branco);
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            border: 1px solid var(--cor-borda);
        }

        .endereco-box h4 {
            color: var(--verde-escuro);
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .endereco-box p {
            color: #666;
            font-size: 0.9rem;
            line-height: 1.5;
        }

        .gerador-info {
            background-color: var(--cor-branco);
            padding: 1rem;
            border-radius: 8px;
            border: 1px solid var(--cor-borda);
            margin-bottom: 1rem;
        }

        .gerador-info h4 {
            color: var(--verde-escuro);
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .gerador-contact {
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
            font-size: 0.9rem;
        }

        .gerador-contact a {
            color: var(--verde-escuro);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .gerador-contact a:hover {
            text-decoration: underline;
        }

        .card-actions {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }

        .btn {
            padding: 0.65rem 1.25rem;
            border: none;
            border-radius: 6px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background-color: var(--cor-secondaria);
            color: var(--verde-escuro);
        }

        .btn-primary:hover {
            background-color: #e6b83d;
            transform: translateY(-2px);
        }

        .btn-danger {
            background-color: var(--cor-erro);
            color: var(--cor-branco);
        }

        .btn-danger:hover {
            background-color: #c82333;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background-color: #e9ecef;
            color: var(--cor-texto-primaria);
        }

        .btn-secondary:hover {
            background-color: #dee2e6;
            transform: translateY(-2px);
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #999;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: var(--cor-borda);
        }

        .empty-state p {
            font-size: 1.1rem;
        }

        @media (max-width: 768px) {
            .header {
                padding: 1.5rem;
            }

            .header h1 {
                font-size: 1.5rem;
            }

            .card-content {
                grid-template-columns: 1fr;
            }

            .card-actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>

    <!-- VLibras -->
    <div vw class="enabled">
        <div vw-access-button class="active"></div>
        <div vw-plugin-wrapper>
            <div class="vw-plugin-top-wrapper"></div>
        </div>
    </div>
    <script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Solicitações de Coleta</h1>
            <p>Gerencie as solicitações de coleta de óleo disponíveis</p>
        </div>

        <?php if (isset($mensagem_sucesso)): ?>
        <div class="message sucesso">
            <i class="ri-check-circle-line"></i>
            <?php echo htmlspecialchars($mensagem_sucesso); ?>
        </div>
        <?php endif; ?>

        <?php if (isset($mensagem_erro)): ?>
        <div class="message erro">
            <i class="ri-alert-line"></i>
            <?php echo htmlspecialchars($mensagem_erro); ?>
        </div>
        <?php endif; ?>

        <!-- Solicitações Pendentes -->
        <div>
            <h2 class="section-title">
                <i class="ri-inbox-line"></i>
                Solicitações Disponíveis (<?php echo count($solicitacoes_pendentes); ?>)
            </h2>

            <?php if (count($solicitacoes_pendentes) > 0): ?>
                <?php foreach ($solicitacoes_pendentes as $coleta): ?>
                <div class="card">
                    <div class="card-header">
                        <div>
                            <div class="card-title">Coleta de <?php echo number_format($coleta['quantidade_oleo'] ?? 0, 1, ',', '.'); ?>L de Óleo</div>
                            <small style="color: #999;">Solicitado em <?php echo date('d/m/Y H:i', strtotime($coleta['data_solicitacao'])); ?></small>
                        </div>
                        <span class="status-badge status-<?php echo $coleta['status']; ?>">
                            <?php echo $coleta['status'] === 'pendente' ? 'Pendente' : 'Agendada'; ?>
                        </span>
                    </div>

                    <div class="card-content">
                        <div class="info-item">
                            <span class="info-label">Data da Coleta</span>
                            <span class="info-value"><?php echo date('d/m/Y', strtotime($coleta['data_coleta'])); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Período</span>
                            <span class="info-value"><?php echo $coleta['periodo'] === 'manha' ? 'Manhã (8h - 12h)' : 'Tarde (13h - 17h)'; ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Volume</span>
                            <span class="info-value"><?php echo number_format($coleta['quantidade_oleo'] ?? 0, 1, ',', '.'); ?> Litros</span>
                        </div>
                    </div>

                    <div class="endereco-box">
                        <h4><i class="ri-map-pin-line"></i> Local de Coleta</h4>
                        <p>
                            <?php 
                                $endereco = ($coleta['rua'] ?? 'Rua não informada') . ', ' . ($coleta['numero'] ?? 'S/N');
                                if (!empty($coleta['complemento'])) {
                                    $endereco .= ' (' . $coleta['complemento'] . ')';
                                }
                                $endereco .= '<br>' . ($coleta['bairro'] ?? 'Bairro não informado') . ' - ' . ($coleta['cidade'] ?? 'Cidade não informada') . '<br>CEP: ' . ($coleta['cep'] ?? 'Não informado');
                                echo $endereco;
                            ?>
                        </p>
                    </div>

                    <div class="gerador-info">
                        <h4><i class="ri-user-line"></i> Gerador</h4>
                        <p style="font-weight: 600; margin-bottom: 0.5rem;"><?php echo htmlspecialchars($coleta['gerador_nome'] ?? 'Não informado'); ?></p>
                        <div class="gerador-contact">
                            <?php if (!empty($coleta['gerador_telefone'])): ?>
                            <a href="tel:<?php echo preg_replace('/\D/', '', $coleta['gerador_telefone']); ?>">
                                <i class="ri-phone-line"></i>
                                <?php echo htmlspecialchars($coleta['gerador_telefone']); ?>
                            </a>
                            <?php endif; ?>
                            <?php if (!empty($coleta['gerador_email'])): ?>
                            <a href="mailto:<?php echo htmlspecialchars($coleta['gerador_email']); ?>">
                                <i class="ri-mail-line"></i>
                                <?php echo htmlspecialchars($coleta['gerador_email']); ?>
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if (!empty($coleta['observacoes'])): ?>
                    <div style="background-color: var(--cor-branco); padding: 1rem; border-radius: 8px; border: 1px solid var(--cor-borda); margin-bottom: 1rem;">
                        <h4 style="color: var(--verde-escuro); font-size: 0.9rem; font-weight: 600; margin-bottom: 0.5rem;">
                            <i class="ri-sticky-note-line"></i> Observações
                        </h4>
                        <p style="color: #666; font-size: 0.9rem;"><?php echo nl2br(htmlspecialchars($coleta['observacoes'])); ?></p>
                    </div>
                    <?php endif; ?>

                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="id_coleta" value="<?php echo $coleta['id']; ?>">
                        <input type="hidden" name="acao" value="aceitar">
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-check-line"></i>
                            Aceitar Solicitação
                        </button>
                    </form>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="ri-inbox-2-line"></i>
                    <p>Nenhuma solicitação disponível no momento</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Coletas Aceitas -->
        <div style="margin-top: 3rem;">
            <h2 class="section-title">
                <i class="ri-check-double-line"></i>
                Minhas Coletas Aceitas (<?php echo count($coletas_aceitas); ?>)
            </h2>

            <?php if (count($coletas_aceitas) > 0): ?>
                <?php foreach ($coletas_aceitas as $coleta): ?>
                <div class="card" style="border-left-color: var(--cor-sucesso);">
                    <div class="card-header">
                        <div>
                            <div class="card-title">Coleta de <?php echo number_format($coleta['quantidade_oleo'] ?? 0, 1, ',', '.'); ?>L de Óleo</div>
                            <small style="color: #999;">Aceita em <?php echo date('d/m/Y H:i', strtotime($coleta['data_solicitacao'])); ?></small>
                        </div>
                        <span class="status-badge status-<?php echo $coleta['status']; ?>">
                            <?php 
                                $status_label = [
                                    'agendada' => 'Agendada',
                                    'em_andamento' => 'Em Andamento',
                                    'concluida' => 'Concluída',
                                    'cancelada' => 'Cancelada'
                                ];
                                echo $status_label[$coleta['status']] ?? $coleta['status'];
                            ?>
                        </span>
                    </div>

                    <div class="card-content">
                        <div class="info-item">
                            <span class="info-label">Data da Coleta</span>
                            <span class="info-value"><?php echo date('d/m/Y', strtotime($coleta['data_coleta'])); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Período</span>
                            <span class="info-value"><?php echo $coleta['periodo'] === 'manha' ? 'Manhã (8h - 12h)' : 'Tarde (13h - 17h)'; ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Volume</span>
                            <span class="info-value"><?php echo number_format($coleta['quantidade_oleo'] ?? 0, 1, ',', '.'); ?> Litros</span>
                        </div>
                    </div>

                    <div class="endereco-box">
                        <h4><i class="ri-map-pin-line"></i> Local de Coleta</h4>
                        <p>
                            <?php 
                                $endereco = ($coleta['rua'] ?? 'Rua não informada') . ', ' . ($coleta['numero'] ?? 'S/N');
                                if (!empty($coleta['complemento'])) {
                                    $endereco .= ' (' . $coleta['complemento'] . ')';
                                }
                                $endereco .= '<br>' . ($coleta['bairro'] ?? 'Bairro não informado') . ' - ' . ($coleta['cidade'] ?? 'Cidade não informada') . '<br>CEP: ' . ($coleta['cep'] ?? 'Não informado');
                                echo $endereco;
                            ?>
                        </p>
                    </div>

                    <div class="gerador-info">
                        <h4><i class="ri-user-line"></i> Gerador</h4>
                        <p style="font-weight: 600; margin-bottom: 0.5rem;"><?php echo htmlspecialchars($coleta['gerador_nome'] ?? 'Não informado'); ?></p>
                        <div class="gerador-contact">
                            <?php if (!empty($coleta['gerador_telefone'])): ?>
                            <a href="tel:<?php echo preg_replace('/\D/', '', $coleta['gerador_telefone']); ?>">
                                <i class="ri-phone-line"></i>
                                <?php echo htmlspecialchars($coleta['gerador_telefone']); ?>
                            </a>
                            <?php endif; ?>
                            <?php if (!empty($coleta['gerador_email'])): ?>
                            <a href="mailto:<?php echo htmlspecialchars($coleta['gerador_email']); ?>">
                                <i class="ri-mail-line"></i>
                                <?php echo htmlspecialchars($coleta['gerador_email']); ?>
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if (!empty($coleta['observacoes'])): ?>
                    <div style="background-color: var(--cor-branco); padding: 1rem; border-radius: 8px; border: 1px solid var(--cor-borda); margin-bottom: 1rem;">
                        <h4 style="color: var(--verde-escuro); font-size: 0.9rem; font-weight: 600; margin-bottom: 0.5rem;">
                            <i class="ri-sticky-note-line"></i> Observações
                        </h4>
                        <p style="color: #666; font-size: 0.9rem;"><?php echo nl2br(htmlspecialchars($coleta['observacoes'])); ?></p>
                    </div>
                    <?php endif; ?>

                    <div class="card-actions">
                        <a href="detalhes-coleta.php?id=<?php echo $coleta['id']; ?>" class="btn btn-secondary">
                            <i class="ri-eye-line"></i>
                            Ver Detalhes
                        </a>
                        <?php if ($coleta['status'] !== 'concluida' && $coleta['status'] !== 'cancelada'): ?>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="id_coleta" value="<?php echo $coleta['id']; ?>">
                            <input type="hidden" name="acao" value="rejeitar">
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Tem certeza que deseja rejeitar esta coleta?');">
                                <i class="ri-close-line"></i>
                                Rejeitar
                            </button>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="ri-checkbox-blank-circle-line"></i>
                    <p>Você ainda não aceitou nenhuma coleta</p>
                </div>
            <?php endif; ?>
        </div>

        <div style="margin-top: 3rem; text-align: center;">
            <a href="home.php" class="btn btn-secondary" style="width: auto; margin: 0 auto;">
                <i class="ri-arrow-left-line"></i>
                Voltar para Home
            </a>
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
        <div class="libras-button" id="librasButton" onclick="toggleLibras(event)" title="Libras">
            👋
        </div>
    </div>

    <script src="../JS/acessibilidade.js"></script><script src="../JS/libras.js"></script>
</body>

</html>



