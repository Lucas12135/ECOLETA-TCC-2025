<?php
session_start();

// Valida se o email foi verificado
if (!isset($_SESSION['cadastro']['email_verificado']) || $_SESSION['cadastro']['email_verificado'] !== true) {
  header('Location: login.php');
  exit;
}

// Verifica se existe a sessão e o índice 'tipo'
$tipo = $_SESSION['cadastro']['tipo'] ?? null;
if ($tipo === "pessoa_juridica") {
  $campo = 'cnpj';
  $tamanho_campo = 18;
  $placeholder_cnpfj = '00.000.000/0000-00';
  $nome = 'Nome da Empresa';
  $placeholder_nome = "Digite o nome da sua empresa";
} elseif ($tipo === "pessoa_fisica") {
  $campo = 'cpf';
  $tamanho_campo = 14;
  $placeholder_cnpfj = '000.000.000-00';
  $nome = 'Nome Completo';
  $placeholder_nome = "Digite seu nome completo";
} else {
  $campo = 'deu ruim bixo'; // fallback
  header('Location: login.php');
  exit;
}
// Processamento do POST do formulário atual
if (!empty($_POST)) {
  // Use nomes corretos de campos: se for cnpj/ cpf, adapte
  $_SESSION['cadastro']['nome'] = $_POST['nome'] ?? '';
  // padroniza cpf/cnpj removendo não dígitos
  $_SESSION['cadastro']['cpf_cnpj'] = preg_replace('/\D/', '', $_POST[$campo] ?? '');
  $_SESSION['cadastro']['celular'] = preg_replace('/\D/', '', $_POST['celular'] ?? '');
  header('Location: ultregistro.php');
  exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Portal de cadastro - Coletor</title>
  <link rel="icon" href="../img/logo.png" type="image/png">
  <link rel="stylesheet" href="../CSS/registro.css">
  <link rel="stylesheet" href="../CSS/global.css">
  <link rel="stylesheet" href="../CSS/acessibilidade.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

</head>

<body>
  <!-- ========== ESTRUTURA DO HEADER AUMENTADO ========== -->
  <header>
    <div class="header-container">
      <div class="logo">
        <div class="logo-placeholder">
          <img src="../img/logo.png" alt="Logo Portal de cadastro - Coletor">
        </div>
        <span class="logo-text">Portal de cadastro - Coletor</span>
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
      <div class="libras-button" id="librasButton" onclick="toggleLibras(event)" title="Libras">
        👋
      </div>

      <div class="form-box">
        <h2>Primeiros passos</h2>
        <form method="POST" action="#" id="registrationForm" novalidate>
          <div class="form-group">
            <label for="nome" class="field-label"><?= $nome ?> *</label>
            <input type="text" id="fullName" name="nome" placeholder="<?= $placeholder_nome ?>" required>
          </div>

          <div class="form-group">
            <label for="<?= $campo ?>" class="field-label"><?= strtoupper($campo) ?> *</label>
            <input type="text" id="<?= $campo ?>" name="<?= $campo ?>" placeholder="<?= $placeholder_cnpfj ?>" maxlength="<?= $tamanho_campo ?>" required>
          </div>

          <div class="form-group">
            <label for="celular" class="field-label">Telefone *</label>
            <input type="tel" id="phone" name="celular" placeholder="(00) 00000-0000" maxlength="15" required>
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
  <script src="../JS/acessibilidade.js"></script>
  <script src="../JS/registro.js"></script>
  <script src="../JS/registro-validacao.js"></script>
</body>

</html>