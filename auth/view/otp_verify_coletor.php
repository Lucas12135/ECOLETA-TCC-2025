<?php
$email   = $_GET['email'] ?? '';
$purpose = $_GET['purpose'] ?? 'cadastro_coletor';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Digitar código - Coletor | Ecoleta</title>
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
      margin-bottom: 20px;
    }

    .otp-email {
      font-size: 0.9rem;
      color: #666;
      background: #f3f3f3;
      border-radius: 999px;
      padding: 6px 12px;
      display: inline-block;
      margin-bottom: 10px;
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
      font-size: 1.2rem;
      letter-spacing: 0.3em;
      text-align: center;
      outline: none;
      background: #fdfdfd;
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
      margin-top: 12px;
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

    header {
      background: var(--cor-primaria, linear-gradient(to right, #223e2a, #2d5238, #386043));
      color: #fff;
      padding: 12px 24px;
      display: flex;
      align-items: center;
      gap: 12px;
    }

    header img {
      height: 32px;
      width: auto;
    }

    header .ecoleta-title {
      font-size: 1.1rem;
      font-weight: 600;
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

<header>
  <img src="../../img/logo.png" alt="Logo Ecoleta">
  <div class="ecoleta-title">Ecoleta • Código de verificação (Coletor)</div>
</header>

<main class="otp-wrapper">
  <section class="otp-card">
    <h1 class="otp-title">Digite o código recebido</h1>
    <p class="otp-subtitle">
      Enviamos um código de 6 dígitos para:
    </p>

    <?php if ($email): ?>
      <div class="otp-email"><?= htmlspecialchars($email) ?></div>
    <?php endif; ?>

    <form method="POST" action="../verify_otp.php">
      <div class="otp-form-group">
        <label class="otp-label" for="code">Código de verificação</label>
        <input
          type="text"
          id="code"
          name="code"
          class="otp-input"
          maxlength="6"
          inputmode="numeric"
          pattern="[0-9]{6}"
          placeholder="• • • • • •"
          required
        >
      </div>

      <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
      <input type="hidden" name="purpose" value="<?= htmlspecialchars($purpose) ?>">

      <button type="submit" class="otp-button">
        Confirmar código
      </button>
    </form>

    <p class="otp-footer-text">
      Não recebeu o código?
      <!-- aqui você pode ligar com seu resend_otp.php depois -->
      <br>
      <a href="otp_request_coletor.php?email=<?= urlencode($email) ?>&purpose=<?= urlencode($purpose) ?>">
        Reenviar código
      </a>
    </p>

    <p class="otp-footer-text">
      Usou o e-mail errado?
      <a href="otp_request_coletor.php">Alterar e-mail</a>
    </p>
  </section>
</main>

</body>
</html>
