<?php
$email = $_GET['email'] ?? '';
$purpose = $_GET['purpose'] ?? 'cadastro';
?>
<!doctype html><html lang="pt-BR"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Verificar e-mail</title>
<style>
  body{font-family:Poppins,Arial,sans-serif;background:#f7f7f7;margin:0;display:grid;place-items:center;height:100vh}
  .card{background:#fff;padding:24px;border-radius:12px;box-shadow:0 10px 20px rgba(0,0,0,.06);width:min(420px,90vw)}
  input,button{width:100%;padding:12px;border-radius:8px;border:1px solid #ddd;margin-top:8px}
  button{background:#223E2A;color:#fff;border:none;cursor:pointer}
  .muted{color:#666;font-size:.9rem}
</style>
</head><body>
<div class="card">
  <h2>Confirmar e-mail</h2>
  <p class="muted">Informe seu e-mail para receber um código de verificação.</p>
  <form method="POST" action="/auth/request_otp.php">
    <input type="email" name="email" placeholder="seu@email.com" value="<?=htmlspecialchars($email)?>" required>
    <input type="hidden" name="purpose" value="<?=htmlspecialchars($purpose)?>">
    <button type="submit">Enviar código</button>
  </form>
  <p class="muted">Depois, você será direcionado para a tela de código.</p>
</div>
</body></html>
