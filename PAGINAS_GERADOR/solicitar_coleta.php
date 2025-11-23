<?php
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../index.php');
    exit;
}

// Definir fuso horário de Brasília
date_default_timezone_set('America/Sao_Paulo');

// Processar formulário
if (!empty($_POST)) {
    $id_gerador = $_SESSION['id_usuario'];
    $quantidade_oleo = $_POST['volume'];
    $tipo_coleta = $_POST['tipo_coleta'] ?? 'automatico';
    
    // Apenas adiciona id_coletor se tipo for específico E o campo existir
    $id_coletor = null;
    if ($tipo_coleta === 'especifico' && isset($_POST['coletor_id']) && !empty($_POST['coletor_id'])) {
        $id_coletor = $_POST['coletor_id'];
    }
    
    $cep = $_POST['cep'];
    $rua = $_POST['rua'];
    $numero = $_POST['numero'];
    $complemento = $_POST['complemento'] ?? '';
    $cidade = $_POST['cidade'];
    $estado = $_POST['estado'];
    $bairro = $_POST['bairro'];
    $data_solicitacao = date('Y-m-d H:i:s'); // Com horário de Brasília
    $data_coleta = $_POST['data'];
    $periodo = $_POST['periodo'];
    $observacoes = $_POST['observacoes'] ?? '';

    include_once('../BANCO/conexao.php');

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
                header('Location: historico.php');
                exit;
            } else {
                $_SESSION['mensagem_erro'] = "Erro ao cadastrar coleta.";
            }
        }
    } catch (PDOException $e) {
        $_SESSION['mensagem_erro'] = "Erro ao cadastrar: " . $e->getMessage();
    }
}

// Buscar dados do gerador para preencher campos
$gerador_data = [];
if (isset($_SESSION['id_usuario'])) {
    include_once('../BANCO/conexao.php');
    try {
        $stmt = $conn->prepare("SELECT * FROM geradores WHERE id_usuario = :id_usuario");
        $stmt->bindParam(':id_usuario', $_SESSION['id_usuario']);
        $stmt->execute();
        $gerador_data = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Silenciosamente ignorar
    }
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
                <h1>Solicitar Coleta</h1>
                <p>Preencha as informações abaixo para solicitar uma coleta de óleo</p>
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

            <form class="collection-form" method="POST">
                
                <!-- Campo hidden para id_coletor -->
                <input type="hidden" id="coletor_id" name="coletor_id" value="">
                
                <!-- Quantidade de Óleo -->
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

                <!-- Tipo de Coleta -->
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

                <!-- Seleção de Coletor (oculto inicialmente) -->
                <div class="form-section" id="coletor-selection" style="display: none;">
                    <h2>Selecionar Coletor</h2>
                    
                    <!-- Lista de Coletores dinâmica -->
                    <div id="coletores-list" class="coletores-grid">
                        <p style="color: #999; text-align: center; padding: 20px;">Preencha o CEP primeiro para carregar os coletores disponíveis</p>
                    </div>
                </div>

                <!-- Local de Coleta -->
                <div class="form-section">
                    <h2>Local de Coleta</h2>
                    <div class="address-container">
                        <div class="form-group">
                            <label for="cep">CEP</label>
                            <input type="text" id="cep" name="cep" required pattern="[0-9]{5}-?[0-9]{3}" 
                                   value="<?php echo htmlspecialchars($gerador_data['cep'] ?? ''); ?>" 
                                   placeholder="00000-000">
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="rua">Rua</label>
                                <input type="text" id="rua" name="rua" required 
                                       value="<?php echo htmlspecialchars($gerador_data['rua'] ?? ''); ?>">
                            </div>
                            <div class="form-group number">
                                <label for="numero">Número</label>
                                <input type="text" id="numero" name="numero" required 
                                       value="<?php echo htmlspecialchars($gerador_data['numero'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="complemento">Complemento (opcional)</label>
                            <input type="text" id="complemento" name="complemento" 
                                   value="<?php echo htmlspecialchars($gerador_data['complemento'] ?? ''); ?>">
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="bairro">Bairro</label>
                                <input type="text" id="bairro" name="bairro" required 
                                       value="<?php echo htmlspecialchars($gerador_data['bairro'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label for="cidade">Cidade</label>
                                <input type="text" id="cidade" name="cidade" required 
                                       value="<?php echo htmlspecialchars($gerador_data['cidade'] ?? ''); ?>">
                            </div>
                        </div>
                        <!-- Mapa -->
                        <div class="map-container">
                            <div id="map"></div>
                            <input type="hidden" id="latitude" name="latitude">
                            <input type="hidden" id="longitude" name="longitude">
                            <input type="hidden" id="estado" name="estado" value="SP">
                        </div>
                    </div>
                </div>

                <!-- Data e Horário -->
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

                <!-- Observações -->
                <div class="form-section">
                    <h2>Observações</h2>
                    <div class="form-group">
                        <label for="observacoes">Informações adicionais (opcional)</label>
                        <textarea id="observacoes" name="observacoes" rows="3"
                            placeholder="Ex.: O óleo está armazenado em garrafas PET, portão azul, etc."></textarea>
                    </div>
                </div>

                <!-- Botões -->
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="window.location.href='home.php'">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Solicitar Coleta</button>
                </div>
            </form>
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
        <div class="libras-button" id="librasButton" onclick="toggleLibras(event)" title="Libras">
            👋
        </div>
    </div>

    <script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
    <script>
        new window.VLibras.Widget('https://vlibras.gov.br/app');
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAe884hZ7UbSCJDuS4hkEWrR-ls0XVBe_U&libraries=places"></script>
    <script src="../JS/solicitar-coleta.js"></script>
    <script src="../JS/navbar.js"></script>
    <script src="../JS/acessibilidade.js"></script><script src="../JS/libras.js"></script>
</body>

</html>


