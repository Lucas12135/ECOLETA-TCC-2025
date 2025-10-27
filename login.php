<?php
session_start();

$errors = [];

if (!empty($_POST)) {
    if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Digite um email válido.';
    }
    if (empty($_POST['senha'])) {
        $errors['senha'] = 'Digite sua senha.';
    }

    if (empty($errors)) {
        // TODO: Adicionar lógica de autenticação
        // Por enquanto, apenas redireciona para a página inicial
        header('Location: index.php');
        exit;
    }
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