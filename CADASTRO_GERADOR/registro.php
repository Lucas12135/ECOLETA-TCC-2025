<?php 
session_start();

$errors = [];

// Garante estrutura de sessão para o fluxo de cadastro
if (!isset($_SESSION['cadastro'])) {
    $_SESSION['cadastro'] = [];
}

/**
 * Valida CPF (11 dígitos) usando o algoritmo oficial
 */
function validar_cpf(string $cpf): bool
{
    // tem que ter 11 dígitos
    if (strlen($cpf) != 11) {
        return false;
    }

    // rejeita CPFs com todos os dígitos iguais (000..., 111..., etc)
    if (preg_match('/^(\d)\1{10}$/', $cpf)) {
        return false;
    }

    // calcula os dois dígitos verificadores
    for ($t = 9; $t < 11; $t++) {
        $d = 0;
        for ($c = 0; $c < $t; $c++) {
            $d += $cpf[$c] * (($t + 1) - $c);
        }
        $d = ((10 * $d) % 11) % 10;

        if ((int)$cpf[$t] !== $d) {
            return false;
        }
    }

    return true;
}

if (!empty($_POST)) {
    require_once('../BANCO/conexao.php');

    // normalização dos dados digitados
    $nome = trim($_POST['nome'] ?? '');
    $cpf  = preg_replace('/\D/', '', $_POST['cpf'] ?? '');      // só dígitos
    $cel  = preg_replace('/\D/', '', $_POST['celular'] ?? '');  // só dígitos
    $dataConsent = $_POST['dataConsent'] ?? '';

    $cpfValido = false; // flag

    // ---------- VALIDAÇÕES BÁSICAS ----------

    // Nome
    if ($nome === '') {
        $errors['nome'] = 'Por favor, digite o seu nome completo.';
    }

    // CPF obrigatório + formato
    if ($cpf === '') {
        $errors['cpf'] = 'Informe o CPF.';
    } elseif (!validar_cpf($cpf)) {
        $errors['cpf'] = 'CPF inválido. Verifique se digitou corretamente.';
    } else {
        $cpfValido = true; // só é true se passou pelo algoritmo
    }

    // Telefone
    if ($cel === '') {
        $errors['celular'] = 'Informe um telefone.';
    } elseif (strlen($cel) < 10) {
        $errors['celular'] = 'Telefone muito curto. Verifique DDD e número.';
    }

    // Consentimento LGPD
    if ($dataConsent !== '1') {
        $errors['dataConsent'] = 'Você precisa concordar com o uso dos dados para continuar o cadastro.';
    }

    // ---------- CHECAGEM DE DUPLICIDADE NO BANCO ----------
    // IMPORTANTE: só roda se o CPF for válido
    if ($cpfValido) {
        try {
            $stmt = $conn->prepare("
                SELECT cpf, telefone
                FROM geradores
                WHERE cpf IS NOT NULL OR telefone IS NOT NULL
            ");
            $stmt->execute();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $cpfBanco = preg_replace('/\D/', '', $row['cpf'] ?? '');
                $telBanco = preg_replace('/\D/', '', $row['telefone'] ?? '');

                if ($cpfBanco !== '' && $cpfBanco === $cpf) {
                    $errors['cpf'] = 'Este CPF já está em uso na Ecoleta.';
                }

                if ($telBanco !== '' && $telBanco === $cel) {
                    $errors['celular'] = 'Este telefone já está em uso em outro cadastro.';
                }

                if (isset($errors['cpf']) && isset($errors['celular'])) {
                    break;
                }
            }
        } catch (PDOException $e) {
            $errors['db'] = 'Erro ao verificar CPF/telefone no banco de dados.';
        }
    }

    // ---------- SE NÃO TIVER ERRO, SALVA NA SESSÃO E VAI PRA PRÓXIMA ETAPA ----------
    if (empty($errors)) {
        $_SESSION['cadastro']['nome']    = $nome;
        $_SESSION['cadastro']['cpf']     = $cpf;   // sem máscara
        $_SESSION['cadastro']['celular'] = $cel;   // sem máscara

        header('Location: ultregistro.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Portal de cadastro - Gerador</title>
  <link rel="icon" href="../img/logo.png" type="image/png">
  <link rel="stylesheet" href="../CSS/registro.css">
  <link rel="stylesheet" href="../CSS/global.css">
  <link rel="stylesheet" href="../CSS/acessibilidade.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>

<body>
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

      <!-- Botão de Libras Separado -->
      <div class="libras-button" id="librasButton" onclick="toggleLibras(event)" title="Libras">
        👋
      </div>

      <div class="form-box">
        <h2>Primeiros passos</h2>

        <?php if (isset($errors['db'])): ?>
          <div class="error-message">⚠️ <?= htmlspecialchars($errors['db']) ?></div>
        <?php endif; ?>

        <form method="POST" action="#" id="registrationForm" novalidate>
          <!-- NOME -->
          <div class="form-group">
            <label for="fullName" class="field-label">Nome *</label>
            <input
              type="text"
              id="fullName"
              name="nome"
              placeholder="Digite seu nome completo"
              required
              value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>"
            >
            <?php if (isset($errors['nome'])): ?>
              <div class="error-message">⚠️ <?= htmlspecialchars($errors['nome']) ?></div>
            <?php endif; ?>
          </div>

          <!-- CPF -->
          <div class="form-group">
            <label for="cpf" class="field-label">CPF *</label>
            <input
              type="text"
              id="cpf"
              name="cpf"
              placeholder="000.000.000-00"
              maxlength="14"
              required
              value="<?= htmlspecialchars($_POST['cpf'] ?? '') ?>"
            >
            <?php if (isset($errors['cpf'])): ?>
              <div class="error-message">⚠️ <?= htmlspecialchars($errors['cpf']) ?></div>
            <?php endif; ?>
          </div>

          <!-- TELEFONE -->
          <div class="form-group">
            <label for="phone" class="field-label">Telefone *</label>
            <input
              type="tel"
              id="phone"
              name="celular"
              placeholder="(00) 00000-0000"
              maxlength="15"
              required
              value="<?= htmlspecialchars($_POST['celular'] ?? '') ?>"
            >
            <?php if (isset($errors['celular'])): ?>
              <div class="error-message">⚠️ <?= htmlspecialchars($errors['celular']) ?></div>
            <?php endif; ?>
          </div>

          <!-- CONSENTIMENTO -->
          <label class="checkbox-label">
            <input
              type="checkbox"
              id="dataConsent"
              name="dataConsent"
              value="1"
              <?= isset($_POST['dataConsent']) ? 'checked' : '' ?>
              required
            >
            <span>Concordo em fornecer meus dados pessoais para o processo de cadastro na Ecoleta</span>
          </label>
          <?php if (isset($errors['dataConsent'])): ?>
            <div class="error-message" style="margin-top:4px;">
              ⚠️ <?= htmlspecialchars($errors['dataConsent']) ?>
            </div>
          <?php endif; ?>

          <div class="button-group">
            <button type="button" class="btn-back" onclick="goBack()">Voltar</button>
            <button type="submit" class="btn-continue">Continuar Cadastro</button>
          </div>
        </form>
      </div>
    </div>
  </main>

  
  <script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
  <script src="../JS/acessibilidade.js"></script>
  <script src="../JS/registro.js"></script>
  <script src="../JS/registro-validacao.js"></script>
</body>

</html>
