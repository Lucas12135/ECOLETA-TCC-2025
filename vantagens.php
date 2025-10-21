<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vantagens do Coletor</title>
    <link rel="icon" href="img/logo.png" type="image/png">
    <link rel="stylesheet" href="CSS/vantagens.css">
</head>
<body>
    
    <!-- Faixa Verde no Topo -->
    <div class="header-top-band"></div>

    <!-- Header -->
      <header>
    <div class="header-container">
      <div class="logo">
        <div class="logo-placeholder">
          <img src="img/logo.png" alt="Logo Portal do Coletor">
        </div>
        <span class="logo-text">Portal do Coletor</span>
      </div>
      <nav>
        <a href="../index.php" class="btn-outline">Home</a>
        <a href="#" class="btn-filled">Entrar</a>
        <div class="menu-icon" onclick="toggleMenu()" id="menuIcon">
          <span></span>
          <span></span>
          <span></span>
        </div>
      </nav>
      
      <div class="mobile-menu" id="mobileMenu">
        <div class="mobile-menu-item">
          <div class="mobile-menu-emoji">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-tools" viewBox="0 0 16 16" stroke-width="1.5">
              <path d="M1 0 0 1l2.2 3.081a1 1 0 0 0 .815.419h.07a1 1 0 0 1 .708.293l2.675 2.675-2.617 2.654A3.003 3.003 0 0 0 0 13a3 3 0 1 0 5.878-.851l2.654-2.617.968.968-.305.914a1 1 0 0 0 .242 1.023l3.27 3.27a.997.997 0 0 0 1.414 0l1.586-1.586a.997.997 0 0 0 0-1.414l-3.27-3.27a1 1 0 0 0-1.023-.242L10.5 9.5l-.96-.96 2.68-2.643A3.005 3.005 0 0 0 16 3q0-.405-.102-.777l-2.14 2.141L12 4l-.364-1.757L13.777.102a3 3 0 0 0-3.675 3.68L7.462 6.46 4.793 3.793a1 1 0 0 1-.293-.707v-.071a1 1 0 0 0-.419-.814zm9.646 10.646a.5.5 0 0 1 .708 0l2.914 2.915a.5.5 0 0 1-.707.707l-2.915-2.914a.5.5 0 0 1 0-.708M3 11l.471.242.529.026.287.445.445.287.026.529L5 13l-.242.471-.026.529-.445.287-.287.445-.529.026L3 15l-.471-.242L2 14.732l-.287-.445L1.268 14l-.026-.529L1 13l.242-.471.026-.529.445-.287.287-.445.529-.026z"/>
            </svg>
          </div>
          <div class="mobile-menu-text">Como funciona</div>
          <div class="mobile-menu-arrow">></div>
        </div>
        <div class="mobile-menu-item">
          <div class="mobile-menu-emoji">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-question-circle" viewBox="0 0 16 16" stroke-width="1.5">
              <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
              <path d="M5.255 5.786a.237.237 0 0 0 .241.247h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286m1.557 5.763c0 .533.425.927 1.01.927.609 0 1.028-.394 1.028-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94"/>
            </svg>
          </div>
          <div class="mobile-menu-text">Ajuda</div>
          <div class="mobile-menu-arrow">></div>
        </div>
        <div class="mobile-menu-item">
          <div class="mobile-menu-emoji">
            <img src="../img/logo.png" alt="Logo Ecoleta">
          </div>
          <div class="mobile-menu-text">Sobre a Ecoleta</div>
          <div class="mobile-menu-arrow">></div>
        </div>
        <div class="mobile-menu-item">
          <div class="mobile-menu-emoji">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 574.859 574.86" width="200" height="200" fill="#ffce46" stroke-width="1.5">
              <g>
                <path d="M181.688,521.185V353.841H19.125v167.344c0,10.566,13.34,23.906,23.906,23.906h124.312
                  C177.91,545.091,181.688,531.751,181.688,521.185z M66.938,502.06c0,2.64-2.142,4.781-4.781,4.781s-4.781-2.142-4.781-4.781
                  V377.748c0-2.64,2.142-4.781,4.781-4.781s4.781,2.142,4.781,4.781V502.06z M105.188,502.06c0,2.64-2.142,4.781-4.781,4.781
                  s-4.781-2.142-4.781-4.781V377.748c0-2.64,2.142-4.781,4.781-4.781s4.781,2.142,4.781,4.781V502.06z M143.438,502.06
                  c0,2.64-2.142,4.781-4.781,4.781s-4.781-2.142-4.781-4.781V377.748c0-2.64,2.142-4.781,4.781-4.781s4.781,2.142,4.781,4.781V502.06
                  z"/>
                <path d="M19.125,334.716h162.562v-19.125h19.125v-19.125h-57.375c0-10.566-6.828-19.125-15.243-19.125H77.399
                  c-8.415,0-15.243,8.559-15.243,19.125H0v19.125h19.125V334.716z"/>
                <path d="M357.007,191.556C370.968,329.811,243.892,542.08,243.892,542.08c145.235-78.212,169.189-207.363,169.189-207.363
                  c42.333,66.479,44.475,228.305,44.475,228.305c80.995-194.109,0-377.049,0-377.049l117.304,48.874
                  c-19.546-74.014-141.047-125.68-141.047-125.68c-110.322,50.27-249.974,44.686-249.974,44.686
                  C259.249,226.469,357.007,191.556,357.007,191.556z"/>
                <circle cx="369.782" cy="55.128" r="43.29"/>
                <path d="M94.43,229.529c5.977-2.391,27.492-13.148,28.764,0c1.271,13.148,11.876,9.562,19.048,0s3.586-25.102,11.953-23.906
                  s15.539-10.758,17.93-21.735c2.391-10.978-22.711-18.905-33.469-21.458s-20.32,13.321-27.492,13.321s-17.93-20.33-25.102-10.768
                  s-11.953,40.641-11.953,40.641c-10.758-5.977-21.516,7.172-25.102,16.734S88.453,231.919,94.43,229.529z"/>
              </g>
            </svg>
          </div>
          <div class="mobile-menu-text">Coletor</div>
          <div class="mobile-menu-arrow">></div>
        </div>
      </div>
    </div>
  </header>

    <!-- Main Content -->
    <main class="main-content">
        <h2 class="title-section">Coletor afiliado da Ecoleta possui uma série de vantagens</h2>

        <!-- Grid de Vantagens -->
        <div class="benefits-grid">
            
            <!-- Card 1: Flexibilidade -->
            <div class="benefit-card card-green-bg">
                <h3>Flexibilidade</h3>
                <p>Total autonomia para definir a própria jornada e área de trabalho.</p>
            </div>

            <!-- Card 2: Capacitação e Suporte -->
            <div class="benefit-card card-green-bg">
                <h3>Capacitação e Suporte</h3>
                <p>A empresa do app pode oferecer treinamentos sobre manuseio correto de resíduos, segurança no trabalho e até mesmo educação financeira, elevando o nível de profissionalismo do coletor.</p>
            </div>

            <!-- Card 3: Acesso à Demanda (Destaque) -->
            <div class="benefit-card card-dark-green-bg">
                <h3>Acesso à Demanda</h3>
                <p>Fonte constante de solicitações de coleta, eliminando a busca por clientes.</p>
            </div>

            <!-- Card 4: Reputação Digital -->
            <div class="benefit-card card-green-bg">
                <h3>Reputação Digital</h3>
                <p>Construção de credibilidade por meio do sistema de avaliação dos usuários.</p>
            </div>

            <!-- Card 5: Roteirização Inteligente -->
            <div class="benefit-card card-green-bg">
                <h3>Roteirização Inteligente</h3>
                <p>Algoritmo que traça a rota mais curta, economizando combustível e tempo.</p>
            </div>
        </div>

        <!-- Pontos de Navegação -->
        <div class="dots-container">
            <div class="dot active" data-card="0"></div>
            <div class="dot" data-card="1"></div>
            <div class="dot" data-card="2"></div>
            <div class="dot" data-card="3"></div>
            <div class="dot" data-card="4"></div>
        </div>

        <!-- Botão Principal -->
        <button class="main-action-btn">Vire um Coletor</button>
    </main>

    <!-- JavaScript para o scroll responsivo e navegação por pontos -->
    <script src="../JS/vantagens.js"></script>
</body>
</html>
