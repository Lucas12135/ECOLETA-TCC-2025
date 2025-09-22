<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmação de Cadastro</title>
    <link rel="icon" href="../img/logo.png" type="image/png">
    <link rel="stylesheet" href="../CSS/final.css">
</head>
<body>

    <!-- Header -->
    <div class="header">
        <div class="header-logo">
            <img src="../img/logo.png" alt="Logo Portal do Coletor" class="logo-image">
        </div>
        <span class="header-text">Portal de Cadastro</span>
    </div>

    <div class="container">
        <div class="main-content">
            <!-- Card de Ilustração -->
            <div class="illustration-card">
                <img src="../img/icone2.jpeg" alt="Ilustração do Coletor" class="illustration">
            </div>

            <!-- Card de Confirmação -->
            <div class="confirmation-card">
                <h2>Obrigado por se juntar à<br>Equipe de Coletores Ecoleta</h2>
                <h3>Parabéns!</h3>
                <p>A Ecoleta espera por você</p>
                <button id="visitProfileBtn" class="profile-button">Visitar Perfil</button>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('visitProfileBtn').addEventListener('click', function() {
            // Lógica do JavaScript aqui
            // Exemplo: redirecionar o usuário
            console.log("Botão 'Visitar Perfil' clicado!");
            // window.location.href = "sua_pagina_de_perfil.html";
            // Para este exemplo, apenas um alerta para demonstrar a interatividade.
            alert("Redirecionando para o perfil do usuário...");
        });
    </script>
</body>
</html>
