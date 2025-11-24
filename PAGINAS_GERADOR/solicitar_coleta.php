<?php
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../index.php');
    exit;
}

// Definir fuso horário de Brasília
date_default_timezone_set('America/Sao_Paulo');

// Verificar se existe coleta ativa (não concluída e não cancelada)
$coleta_ativa = null;
include_once('../BANCO/conexao.php');

try {
    $stmt_check = $conn->prepare("
        SELECT c.*, 
               col.id as id_coletor,
               col.nome_completo as coletor_nome, 
               col.telefone as coletor_telefone,
               col.foto_perfil as coletor_foto
        FROM coletas c
        LEFT JOIN coletores col ON c.id_coletor = col.id
        WHERE c.id_gerador = :id_gerador 
        AND c.status NOT IN ('concluida', 'cancelada')
        ORDER BY c.created_at DESC
        LIMIT 1
    ");
    $stmt_check->bindParam(':id_gerador', $_SESSION['id_usuario']);
    $stmt_check->execute();
    $coleta_ativa = $stmt_check->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Erro ao verificar coleta ativa: " . $e->getMessage());
}

// Processar cancelamento de coleta
if (isset($_POST['cancelar_coleta']) && $coleta_ativa) {
    try {
        $stmt_cancel = $conn->prepare("
            UPDATE coletas 
            SET status = 'cancelada', updated_at = NOW() 
            WHERE id = :id_coleta AND id_gerador = :id_gerador
        ");
        $stmt_cancel->bindParam(':id_coleta', $coleta_ativa['id']);
        $stmt_cancel->bindParam(':id_gerador', $_SESSION['id_usuario']);
        
        if ($stmt_cancel->execute()) {
            $_SESSION['mensagem_sucesso'] = "Coleta cancelada com sucesso!";
            header('Location: solicitar_coleta.php');
            exit;
        }
    } catch (PDOException $e) {
        $_SESSION['mensagem_erro'] = "Erro ao cancelar coleta: " . $e->getMessage();
    }
}

// Processar formulário de nova coleta (só se não houver coleta ativa)
if (!empty($_POST) && !isset($_POST['cancelar_coleta']) && !$coleta_ativa) {
    $id_gerador = $_SESSION['id_usuario'];
    $quantidade_oleo = $_POST['volume'] ?? null;
    $tipo_coleta = $_POST['tipo_coleta'] ?? 'automatico';
    
    $id_coletor = null;
    if ($tipo_coleta === 'especifico' && isset($_POST['coletor_id']) && !empty($_POST['coletor_id'])) {
        $id_coletor = $_POST['coletor_id'];
    }
    
    // Validar campos obrigatórios
    $cep = $_POST['cep'] ?? null;
    $rua = $_POST['rua'] ?? null;
    $numero = $_POST['numero'] ?? null;
    $complemento = $_POST['complemento'] ?? '';
    $cidade = $_POST['cidade'] ?? null;
    $estado = $_POST['estado'] ?? null;
    $bairro = $_POST['bairro'] ?? null;
    $data_coleta = $_POST['data'] ?? null;
    $periodo = $_POST['periodo'] ?? null;
    $observacoes = $_POST['observacoes'] ?? '';
    
    // Verificar se todos os campos obrigatórios estão preenchidos
    if (!$quantidade_oleo || !$cep || !$rua || !$numero || !$cidade || !$estado || !$bairro || !$data_coleta || !$periodo) {
        $_SESSION['mensagem_erro'] = "Por favor, preencha todos os campos obrigatórios.";
    } else {
        $data_solicitacao = date('Y-m-d H:i:s');

        try {
            if ($conn) {
                $stmt_coleta = $conn->prepare("INSERT INTO coletas (
                    id_gerador, quantidade_oleo, id_coletor, data_agendada, data_solicitacao, 
                    periodo, numero, complemento, cidade, cep, rua, estado, bairro, observacoes
                ) VALUES (
                    :id_gerador, :quantidade_oleo, :id_coletor, :data_agendada, :data_solicitacao, 
                    :periodo, :numero, :complemento, :cidade, :cep, :rua, :estado, :bairro, :observacoes
                )");
                
                $stmt_coleta->bindParam(':id_gerador', $id_gerador);
                $stmt_coleta->bindParam(':quantidade_oleo', $quantidade_oleo);
                $stmt_coleta->bindParam(':id_coletor', $id_coletor);
                $stmt_coleta->bindParam(':data_agendada', $data_coleta);
                $stmt_coleta->bindParam(':data_solicitacao', $data_solicitacao);
                $stmt_coleta->bindParam(':periodo', $periodo);
                $stmt_coleta->bindParam(':cep', $cep);
                $stmt_coleta->bindParam(':numero', $numero);
                $stmt_coleta->bindParam(':complemento', $complemento);
                $stmt_coleta->bindParam(':cidade', $cidade);
                $stmt_coleta->bindParam(':rua', $rua);
                $stmt_coleta->bindParam(':estado', $estado);
                $stmt_coleta->bindParam(':bairro', $bairro);
                $stmt_coleta->bindParam(':observacoes', $observacoes);
                
                if ($stmt_coleta->execute()) {
                    $id_coleta = $conn->lastInsertId();
                    $_SESSION['mensagem_sucesso'] = "Coleta solicitada com sucesso! ID: #$id_coleta";
                    header('Location: solicitar_coleta.php');
                    exit;
                } else {
                    $_SESSION['mensagem_erro'] = "Erro ao cadastrar coleta.";
                }
            }
        } catch (PDOException $e) {
            $_SESSION['mensagem_erro'] = "Erro ao cadastrar: " . $e->getMessage();
        }
    }
}

// Buscar dados do gerador para preencher campos
$gerador_data = [];
if (isset($_SESSION['id_usuario'])) {
    try {
        $stmt = $conn->prepare("
            SELECT g.*, e.* 
            FROM geradores g
            LEFT JOIN enderecos e ON g.id_endereco = e.id
            WHERE g.id = :id_usuario
        ");
        $stmt->bindParam(':id_usuario', $_SESSION['id_usuario']);
        $stmt->execute();
        $gerador_data = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erro ao buscar dados do gerador: " . $e->getMessage());
    }
}

// Função para traduzir status
function traduzirStatus($status) {
    $traducoes = [
        'solicitada' => 'Aguardando Coletor',
        'pendente' => 'Pendente',
        'agendada' => 'Agendada',
        'em_andamento' => 'Em Andamento',
        'concluida' => 'Concluída',
        'cancelada' => 'Cancelada'
    ];
    return $traducoes[$status] ?? $status;
}

// Função para cor do status
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
    <title>Solicitar Coleta - Ecoleta</title>
    <link rel="icon" href="../img/logo.png" type="image/png">
    <link rel="stylesheet" href="../CSS/solicitar-coleta.css">
    <link rel="stylesheet" href="../CSS/navbar.css">
    <link rel="stylesheet" href="../CSS/libras.css">
    <link rel="stylesheet" href="../CSS/acessibilidade.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        .checkbox-group {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #22c55e;
        }

        .checkbox-group label {
            display: flex;
            align-items: center;
            cursor: pointer;
            font-weight: 500;
            color: #1e293b;
        }

        .checkbox-group input[type="checkbox"] {
            width: 20px;
            height: 20px;
            margin-right: 10px;
            cursor: pointer;
            accent-color: #22c55e;
        }

        .checkbox-group label:hover {
            color: #22c55e;
        }

        .checkbox-group.disabled {
            opacity: 0.6;
            background-color: #f5f5f5;
            border-left-color: #ccc;
        }

        .checkbox-group.disabled label {
            cursor: not-allowed;
            color: #999;
        }

        .checkbox-group.disabled input[type="checkbox"] {
            cursor: not-allowed;
        }

        .address-container input:disabled,
        .address-container select:disabled,
        .address-container textarea:disabled {
            background-color: #f5f5f5;
            color: #666;
            cursor: not-allowed;
            opacity: 0.7;
        }

        /* Estilos para exibição da coleta ativa */
        .coleta-ativa-container {
            background: white;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .coleta-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
        }

        .coleta-id {
            font-size: 24px;
            font-weight: 600;
            color: #1e293b;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 14px;
            color: white;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .info-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 12px;
            border-left: 4px solid #22c55e;
        }

        .info-card h3 {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            font-weight: 600;
            color: #64748b;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .info-card p {
            font-size: 16px;
            color: #1e293b;
            font-weight: 500;
            margin: 5px 0;
        }

        .coletor-info {
            display: flex;
            align-items: center;
            gap: 15px;
            background: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 30px;
            border: 2px solid #e5e7eb;
        }

        .coletor-foto {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
        }

        .coletor-dados h3 {
            font-size: 18px;
            color: #1e293b;
            margin-bottom: 5px;
        }

        .coletor-dados p {
            font-size: 14px;
            color: #64748b;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .btn-cancelar {
            flex: 1;
            padding: 14px;
            background: #ef4444;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-cancelar:hover {
            background: #dc2626;
            transform: translateY(-2px);
        }

        .btn-historico {
            flex: 1;
            padding: 14px;
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-historico:hover {
            background: #2563eb;
            transform: translateY(-2px);
        }

        .observacoes-box {
            background: #fff8e1;
            padding: 20px;
            border-radius: 12px;
            border-left: 4px solid #ffc107;
            margin-top: 20px;
        }

        .observacoes-box h3 {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            font-weight: 600;
            color: #f59e0b;
            margin-bottom: 10px;
        }

        .observacoes-box p {
            color: #92400e;
            line-height: 1.6;
        }
    </style>
</head>

<body>
    <!-- VLibras -->
    <div vw class="enabled">
        <div vw-access-button class="active"></div>
        <div vw-plugin-wrapper>
            <div class="vw-plugin-top-wrapper"></div>
        </div>
    </div>

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
                        <a href="#" class="nav-link">
                            <i class="ri-add-circle-line"></i>
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
        </header>

        <!-- Conteúdo Principal -->
        <main class="main-content">
            <header class="content-header">
                <h1><?php echo $coleta_ativa ? 'Coleta em Andamento' : 'Solicitar Coleta'; ?></h1>
                <p><?php echo $coleta_ativa ? 'Acompanhe os detalhes da sua coleta atual' : 'Preencha as informações abaixo para solicitar uma coleta de óleo'; ?></p>
            </header>

            <!-- Mensagens de Sucesso/Erro -->
            <?php if (isset($_SESSION['mensagem_sucesso'])): ?>
                <div class="alert alert-success">
                    <i class="ri-check-circle-line"></i>
                    <?php 
                        echo $_SESSION['mensagem_sucesso']; 
                        unset($_SESSION['mensagem_sucesso']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['mensagem_erro'])): ?>
                <div class="alert alert-error">
                    <i class="ri-error-warning-line"></i>
                    <?php 
                        echo $_SESSION['mensagem_erro']; 
                        unset($_SESSION['mensagem_erro']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if ($coleta_ativa): ?>
                <!-- Exibir detalhes da coleta ativa -->
                <div class="coleta-ativa-container">
                    <div class="coleta-header">
                        <div class="coleta-id">
                            <i class="ri-file-list-3-line"></i> Coleta #<?php echo $coleta_ativa['id']; ?>
                        </div>
                        <div class="status-badge" style="background-color: <?php echo corStatus($coleta_ativa['status']); ?>">
                            <i class="ri-information-line"></i>
                            <?php echo traduzirStatus($coleta_ativa['status']); ?>
                        </div>
                    </div>

                    <?php if ($coleta_ativa['coletor_nome']): ?>
                    <div class="coletor-info">
                        <img src="<?php echo $coleta_ativa['coletor_foto'] ? '../uploads/profile_photos/' . $coleta_ativa['coletor_foto'] : '../img/avatar-default.png'; ?>" 
                             alt="<?php echo htmlspecialchars($coleta_ativa['coletor_nome']); ?>" 
                             class="coletor-foto"
                             onerror="this.src='../img/avatar-default.png'">
                        <div class="coletor-dados">
                            <h3><?php echo htmlspecialchars($coleta_ativa['coletor_nome']); ?></h3>
                            <p>
                                <i class="ri-phone-line"></i> 
                                <?php echo htmlspecialchars($coleta_ativa['coletor_telefone'] ?? 'Não informado'); ?>
                            </p>
                            <button class="btn-ver-perfil-solicitar" onclick="abrirPerfilColetor(<?php echo $coleta_ativa['id_coletor']; ?>)" style="margin-top: 10px; padding: 8px 15px; background-color: #3b82f6; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: 600; transition: all 0.3s; font-size: 14px;">
                                <i class="ri-eye-line"></i> Ver Perfil
                            </button>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="info-card" style="border-left-color: #FFA726;">
                        <h3><i class="ri-time-line"></i> Aguardando Coletor</h3>
                        <p>Um coletor irá aceitar sua solicitação em breve</p>
                    </div>
                    <?php endif; ?>

                    <div class="info-grid">
                        <div class="info-card">
                            <h3><i class="ri-calendar-line"></i> Data e Horário</h3>
                            <p><?php echo date('d/m/Y', strtotime($coleta_ativa['data_agendada'])); ?></p>
                            <p><?php echo $coleta_ativa['periodo'] == 'manha' ? 'Manhã (8h - 12h)' : 'Tarde (13h - 17h)'; ?></p>
                        </div>

                        <div class="info-card">
                            <h3><i class="ri-drop-line"></i> Quantidade de Óleo</h3>
                            <p><?php echo number_format($coleta_ativa['quantidade_oleo'], 1, ',', '.'); ?> litros</p>
                        </div>

                        <div class="info-card">
                            <h3><i class="ri-map-pin-line"></i> Local da Coleta</h3>
                            <p><?php echo htmlspecialchars($coleta_ativa['rua']); ?>, <?php echo htmlspecialchars($coleta_ativa['numero']); ?></p>
                            <?php if ($coleta_ativa['complemento']): ?>
                            <p style="font-size: 14px; color: #64748b;"><?php echo htmlspecialchars($coleta_ativa['complemento']); ?></p>
                            <?php endif; ?>
                            <p><?php echo htmlspecialchars($coleta_ativa['bairro']); ?> - <?php echo htmlspecialchars($coleta_ativa['cidade']); ?>/<?php echo htmlspecialchars($coleta_ativa['estado']); ?></p>
                            <p style="font-size: 14px; color: #64748b;">CEP: <?php echo htmlspecialchars($coleta_ativa['cep']); ?></p>
                        </div>
                    </div>

                    <?php if ($coleta_ativa['observacoes']): ?>
                    <div class="observacoes-box">
                        <h3><i class="ri-chat-3-line"></i> Observações</h3>
                        <p><?php echo nl2br(htmlspecialchars($coleta_ativa['observacoes'])); ?></p>
                    </div>
                    <?php endif; ?>

                    <div class="action-buttons">
                        <a href="historico.php" class="btn-historico">
                            <i class="ri-history-line"></i>
                            Ver Histórico
                        </a>
                        <?php if ($coleta_ativa['status'] == 'solicitada' || $coleta_ativa['status'] == 'pendente'): ?>
                        <form method="POST" style="flex: 1;" onsubmit="return confirm('Tem certeza que deseja cancelar esta coleta?');">
                            <input type="hidden" name="cancelar_coleta" value="1">
                            <button type="submit" class="btn-cancelar">
                                <i class="ri-close-circle-line"></i>
                                Cancelar Coleta
                            </button>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <!-- Formulário de nova coleta -->
                <form class="collection-form" method="POST">
                    
                    <input type="hidden" id="coletor_id" name="coletor_id" value="">
                    
                    <input type="hidden" id="default_cep" value="<?php echo htmlspecialchars($gerador_data['cep'] ?? ''); ?>">
                    <input type="hidden" id="default_rua" value="<?php echo htmlspecialchars($gerador_data['rua'] ?? ''); ?>">
                    <input type="hidden" id="default_numero" value="<?php echo htmlspecialchars($gerador_data['numero'] ?? ''); ?>">
                    <input type="hidden" id="default_complemento" value="<?php echo htmlspecialchars($gerador_data['complemento'] ?? ''); ?>">
                    <input type="hidden" id="default_bairro" value="<?php echo htmlspecialchars($gerador_data['bairro'] ?? ''); ?>">
                    <input type="hidden" id="default_cidade" value="<?php echo htmlspecialchars($gerador_data['cidade'] ?? ''); ?>">
                    <input type="hidden" id="default_estado" value="<?php echo htmlspecialchars($gerador_data['estado'] ?? 'SP'); ?>">
                    
                    <div class="form-section">
                        <h2>Quantidade de Óleo</h2>
                        <div class="form-group">
                            <label for="volume">Volume aproximado de óleo (em litros)</label>
                            <div class="volume-input">
                                <input type="number" id="volume" name="volume" min="1" step="0.5" required>
                                <span class="unit">L</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h2>Tipo de Coleta</h2>
                        <div class="collection-type-options">
                            <label class="radio-card">
                                <input type="radio" name="tipo_coleta" value="automatico" checked>
                                <div class="radio-card-content">
                                    <i class="ri-map-pin-user-line"></i>
                                    <h3>Coletor Próximo</h3>
                                    <p>Um coletor disponível na sua região irá aceitar a coleta</p>
                                </div>
                            </label>

                            <label class="radio-card">
                                <input type="radio" name="tipo_coleta" value="especifico">
                                <div class="radio-card-content">
                                    <i class="ri-user-search-line"></i>
                                    <h3>Escolher Coletor</h3>
                                    <p>Selecione um coletor específico para realizar a coleta</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="form-section" id="coletor-selection" style="display: none;">
                        <h2>Selecionar Coletor</h2>
                        <div id="coletores-list" class="coletores-grid">
                            <p style="color: #999; text-align: center; padding: 20px;">Preencha o CEP primeiro para carregar os coletores disponíveis</p>
                        </div>
                    </div>

                    <div class="form-section">
                        <h2>Local de Coleta</h2>
                        
                        <?php if (!empty($gerador_data['cep'])): ?>
                        <div class="checkbox-group">
                            <label>
                                <input type="checkbox" id="usar_endereco_padrao">
                                <span><i class="ri-home-smile-line"></i> Usar meu endereço cadastrado</span>
                            </label>
                        </div>
                        <?php else: ?>
                        <div class="checkbox-group disabled">
                            <label>
                                <input type="checkbox" id="usar_endereco_padrao" disabled>
                                <span><i class="ri-information-line"></i> Você ainda não possui um endereço cadastrado</span>
                            </label>
                        </div>
                        <?php endif; ?>
                        
                        <div class="address-container">
                            <div class="form-group">
                                <label for="cep">CEP</label>
                                <input type="text" id="cep" name="cep" required pattern="[0-9]{5}-?[0-9]{3}" 
                                       placeholder="00000-000">
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="rua">Rua</label>
                                    <input type="text" id="rua" name="rua" required>
                                </div>
                                <div class="form-group number">
                                    <label for="numero">Número</label>
                                    <input type="text" id="numero" name="numero" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="complemento">Complemento (opcional)</label>
                                <input type="text" id="complemento" name="complemento">
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
                            <div class="map-container">
                                <div id="map"></div>
                                <input type="hidden" id="latitude" name="latitude">
                                <input type="hidden" id="longitude" name="longitude">
                                <input type="hidden" id="estado" name="estado" value="SP">
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h2>Preferência de Coleta</h2>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="data">Data preferencial</label>
                                <input type="date" id="data" name="data" required>
                            </div>
                            <div class="form-group">
                                <label for="periodo">Período</label>
                                <select id="periodo" name="periodo" required>
                                    <option value="">Selecione um período</option>
                                    <option value="manha">Manhã (8h - 12h)</option>
                                    <option value="tarde">Tarde (13h - 17h)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h2>Observações</h2>
                        <div class="form-group">
                            <label for="observacoes">Informações adicionais (opcional)</label>
                            <textarea id="observacoes" name="observacoes" rows="3"
                                placeholder="Ex.: O óleo está armazenado em garrafas PET, portão azul, etc."></textarea>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="window.location.href='home.php'">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Solicitar Coleta</button>
                    </div>
                </form>
            <?php endif; ?>
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
    </div>

    <!-- Modal de Perfil do Coletor -->
    <div id="modalPerfilColetor" class="modal-perfil-coletor">
        <div class="modal-perfil-content">
            <button class="modal-perfil-close">&times;</button>
            <div id="perfilColetorConteudo" class="perfil-coletor-conteudo">
                <div style="text-align: center; padding: 40px;">
                    <div style="display: inline-block; width: 40px; height: 40px; border: 4px solid #3b82f6; border-radius: 50%; border-top: 4px solid transparent; animation: spin 1s linear infinite;"></div>
                    <p style="margin-top: 15px; color: #666;">Carregando perfil...</p>
                </div>
            </div>
        </div>
    </div>

    <style>
        .modal-perfil-coletor {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.3s;
        }

        .modal-perfil-coletor.show {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .modal-perfil-content {
            background-color: white;
            padding: 30px;
            border-radius: 12px;
            max-width: 600px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            position: relative;
        }

        .modal-perfil-close {
            position: absolute;
            right: 15px;
            top: 15px;
            background: none;
            border: none;
            font-size: 28px;
            cursor: pointer;
            color: #999;
        }

        .modal-perfil-close:hover {
            color: #333;
        }

        .perfil-coletor-conteudo {
            padding-top: 20px;
        }

        .perfil-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
        }

        .perfil-foto {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
            border: 4px solid #3b82f6;
        }

        .perfil-nome {
            font-size: 24px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 5px;
        }

        .perfil-tipo {
            font-size: 14px;
            color: #64748b;
            background-color: #e0f2fe;
            padding: 5px 15px;
            border-radius: 20px;
            display: inline-block;
        }

        .perfil-info-section {
            margin-bottom: 25px;
        }

        .perfil-info-title {
            font-size: 14px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .perfil-info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px;
            background-color: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 8px;
        }

        .perfil-info-label {
            font-weight: 500;
            color: #475569;
        }

        .perfil-info-value {
            color: #1e293b;
            font-weight: 600;
        }

        .perfil-avaliacao {
            background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 8px;
        }

        .perfil-stars {
            font-size: 18px;
            color: #ffc107;
        }

        .perfil-transporte {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }

        .perfil-transporte-icon {
            font-size: 24px;
        }

        .perfil-transporte-info {
            flex: 1;
        }

        .perfil-transporte-label {
            font-size: 12px;
            color: #64748b;
        }

        .perfil-transporte-valor {
            font-weight: 600;
            color: #1e293b;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .btn-ver-perfil-solicitar:hover {
            background-color: #2563eb;
            transform: translateY(-2px);
        }
    </style>

    <script>
        function abrirPerfilColetor(idColetor) {
            const modal = document.getElementById('modalPerfilColetor');
            const conteudo = document.getElementById('perfilColetorConteudo');
            
            modal.classList.add('show');

            fetch(`../api/get_perfil_coletor.php?id=${idColetor}`)
                .then(response => response.json())
                .then(data => {
                    if (data.sucesso) {
                        const coletor = data.coletor;
                        const estrelas = Array(5).fill('<i class="ri-star-fill"></i>').slice(0, Math.round(coletor.avaliacao_media)).join('');
                        const estrelasBrancas = Array(5 - Math.round(coletor.avaliacao_media)).fill('<i class="ri-star-line"></i>').join('');

                        const tipoTransporte = {
                            'carro': '🚗 Carro',
                            'moto': '🏍️ Motocicleta',
                            'bicicleta': '🚴 Bicicleta',
                            'carroca': '🚐 Carroça',
                            'a_pe': '🚶 A Pé'
                        };

                        conteudo.innerHTML = `
                            <div class="perfil-coletor-conteudo">
                                <div class="perfil-header">
                                    <img src="${coletor.foto_url ? '../' + coletor.foto_url : '../img/avatar-default.png'}" alt="${coletor.nome_completo}" class="perfil-foto" onerror="this.src='../img/avatar-default.png'">
                                    <div class="perfil-nome">${coletor.nome_completo}</div>
                                    <div class="perfil-tipo">${coletor.tipo_coletor === 'pessoa_fisica' ? 'Pessoa Física' : 'Pessoa Jurídica'}</div>
                                </div>

                                <div class="perfil-info-section">
                                    <div class="perfil-info-title">
                                        <i class="ri-phone-line"></i> Contato
                                    </div>
                                    <div class="perfil-info-item">
                                        <span class="perfil-info-label">Telefone</span>
                                        <span class="perfil-info-value">${coletor.telefone}</span>
                                    </div>
                                    <div class="perfil-info-item">
                                        <span class="perfil-info-label">Email</span>
                                        <span class="perfil-info-value">${coletor.email}</span>
                                    </div>
                                </div>

                                <div class="perfil-info-section">
                                    <div class="perfil-info-title">
                                        <i class="ri-star-line"></i> Avaliação
                                    </div>
                                    <div class="perfil-avaliacao">
                                        <div class="perfil-stars">${estrelas}${estrelasBrancas}</div>
                                        <div style="margin-top: 8px; color: #333; font-weight: 600;">
                                            ${parseFloat(coletor.avaliacao_media).toFixed(1)} / 5.0 (${coletor.total_avaliacoes} avaliações)
                                        </div>
                                    </div>
                                </div>

                                <div class="perfil-info-section">
                                    <div class="perfil-info-title">
                                        <i class="ri-truck-line"></i> Meio de Transporte
                                    </div>
                                    <div class="perfil-transporte">
                                        <div class="perfil-transporte-icon">${tipoTransporte[coletor.meio_transporte]?.split(' ')[0] || '🚗'}</div>
                                        <div class="perfil-transporte-info">
                                            <div class="perfil-transporte-label">Transporta com</div>
                                            <div class="perfil-transporte-valor">${tipoTransporte[coletor.meio_transporte] || coletor.meio_transporte}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="perfil-info-section">
                                    <div class="perfil-info-title">
                                        <i class="ri-history-line"></i> Estatísticas
                                    </div>
                                    <div class="perfil-info-item">
                                        <span class="perfil-info-label">Total de Coletas</span>
                                        <span class="perfil-info-value">${coletor.coletas}</span>
                                    </div>
                                    <div class="perfil-info-item">
                                        <span class="perfil-info-label">Óleo Total Coletado</span>
                                        <span class="perfil-info-value">${parseFloat(coletor.total_oleo).toFixed(1)}L</span>
                                    </div>
                                </div>
                            </div>
                        `;
                    } else {
                        conteudo.innerHTML = `<div style="text-align: center; padding: 40px; color: #e74c3c;"><i class="ri-error-warning-line" style="font-size: 48px; display: block; margin-bottom: 15px;"></i><p>${data.mensagem}</p></div>`;
                    }
                })
                .catch(error => {
                    conteudo.innerHTML = `<div style="text-align: center; padding: 40px; color: #e74c3c;"><i class="ri-error-warning-line" style="font-size: 48px; display: block; margin-bottom: 15px;"></i><p>Erro ao carregar perfil</p></div>`;
                });
        }

        document.querySelector('.modal-perfil-close').addEventListener('click', () => {
            document.getElementById('modalPerfilColetor').classList.remove('show');
        });

        document.getElementById('modalPerfilColetor').addEventListener('click', (e) => {
            if (e.target === document.getElementById('modalPerfilColetor')) {
                document.getElementById('modalPerfilColetor').classList.remove('show');
            }
        });
    </script>

    <script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
    <script>
        new window.VLibras.Widget('https://vlibras.gov.br/app');
    </script>
    <?php if (!$coleta_ativa): ?>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAe884hZ7UbSCJDuS4hkEWrR-ls0XVBe_U&libraries=places"></script>
    <script src="../JS/solicitar-coleta.js"></script>
    <?php endif; ?>
    <script src="../JS/navbar.js"></script>
    <script src="../JS/acessibilidade.js"></script><script src="../JS/libras.js"></script>
</body>

</html>


