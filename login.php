<?php
session_start();

try {
    include_once('BANCO/conexao.php');
    if (!isset($conn) || !$conn) {
        $errors['db'] = 'Não foi possível conectar ao banco de dados.';
    } else {
        $stmt = $conn->prepare('SELECT id, nome, email, senha, tipo FROM coletores WHERE email = :email LIMIT 1');
        $stmt->bindParam(':email', $_POST['email']);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            // Verifica senha hashed. Se a senha estiver em texto plano (não recomendado),
            // tentamos migrar para hash automaticamente.
            if (password_verify($_POST['senha'], $user['senha'])) {
                // sucesso
            } elseif ($user['senha'] === $_POST['senha']) {
                // senha em texto plano — fazer rehash e atualizar (migração)
                $newHash = password_hash($_POST['senha'], PASSWORD_DEFAULT);
                $upd = $conn->prepare('UPDATE coletores SET senha = :senha WHERE id = :id');
                $upd->bindParam(':senha', $newHash);
                $upd->bindParam(':id', $user['id']);
                $upd->execute();
            } else {
                $user = false;
            }
        }
        if ($user) {
            // autentica usuário
            $_SESSION['user'] = [
                'id' => $user['id'],
                'nome' => $user['nome'],
                'email' => $user['email'],
                'tipo' => $user['tipo'] ?? null
            ];
            header('Location: index.php');
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
    <link rel="icon" href="img/logo.png" type="image/png">
    <link rel="stylesheet" href="CSS/login.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <!-- ========== ESTRUTURA DO HEADER ========== -->
    <header>
        <div class="header-container">
            <div class="logo">
                <div class="logo-placeholder">
                    <img src="img/logo.png" alt="Logo Ecoleta">
                </div>
                <span class="logo-text">Ecoleta</span>
            </div>
            <nav>
                <a href="index.php" class="btn-outline">Home</a>
                <a href="cadastros.php" class="btn-outline">Criar Conta</a>
            </nav>
        </div>
    </header>

    <!-- ========== CONTEUDO INICIAL ========== -->
    <main class="login-unified">
        <div class="login-container">
            <div class="form-box login-form">
                <h2>Login</h2>
                <form method="POST" action="#">
                    <input type="email" id="email" name="email" placeholder="Digite seu email" required value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                    <?php if (isset($errors['email'])): ?>
                        <div class="input-error"><?= $errors['email'] ?></div>
                    <?php endif; ?>
                    <input type="password" id="senha" name="senha" placeholder="Digite sua senha" required>
                    <?php if (isset($errors['senha'])): ?>
                        <div class="input-error"><?= $errors['senha'] ?></div>
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
    <div vw class="enabled">
        <div vw-access-button class="active"></div>
        <div vw-plugin-wrapper>
            <div class="vw-plugin-top-wrapper"></div>
        </div>
    </div>
    <script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
    <script src="JS/login.js"></script>
</body>

</html>