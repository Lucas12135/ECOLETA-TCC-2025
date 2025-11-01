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
    <link rel="stylesheet" href="../CSS/final.css">
    <link rel="stylesheet" href="../CSS/global.css">
</head>

<body>

    <!-- Header -->
    <div class="header">
        <div class="header-logo">
            <img src="../img/logo.png" alt="Logo Portal de cadastro - Gerador" class="logo-image">
        </div>
        <span class="header-text">Portal de Cadastro - Gerador</span>
    </div>

    <div class="container">
        <div class="main-content">
            <!-- Card de Ilustração -->
            <div class="illustration-card">
                <img src="../img/icone2.jpeg" alt="Ilustração do Coletor" class="illustration">
            </div>

            <!-- Card de Confirmação -->
            <div class="confirmation-card">
                <h2>Obrigado por se cadastrar como gerador!</h2>
                <h3>Parabéns!</h3>
                <p>A Ecoleta espera por você</p>
                <button id="visitProfileBtn" class="profile-button">Visitar Home</button>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('visitProfileBtn').addEventListener('click', function() {
            window.location.href = "../index.php";
        });
    </script>
</body>

</html>