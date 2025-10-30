<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitar Coleta - Ecoleta</title>
    <link rel="icon" href="../img/logo.png" type="image/png">
    <link rel="stylesheet" href="../CSS/solicitar-coleta.css">
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
            <header class="content-header">
                <h1>Solicitar Coleta</h1>
                <p>Preencha as informações abaixo para solicitar uma coleta de óleo</p>
            </header>

            <form class="collection-form" action="processar-solicitacao.php" method="POST">
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

                <!-- Local de Coleta -->
                <div class="form-section">
                    <h2>Local de Coleta</h2>
                    <div class="address-container">
                        <div class="form-group">
                            <label for="cep">CEP</label>
                            <input type="text" id="cep" name="cep" required pattern="[0-9]{5}-?[0-9]{3}">
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
                        <!-- Mapa -->
                        <div class="map-container">
                            <div id="map"></div>
                            <input type="hidden" id="latitude" name="latitude">
                            <input type="hidden" id="longitude" name="longitude">
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

    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAe884hZ7UbSCJDuS4hkEWrR-ls0XVBe_U&libraries=places"></script>
    <script src="../JS/solicitar-coleta.js"></script>
</body>

</html>