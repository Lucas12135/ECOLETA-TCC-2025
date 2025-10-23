<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Escolha o tipo</title>
    <link rel="icon" href="img/logo.png" type="image/png">
    <link rel="stylesheet" href="CSS/index.css">
    <link rel="stylesheet" href="CSS/login.css">
    <link rel="stylesheet" href="CSS/cadastros.css">
</head>

<body>
    <header>
        <div class="header-container">
            <div class="logo">
                <div class="logo-placeholder">
                    <img src="img/logo.png" alt="Logo" />
                </div>
                <span class="logo-text">Portal de Cadastro</span>
            </div>
            <nav>
                <a href="index.php" class="btn-outline">Home</a>
                <a href="CADASTRO_COLETOR/login.php" class="btn-filled">Entrar</a>
            </nav>
        </div>
    </header>

    <main class="cadastros-page">
        <div class="cadastros-header">
            <h1>Criar conta</h1>
            <p>Escolha o tipo de conta que deseja criar:</p>
        </div>

        <div class="cadastros-grid">
            <article class="cadastro-card cadastro-gerador">
                <div class="card-content">
                    <h2>Gerador de Óleo</h2>
                    <p>Sou um gerador de óleo e quero agendar coletas.</p>
                </div>
                <div class="card-actions">
                    <a href="cadastro_gerador.php" class="card-cta">Cadastrar</a>
                </div>
            </article>

            <article class="cadastro-card cadastro-coletor">
                <div class="card-content">
                    <h2>Coletor</h2>
                    <p>Quero me cadastrar como coletor afiliado para recolher óleo de geradores.</p>
                </div>
                <div class="card-actions">
                    <a href="CADASTRO_COLETOR/login.php" class="card-cta">Cadastrar</a>
                </div>
            </article>
        </div>
    </main>

    <div vw class="enabled">
        <div vw-access-button class="active"></div>
        <div vw-plugin-wrapper>
            <div class="vw-plugin-top-wrapper"></div>
        </div>
    </div>

    <script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
</body>

</html>