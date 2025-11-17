<?php
$email = $_GET['email'] ?? '';
$purpose = $_GET['purpose'] ?? 'cadastro';
?>
<!doctype html><html lang="pt-BR"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Digitar código</title>
<style>
  body{font-family:Poppins,Arial,sans-serif;background:#f7f7f7;margin:0;display:grid;place-items:center;height:100vh}
  .card{background:#fff;padding:24px;border-radius:12px;box-shadow:0 10px 20px rgba(0,0,0,.06);width:min(420px,90vw)}
  input,button{width:100%;padding:12px;border-radius:8px;border:1px solid #ddd;margin-top:8px}
  button{background:#223E2A;color:#fff;border:none;cursor:pointer}
  .row{display:flex;gap:8px}
  .link{display:inline-block;margin-top:10px;text-decoration:none;color:#223E2A}
  .muted{color:#666;font-size:.9rem}
</style>
</head><body>
<div class="card">
  <h2>Digite o código</h2>
  <p class="muted">Enviamos um código de 6 dígitos para <strong><?=htmlspecialchars($email)?></strong>.</p>

  <form method="POST" action="../verify_otp.php">
    <input type="hidden" name="email" value="<?=htmlspecialchars($email)?>">
    <input type="hidden" name="purpose" value="<?=htmlspecialchars($purpose)?>">
    <input type="text" name="code" inputmode="numeric" pattern="\d{6}" maxlength="6" placeholder="123456" required>
    <button type="submit">Confirmar</button>
  </form>

  <form method="POST" action="../mail/resend_otp.php" style="margin-top:8px;">
    <input type="hidden" name="email" value="<?=htmlspecialchars($email)?>">
    <input type="hidden" name="purpose" value="<?=htmlspecialchars($purpose)?>">
    <button type="submit">Reenviar código</button>
  </form>

  <a class="link" href="otp_request.php?email=<?=urlencode($email)?>&purpose=<?=urlencode($purpose)?>">Trocar e-mail</a>

</div>
</body></html>
