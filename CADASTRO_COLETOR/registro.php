<?php
session_start();

if(!empty($_POST)) {
  $_SESSION['cadastro']['nome'] = $_POST['nome'];
  $_SESSION['cadastro']['cpf'] = preg_replace('/\D/', '', $_POST['cpf']);
  $_SESSION['cadastro']['celular'] = preg_replace('/\D/', '', $_POST['celular']);
  // Redireciona para a próxima página do cadastro
  header('Location: ultregistro.php');
  exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Portal do Coletor - Cadastro</title>
  <link rel="icon" href="../img/logo.png" type="image/png">
  <link rel="stylesheet" href="../CSS/registro.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  
</head>
<body>
  <!-- ========== ESTRUTURA DO HEADER AUMENTADO ========== -->
  <header>
    <div class="header-container">
      <div class="logo">
        <div class="logo-placeholder">
          <img src="../img/logo.png" alt="Logo Portal do Coletor">
        </div>
        <span class="logo-text">Portal do Coletor</span>
      </div>
    </div>
  </header>

  <!-- ========== CONTEUDO PRINCIPAL ========== -->
  <main>
    <div class="left">
      <div class="image-container">
        <img src="../img/icone1.jpeg" alt="Icone de uma mulher coletora, em estilo animado">
      </div>
    </div>

    <div class="right">
      <div class="accessibility-button" onclick="toggleAccessibility(event)" title="Ferramentas de Acessibilidade">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="25" height="25" fill="white">
          <title>accessibility</title>
          <g>
            <circle cx="24" cy="7" r="4"/>
            <path d="M40,13H8a2,2,0,0,0,0,4H19.9V27L15.1,42.4a2,2,0,0,0,1.3,2.5H17a2,2,0,0,0,1.9-1.4L23.8,28h.4l4.9,15.6A2,2,0,0,0,31,45h.6a2,2,0,0,0,1.3-2.5L28.1,27V17H40a2,2,0,0,0,0-4Z"/>
          </g>
        </svg>
      </div>

      <div class="form-box">
        <h2>Primeiros passos</h2>
        <form method="POST" action="#" id="registrationForm" novalidate>
          <div class="form-group">
            <label for="nome" class="field-label">Nome Completo *</label>
            <input type="text" id="fullName" name="nome" placeholder="Digite seu nome completo" required>
            <div class="error-message">Por favor, digite seu nome completo</div>
          </div>

          <div class="form-group">
            <label for="cpf" class="field-label">CPF *</label>
            <input type="text" id="cpf" name="cpf" placeholder="000.000.000-00" maxlength="14" required>
            <div class="error-message">Por favor, digite um CPF válido</div>
          </div>

          <div class="form-group">
            <label for="celular" class="field-label">Telefone *</label>
            <input type="tel" id="phone" name="celular" placeholder="(00) 00000-0000" maxlength="15" required>
            <div class="error-message">Por favor, digite um telefone válido</div>
          </div>

          <label class="checkbox-label">
            <input type="checkbox" id="dataConsent" required>
            <span>Concordo em fornecer meus dados pessoais para o processo de cadastro na Ecoleta</span>
          </label>

          <div class="button-group">
            <button type="button" class="btn-back" onclick="goBack()">Voltar</button>
            <button type="submit" class="btn-continue" href="ultregistro.html">Continuar Cadastro</button>
          </div>
        </form>
        
        <p>
          Seus dados estão protegidos conforme nossa <a href="#">Política de Privacidade</a>.
        </p>
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
  <script src="../JS/registro.js"></script>
</body>
</html>