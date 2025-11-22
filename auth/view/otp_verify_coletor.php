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

    .otp-button:hover:not(:disabled) {
      filter: brightness(1.03);
      box-shadow: 0 10px 20px rgba(34, 62, 42, 0.2);
      transform: translateY(-1px);
    }

    .otp-button:disabled {
      cursor: not-allowed;
      opacity: 0.6;
      box-shadow: none;
      transform: none;
    }

    .otp-error {
      color: #d32f2f;
      font-size: 0.85rem;
      margin-top: 10px;
      text-align: center;
    }

    .otp-resend-info {
      margin-top: 18px;
      font-size: 0.85rem;
      color: #555;
      text-align: center;
    }

    .otp-resend-label {
      margin-right: 4px;
    }

    .otp-resend-link {
      color: #223e2a;
      font-weight: 600;
      text-decoration: none;
      cursor: pointer;
    }

    .otp-resend-link:hover:not(.disabled) {
      text-decoration: underline;
    }

    .otp-resend-link.disabled {
      opacity: 0.5;
      cursor: not-allowed;
    }

    #resendTimerText {
      margin-top: 8px;
      font-size: 0.8rem;
      color: #888;
    }

    .otp-resend-message {
      margin-top: 8px;
      font-size: 0.85rem;
      text-align: center;
    }

    .otp-resend-message.success {
      color: #2e7d32;
    }

    .otp-resend-message.error {
      color: #d32f2f;
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

      <form id="otpForm" method="POST" action="../verify_otp.php">
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
            required>
        </div>

        <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
        <input type="hidden" name="purpose" value="<?= htmlspecialchars($purpose) ?>">

        <button type="submit" id="submitBtn" class="otp-button">
          Confirmar código
        </button>

        <!-- Mensagem de erro do código -->
        <div id="codeError" class="otp-error"></div>
      </form>

      <!-- Reenvio discreto, estilo link, com timer de 1m30s após o clique -->
      <div class="otp-resend-info">
        <span class="otp-resend-label">Não recebeu o código?</span>
        <a href="#" id="resendLink" class="otp-resend-link">Reenviar código</a>
        <div id="resendTimerText"></div>
        <div id="resendMessage" class="otp-resend-message"></div>
      </div>

      <p class="otp-footer-text" style="margin-top: 18px;">
        Usou o e-mail errado?
        <a href="../../CADASTRO_COLETOR/login.php">Alterar e-mail</a>
      </p>
    </section>
  </main>

  <script>
    (function() {
      const email = "<?= htmlspecialchars($email, ENT_QUOTES) ?>";
      const purpose = "<?= htmlspecialchars($purpose, ENT_QUOTES) ?>";

      const resendLink = document.getElementById('resendLink');
      const resendTimerText = document.getElementById('resendTimerText');
      const resendMessage = document.getElementById('resendMessage');
      const codeInput = document.getElementById('code');
      const codeError = document.getElementById('codeError');
      const form = document.getElementById('otpForm');
      const submitBtn = document.getElementById('submitBtn');

      // foca no input do código
      if (codeInput) {
        codeInput.focus();
      }

      /* ========= SUBMISSÃO DO CÓDIGO VIA FETCH (SEM SAIR DA TELA) ========= */
      if (form && submitBtn && codeInput && codeError) {
        form.addEventListener('submit', async function(e) {
          e.preventDefault();

          const code = codeInput.value.trim();
          codeError.textContent = '';

          if (!/^[0-9]{6}$/.test(code)) {
            codeError.textContent = 'Digite um código de 6 dígitos.';
            return;
          }

          submitBtn.disabled = true;
          const originalText = submitBtn.textContent;
          submitBtn.textContent = 'Verificando...';

          try {
            const resp = await fetch('../verify_otp.php', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8'
              },
              body: new URLSearchParams({
                email: email,
                purpose: purpose,
                code: code
              }).toString()
            });

            // Se o backend redirecionar para registro.php, o fetch segue o redirect
            if (resp.redirected && resp.url) {
              window.location.href = resp.url;
              return;
            }

            const text = (await resp.text()).trim();

            // Se status NÃO foi 2xx, há um erro
            if (!resp.ok) {
              codeError.textContent = text || 'Não foi possível validar o código. Tente novamente.';
            } else {
              // Status 200-299, mas pode haver mensagem de erro na resposta
              if (text) {
                // Verifica se a resposta parece ser um erro
                const isError = text.toLowerCase().includes('erro') ||
                  text.toLowerCase().includes('inválido') ||
                  text.toLowerCase().includes('expirou') ||
                  text.toLowerCase().includes('não');

                if (isError) {
                  codeError.textContent = text;
                } else {
                  // Sem erro, mensagem passou ou redirecionamento
                  codeError.textContent = '';
                }
              }
            }
          } catch (err) {
            codeError.textContent = 'Erro de conexão ao tentar verificar o código.';
          } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
          }
        });
      }

      /* ========= LÓGICA DO REENVIO COM TIMER ========= */
      let timer = null;
      let remaining = 0;
      let originalLinkText = resendLink ? resendLink.textContent : '';

      function formatTime(sec) {
        const m = Math.floor(sec / 60);
        const s = sec % 60;
        const mm = String(m).padStart(2, '0');
        const ss = String(s).padStart(2, '0');
        return `${mm}:${ss}`;
      }

      function updateTimerText() {
        if (remaining > 0) {
          resendTimerText.innerHTML =
            'Você poderá reenviar um novo código em <strong>' + formatTime(remaining) + '</strong>.';
        } else {
          resendTimerText.innerHTML = '';
        }
      }

      function startCountdown(seconds) {
        if (timer) {
          clearInterval(timer);
        }
        remaining = seconds;

        resendLink.classList.add('disabled');
        updateTimerText();

        timer = setInterval(() => {
          remaining--;
          if (remaining <= 0) {
            clearInterval(timer);
            timer = null;
            remaining = 0;
            resendLink.classList.remove('disabled');
            updateTimerText();
          } else {
            updateTimerText();
          }
        }, 1000);
      }

      async function resendCode() {
        if (!email) {
          resendMessage.textContent = 'E-mail não encontrado para reenviar código.';
          resendMessage.className = 'otp-resend-message error';
          return;
        }

        resendLink.classList.add('disabled');
        originalLinkText = resendLink.textContent;
        resendLink.textContent = 'Enviando...';
        resendMessage.textContent = '';
        resendMessage.className = 'otp-resend-message';

        try {
          const resp = await fetch('../request_otp.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8'
            },
            body: new URLSearchParams({
              email: email,
              purpose: purpose
            }).toString()
          });

          let data = null;
          try {
            data = await resp.json();
          } catch (e) {
            data = null;
          }

          if (!resp.ok) {
            if (data && data.code === 'cooldown' && typeof data.remaining === 'number') {
              startCountdown(data.remaining);
              resendMessage.textContent = data.message || 'Aguarde antes de reenviar novamente.';
              resendMessage.className = 'otp-resend-message error';
            } else if (data && data.message) {
              resendMessage.textContent = data.message;
              resendMessage.className = 'otp-resend-message error';
              resendLink.classList.remove('disabled');
            } else {
              resendMessage.textContent = 'Não foi possível reenviar o código. Tente novamente mais tarde.';
              resendMessage.className = 'otp-resend-message error';
              resendLink.classList.remove('disabled');
            }
          } else {
            if (data && data.message) {
              resendMessage.textContent = data.message;
            } else {
              resendMessage.textContent = 'Novo código enviado para seu e-mail.';
            }
            resendMessage.className = 'otp-resend-message success';

            // timer de 1m30s
            startCountdown(90);
          }
        } catch (e) {
          resendMessage.textContent = 'Erro de conexão ao tentar reenviar o código.';
          resendMessage.className = 'otp-resend-message error';
          resendLink.classList.remove('disabled');
        } finally {
          resendLink.textContent = originalLinkText || 'Reenviar código';
        }
      }

      if (resendLink) {
        resendLink.addEventListener('click', function(e) {
          e.preventDefault();
          if (resendLink.classList.contains('disabled')) {
            return;
          }
          resendCode();
        });
      }
    })();
  </script>