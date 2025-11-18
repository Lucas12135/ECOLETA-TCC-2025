<?php
session_start();
require_once '../BANCO/conexao.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../index.php');
    exit;
}

// Buscar dados do gerador para pré-preencher o formulário
$id_gerador = $_SESSION['id_usuario'];
$sql_gerador = "SELECT g.email, g.telefone, 
                       e.cep, e.rua, e.numero, e.complemento, e.bairro, e.cidade
                FROM geradores g
                LEFT JOIN enderecos e ON g.id_endereco = e.id
                WHERE g.id = :id";
$stmt_gerador = $conn->prepare($sql_gerador);
$stmt_gerador->bindParam(':id', $id_gerador, PDO::PARAM_INT);
$stmt_gerador->execute();
$gerador_data = $stmt_gerador->fetch(PDO::FETCH_ASSOC);
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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

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

            <form class="collection-form" method="POST">
                <!-- Campos Ocultos -->
                <input type="hidden" name="tipo_coleta_hidden" id="tipo_coleta_hidden" value="automatico">
                <input type="hidden" name="rua_hidden" id="rua_hidden">
                <input type="hidden" name="numero_hidden" id="numero_hidden">
                <input type="hidden" name="complemento_hidden" id="complemento_hidden">
                <input type="hidden" name="bairro_hidden" id="bairro_hidden">
                <input type="hidden" name="cidade_hidden" id="cidade_hidden">

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
                        <!-- Será preenchido dinamicamente após selecionar localização -->
                    </div>
                    
                    <div class="form-group" id="coletor-select-group" style="display: none;">
                        <label for="coletor">Escolha o coletor</label>
                        <select id="coletor" name="coletor_id">
                            <option value="">Selecione um coletor</option>
                        </select>
                    </div>
                    
                    <input type="hidden" id="coletor_id" name="coletor_id" value="">
                </div>

                <!-- Local de Coleta -->
                <div class="form-section">
                    <h2>Local de Coleta</h2>
                    <div class="address-container">
                        <div class="form-group">
                            <label for="cep">CEP</label>
                            <input type="text" id="cep" name="cep" required pattern="[0-9]{5}-?[0-9]{3}" value="<?php echo htmlspecialchars($gerador_data['cep'] ?? ''); ?>">
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="rua">Rua</label>
                                <input type="text" id="rua" name="rua" required value="<?php echo htmlspecialchars($gerador_data['rua'] ?? ''); ?>">
                            </div>
                            <div class="form-group number">
                                <label for="numero">Número</label>
                                <input type="text" id="numero" name="numero" required value="<?php echo htmlspecialchars($gerador_data['numero'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="complemento">Complemento (opcional)</label>
                            <input type="text" id="complemento" name="complemento" value="<?php echo htmlspecialchars($gerador_data['complemento'] ?? ''); ?>">
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="bairro">Bairro</label>
                                <input type="text" id="bairro" name="bairro" required value="<?php echo htmlspecialchars($gerador_data['bairro'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label for="cidade">Cidade</label>
                                <input type="text" id="cidade" name="cidade" required value="<?php echo htmlspecialchars($gerador_data['cidade'] ?? ''); ?>">
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
<div vw class="enabled">
        <div vw-access-button class="active"></div>
        <div vw-plugin-wrapper>
            <div class="vw-plugin-top-wrapper"></div>
        </div>
    </div>


    <script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAe884hZ7UbSCJDuS4hkEWrR-ls0XVBe_U&libraries=places"></script>
    
    <!-- Manuseio de seleção de tipo de coleta -->
    <script>
        // Mostrar/ocultar seleção de coletor
        const tipoColetaInputs = document.querySelectorAll('input[name="tipo_coleta"]');
        const coletorSelection = document.getElementById('coletor-selection');
        
        tipoColetaInputs.forEach(input => {
            input.addEventListener('change', function() {
                if (this.value === 'especifico') {
                    coletorSelection.style.display = 'block';
                    carregarColetores();
                } else {
                    coletorSelection.style.display = 'none';
                    document.getElementById('coletor_id').value = '';
                }
            });
        });
        
        // Carregar coletores baseado na localização
        function carregarColetores() {
            const cidade = document.getElementById('cidade').value.trim();
            const bairro = document.getElementById('bairro').value.trim();
            
            if (!cidade) {
                document.getElementById('coletores-list').innerHTML = '<p style="color: #999; text-align: center;">Preencha a cidade primeiro</p>';
                return;
            }
            
            document.getElementById('coletores-list').innerHTML = '<p style="color: #999; text-align: center;">Carregando coletores...</p>';
            
            // Fazer requisição ao servidor PHP
            fetch('../api/get_coletores_por_localizacao.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'cidade=' + encodeURIComponent(cidade) + '&bairro=' + encodeURIComponent(bairro)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.coletores && data.coletores.length > 0) {
                    exibirColetores(data.coletores);
                } else {
                    document.getElementById('coletores-list').innerHTML = '<p style="color: #999; text-align: center;">Nenhum coletor disponível nesta região</p>';
                }
            })
            .catch(error => {
                console.error('Erro ao carregar coletores:', error);
                document.getElementById('coletores-list').innerHTML = '<p style="color: #d32f2f; text-align: center;">Erro ao carregar coletores</p>';
            });
        }
        
        // Exibir coletores em cards
        function exibirColetores(coletores) {
            const container = document.getElementById('coletores-list');
            container.innerHTML = '';
            
            coletores.forEach(coletor => {
                const card = document.createElement('div');
                card.className = 'coletor-card';
                card.innerHTML = `
                    <div class="coletor-card-header">
                        <img src="${coletor.foto_perfil ? '../uploads/profile_photos/' + coletor.foto_perfil : '../img/default-avatar.png'}" alt="${coletor.nome_completo}" class="coletor-foto">
                    </div>
                    <div class="coletor-card-body">
                        <h3>${coletor.nome_completo}</h3>
                        <p class="coletor-localizacao"><i class="ri-map-pin-line"></i> ${coletor.localizacao}</p>
                        <div class="coletor-info">
                            <div class="rating">
                                <i class="ri-star-fill"></i>
                                <span>${coletor.avaliacao_media} (${coletor.total_avaliacoes})</span>
                            </div>
                            ${coletor.meio_transporte ? '<div><i class="ri-e-bike-2-line"></i> ' + coletor.meio_transporte + '</div>' : ''}
                        </div>
                        ${coletor.experiencia ? '<p class="coletor-experiencia"><i class="ri-award-line"></i> ' + coletor.experiencia + ' anos</p>' : ''}
                    </div>
                    <button type="button" class="btn-select-coletor" data-id="${coletor.id}" data-nome="${coletor.nome_completo}">
                        Selecionar
                    </button>
                `;
                container.appendChild(card);
                
                // Adicionar evento de click ao botão
                card.querySelector('.btn-select-coletor').addEventListener('click', function(e) {
                    e.preventDefault();
                    selecionarColetor(this.dataset.id, this.dataset.nome);
                });
            });
        }
        
        // Selecionar coletor
        function selecionarColetor(id, nome) {
            document.getElementById('coletor_id').value = id;
            
            // Destacar coletor selecionado
            document.querySelectorAll('.coletor-card').forEach(card => {
                card.classList.remove('selected');
                if (card.querySelector('[data-id="' + id + '"]')) {
                    card.classList.add('selected');
                }
            });
            
            // Mostrar mensagem
            const msgDiv = document.createElement('div');
            msgDiv.className = 'success-message';
            msgDiv.textContent = 'Coletor ' + nome + ' selecionado!';
            document.getElementById('coletor-selection').insertBefore(msgDiv, document.getElementById('coletores-list'));
            
            setTimeout(() => msgDiv.remove(), 3000);
        }
        
        // Recarregar coletores quando cidade/bairro mudarem
        document.getElementById('cidade').addEventListener('change', carregarColetores);
        document.getElementById('bairro').addEventListener('change', carregarColetores);
    </script>
    
    <script src="../JS/solicitar-coleta.js"></script>
    <script src="../JS/navbar.js"></script>
    <script src="../JS/libras.js"></script>
</body>

</html>