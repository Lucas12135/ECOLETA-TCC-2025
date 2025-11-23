<?php
session_start();

if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header('Location: ../logins.php');
    exit;
}

include_once('../BANCO/conexao.php');
date_default_timezone_set('America/Sao_Paulo');

// Estatísticas gerais
try {
    // Coletas por status
    $stmt_status = $conn->query("
        SELECT status, COUNT(*) as total
        FROM coletas
        GROUP BY status
    ");
    $coletas_status = $stmt_status->fetchAll(PDO::FETCH_ASSOC);

    // Óleo coletado por mês
    $stmt_mensal = $conn->query("
        SELECT DATE_FORMAT(data_solicitacao, '%Y-%m') as mes, COUNT(*) as total, SUM(quantidade_oleo) as oleo
        FROM coletas
        WHERE status = 'concluida'
        GROUP BY DATE_FORMAT(data_solicitacao, '%Y-%m')
        ORDER BY mes DESC
        LIMIT 12
    ");
    $coletas_mensais = $stmt_mensal->fetchAll(PDO::FETCH_ASSOC);

    // Top geradores
    $stmt_geradores = $conn->query("
        SELECT g.nome_completo, COUNT(c.id) as coletas, SUM(c.quantidade_oleo) as oleo
        FROM geradores g
        LEFT JOIN coletas c ON g.id = c.id_gerador
        GROUP BY g.id
        ORDER BY oleo DESC
        LIMIT 10
    ");
    $top_geradores = $stmt_geradores->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    $_SESSION['mensagem_erro'] = "Erro ao buscar dados: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatórios - Admin Dashboard</title>
    <link rel="icon" href="../img/logo.png" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="admin-styles.css">
</head>
<body>
    <div class="admin-container">
        <aside class="admin-sidebar" id="adminSidebar">
            <div class="admin-logo">
                <img src="../img/logo.png" alt="Ecoleta">
                <div class="admin-logo-text">
                    <h2>Ecoleta</h2>
                    <p>Admin</p>
                </div>
            </div>

            <nav class="admin-nav">
                <a href="dashboard.php">
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
                <a href="relatorios.php" class="active">
                    <i class="ri-bar-chart-line"></i>
                    <span>Relatórios</span>
                </a>
                <a href="configuracoes.php">
                    <i class="ri-settings-line"></i>
                    <span>Configurações</span>
                </a>
            </nav>
        </aside>

        <main class="admin-content">
            <header class="admin-header">
                <div>
                    <h1>Relatórios e Análises</h1>
                    <p style="color: #64748b; margin-top: 5px;">Visualize estatísticas detalhadas do sistema</p>
                </div>
                <a href="../logout.php" class="admin-logout">
                    <i class="ri-logout-circle-line"></i>
                    Sair
                </a>
            </header>

            <!-- Coletas por Status -->
            <div class="admin-section">
                <div class="section-title">
                    <i class="ri-pie-chart-line"></i>
                    Coletas por Status
                </div>

                <?php if (!empty($coletas_status)): ?>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                    <?php foreach ($coletas_status as $status): ?>
                    <div style="background: #f8f9fa; padding: 20px; border-radius: 12px; text-align: center;">
                        <div style="font-size: 32px; font-weight: 700; color: #1e293b;">
                            <?php echo $status['total']; ?>
                        </div>
                        <div style="color: #64748b; margin-top: 10px; text-transform: capitalize;">
                            <?php echo str_replace('_', ' ', $status['status']); ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Coletas Mensais -->
            <div class="admin-section">
                <div class="section-title">
                    <i class="ri-calendar-line"></i>
                    Coletas Mensais
                </div>

                <?php if (!empty($coletas_mensais)): ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Mês</th>
                                <th>Total de Coletas</th>
                                <th>Óleo Coletado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($coletas_mensais as $coleta): ?>
                            <tr>
                                <td><?php echo date('M/Y', strtotime($coleta['mes'] . '-01')); ?></td>
                                <td><?php echo $coleta['total']; ?></td>
                                <td><?php echo number_format($coleta['oleo'] ?? 0, 1, ',', '.'); ?>L</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>

            <!-- Top Geradores -->
            <div class="admin-section">
                <div class="section-title">
                    <i class="ri-trophy-line"></i>
                    Top 10 Geradores por Óleo Coletado
                </div>

                <?php if (!empty($top_geradores)): ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Posição</th>
                                <th>Nome</th>
                                <th>Coletas</th>
                                <th>Óleo Total</th>
                                <th>Barra de Progresso</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $posicao = 1;
                            $max_oleo = $top_geradores[0]['oleo'] ?? 1;
                            foreach ($top_geradores as $gerador): 
                            ?>
                            <tr>
                                <td><strong><?php echo $posicao++; ?>º</strong></td>
                                <td><?php echo htmlspecialchars($gerador['nome_completo'] ?? 'N/A'); ?></td>
                                <td><?php echo $gerador['coletas'] ?? 0; ?></td>
                                <td><?php echo number_format($gerador['oleo'] ?? 0, 1, ',', '.'); ?>L</td>
                                <td>
                                    <div style="background: #e5e7eb; height: 20px; border-radius: 10px; overflow: hidden;">
                                        <div style="background: linear-gradient(90deg, #22c55e 0%, #16a34a 100%); height: 100%; width: <?php echo ($gerador['oleo'] / $max_oleo * 100); ?>%;"></div>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <link rel="stylesheet" href="admin-styles.css">
</body>
</html>
