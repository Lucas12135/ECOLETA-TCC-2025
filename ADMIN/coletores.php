<?php
session_start();

if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

include_once('../BANCO/conexao.php');

// Processar exclusão
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    try {
        $id = intval($_GET['delete']);
        $stmt = $conn->prepare("DELETE FROM coletores WHERE id = :id");
        $stmt->bindParam(':id', $id);
        if ($stmt->execute()) {
            $_SESSION['mensagem_sucesso'] = "Coletor deletado com sucesso!";
            header('Location: coletores.php');
            exit;
        }
    } catch (Exception $e) {
        $_SESSION['mensagem_erro'] = "Erro ao deletar: " . $e->getMessage();
    }
}

// Buscar todos os coletores
try {
    $stmt = $conn->query("
        SELECT c.*, e.cep, e.cidade, e.estado
        FROM coletores c
        LEFT JOIN enderecos e ON c.id_endereco = e.id
        ORDER BY c.created_at DESC
    ");
    $coletores = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $coletores = [];
    $_SESSION['mensagem_erro'] = "Erro ao buscar coletores: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coletores - Admin Dashboard</title>
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
                <a href="coletores.php" class="active">
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

        <main class="admin-content">
            <header class="admin-header">
                <div>
                    <h1>Gerenciamento de Coletores</h1>
                    <p style="color: #64748b; margin-top: 5px;">Monitore e gerencie todos os coletores cadastrados</p>
                </div>
                <a href="../index.php" class="admin-logout">
                    <i class="ri-logout-circle-line"></i>
                    Sair
                </a>
            </header>

            <?php if (isset($_SESSION['mensagem_sucesso'])): ?>
                <div style="background: #dcfce7; color: #166534; padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #22c55e;">
                    <?php echo $_SESSION['mensagem_sucesso']; unset($_SESSION['mensagem_sucesso']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['mensagem_erro'])): ?>
                <div style="background: #fee2e2; color: #991b1b; padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #ef4444;">
                    <?php echo $_SESSION['mensagem_erro']; unset($_SESSION['mensagem_erro']); ?>
                </div>
            <?php endif; ?>

            <div class="admin-section">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <div class="section-title">
                        <i class="ri-team-line"></i>
                        Total de Coletores: <strong><?php echo count($coletores); ?></strong>
                    </div>
                </div>

                <?php if (!empty($coletores)): ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Email</th>
                                <th>Avaliação</th>
                                <th>Coletas</th>
                                <th>Transporte</th>
                                <th>Cidade</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($coletores as $coletor): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($coletor['nome_completo']); ?></td>
                                <td><?php echo htmlspecialchars($coletor['email']); ?></td>
                                <td>
                                    <span style="color: #ffc107;">★</span>
                                    <?php echo number_format($coletor['avaliacao_media'], 1, ',', '.'); ?>/5
                                </td>
                                <td><?php echo $coletor['coletas'] ?? 0; ?></td>
                                <td>
                                    <span style="font-size: 12px; background: #f0f0f0; padding: 4px 8px; border-radius: 4px;">
                                        <?php echo $coletor['meio_transporte']; ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($coletor['cidade'] ?? 'N/A'); ?></td>
                                <td>
                                    <button class="action-button" onclick="alert('Ver detalhes de <?php echo htmlspecialchars($coletor['nome_completo']); ?>')">
                                        <i class="ri-eye-line"></i> Ver
                                    </button>
                                    <a href="?delete=<?php echo $coletor['id']; ?>" class="action-button" onclick="return confirm('Tem certeza que deseja deletar este coletor?')" style="background: #ef4444;">
                                        <i class="ri-delete-line"></i> Deletar
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="empty-message">
                    <i class="ri-inbox-line" style="font-size: 48px; color: #ccc; margin-bottom: 15px; display: block;"></i>
                    <p>Nenhum coletor encontrado</p>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <link rel="stylesheet" href="admin-styles.css">
</body>
</html>
