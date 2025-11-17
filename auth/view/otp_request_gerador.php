<?php
session_start();

require __DIR__ . '/../../BANCO/conexao.php';

$email   = $_GET['email'] ?? '';
$purpose = $_GET['purpose'] ?? 'cadastro_gerador';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $purpose = $_POST['purpose'] ?? $purpose;

    $novoEmail = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    if (!$novoEmail) {
        $errors['email'] = 'Digite um e-mail válido.';
    } else {
        $email = trim($novoEmail);

        // 🔍 Verificar se o e-mail já existe na tabela geradores
        try {
            $stmt = $conn->prepare("SELECT id FROM geradores WHERE email = :email LIMIT 1");
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();

            if ($stmt->fetch()) {
                $errors['email'] = 'Este e-mail já está em uso.';
            }
        } catch (Exception $e) {
            $errors['geral'] = 'Erro ao verificar e-mail no banco de dados.';
        }
    }

    // Se não teve erro até aqui, dispara OTP e volta para a tela de código
    if (empty($errors)) {
        // Dispara o OTP chamando request_otp.php internamente
        $postData = http_build_query([
            'email'   => $email,
            'purpose' => $purpose
        ]);

        $opts = [
            'http' => [
                'method'  => 'POST',
                'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
                'content' => $postData,
                'timeout' => 10,
            ]
        ];

        $context = stream_context_create($opts);
        $result  = @file_get_contents(__DIR__ . '/../request_otp.php', false, $context);

        if ($result === false) {
            $errors['geral'] = 'Não foi possível enviar o código para o novo e-mail. Tente novamente.';
        } else {
            // Atualiza sessão para o novo e-mail continuar no fluxo
            $_SESSION['otp_email']   = $email;
            $_SESSION['otp_purpose'] = $purpose;

            // Volta para a tela de digitar código com o novo e-mail
            header('Location: otp_verify_gerador.php?email=' . urlencode($email) . '&purpose=' . urlencode($purpose));
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Verificar e-mail - Gerador | Ecoleta</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="../../img/logo.png" type="image/png">
  <link rel="stylesheet" href="../../CSS/login.css">
  <link rel="stylesheet" href="../../CSS/global.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      background: var(--background, #f8f4e7);
      font-family: 'Poppins', sans-serif;
      margin: 0;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    .otp-wrapper {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 24px;
    }

    .otp-card {
      background: #ffffff;
      border-radius: 24px;
      box-shadow: 0 16px 40px rgba(0, 0, 0, 0.08);
      padding: 32px 28px;
      max-width: 480px;
      width: 100%;
      border-top: 6px solid #223e2a;
    }

    .otp-title {
      font-size: 1.6rem;
      font-weight: 700;
      color: #223e2a;
      margin-bottom: 8px;
    }

    .otp-subtitle {
      font-size: 0.95rem;
      color: #4c4c4c;
      margin-bottom: 24px;
    }

    .otp-form-group {
      margin-bottom: 18px;
    }

    .otp-label {
      display: block;
      font-size: 0.9rem;
      font-weight: 600;
      color: #4c4c4c;
      margin-bottom: 6px;
    }

    .otp-input {
      width: 100%;
      padding: 12px 14px;
      border-radius: 10px;
      border: 1px solid #d4d4d4;
      font-size: 0.95rem;
      outline: none;
      transition: border-color 0.2s, box-shadow 0.2s;
      background-color: #fdfdfd;
    }

    .otp-input:focus {
      border-color: #223e2a;
      box-shadow: 0 0 0 2px rgba(34, 62, 42, 0.15);
    }

    .otp-button {
      width: 100%;
      padding: 12px 16px;
      border: none;
      border-radius: 999px;
      background: linear-gradient(to right, #223e2a, #2d5238, #386043);
      color: #ffffff;
      font-size: 1rem;
      font-weight: 600;
      cursor: pointer;
      margin-top: 8px;
      transition: transform 0.1s ease, box-shadow 0.1s ease, filter 0.15s ease;
    }

    .otp-button:hover {
      filter: brightness(1.03);
      box-shadow: 0 10px 20px rgba(34, 62, 42, 0.2);
      transform: translateY(-1px);
    }

    .otp-footer-text {
      margin-top: 16px;
      font-size: 0.85rem;
      color: #666;
      text-align: center;
    }

    .otp-footer-text a {
      color: #223e2a;
      font-weight: 600;
      text-decoration: none;
    }

    .otp-footer-text a:hover {
      text-decoration: underline;
    }

    .otp-error {
      margin-top: 6px;
      font-size: 0.85rem;
      color: #b91c1c;
    }

    @media (max-width: 480px) {
      .otp-card {
        padding: 24px 18px;
        border-radius: 18px;
      }

      .otp-title {
        font-size: 1.3rem;
      }
    }
  </style>
</head>
<body>

<main class="otp-wrapper">
  <section class="otp-card">
    <h1 class="otp-title">Confirme seu e-mail</h1>
    <p class="otp-subtitle">
      Informe o e-mail que você está usando no cadastro de <strong>Gerador</strong>.
      Nós enviaremos um código de verificação para continuar seu cadastro com segurança.
    </p>

    <?php if (!empty($errors['geral'])): ?>
      <div class="otp-error"><?= htmlspecialchars($errors['geral']) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
      <div class="otp-form-group">
        <label class="otp-label" for="email">E-mail</label>
        <input
          type="email"
          id="email"
          name="email"
          class="otp-input"
          placeholder="seuemail@exemplo.com"
          value="<?= htmlspecialchars($email) ?>"
          required
        >
        <?php if (!empty($errors['email'])): ?>
          <div class="otp-error"><?= htmlspecialchars($errors['email']) ?></div>
        <?php endif; ?>
      </div>

      <input type="hidden" name="purpose" value="<?= htmlspecialchars($purpose) ?>">

      <button type="submit" class="otp-button">
        Enviar código para meu e-mail
      </button>
    </form>

    <p class="otp-footer-text">
      Voltar para o fluxo de cadastro de gerador:
      <a href="../../CADASTRO_GERADOR/login.php">Cadastro de Gerador</a>
    </p>
  </section>
</main>

</body>
</html>
