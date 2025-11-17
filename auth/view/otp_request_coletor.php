<?php
$email   = $_GET['email'] ?? '';
$purpose = $_GET['purpose'] ?? 'cadastro_coletor';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Verificar e-mail - Coletor | Ecoleta</title>
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
  <div class="ecoleta-title">Ecoleta • Verificação de e-mail (Coletor)</div>
</header>

<main class="otp-wrapper">
  <section class="otp-card">
    <h1 class="otp-title">Confirme seu e-mail</h1>
    <p class="otp-subtitle">
      Informe o e-mail que você está usando no cadastro de <strong>Coletor</strong>.
      Nós enviaremos um código de verificação para continuar seu cadastro com segurança.
    </p>

    <form method="POST" action="../request_otp.php">
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
      </div>

      <input type="hidden" name="purpose" value="<?= htmlspecialchars($purpose) ?>">

      <button type="submit" class="otp-button">
        Enviar código para meu e-mail
      </button>
    </form>

    <p class="otp-footer-text">
      Está cadastrando outro tipo de conta? <br>
      <a href="otp_request_gerador.php">Verificação para Gerador</a>
    </p>

    <p class="otp-footer-text">
      Voltar para o fluxo de cadastro de coletor:
      <a href="../../CADASTRO_COLETOR/login.php">Cadastro de Coletor</a>
    </p>
  </section>
</main>

</body>
</html>
