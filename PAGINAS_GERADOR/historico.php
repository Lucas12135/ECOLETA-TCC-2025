<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../login.php");
    exit();
}

include '../BANCO/conexao.php';

// Buscar histórico de coletas do usuário
$id_usuario = $_SESSION['id_usuario'];
$sql = "SELECT c.*, cl.nome as nome_coletor, cl.telefone as telefone_coletor 
        FROM coletas c 
        LEFT JOIN coletores cl ON c.id_coletor = cl.id 
        WHERE c.id_gerador = ? 
        ORDER BY c.data_solicitacao DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico de Coletas - Ecoleta</title>
    <link rel="stylesheet" href="../CSS/global.css">
    <link rel="stylesheet" href="../CSS/gerador-historico.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
    <header>
        <nav>
            <a href="home.php">
                <img src="../img/logo.png" alt="Logo Ecoleta">
            </a>
            <ul>
                <li><a href="home.php">Home</a></li>
                <li><a href="solicitar_coleta.php">Solicitar Coleta</a></li>
                <li><a href="historico.php" class="active">Histórico</a></li>
                <li><a href="configuracoes.php">Configurações</a></li>
                <li><a href="perfil.php">Perfil</a></li>
                <li><a href="../login.php?logout=1">Sair</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <h1>Histórico de Coletas</h1>
        
        <div class="historico-container">
            <?php if ($result->num_rows > 0): ?>
                <?php while($coleta = $result->fetch_assoc()): ?>
                    <div class="coleta-card">
                        <div class="coleta-header">
                            <span class="material-icons">recycling</span>
                            <h3>Coleta #<?php echo $coleta['id']; ?></h3>
                            <span class="status <?php echo strtolower($coleta['status']); ?>">
                                <?php echo $coleta['status']; ?>
                            </span>
                        </div>
                        <div class="coleta-info">
                            <p><strong>Data da Solicitação:</strong> <?php echo date('d/m/Y H:i', strtotime($coleta['data_solicitacao'])); ?></p>
                            <p><strong>Tipo de Material:</strong> <?php echo $coleta['tipo_material']; ?></p>
                            <p><strong>Quantidade:</strong> <?php echo $coleta['quantidade']; ?> kg</p>
                            <?php if($coleta['status'] != 'PENDENTE'): ?>
                                <p><strong>Coletor:</strong> <?php echo $coleta['nome_coletor']; ?></p>
                                <p><strong>Telefone do Coletor:</strong> <?php echo $coleta['telefone_coletor']; ?></p>
                            <?php endif; ?>
                            <?php if($coleta['data_coleta']): ?>
                                <p><strong>Data da Coleta:</strong> <?php echo date('d/m/Y H:i', strtotime($coleta['data_coleta'])); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-records">
                    <span class="material-icons">inbox</span>
                    <p>Você ainda não possui histórico de coletas.</p>
                    <a href="solicitar_coleta.php" class="btn">Solicitar uma Coleta</a>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 Ecoleta. Todos os direitos reservados.</p>
    </footer>
</body>
</html>
