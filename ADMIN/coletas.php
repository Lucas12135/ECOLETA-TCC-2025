<?php
session_start();

if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

include_once('../BANCO/conexao.php');

// Buscar todas as coletas
try {
    $stmt = $conn->query("
        SELECT c.*,
               g.nome_completo as gerador_nome,
               col.nome_completo as coletor_nome
        FROM coletas c
        LEFT JOIN geradores g ON c.id_gerador = g.id
        LEFT JOIN coletores col ON c.id_coletor = col.id
        ORDER BY c.data_solicitacao DESC
    ");
    $coletas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $coletas = [];
    $_SESSION['mensagem_erro'] = "Erro ao buscar coletas: " . $e->getMessage();
}

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
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coletas - Admin Dashboard</title>
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
                <a href="coletas.php" class="active">
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

        <main class="admin-content">
            <header class="admin-header">
                <div>
                    <h1>Gerenciamento de Coletas</h1>
                    <p style="color: #64748b; margin-top: 5px;">Acompanhe todas as solicitações de coleta do sistema</p>
                </div>
                <a href="../index.php" class="admin-logout">
                    <i class="ri-logout-circle-line"></i>
                    Sair
                </a>
            </header>

            <div class="admin-section">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <div class="section-title">
                        <i class="ri-oil-line"></i>
                        Total de Coletas: <strong><?php echo count($coletas); ?></strong>
                    </div>
                </div>

                <?php if (!empty($coletas)): ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Gerador</th>
                                <th>Coletor</th>
                                <th>Quantidade</th>
                                <th>Data Agendada</th>
                                <th>Local</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($coletas as $coleta): ?>
                            <tr>
                                <td>#<?php echo $coleta['id']; ?></td>
                                <td><?php echo htmlspecialchars($coleta['gerador_nome'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($coleta['coletor_nome'] ?? 'Aguardando'); ?></td>
                                <td><?php echo number_format($coleta['quantidade_oleo'] ?? 0, 1, ',', '.'); ?>L</td>
                                <td><?php echo date('d/m/Y H:i', strtotime($coleta['data_agendada'] ?? $coleta['data_solicitacao'])); ?></td>
                                <td>
                                    <small><?php echo htmlspecialchars($coleta['rua'] . ', ' . $coleta['numero'] . ' - ' . $coleta['cidade'] . ', ' . $coleta['estado']); ?></small>
                                </td>
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
        </main>
    </div>

    <link rel="stylesheet" href="admin-styles.css">
</body>
</html>
