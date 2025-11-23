<?php
session_start();

// Extrai primeiro e último nome
$nomeCompleto = $_SESSION['nome_usuario'] ?? 'Produtor de Óleo usado';
$nomePartes = explode(' ', trim($nomeCompleto));
$primeiroNome = $nomePartes[0] ?? 'Produtor';
$ultimoNome = end($nomePartes);
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Gerador</title>
    <link rel="icon" href="../img/logo.png" type="image/png">
    <link rel="stylesheet" href="../CSS/home-gerador.css">
    <link rel="stylesheet" href="../CSS/navbar.css">
    <link rel="stylesheet" href="../CSS/libras.css">
    <link rel="stylesheet" href="../CSS/acessibilidade.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Biblioteca de ícones -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
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
                    <li class="active">
                        <a href="#" class="nav-link">
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
                    <li>
                        <a href="solicitar_coleta.php" class="nav-link">
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
            <div style="margin-bottom: 20px;">
                <a href="../index.php"><button class="back-button" style="background-color: #ff6b6b; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-size: 14px; display: flex; align-items: center; gap: 8px;"><i class="ri-arrow-left-line"></i>Voltar</button></a>
            </div>
            <header class="content-header">
                <div class="welcome-message">
                    <h1>Olá, <?php echo htmlspecialchars($primeiroNome) . ' ' . htmlspecialchars($ultimoNome); ?>!</h1>
                    <p>Gerencie suas solicitações de coleta de óleo</p>
                </div>
            </header>

            <!-- Cards de Informações -->
            <div class="info-cards">

                <div class="card oil-stats">
                    <h3>Estatísticas de Reciclagem</h3>
                    <div class="card-content">
                        <div class="stat-item">
                            <span class="number">25L</span>
                            <span class="label">Óleo Reciclado</span>
                        </div>
                        <div class="eco-impact">
                            <span class="impact-text">Você ajudou a evitar a poluição de aproximadamente 500.000L de água!</span>
                        </div>
                    </div>
                </div>

                <div class="card quick-actions">
                    <h3>Ações Rápidas</h3>
                    <div class="card-content">
                        <a href="solicitar_coleta.php">
                        <button class="action-btn request-collection">
                            <i class="ri-oil-line"></i>
                            Nova Solicitação
                        </button>
                        </a>
                        <a href="historico.php">
                        <button class="action-btn view-history">
                            <i class="ri-history-line"></i>
                            Ver Histórico
                        </button>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Status da Coleta Atual -->
            <div class="collection-status-container">
                <div class="status-tracking">
                    <h3>Situação da Coleta Atual</h3>
                    <div class="status-timeline">
                        <div class="status-step completed">
                            <div class="step-icon">
                                <i class="ri-check-line"></i>
                            </div>
                            <div class="step-info">
                                <h4>Solicitação Enviada</h4>
                                <span>27/10 - 10:30</span>
                            </div>
                        </div>
                        <div class="status-step completed">
                            <div class="step-icon">
                                <i class="ri-check-line"></i>
                            </div>
                            <div class="step-info">
                                <h4>Coleta Aceita</h4>
                                <span>27/10 - 11:15</span>
                            </div>
                        </div>
                        <div class="status-step active">
                            <div class="step-icon">
                                <i class="ri-time-line"></i>
                            </div>
                            <div class="step-info">
                                <h4>Aguardando Coleta</h4>
                                <span>Agendada para 29/10</span>
                            </div>
                        </div>
                        <div class="status-step">
                            <div class="step-icon">
                                <i class="ri-checkbox-blank-circle-line"></i>
                            </div>
                            <div class="step-info">
                                <h4>Coleta Concluída</h4>
                                <span>Pendente</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="collection-details">
                    <div class="details-card">
                        <h4>Detalhes da Coleta</h4>
                        <div class="detail-item">
                            <i class="ri-oil-line"></i>
                            <span>Volume: 5L</span>
                        </div>
                        <div class="detail-item">
                            <i class="ri-map-pin-line"></i>
                            <span>Local: Rua das Flores, 123</span>
                        </div>
                        <div class="detail-item">
                            <i class="ri-user-line"></i>
                            <span>Coletor: João Silva</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Histórico Recente -->
            <div class="recent-history">
                <h3>Histórico Recente</h3>
                <div class="history-items">
                    <div class="history-item">
                        <div class="history-content">
                            <div class="history-info">
                                <span class="date">15/10/2025</span>
                                <span class="volume">3L</span>
                            </div>
                            <span class="status completed">Concluída</span>
                        </div>
                    </div>
                    <div class="history-item">
                        <div class="history-content">
                            <div class="history-info">
                                <span class="date">01/10/2025</span>
                                <span class="volume">4L</span>
                            </div>
                            <span class="status completed">Concluída</span>
                        </div>
                    </div>
                </div>
            </div>
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
<div vw class="enabled">
        <div vw-access-button class="active"></div>
        <div vw-plugin-wrapper>
            <div class="vw-plugin-top-wrapper"></div>
        </div>
    </div>


    <script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
    <script src="../JS/home-gerador.js"></script>
    <script src="../JS/navbar.js"></script>
    <script src="../JS/acessibilidade.js"></script><script src="../JS/libras.js"></script>
</body>

</html>


