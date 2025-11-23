<?php
session_start();

if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header('Location: ../logins.php');
    exit;
}

include_once('../BANCO/conexao.php');

// Processar exclusão
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    try {
        $id = intval($_GET['delete']);
        $stmt = $conn->prepare("DELETE FROM geradores WHERE id = :id");
        $stmt->bindParam(':id', $id);
        if ($stmt->execute()) {
            $_SESSION['mensagem_sucesso'] = "Gerador deletado com sucesso!";
            header('Location: geradores.php');
            exit;
        }
    } catch (Exception $e) {
        $_SESSION['mensagem_erro'] = "Erro ao deletar: " . $e->getMessage();
    }
}

// Buscar todos os geradores
try {
    $stmt = $conn->query("
        SELECT g.*, e.cep, e.cidade, e.estado,
               COUNT(c.id) as total_coletas
        FROM geradores g
        LEFT JOIN enderecos e ON g.id_endereco = e.id
        LEFT JOIN coletas c ON g.id = c.id_gerador
        GROUP BY g.id
        ORDER BY g.created_at DESC
    ");
    $geradores = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $geradores = [];
    $_SESSION['mensagem_erro'] = "Erro ao buscar geradores: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Geradores - Admin Dashboard</title>
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
                <a href="geradores.php" class="active">
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

        <main class="admin-content">
            <header class="admin-header">
                <div>
                    <h1>Geradores de Óleo</h1>
                    <p style="color: #64748b; margin-top: 5px;">Gerencie todos os produtores de óleo do sistema</p>
                </div>
                <a href="../logout.php" class="admin-logout">
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
                        <i class="ri-user-add-line"></i>
                        Total de Geradores: <strong><?php echo count($geradores); ?></strong>
                    </div>
                </div>

                <?php if (!empty($geradores)): ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Email</th>
                                <th>Telefone</th>
                                <th>Cidade</th>
                                <th>Coletas</th>
                                <th>Data de Cadastro</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($geradores as $gerador): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($gerador['nome_completo']); ?></td>
                                <td><?php echo htmlspecialchars($gerador['email']); ?></td>
                                <td><?php echo htmlspecialchars($gerador['telefone']); ?></td>
                                <td><?php echo htmlspecialchars($gerador['cidade'] ?? 'N/A'); ?></td>
                                <td><?php echo $gerador['total_coletas'] ?? 0; ?></td>
                                <td><?php echo date('d/m/Y', strtotime($gerador['created_at'])); ?></td>
                                <td>
                                    <button class="action-button" onclick="alert('Ver detalhes de <?php echo htmlspecialchars($gerador['nome_completo']); ?>')">
                                        <i class="ri-eye-line"></i> Ver
                                    </button>
                                    <a href="?delete=<?php echo $gerador['id']; ?>" class="action-button" onclick="return confirm('Tem certeza que deseja deletar este gerador?')" style="background: #ef4444;">
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
                    <p>Nenhum gerador encontrado</p>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <link rel="stylesheet" href="admin-styles.css">
</body>
</html>
