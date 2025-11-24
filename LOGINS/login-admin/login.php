<?php
session_start();

$errors = [];

try {
    include_once('../../BANCO/conexao.php');

    if (!isset($conn) || !$conn) {
        $errors['db'] = 'Não foi possível conectar ao banco de dados.';
    } elseif (!empty($_POST)) {
        $email = $_POST['email'] ?? '';
        $senha = $_POST['senha'] ?? '';

        if (empty($email) || empty($senha)) {
            $errors['login'] = 'Email e senha são obrigatórios.';
        } else {
            if ($email === "admin@gmail.com" && $senha === "Admin123$") {
                // Senha correta
                $_SESSION['nome_usuario'] = 'Super Administrador';
                $_SESSION['id_usuario'] = 0;
                $_SESSION['tipo_usuario'] = 'admin';
                
                header('Location: ../../ADMIN/dashboard.php');
                exit;
            } else {
                $errors['login'] = 'Email ou senha inválidos.';
            }
        }
    }
} catch (Exception $e) {
    $errors['db'] = 'Erro no servidor: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Ecoleta</title>
    <link rel="icon" href="../../img/logo.png" type="image/png">
    <link rel="stylesheet" href="../../CSS/login.css">
    <link rel="stylesheet" href="../../CSS/libras.css">
    <link rel="stylesheet" href="../../CSS/acessibilidade.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <header>
        <div class="header-container">
            <div class="logo">
                <div class="logo-placeholder">
                    <img src="../../img/logo.png" alt="Logo Ecoleta">
                </div>
                <span class="logo-text">Ecoleta</span>
            </div>
            <nav>
                <a href="../../logins.php" class="btn-outline">Voltar</a>
                <a href="../../index.php" class="btn-outline">Home</a>
            </nav>
        </div>
    </header>

    <main class="login-unified">
        <div class="login-container">
            <div class="form-box login-form">
                <h2>Login - Administrador</h2>

                <form method="POST" action="#">
                    <input type="email" id="email" name="email" placeholder="Digite seu email" required value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                    <input type="password" id="senha" name="senha" placeholder="Digite sua senha" required>

                    <?php if (isset($errors['login'])): ?>
                        <div class="input-error"><?= $errors['login'] ?></div>
                    <?php elseif (isset($errors['db'])): ?>
                        <div class="input-error"><?= $errors['db'] ?></div>
                    <?php endif; ?>

                    <button type="submit">Entrar</button>
                </form>

                <div class="form-footer">
                    <p><a href="../../logins.php">Voltar ao Login</a></p>
                </div>
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
      <!-- Painel de Acessibilidade -->
      <div class="accessibility-overlay"></div>
      <div class="accessibility-panel">
          <div class="accessibility-header">
              <h3>Acessibilidade</h3>
              <button class="accessibility-close">×</button>
          </div>
          <div class="accessibility-group">
              <div class="accessibility-group-title">Tamanho de Texto</div>
              <div class="size-control">
                  <span class="size-label">A</span>
                  <input type="range" class="size-slider" min="50" max="150" value="100">
                  <span class="size-label" style="font-weight: bold;">A</span>
                  <span class="size-value">100%</span>
              </div>
          </div>
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
          <div class="accessibility-group">
              <div class="accessibility-group-title">Espaçamento</div>
              <div class="accessibility-options">
                  <label class="accessibility-option">
                      <input type="checkbox" id="increased-spacing">
                      <span>Aumentar Espaçamento</span>
                  </label>
              </div>
          </div>
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
          <button class="accessibility-reset-btn">Restaurar Padrões</button>
      </div>
      <!-- Botão de Libras Separado -->
      <div class="libras-button" id="librasButton" onclick="toggleAccessibility(event)" title="Libras">
          👋
      </div>
    <div vw class="enabled">
        <div vw-access-button class="active"></div>
        <div vw-plugin-wrapper>
            <div class="vw-plugin-top-wrapper"></div>
        </div>
    </div>

    <script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
    <script src="../../JS/libras.js"></script>
    <script src="../../JS/acessibilidade.js"></script>
</body>

</html>
