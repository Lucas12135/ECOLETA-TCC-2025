<?php
session_start();

$errors = [];
include_once '../BANCO/conexao.php'; // deve fornecer a variável $conn como PDO

// Garante estrutura de sessão para o fluxo de cadastro
if (!isset($_SESSION['cadastro'])) {
  $_SESSION['cadastro'] = [];
}

if (!empty($_POST)) {
  if (!isset($conn) || !$conn) {
    $errors['db'] = 'Não foi possível conectar ao banco de dados.';
  } else {

    // ===========================
    // VALIDAÇÃO DE E-MAIL
    // ===========================
    if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
      $errors['email'] = 'Digite um email válido.';
    } else {
      $email = trim($_POST['email']);

      // Verifica se já existe um coletor com este e-mail
      $stmt = $conn->prepare("SELECT id FROM coletores WHERE email = :email LIMIT 1");
      $stmt->bindParam(':email', $email, PDO::PARAM_STR);
      $stmt->execute();

      if ($stmt->fetch()) {
        // aqui é tela de CADASTRO, então email já usado é erro
        $errors['email'] = 'Este email já está em uso.';
      }
    }

    // ===========================
    // VALIDAÇÃO DE SENHA
    // ===========================
    if (empty($_POST['senha'])) {
      $errors['senha'] = 'Digite uma senha.';
    } else {
      $senha = $_POST['senha'];

      if (
        strlen($senha) < 8 ||
        !preg_match('/[A-Z]/', $senha) ||
        !preg_match('/[a-z]/', $senha) ||
        !preg_match('/[0-9]/', $senha) ||
        !preg_match('/[^A-Za-z0-9]/', $senha)
      ) {
        $errors['senha'] = '* A senha deve ter no mínimo 8 caracteres, incluindo uma letra maiúscula, uma minúscula, um número e um caractere especial.';
      }
    }

    // ===========================
    // VALIDAÇÃO CONFIRMAÇÃO
    // ===========================
    if (empty($_POST['confirmar_senha'])) {
      $errors['confirmar_senha'] = 'Por favor, confirme sua senha.';
    } elseif (!empty($_POST['senha']) && $_POST['senha'] !== $_POST['confirmar_senha']) {
      $errors['confirmar_senha'] = 'As senhas não coincidem.';
    }

    // ===========================
    // VALIDAÇÃO DO TIPO
    // ===========================
    if (empty($_POST['tipo'])) {
      $errors['tipo'] = 'Selecione o tipo de coletor.';
    }

    // ===========================
    // SE NÃO HOUVE ERROS:
    //  1) guarda e-mail/senha na sessão
    //  2) dispara OTP para o e-mail
    //  3) redireciona para tela de inserir código
    // ===========================
    if (empty($errors)) {

      // Garante que $email, $senha e $tipo existam
      $email = trim($_POST['email']);
      $senha = $_POST['senha'];
      $tipo = $_POST['tipo'];

      // Guarda no "wizard" de cadastro
      $_SESSION['cadastro']['email'] = $email;
      $_SESSION['cadastro']['senha'] = password_hash($senha, PASSWORD_DEFAULT);
      $_SESSION['cadastro']['tipo'] = $tipo;

      // Guarda dados para fluxo de OTP
      $_SESSION['otp_email']   = $email;
      $_SESSION['otp_purpose'] = 'cadastro_coletor';

      // Disparo do OTP via endpoint HTTP
      $otpUrl   = 'http://localhost/ECOLETA/ECOLETA-TCC-2025/auth/request_otp.php';
      $postData = http_build_query([
        'email'   => $email,
        'purpose' => 'cadastro_coletor'
      ]);

      $context = stream_context_create([
        'http' => [
          'method'  => 'POST',
          'header'  => "Content-Type: application/x-www-form-urlencoded\r\n" .
            "Content-Length: " . strlen($postData) . "\r\n",
          'content' => $postData,
          'timeout' => 5
        ]
      ]);

      // Tenta enviar o OTP
      $response = file_get_contents($otpUrl, false, $context);

      if ($response === false) {
        $error = error_get_last();
        error_log("Erro ao enviar OTP: " . ($error['message'] ?? 'Erro desconhecido'));
        $errors['otp'] = 'Não foi possível enviar o código de verificação. Tente novamente mais tarde.';
      } else {
        // Log opcional para debug (remove em produção):
        // error_log("OTP Response: " . $response);

        header('Location: ../auth/view/otp_verify_coletor.php?email=' . urlencode($email) . '&origin=cadastro_coletor');
        exit;
      }
    }
  }
}
?>


<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Portal de cadastro - Coletor</title>
  <link rel="icon" href="../img/logo.png" type="image/png">
  <link rel="stylesheet" href="../CSS/login.css">
  <link rel="stylesheet" href="../CSS/global.css">
  <link rel="stylesheet" href="../CSS/acessibilidade.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    .form-box .password-field {
      position: relative;
      width: 100%;
    }

    /* input com espaço pro ícone à direita */
    .form-box .password-field input[type="password"],
    .form-box .password-field input[type="text"] {
      width: 100%;
      padding: 12px;
      padding-right: 2.75rem;
      /* espaço pro botão */
      margin-bottom: 12px;
      border: 2px solid #ddd;
      border-radius: 8px;
      font-size: 14px;
      font-family: "Poppins", sans-serif;
      color: #223e2a;
      background-color: #fff;
      transition: border-color 0.3s ease, box-shadow 0.3s ease;
      line-height: normal;
      letter-spacing: normal;
    }

    .form-box .password-field input[type="password"]:focus,
    .form-box .password-field input[type="text"]:focus {
      outline: none;
      border-color: #ffce46;
      box-shadow: 0 0 0 3px rgba(255, 206, 70, 0.2);
    }

    /* BOTÃO FIXO – não se mexe em hover/click */
    .form-box .pw-toggle {
      all: unset;
      position: absolute;
      right: .6rem;
      top: 50%;
      transform: translateY(-50%);
      /* central fixo */
      display: flex;
      align-items: center;
      justify-content: center;
      width: 1.9rem;
      height: 1.9rem;
      cursor: pointer;
      color: #6b7280;
      border-radius: .5rem;
      user-select: none;
      -webkit-user-select: none;
    }

    .form-box .pw-toggle:hover {
      background: #f3f4f6;
      color: #374151;
      transform: translateY(-50%);
      /* continua no mesmo lugar */
    }

    .form-box .pw-toggle:active {
      /* sem “pulinho” ao clicar */
      transform: translateY(-50%);
    }

    .form-box .pw-toggle:focus-visible {
      outline: 2px solid #2563eb;
      outline-offset: 2px;
    }

    .form-box .pw-toggle svg {
      width: 1.25rem;
      height: 1.25rem;
      display: block;
    }

    /* se quiser manter, pode deixar vazio mesmo */
    .form-box button[type="submit"] {}
  </style>
</head>

<body>
  <!-- ========== ESTRUTURA DO HEADER ========== -->
  <header>
    <div class="header-container">
      <div class="logo">
        <div class="logo-placeholder">
          <img src="../img/logo.png" alt="Logo Portal de cadastro - Coletor">
        </div>
        <span class="logo-text">Portal de cadastro - Coletor</span>
      </div>
      <nav>
        <a href="../index.php" class="btn-outline">Home</a>
        <a href="../logins.php" class="btn-filled">Entrar</a>
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
              <path d="M1 0 0 1l2.2 3.081a1 1 0 0 0 .815.419h.07a1 1 0 0 1 .708.293l2.675 2.675-2.617 2.654A3.003 3.003 0 0 0 0 13a3 3 0 1 0 5.878-.851l2.654-2.617.968.968-.305.914a1 1 0 0 0 .242 1.023l3.27 3.27a.997.997 0 0 0 1.414 0l1.586-1.586a.997.997 0 0 0 0-1.414l-3.27-3.27a1 1 0 0 0-1.023-.242L10.5 9.5l-.96-.96 2.68-2.643A3.005 3.005 0 0 0 16 3q0-.405-.102-.777l-2.14 2.141L12 4l-.364-1.757L13.777.102a3 3 0 0 0-3.675 3.68L7.462 6.46 4.793 3.793a1 1 0 0 1-.293-.707v-.071a1 1 0 0 0-.419-.814zm9.646 10.646a.5.5 0 0 1 .708 0l2.914 2.915a.5.5 0 0 1-.707.707l-2.915-2.914a.5.5 0 0 1 0-.708M3 11l.471.242.529.026.287.445.445.287.026.529L5 13l-.242.471-.026.529-.445.287-.287.445-.529.026L3 15l-.471-.242L2 14.732l-.287-.445L1.268 14l-.026-.529L1 13l.242-.471.026-.529.445-.287.287-.445.529-.026z" />
            </svg>
          </div>
          <div class="mobile-menu-text">Como funciona</div>
          <div class="mobile-menu-arrow">></div>
        </div>
        <div class="mobile-menu-item">
          <div class="mobile-menu-emoji">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-question-circle" viewBox="0 0 16 16" stroke-width="1.5">
              <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
              <path d="M5.255 5.786a.237.237 0 0 0 .241.247h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286m1.557 5.763c0 .533.425.927 1.01.927.609 0 1.028-.394 1.028-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94" />
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
                  z" />
                <path d="M19.125,334.716h162.562v-19.125h19.125v-19.125h-57.375c0-10.566-6.828-19.125-15.243-19.125H77.399
                  c-8.415,0-15.243,8.559-15.243,19.125H0v19.125h19.125V334.716z" />
                <path d="M357.007,191.556C370.968,329.811,243.892,542.08,243.892,542.08c145.235-78.212,169.189-207.363,169.189-207.363
                  c42.333,66.479,44.475,228.305,44.475,228.305c80.995-194.109,0-377.049,0-377.049l117.304,48.874
                  c-19.546-74.014-141.047-125.68-141.047-125.68c-110.322,50.27-249.974,44.686-249.974,44.686
                  C259.249,226.469,357.007,191.556,357.007,191.556z" />
                <circle cx="369.782" cy="55.128" r="43.29" />
                <path d="M94.43,229.529c5.977-2.391,27.492-13.148,28.764,0c1.271,13.148,11.876,9.562,19.048,0s3.586-25.102,11.953-23.906
                  s15.539-10.758,17.93-21.735c2.391-10.978-22.711-18.905-33.469-21.458s-20.32,13.321-27.492,13.321s-17.93-20.33-25.102-10.768
                  s-11.953,40.641-11.953,40.641c-10.758-5.977-21.516,7.172-25.102,16.734S88.453,231.919,94.43,229.529z" />
              </g>
            </svg>
          </div>
          <div class="mobile-menu-text">Coletor</div>
          <div class="mobile-menu-arrow">></div>
        </div>
      </div>
    </div>
  </header>

  <div class="menu-overlay" id="menuOverlay"></div>

  <!-- ========== CONTEUDO INICIAL ========== -->
  <main>
    <div class="left">
      <div class="linha1">Facilidade que só a Ecoleta oferece</div>
      <div class="linha2">Torne-se um Coletor Afiliado</div>
      <div class="linha3">
        <div class="icon-box">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" width="200" height="200" fill="white">
            <path d="M5.68623 0H10.3138L12.178 3.27835L13.9282 2.26789L14.4282 3.13392L13.3301 7.232L9.23203 6.13392L8.73203 5.26789L10.4459 4.27837L9.15033 2L6.84966 2L6.29552 2.97447L4.56343 1.97445L5.68623 0Z" fill="white" />
            <path d="M13.1649 9.05964L13.7039 10.0076L12.6055 12H9.99998L9.99998 9.99995H8.99998L5.99998 12.9999L8.99998 15.9999H9.99998L9.99998 14H13.7868L15.996 9.99242L14.8969 8.05962L13.1649 9.05964Z" fill="white" />
            <path d="M3.39445 12H4.49998V14H2.21325L0.00390625 9.99242L1.8446 6.75554L0.0717772 5.732L0.571776 4.86598L4.66986 3.7679L5.76793 7.86598L5.26793 8.732L3.57669 7.75556L2.29605 10.0076L3.39445 12Z" fill="white" />
          </svg>
        </div>
        <div class="linha3-texto">E ajude sua região</div>
      </div>
    </div>

      <div class="form-box">
        <h2>Cadastre-se como coletor</h2>
        <form method="POST" action="#">
          <input type="email" id="email" name="email" placeholder="Digite seu melhor email para contato" required value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
          <?php if (isset($errors['email'])): ?>
            <div class="input-error"><?= $errors['email'] ?></div>
          <?php endif; ?>
          <div class="password-field">
            <input type="password" id="senha" name="senha" placeholder="Insira a sua melhor senha" required
              value="<?= isset($_POST['senha']) ? htmlspecialchars($_POST['senha']) : '' ?>">
            <button type="button" class="pw-toggle" aria-label="Mostrar senha"
              aria-pressed="false" data-target="senha" title="Mostrar/ocultar senha">
              <svg viewBox="0 0 24 24" aria-hidden="true"></svg>
            </button>
          </div>
          <?php if (isset($errors['senha'])): ?>
            <div class="input-error"><?= $errors['senha'] ?></div>
          <?php endif; ?>
          <div class="password-field">
            <input type="password" id="confirmar_senha" name="confirmar_senha" placeholder="Confirme sua senha" required
              value="<?= isset($_POST['confirmar_senha']) ? htmlspecialchars($_POST['confirmar_senha']) : '' ?>">
            <button type="button" class="pw-toggle" aria-label="Mostrar senha"
              aria-pressed="false" data-target="confirmar_senha" title="Mostrar/ocultar senha">
              <svg viewBox="0 0 24 24" aria-hidden="true"></svg>
            </button>
          </div> <?php if (isset($errors['confirmar_senha'])): ?>
            <div class="input-error"><?= $errors['confirmar_senha'] ?></div>
          <?php endif; ?>
          <select id="tipo" name="tipo" required>
            <option value="" disabled <?= !isset($_POST['tipo']) ? 'selected' : '' ?>>Selecione o tipo de coletor</option>
            <option value="pessoa_fisica" <?= (isset($_POST['tipo']) && $_POST['tipo'] == 'pessoa_fisica') ? 'selected' : '' ?>>Pessoa Física</option>
            <option value="pessoa_juridica" <?= (isset($_POST['tipo']) && $_POST['tipo'] == 'pessoa_juridica') ? 'selected' : '' ?>>Pessoa Jurídica</option>
          </select>
          <?php if (isset($errors['tipo'])): ?>
            <div class="input-error"><?= $errors['tipo'] ?></div>
          <?php endif; ?>
          <label>
          </label>
          <button type="submit">Cadastrar agora</button>
        </form>
      </div>
    </div>
  </main>
  

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

        

      <!-- Importar VLibras -->
      <div vw class="enabled">
        <div vw-access-button class="active"></div>
        <div vw-plugin-wrapper>
          <div class="vw-plugin-top-wrapper"></div>
        </div>
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

      

      <!-- Botão de Libras -->
      <div class="libras-button" id="librasButton" onclick="toggleLibras(event)" title="Libras">
        👋
      </div>

  <script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
  <script src="../JS/login.js"></script>
  <script src="../JS/acessibilidade.js"></script>
</body>

</html>