<?php
session_start();

$errors = [];

try {
    include_once('../../BANCO/conexao.php');

    if (!isset($conn) || !$conn) {
        $errors['db'] = 'Não foi possível conectar ao banco de dados.';
    } elseif (!empty($_POST)) {

        $email = trim($_POST['email']);
        $senha = $_POST['senha'];

        // Busca o coletor pelo e-mail
        $stmt = $conn->prepare('SELECT id, nome_completo, email, senha, tipo_coletor FROM coletores WHERE email = :email LIMIT 1');
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Verifica senha hash
            if (password_verify($senha, $user['senha'])) {
                // Senha correta
            } elseif ($user['senha'] === $senha) {
                // Senha em texto plano (migração)
                $newHash = password_hash($senha, PASSWORD_DEFAULT);
                $upd = $conn->prepare('UPDATE coletores SET senha = :senha WHERE id = :id');
                $upd->bindParam(':senha', $newHash);
                $upd->bindParam(':id', $user['id']);
                $upd->execute();
            } else {
                $user = false; // senha incorreta
            }
        }

        if ($user) {
            // Cria sessão
            $_SESSION['id_usuario'] = $user['id'];
            $_SESSION['nome_usuario'] = $user['nome_completo'];
            $_SESSION['tipo_usuario'] = 'coletor';

            // Redireciona para a página principal do coletor
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Ecoleta</title>
    <link rel="icon" href="../../img/logo.png" type="image/png">
    <link rel="stylesheet" href="../../CSS/login.css">
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
                <a href="index.php" class="btn-outline">Home</a>
                <a href="cadastros.php" class="btn-outline">Criar Conta</a>
            </nav>
        </div>
    </header>

    <main class="login-unified">
        <div class="login-container">
            <div class="form-box login-form">
                <h2>Login - Coletor</h2>

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
                    <p>Não tem uma conta? <a href="cadastros.php">Criar conta</a></p>
                    <p><a href="#">Esqueceu sua senha?</a></p>
                </div>
            </div>
        </div>
    </main>

    <script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
    <script src="JS/login.js"></script>
</body>

</html>
