<?php
session_start();

// Verifica se o usuário está logado e qual seu tipo
$usuarioLogado = isset($_SESSION['id_usuario']);
$tipoUsuario = $_SESSION['tipo_usuario'] ?? null;
$nomeCompleto = $_SESSION['nome_usuario'] ?? 'Usuário';

// Extrai primeiro e último nome
$nomePartes = explode(' ', trim($nomeCompleto));
$primeiroNome = $nomePartes[0] ?? 'Usuário';
$ultimoNome = end($nomePartes);
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página Inicial</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" href="img/logo.png" type="image/png">
    <link rel="stylesheet" href="CSS/global.css">
    <link rel="stylesheet" href="CSS/index.css">
    <link rel="stylesheet" href="CSS/acessibilidade.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

</head>

<body>
    <!-- Header -->
    <header>
        <div class="header-container">
            <div class="logo">
                <div class="logo-placeholder">
                    <img src="img/logo.png" alt="Logo" />
                </div>
                <span class="logo-text">Página Inicial</span>
            </div>
            <nav>
                <?php if ($usuarioLogado): ?>
                    <!-- Usuário Logado -->
                    <div class="user-menu">
                        <span class="welcome-text">Olá, <?php echo htmlspecialchars($primeiroNome) . ' ' . htmlspecialchars($ultimoNome); ?>!</span>
                        <?php if ($tipoUsuario === 'coletor'): ?>
                            <a href="PAGINAS_COLETOR/home.php" class="btn-filled">Ver Dashboard</a>
                        <?php elseif ($tipoUsuario === 'gerador'): ?>
                            <a href="PAGINAS_GERADOR/home.php" class="btn-filled">Ver Dashboard</a>
                        <?php elseif ($tipoUsuario === 'admin'): ?>
                            <a href="ADMIN/dashboard.php" class="btn-filled">Ver Dashboard</a>
                        <?php endif; ?>
                        <a href="logout.php" class="btn-outline">Sair</a>
                    </div>
                <?php else: ?>
                    <!-- Usuário Não Logado -->
                    <a href="cadastros.php" class="btn-outline">Criar Conta</a>
                    <a href="logins.php" class="btn-filled">Entrar</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>
    <!-- Main Content -->
    <main class="main-content">
        <div class="hero-text">
            <h1>Recicle e facilite o seu dia a dia.</h1>
            <p>A rapidez que você precisa está aqui!</p>
        </div>

        <!-- Search Bar -->
        <div class="search-bar">
            <div class="search-input-container">
                <input type="text" placeholder="Local de retirada">
                <!-- Location Icon SVG -->
                <svg class="location-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                </svg>
            </div>
            <button class="search-button">Buscar</button>
        </div>

        <div class="coletores-proximos">
            <h1 class="coletores-titulo">
                Coletores mais bem avaliados perto de você
            </h1>

            <!-- Container dos Cards (Responsivo: Grid em Desktop, Coluna em Mobile) -->
            <section class="coletores-container">
            </section>
        </div>

        <!-- Seção de Vantagens -->
        <section class="benefits-section">
            <h2 class="title-section">Por que se tornar um coletor?</h2>
            <!-- Bootstrap Carousel -->
            <div id="benefitsCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    <button type="button" data-bs-target="#benefitsCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                    <button type="button" data-bs-target="#benefitsCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
                    <button type="button" data-bs-target="#benefitsCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
                    <button type="button" data-bs-target="#benefitsCarousel" data-bs-slide-to="3" aria-label="Slide 4"></button>
                    <button type="button" data-bs-target="#benefitsCarousel" data-bs-slide-to="4" aria-label="Slide 5"></button>
                </div>

                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <div class="benefit-card card-green-bg">
                            <h3>Flexibilidade</h3>
                            <p>Defina seus próprios horários e áreas de atuação</p>
                        </div>
                    </div>

                    <div class="carousel-item">
                        <div class="benefit-card card-green-bg">
                            <h3>Renda Extra</h3>
                            <p>Ganhe dinheiro ajudando o meio ambiente</p>
                        </div>
                    </div>

                    <div class="carousel-item">
                        <div class="benefit-card card-green-bg">
                            <h3>Impacto Social</h3>
                            <p>Contribua para um mundo mais sustentável</p>
                        </div>
                    </div>

                    <div class="carousel-item">
                        <div class="benefit-card card-green-bg">
                            <h3>Reconhecimento</h3>
                            <p>Ganhe destaque na comunidade local</p>
                        </div>
                    </div>

                    <div class="carousel-item">
                        <div class="benefit-card card-green-bg">
                            <h3>Crescimento</h3>
                            <p>Expanda sua rede de contatos e oportunidades</p>
                        </div>
                    </div>
                </div>

                <button class="carousel-control-prev" type="button" data-bs-target="#benefitsCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#benefitsCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>

            <?php if (!$usuarioLogado): ?>
                <button class="main-action-btn" onclick="location.href='cadastros.php'">Vire um Coletor</button>
            <?php endif; ?>
        </section>

        <!-- Portal do Coletor -->
        <div class="layout-grid">
            <!-- COLUNA DA ESQUERDA -->
            <section class="coluna-esquerda">
                <div class="hero-section">
                    <div class="hero-logo"><img src="img/logo.png" alt="Logo Reciclagem" class="logo-image" style='width: 80px; height: 80px;'></div>
                    <div class="hero-texto">
                        <h1>Ecoleta</h1>
                        <h2>Você conta com:</h2>
                    </div>
                </div>

                <div class="feature-card">
                    <h3>Reconhecimento do trabalho.</h3>
                    <p>O aplicativo transforma o "catador" em "Coletor Parceiro" ou "Agente Ambiental Certificado" pela plataforma. Isso é reforçado por um perfil visível aos usuários, que inclui nome, foto, e histórico de coletas realizadas, construindo uma reputação digital e credibilidade.</p>
                </div>

                <div class="feature-card">
                    <h3>Apoio aos coletores.</h3>
                    <p>A essência tecnológica do projeto: o algoritmo de roteirização (baseado em GPS) agrupa coletas próximas e sugere a rota mais eficiente. O coletor recebe o endereço e as informações de volume do material antes de aceitar, otimizando o uso do tempo e combustível.</p>
                </div>
            </section>

            <!-- COLUNA DA DIREITA -->
            <section class="coluna-direita">
                <div class="feature-card">
                    <h3>Orientar os consumidores.</h3>
                    <p>O aplicativo deve oferecer guias de descarte e dicas de preparação do material (ex.: como armazenar óleo de cozinha). Isso assegura que o coletor receba um material de melhor qualidade (mais valorizado) e engaja o consumidor na logística reversa.</p>
                </div>

                <div class="feature-card">
                    <h3>Comunicação Padronizada.</h3>
                    <p>O app padroniza a comunicação da solicitação. O consumidor indica o material por meio de menus estruturados, e o coletor recebe a informação de forma clara e objetiva, minimizando erros e mal-entendidos.</p>
                </div>
            </section>
        </div>
        </div>

        <div class="right">
        </div>
    
    <!-- Botões de Acessibilidade - Fora da div.right para evitar filtros -->
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

        <!-- Tamanho de Texto -->
        <div class="accessibility-group">
            <div class="accessibility-group-title">Tamanho de Texto</div>
            <div class="size-control">
                <span class="size-label">A</span>
                <input type="range" class="size-slider" min="50" max="150" value="100">
                <span class="size-label" style="font-weight: bold;">A</span>
                <span class="size-value">100%</span>
            </div>
        </div>

        <!-- Opções de Visão -->
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

        <!-- Opções de Fonte -->
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

        <!-- Opções de Espaçamento -->
        <div class="accessibility-group">
            <div class="accessibility-group-title">Espaçamento</div>
            <div class="accessibility-options">
                <label class="accessibility-option">
                    <input type="checkbox" id="increased-spacing">
                    <span>Aumentar Espaçamento</span>
                </label>
            </div>
        </div>

        <!-- Opções de Foco e Cursor -->
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

        <!-- Botão de Reset -->
        <button class="accessibility-reset-btn">Restaurar Padrões</button>
    </div>

    <!-- Botão de Libras Separado -->
    <div class="libras-button" id="librasButton" onclick="toggleAccessibility(event)" title="Libras">
        👋
    </div>
    <!-- Modal -->
    <div class="modal fade" id="modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <button type="button" class="close-btn" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="modal-body">
                    <h2 class="modal-title">Encontre coletores da sua região</h2>
                    <p class="modal-subtitle">A rapidez que você precisa está aqui!</p>
                    <div class="modal-input-container">
                        <input type="text" id="modalLocationInput" placeholder="Insira sua localização">
                        <!-- Location Icon SVG -->
                        <svg class="modal-search-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div id="map-container" style="margin-top: 20px;">
                        <div id="map" style="height: 400px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer-principal">
        <div class="footer-container-amarelo">
            <div class="footer-links">
                <span class="footer-titulo-redes">Siga-nos nas redes sociais</span>
                <div class="icones-sociais">
                    <!-- Placeholders para ícones -->
                    <a href="#" class="icone-social"><img src="img/youtube-icon.png" alt="YouTube" class='logo-image'></a> <!-- YouTube (Play) -->
                    <a href="https://www.instagram.com/ecoleta0?igsh=MXNoNGR1YXhrbWdx" target="_blank" class="icone-social"><img src="img/instagram-icon.png" alt="Instagram" class='logo-image'></a> <!-- Instagram (Camera) -->
                    <a href="#" class="icone-social pix-icon"><img src="img/facebook-icon.png" alt="Pix" class='logo-image'></a> <!-- Pix (Texto) -->
                    <a href="#" class="icone-social ecoleta-icon"><img src="img/logo.png" alt="Ecoleta" class='logo-image-ecoleta'></a> <!-- Ecoleta (Emoji) -->
                </div>
            </div>
            <a href="#" class="footer-cta">
                <span>Seja coletor Ecoleta</span>
                <!-- Reutilizando o placeholder de logo do header -->
                <div class="footer-logo"><img src="img/logo.png" alt="Logo Reciclagem" class="logo-image" style='width: 80px; height: 80px;'></div>
            </a>
        </div>
        <div class="footer-copyright">
            &copy; 2025 Ecoleta | Portal do coletor
        </div>
    </footer>
    
    <div vw class="enabled">
        <div vw-access-button class="active"></div>
        <div vw-plugin-wrapper>
            <div class="vw-plugin-top-wrapper"></div>
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
            z-index: 2000;
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

        .btn-solicitar-coleta {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-solicitar-coleta:hover:not(.btn-disabled) {
            background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(34, 197, 94, 0.3);
        }

        .btn-solicitar-coleta:active:not(.btn-disabled) {
            transform: translateY(0);
        }

        .btn-solicitar-coleta.btn-disabled {
            background: linear-gradient(135deg, #d1d5db 0%, #9ca3af 100%);
            cursor: not-allowed;
            opacity: 0.6;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    </style>

    <script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
    <script src="JS/perfil-coletor.js"></script>
    <script src="JS/coletores-proximos.js"></script>
    <script src="JS/index.js"></script>
    <script src="JS/acessibilidade.js"></script>
    <script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAe884hZ7UbSCJDuS4hkEWrR-ls0XVBe_U&libraries=places,marker&v=beta&callback=initAutocomplete&loading=async" async defer></script>
    <script>
        // Dados do usuário logado (via PHP)
        usuarioLogado = <?php echo json_encode($usuarioLogado); ?>;
        tipoUsuario = <?php echo json_encode($tipoUsuario); ?>;

        // Inicializa o carousel do Bootstrap
        const benefitsCarousel = new bootstrap.Carousel(document.querySelector('#benefitsCarousel'), {
            interval: 3500,
            wrap: true
        });
    </script>
</body>

</html>