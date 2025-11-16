<?php
session_start();

$errors = [];

try {
    include_once('../../BANCO/conexao.php');

    if (!isset($conn) || !$conn) {
        $errors['db'] = 'Não foi possível conectar ao banco de dados.';
    } elseif (!empty($_POST)) {
        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';

        // Busca o gerador pelo e-mail
        $stmt = $conn->prepare('SELECT id, nome_completo, email, senha, status FROM geradores WHERE email = :email LIMIT 1');
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Verifica senha hash (preferencial)
            if (password_verify($senha, $user['senha'])) {
                // OK
            }
            // Migração: se ainda estiver em texto plano, faz upgrade para hash
            elseif ($user['senha'] === $senha) {
                $newHash = password_hash($senha, PASSWORD_DEFAULT);
                $upd = $conn->prepare('UPDATE geradores SET senha = :senha WHERE id = :id');
                $upd->bindParam(':senha', $newHash);
                $upd->bindParam(':id', $user['id'], PDO::PARAM_INT);
                $upd->execute();
            } else {
                $user = false; // senha incorreta
            }
        }

        if ($user) {
            // (Opcional) bloquear logins inativos/suspensos:
            // if (!in_array($user['status'], ['ativo','pendente'])) { $errors['login'] = 'Conta inativa ou suspensa.'; $user = false; }

            // Cria sessão
            $_SESSION['id_usuario']   = $user['id'];
            $_SESSION['nome_usuario'] = $user['nome_completo'];
            $_SESSION['tipo_usuario'] = 'gerador';

            // Redireciona para a página principal do Gerador
            header('Location: ../../index.php');
            exit;
        } else {
            $errors['login'] = 'Email ou senha inválidos.';
        }
    }
} catch (Exception $e) {
    $errors['db'] = 'Erro no servidor: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Login - Gerador | Ecoleta</title>
    <link rel="icon" href="../../img/logo.png" type="image/png"/>
    <link rel="stylesheet" href="../../CSS/login.css"/>
    <link rel="stylesheet" href="../../CSS/libras.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet"/>
</head>
<body>
<header>
    <div class="header-container">
        <div class="logo">
            <div class="logo-placeholder">
                <img src="../../img/logo.png" alt="Logo Ecoleta"/>
            </div>
            <span class="logo-text">Ecoleta</span>
        </div>
        <nav>
            <a href="../../cadastros.php" class="btn-outline">Criar Conta</a>
            <a href="../../index.php" class="btn-outline">Home</a>
        </nav>
    </div>
</header>

<main class="login-unified">
    <div class="login-container">
        <div class="form-box login-form">
            <h2>Login - Gerador</h2>

            <form method="POST" action="#">
                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Digite seu email"
                    required
                    value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>"
                />
                <input
                    type="password"
                    id="senha"
                    name="senha"
                    placeholder="Digite sua senha"
                    required
                />

                <?php if (isset($errors['login'])): ?>
                    <div class="input-error"><?= $errors['login'] ?></div>
                <?php elseif (isset($errors['db'])): ?>
                    <div class="input-error"><?= $errors['db'] ?></div>
                <?php endif; ?>

                <button type="submit">Entrar</button>
            </form>

            <div class="form-footer">
                <p>Não tem uma conta? <a href="../../cadastros.php">Criar conta</a></p>
                <p><a href="#">Esqueceu sua senha?</a></p>
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
<div vw class="enabled">
        <div vw-access-button class="active"></div>
        <div vw-plugin-wrapper>
            <div class="vw-plugin-top-wrapper"></div>
        </div>
    </div>


    <script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
<script src="JS/login.js"></script>
 <script src="../../JS/libras.js"></script>
</body>
</html>
