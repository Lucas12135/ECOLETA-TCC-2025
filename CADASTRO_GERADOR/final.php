<?php
session_start();

// Verifica se há dados de cadastro na sessão
if (!isset($_SESSION['cadastro_completo'])) {
    // Se não há confirmação de cadastro, redireciona para login
    header('Location: ../CADASTRO_GERADOR/login.php');
    exit;
}

// Limpa o flag de cadastro completo
unset($_SESSION['cadastro_completo']);
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmação de Cadastro - Gerador</title>
    <link rel="icon" href="../img/logo.png" type="image/png">
    <link rel="stylesheet" href="../CSS/registro.css">
    <link rel="stylesheet" href="../CSS/global.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <!-- ========== ESTRUTURA DO HEADER AUMENTADO ========== -->
    <header>
        <div class="header-container">
            <div class="logo">
                <div class="logo-placeholder">
                    <img src="../img/logo.png" alt="Logo Portal de cadastro - Gerador">
                </div>
                <span class="logo-text">Portal de cadastro - Gerador</span>
            </div>
        </div>
    </header>

    <!-- ========== CONTEUDO PRINCIPAL ========== -->
    <main>
        <div class="left">
            <div class="image-container">
                <img src="../img/icone2.jpeg" alt="Ilustração de confirmação de cadastro">
            </div>
        </div>

        <div class="right">
            <div class="form-box">
                <h2>Cadastro Confirmado!</h2>
                <div class="confirmation-content">
                    <h3>Parabéns!</h3>
                    <p>Obrigado por se juntar à comunidade de geradores Ecoleta</p>
                    <p class="subtitle">A Ecoleta espera por você</p>
                    <button id="visitProfileBtn" class="btn-continue" style="margin-top: 30px; width: 100%;">Ir para Home</button>
                </div>
            </div>
        </div>
    </main>

    <div vw class="enabled">
        <div vw-access-button class="active"></div>
        <div vw-plugin-wrapper>
            <div class="vw-plugin-top-wrapper"></div>
        </div>
    </div>
    <script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
    <script src="../JS/libras.js"></script>
    <script>
        document.getElementById('visitProfileBtn').addEventListener('click', function() {
            window.location.href = "../index.php";
        });
    </script>
</body>

</html>