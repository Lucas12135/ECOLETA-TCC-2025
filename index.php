<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página Inicial</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <link rel="icon" href="img/logo.png" type="image/png">
    <link rel="stylesheet" href="CSS/index.css">
</head>
<body class="overflow-x-hidden">
    <!-- Header -->
    <header class="header">
        <div class="header-logo">
            <div class="logo-placeholder">
                <img src="img/logo.png" alt="Logo Reciclagem" class="logo-image">
            </div>
            
        <div class="header-nav">
            <a href="#">Home</a>
        </div>
        </div>
        <div class="header-buttons">
            <button class="btn btn-account" onclick="location.href='CADASTRO_COLETOR/login.php'">Criar Conta</button>
            <button class="btn btn-login" id="open-login-modal-btn">Entrar</button>
            <div class="menu-icon">
                <div></div>
                <div></div>
                <div></div>
            </div>
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
        
        <!-- Cards Section -->
        <div class="cards-section">
            <!-- Card 1: Ecopontos -->
            <div class="card" style="background-color: #447B56;">
                <div class="card-overlay"></div>
                <div class="card-content">
                    <h2>EMPRESAS</h2>
                    <img src="img/ecoponto.png" alt="Ícone Ecoponto" class="card-image">
                    <button class="card-button">
                        <span>Ver opções</span>
                        <!-- Arrow Icon SVG -->
                        <svg class="arrow-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10.293 15.707a1 1 0 010-1.414L14.586 10l-4.293-4.293a1 1 0 111.414-1.414l5 5a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            <path fill-rule="evenodd" d="M4.293 15.707a1 1 0 010-1.414L8.586 10 4.293 5.707a1 1 0 011.414-1.414l5 5a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Card 2: Coletores -->
            <div class="card" style="background-color: #E2B633;">
                <div class="card-content card-content-green">
                    <h2>PESSOAS FÍSICAS</h2>
                    <img src="img/garrafa_de_oleo.png" alt="Ícone de garrafa de óleo" class="card-image">
                    <button class="card-button yellow" data-bs-toggle="modal" data-bs-target="#modal">
                        <span>Ver opções</span>
                        <!-- Arrow Icon SVG -->
                        <svg class="arrow-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10.293 15.707a1 1 0 010-1.414L14.586 10l-4.293-4.293a1 1 0 111.414-1.414l5 5a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            <path fill-rule="evenodd" d="M4.293 15.707a1 1 0 010-1.414L8.586 10 4.293 5.707a1 1 0 011.414-1.414l5 5a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </main>
    <!-- Modal -->
<div class="modal fade" id="modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
            <button type="button" class="close-btn" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-body">
                <h2 class="modal-title">Encontre coletores da sua região</h2>
                <p class="modal-subtitle">A rapidez que você precisa está aqui!</p>
                <div class="modal-input-container">
                    <input type="text" placeholder="Insira sua localização">
                    <!-- Search Icon SVG -->
                    <svg class="modal-search-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8A4 4 0 008 4zM16 16l-4.9-4.9" clip-rule="evenodd" />
                    </svg>
                </div>
            </div>
        </div>
  </div>
</div>

    <script src="JS/index.js"></script>
</body>
</html>
