<?php
session_start();

// Verificar se o usuário está logado como admin
if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header('Location: ../logins.php');
    exit;
}

// Definir fuso horário
date_default_timezone_set('America/Sao_Paulo');

// Incluir conexão com banco
include_once('../BANCO/conexao.php');

try {
    // Estatísticas gerais
    $stmt_total_usuarios = $conn->query("
        SELECT 
            (SELECT COUNT(*) FROM geradores) as total_geradores,
            (SELECT COUNT(*) FROM coletores) as total_coletores,
            (SELECT COUNT(*) FROM coletas) as total_coletas,
            COALESCE(SUM(hc.quantidade_coletada), 0) AS total_oleo_coletado
            FROM historico_coletas hc
            WHERE hc.status = 'concluida'
    ");
    $stats = $stmt_total_usuarios->fetch(PDO::FETCH_ASSOC);

    // Coletas recentes
    $stmt_coletas_recentes = $conn->query("
        SELECT c.id, c.data_solicitacao, c.status, c.quantidade_oleo,
               g.nome_completo as gerador, col.nome_completo as coletor
        FROM coletas c
        LEFT JOIN geradores g ON c.id_gerador = g.id
        LEFT JOIN coletores col ON c.id_coletor = col.id
        ORDER BY c.data_solicitacao DESC
        LIMIT 10
    ");
    $coletas_recentes = $stmt_coletas_recentes->fetchAll(PDO::FETCH_ASSOC);

    // Coletores top avaliados
    $stmt_coletores_top = $conn->query("
        SELECT id, nome_completo, avaliacao_media, total_avaliacoes, coletas, total_oleo
        FROM coletores
        ORDER BY avaliacao_media DESC, total_avaliacoes DESC
        LIMIT 5
    ");
    $coletores_top = $stmt_coletores_top->fetchAll(PDO::FETCH_ASSOC);

    // Geradores mais ativos
    $stmt_geradores_ativos = $conn->query("
        SELECT g.id, g.nome_completo, COUNT(c.id) as total_coletas_solicitadas, COALESCE(SUM(c.quantidade_oleo), 0) as total_oleo
        FROM geradores g
        LEFT JOIN coletas c ON g.id = c.id_gerador AND c.status != 'cancelada'
        GROUP BY g.id
        ORDER BY total_coletas_solicitadas DESC
        LIMIT 5
    ");
    $geradores_ativos = $stmt_geradores_ativos->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Erro ao buscar estatísticas: " . $e->getMessage());
    $stats = [];
    $coletas_recentes = [];
    $coletores_top = [];
    $geradores_ativos = [];
}

// Função para traduzir status
function traduzirStatus($status) {
    $traducoes = [
        'solicitada' => 'Solicitada',
        'pendente' => 'Pendente',
        'agendada' => 'Agendada',
        'em_andamento' => 'Em Andamento',
        'concluida' => 'Concluída',
        'cancelada' => 'Cancelada'
    ];
    return $traducoes[$status] ?? $status;
}

function corStatus($status) {
    $cores = [
        'solicitada' => '#FFA726',
        'pendente' => '#FFA726',
        'agendada' => '#42A5F5',
        'em_andamento' => '#66BB6A',
        'concluida' => '#66BB6A',
        'cancelada' => '#EF5350'
    ];
    return $cores[$status] ?? '#999';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Administrativo - Ecoleta</title>
    <link rel="icon" href="../img/logo.png" type="image/png">
    <link rel="stylesheet" href="admin-styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f7fa;
            color: #333;
        }

        .admin-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .admin-sidebar {
            width: 280px;
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            color: white;
            padding: 30px 0;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }

        .admin-logo {
            padding: 0 20px 30px;
            display: flex;
            align-items: center;
            gap: 15px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .admin-logo img {
            width: 40px;
            height: 40px;
            border-radius: 8px;
        }

        .admin-logo-text h2 {
            font-size: 18px;
            font-weight: 700;
        }

        .admin-logo-text p {
            font-size: 12px;
            opacity: 0.8;
        }

        .admin-nav {
            margin-top: 20px;
        }

        .admin-nav a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 15px 20px;
            color: white;
            text-decoration: none;
            transition: all 0.3s;
            border-left: 4px solid transparent;
        }

        .admin-nav a:hover {
            background: rgba(255,255,255,0.1);
            border-left-color: white;
        }

        .admin-nav a.active {
            background: rgba(255,255,255,0.15);
            border-left-color: white;
            font-weight: 600;
        }

        .admin-nav i {
            font-size: 18px;
        }

        /* Main Content */
        .admin-content {
            margin-left: 280px;
            flex: 1;
            padding: 30px;
        }

        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            background: white;
            padding: 20px 25px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .admin-header h1 {
            font-size: 28px;
            color: #1e293b;
        }

        .admin-logout {
            padding: 10px 20px;
            background: #ef4444;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .admin-logout:hover {
            background: #dc2626;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            border-left: 4px solid;
        }

        .stat-card.usuarios {
            border-left-color: #3b82f6;
        }

        .stat-card.geradores {
            border-left-color: #8b5cf6;
        }

        .stat-card.coletores {
            border-left-color: #22c55e;
        }

        .stat-card.coletas {
            border-left-color: #f59e0b;
        }

        .stat-label {
            font-size: 14px;
            color: #64748b;
            font-weight: 500;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .stat-value {
            font-size: 32px;
            font-weight: 700;
            color: #1e293b;
        }

        /* Sections */
        .admin-section {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #1e293b;
        }

        /* Tables */
        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table thead {
            background: #f8f9fa;
        }

        table th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #64748b;
            border-bottom: 2px solid #e2e8f0;
            font-size: 13px;
            text-transform: uppercase;
        }

        table td {
            padding: 15px;
            border-bottom: 1px solid #e2e8f0;
            color: #475569;
        }

        table tr:hover {
            background: #f8f9fa;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            color: white;
        }

        .badge-solicitada, .badge-pendente {
            background: #FFA726;
        }

        .badge-agendada {
            background: #42A5F5;
        }

        .badge-em_andamento {
            background: #66BB6A;
        }

        .badge-concluida {
            background: #66BB6A;
        }

        .badge-cancelada {
            background: #EF5350;
        }

        .action-button {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 12px;
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .action-button:hover {
            background: #2563eb;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 30px;
        }

        .empty-message {
            text-align: center;
            padding: 40px 20px;
            color: #999;
        }

        @media (max-width: 1024px) {
            .admin-sidebar {
                width: 200px;
            }

            .admin-content {
                margin-left: 200px;
            }

            .grid-2 {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .admin-sidebar {
                position: absolute;
                left: -280px;
                transition: left 0.3s;
                z-index: 1000;
            }

            .admin-sidebar.active {
                left: 0;
            }

            .admin-content {
                margin-left: 0;
            }

            .admin-header {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="admin-sidebar" id="adminSidebar">
            <div class="admin-logo">
                <img src="../img/logo.png" alt="Ecoleta">
                <div class="admin-logo-text">
                    <h2>Ecoleta</h2>
                    <p>Admin</p>
                </div>
            </div>

            <nav class="admin-nav">
                <a href="dashboard.php" class="active">
                    <i class="ri-dashboard-line"></i>
                    <span>Dashboard</span>
                </a>
                <a href="geradores.php">
                    <i class="ri-user-line"></i>
                    <span>Geradores</span>
                </a>
                <a href="coletores.php">
                    <i class="ri-team-line"></i>
                    <span>Coletores</span>
                </a>
                <a href="coletas.php">
                    <i class="ri-oil-line"></i>
                    <span>Coletas</span>
                </a>
                <a href="relatorios.php">
                    <i class="ri-bar-chart-line"></i>
                    <span>Relatórios</span>
                </a>
                <a href="configuracoes.php">
                    <i class="ri-settings-line"></i>
                    <span>Configurações</span>
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="admin-content">
            <header class="admin-header">
                <div>
                    <h1>Dashboard Administrativo</h1>
                    <p style="color: #64748b; margin-top: 5px;">Bem-vindo ao painel de controle da Ecoleta</p>
                </div>
                <a href="../logout.php" class="admin-logout">
                    <i class="ri-logout-circle-line"></i>
                    Sair
                </a>
            </header>

            <!-- Statistics -->
            <div class="stats-grid">
                <div class="stat-card usuarios">
                    <div class="stat-label">
                        <i class="ri-user-line"></i>
                        Total de Usuários
                    </div>
                    <div class="stat-value"><?php echo ($stats['total_geradores'] ?? 0) + ($stats['total_coletores'] ?? 0); ?></div>
                </div>

                <div class="stat-card geradores">
                    <div class="stat-label">
                        <i class="ri-user-add-line"></i>
                        Geradores de Óleo
                    </div>
                    <div class="stat-value"><?php echo $stats['total_geradores'] ?? 0; ?></div>
                </div>

                <div class="stat-card coletores">
                    <div class="stat-label">
                        <i class="ri-team-line"></i>
                        Coletores
                    </div>
                    <div class="stat-value"><?php echo $stats['total_coletores'] ?? 0; ?></div>
                </div>

                <div class="stat-card coletas">
                    <div class="stat-label">
                        <i class="ri-oil-line"></i>
                        Total de Coletas
                    </div>
                    <div class="stat-value"><?php echo $stats['total_coletas'] ?? 0; ?></div>
                </div>
            </div>

            <!-- Oil Collected -->
            <div class="admin-section">
                <div class="section-title">
                    <i class="ri-recycle-line"></i>
                    Óleo Coletado
                </div>
                <div style="font-size: 36px; font-weight: 700; color: #22c55e;">
                    <?php echo number_format($stats['total_oleo_coletado'] ?? 0, 1, ',', '.'); ?>L
                </div>
                <p style="color: #64748b; margin-top: 10px;">Total de óleo coletado no sistema</p>
            </div>

            <!-- Recent Collections -->
            <div class="admin-section">
                <div class="section-title">
                    <i class="ri-history-line"></i>
                    Coletas Recentes
                </div>

                <?php if (!empty($coletas_recentes)): ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Gerador</th>
                                <th>Coletor</th>
                                <th>Data</th>
                                <th>Quantidade</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($coletas_recentes as $coleta): ?>
                            <tr>
                                <td>#<?php echo $coleta['id']; ?></td>
                                <td><?php echo htmlspecialchars($coleta['gerador'] ?? 'Não atribuído'); ?></td>
                                <td><?php echo htmlspecialchars($coleta['coletor'] ?? 'Aguardando'); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($coleta['data_solicitacao'])); ?></td>
                                <td><?php echo number_format($coleta['quantidade_oleo'] ?? 0, 1, ',', '.'); ?>L</td>
                                <td>
                                    <span class="status-badge badge-<?php echo $coleta['status']; ?>">
                                        <?php echo traduzirStatus($coleta['status']); ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="empty-message">
                    <i class="ri-inbox-line" style="font-size: 48px; color: #ccc; margin-bottom: 15px; display: block;"></i>
                    <p>Nenhuma coleta encontrada</p>
                </div>
                <?php endif; ?>
            </div>

            <!-- Top Collectors and Active Generators -->
            <div class="grid-2">
                <!-- Top Collectors -->
                <div class="admin-section">
                    <div class="section-title">
                        <i class="ri-star-line"></i>
                        Coletores Melhor Avaliados
                    </div>

                    <?php if (!empty($coletores_top)): ?>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Avaliação</th>
                                    <th>Coletas</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($coletores_top as $coletor): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($coletor['nome_completo']); ?></td>
                                    <td>
                                        <?php echo number_format($coletor['avaliacao_media'], 1, ',', '.'); ?>/5
                                        <span style="color: #ffc107;">★</span>
                                    </td>
                                    <td><?php echo $coletor['coletas'] ?? 0; ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="empty-message">
                        <p>Nenhum coletor encontrado</p>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Active Generators -->
                <div class="admin-section">
                    <div class="section-title">
                        <i class="ri-fire-line"></i>
                        Geradores Mais Ativos
                    </div>

                    <?php if (!empty($geradores_ativos)): ?>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Coletas</th>
                                    <th>Óleo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($geradores_ativos as $gerador): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($gerador['nome_completo']); ?></td>
                                    <td><?php echo $gerador['total_coletas_solicitadas'] ?? 0; ?></td>
                                    <td><?php echo number_format($gerador['total_oleo'] ?? 0, 1, ',', '.'); ?>L</td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="empty-message">
                        <p>Nenhum gerador encontrado</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Toggle sidebar on mobile
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('adminSidebar');
            const navLinks = document.querySelectorAll('.admin-nav a');

            navLinks.forEach(link => {
                link.addEventListener('click', () => {
                    sidebar.classList.remove('active');
                });
            });
        });
    </script>
</body>
</html>
