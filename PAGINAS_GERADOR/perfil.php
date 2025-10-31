<?php
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../login.php');
    exit();
}

// Verificar se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: solicitar_coleta.php');
    exit();
}

// Incluir conexão com banco de dados
require_once('../config/database.php');

// Capturar dados do formulário
$usuario_id = $_SESSION['usuario_id'];
$volume = floatval($_POST['volume']);
$tipo_coleta = $_POST['tipo_coleta']; // 'automatico' ou 'especifico'
$coletor_id = isset($_POST['coletor_id']) ? intval($_POST['coletor_id']) : null;

// Endereço
$cep = preg_replace('/[^0-9]/', '', $_POST['cep']);
$rua = $_POST['rua'];
$numero = $_POST['numero'];
$complemento = $_POST['complemento'] ?? null;
$bairro = $_POST['bairro'];
$cidade = $_POST['cidade'];
$latitude = floatval($_POST['latitude']);
$longitude = floatval($_POST['longitude']);

// Data e período
$data_preferencial = $_POST['data'];
$periodo = $_POST['periodo'];

// Observações
$observacoes = $_POST['observacoes'] ?? null;

// Validações básicas
$erros = [];

if ($volume < 1) {
    $erros[] = "Volume deve ser maior que 0 litros.";
}

if (strlen($cep) !== 8) {
    $erros[] = "CEP inválido.";
}

if (empty($rua) || empty($numero) || empty($bairro) || empty($cidade)) {
    $erros[] = "Preencha todos os campos de endereço obrigatórios.";
}

if (strtotime($data_preferencial) < strtotime(date('Y-m-d'))) {
    $erros[] = "Data não pode ser no passado.";
}

if (!in_array($periodo, ['manha', 'tarde'])) {
    $erros[] = "Período inválido.";
}

if ($tipo_coleta === 'especifico' && empty($coletor_id)) {
    $erros[] = "Selecione um coletor.";
}

// Se houver erros, redirecionar com mensagem
if (!empty($erros)) {
    $_SESSION['erro'] = implode('<br>', $erros);
    header('Location: solicitar_coleta.php');
    exit();
}

try {
    // Iniciar transação
    $pdo->beginTransaction();
    
    // Inserir solicitação de coleta
    $sql = "INSERT INTO solicitacoes_coleta (
        usuario_id, 
        volume, 
        tipo_coleta,
        coletor_id,
        cep, 
        rua, 
        numero, 
        complemento, 
        bairro, 
        cidade, 
        latitude, 
        longitude,
        data_preferencial,
        periodo,
        observacoes,
        status,
        data_solicitacao
    ) VALUES (
        :usuario_id, 
        :volume, 
        :tipo_coleta,
        :coletor_id,
        :cep, 
        :rua, 
        :numero, 
        :complemento, 
        :bairro, 
        :cidade, 
        :latitude, 
        :longitude,
        :data_preferencial,
        :periodo,
        :observacoes,
        :status,
        NOW()
    )";
    
    $stmt = $pdo->prepare($sql);
    
    // Definir status inicial
    $status_inicial = ($tipo_coleta === 'especifico') ? 'aguardando_confirmacao' : 'aguardando_coletor';
    
    $stmt->execute([
        ':usuario_id' => $usuario_id,
        ':volume' => $volume,
        ':tipo_coleta' => $tipo_coleta,
        ':coletor_id' => $coletor_id,
        ':cep' => $cep,
        ':rua' => $rua,
        ':numero' => $numero,
        ':complemento' => $complemento,
        ':bairro' => $bairro,
        ':cidade' => $cidade,
        ':latitude' => $latitude,
        ':longitude' => $longitude,
        ':data_preferencial' => $data_preferencial,
        ':periodo' => $periodo,
        ':observacoes' => $observacoes,
        ':status' => $status_inicial
    ]);
    
    $solicitacao_id = $pdo->lastInsertId();
    
    // Se for coleta específica, notificar o coletor selecionado
    if ($tipo_coleta === 'especifico' && $coletor_id) {
        $sql_notificacao = "INSERT INTO notificacoes (
            usuario_id,
            tipo,
            titulo,
            mensagem,
            referencia_id,
            data_criacao
        ) VALUES (
            :coletor_id,
            'nova_solicitacao',
            'Nova Solicitação de Coleta',
            'Você foi selecionado para uma nova coleta de :volume litros',
            :solicitacao_id,
            NOW()
        )";
        
        $stmt_notificacao = $pdo->prepare($sql_notificacao);
        $stmt_notificacao->execute([
            ':coletor_id' => $coletor_id,
            ':volume' => $volume,
            ':solicitacao_id' => $solicitacao_id
        ]);
    }
    
    // Se for coleta automática, notificar coletores próximos
    if ($tipo_coleta === 'automatico') {
        // Buscar coletores em um raio de 10km
        $sql_coletores = "SELECT 
            c.id,
            c.usuario_id,
            (6371 * acos(
                cos(radians(:latitude)) * 
                cos(radians(c.latitude)) * 
                cos(radians(c.longitude) - radians(:longitude)) + 
                sin(radians(:latitude)) * 
                sin(radians(c.latitude))
            )) AS distancia
        FROM coletores c
        WHERE c.ativo = 1
        AND c.disponivel = 1
        HAVING distancia <= 10
        ORDER BY distancia
        LIMIT 5";
        
        $stmt_coletores = $pdo->prepare($sql_coletores);
        $stmt_coletores->execute([
            ':latitude' => $latitude,
            ':longitude' => $longitude
        ]);
        
        $coletores_proximos = $stmt_coletores->fetchAll(PDO::FETCH_ASSOC);
        
        // Notificar cada coletor próximo
        foreach ($coletores_proximos as $coletor) {
            $sql_notificacao = "INSERT INTO notificacoes (
                usuario_id,
                tipo,
                titulo,
                mensagem,
                referencia_id,
                data_criacao
            ) VALUES (
                :coletor_id,
                'nova_solicitacao_area',
                'Nova Coleta Disponível',
                'Nova coleta de :volume litros disponível a :distancia km de você',
                :solicitacao_id,
                NOW()
            )";
            
            $stmt_notificacao = $pdo->prepare($sql_notificacao);
            $stmt_notificacao->execute([
                ':coletor_id' => $coletor['usuario_id'],
                ':volume' => $volume,
                ':distancia' => round($coletor['distancia'], 1),
                ':solicitacao_id' => $solicitacao_id
            ]);
        }
    }
    
    // Confirmar transação
    $pdo->commit();
    
    // Redirecionar com mensagem de sucesso
    $_SESSION['sucesso'] = "Solicitação de coleta realizada com sucesso!";
    
    if ($tipo_coleta === 'especifico') {
        $_SESSION['sucesso'] .= " O coletor selecionado foi notificado.";
    } else {
        $_SESSION['sucesso'] .= " Coletores próximos foram notificados.";
    }
    
    header('Location: historico.php');
    exit();
    
} catch (PDOException $e) {
    // Reverter transação em caso de erro
    $pdo->rollBack();
    
    // Log do erro (em produção, usar um sistema de logs adequado)
    error_log("Erro ao processar solicitação: " . $e->getMessage());
    
    $_SESSION['erro'] = "Erro ao processar solicitação. Tente novamente.";
    header('Location: solicitar_coleta.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil - Ecoleta</title>
    <link rel="icon" href="../img/logo.png" type="image/png">
    <link rel="stylesheet" href="../CSS/gerador-perfil.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <!-- Barra Lateral -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo-placeholder">
                    <img src="../img/logo.png" alt="Logo Ecoleta" class="logo">
                </div>
                <span class="logo-text">Ecoleta</span>
            </div>

            <nav class="sidebar-nav">
                <ul>
                    <li class="nav-link">
                        <a href="../index.php" class="nav-link">
                            <i class="ri-arrow-left-line"></i>
                            <span>Voltar</span>
                        </a>
                    </li>
                    <li>
                        <a href="home.php" class="nav-link">
                            <i class="ri-home-4-line"></i>
                            <span>Home</span>
                        </a>
                    </li>
                    <li class="active">
                        <a href="#" class="nav-link">
                            <i class="ri-user-line"></i>
                            <span>Perfil</span>
                        </a>
                    </li>
                    <li>
                        <a href="solicitar_coleta.php" class="nav-link">
                            <i class="ri-oil-line"></i>
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
        </aside>

        <!-- Conteúdo Principal -->
        <main class="main-content">
            <!-- Cabeçalho do Perfil -->
            <div class="profile-header">
                <div class="profile-cover">
                    <div class="profile-photo-container">
                        <img src="../img/profile-placeholder.jpg" alt="Foto de Perfil" id="profilePhoto" class="profile-photo">
                        <button class="change-photo-btn" onclick="document.getElementById('photoInput').click()">
                            <i class="ri-camera-line"></i>
                        </button>
                        <input type="file" id="photoInput" hidden accept="image/*" onchange="updateProfilePhoto(this)">
                    </div>
                </div>
                <div class="profile-info">
                    <h1><?php echo isset($_SESSION['nome']) ? $_SESSION['nome'] : 'Nome do Usuário'; ?></h1>
                    <p class="user-type">Gerador de Óleo</p>
                </div>
            </div>

            <!-- Seções do Perfil -->
            <div class="profile-sections">
                <!-- Informações Pessoais -->
                <section class="profile-section">
                    <h2>Informações Pessoais</h2>
                    <form class="profile-form" id="personalInfoForm">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="nome">Nome Completo</label>
                                <input type="text" id="nome" name="nome" value="<?php echo isset($_SESSION['nome']) ? $_SESSION['nome'] : ''; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="email">E-mail</label>
                                <input type="email" id="email" name="email" value="<?php echo isset($_SESSION['email']) ? $_SESSION['email'] : ''; ?>" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="telefone">Telefone</label>
                                <input type="tel" id="telefone" name="telefone" value="<?php echo isset($_SESSION['telefone']) ? $_SESSION['telefone'] : ''; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="cpf">CPF</label>
                                <input type="text" id="cpf" name="cpf" value="<?php echo isset($_SESSION['cpf']) ? $_SESSION['cpf'] : ''; ?>" required readonly>
                            </div>
                        </div>
                    </form>
                </section>

                <!-- Endereço Padrão -->
                <section class="profile-section">
                    <h2>Endereço Padrão</h2>
                    <form class="profile-form" id="addressForm">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="cep">CEP</label>
                                <input type="text" id="cep" name="cep" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="rua">Rua</label>
                                <input type="text" id="rua" name="rua" required>
                            </div>
                            <div class="form-group">
                                <label for="numero">Número</label>
                                <input type="text" id="numero" name="numero" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="complemento">Complemento</label>
                                <input type="text" id="complemento" name="complemento">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="bairro">Bairro</label>
                                <input type="text" id="bairro" name="bairro" required>
                            </div>
                            <div class="form-group">
                                <label for="cidade">Cidade</label>
                                <input type="text" id="cidade" name="cidade" required>
                            </div>
                        </div>
                    </form>
                </section>

                <!-- Estatísticas -->
                <section class="profile-section">
                    <h2>Suas Estatísticas</h2>
                    <div class="statistics-grid">
                        <div class="stat-card">
                            <i class="ri-oil-line"></i>
                            <div class="stat-info">
                                <span class="stat-value">25L</span>
                                <span class="stat-label">Óleo Reciclado</span>
                            </div>
                        </div>
                        <div class="stat-card">
                            <i class="ri-recycle-line"></i>
                            <div class="stat-info">
                                <span class="stat-value">12</span>
                                <span class="stat-label">Coletas Realizadas</span>
                            </div>
                        </div>
                        <div class="stat-card">
                            <i class="ri-water-flash-line"></i>
                            <div class="stat-info">
                                <span class="stat-value">500mil L</span>
                                <span class="stat-label">Água Preservada</span>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Botões de Ação -->
                <div class="profile-actions">
                    <button type="button" class="btn btn-secondary" onclick="resetForms()">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="saveProfile()">Salvar Alterações</button>
                </div>
            </div>
        </main>
    </div>

    <script src="../JS/perfil.js"></script>
</body>

</html>
