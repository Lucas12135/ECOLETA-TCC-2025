<?php
// ==========================================
// Ecoleta - Teste de Envio de E-mail (Brevo)
// ==========================================

// 1️⃣ Importa as dependências instaladas via Composer
require __DIR__ . '/../vendor/autoload.php';

// 2️⃣ Lê o arquivo de configuração SMTP (Brevo)
$config = require __DIR__ . '/smtp_config.php';

// 3️⃣ Usa as classes principais do PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// 4️⃣ Inicia o teste de envio
$mail = new PHPMailer(true);

try {
    // Configuração básica do servidor SMTP
    $mail->isSMTP();
    $mail->Host       = $config['host'];
    $mail->Port       = $config['port'];
    $mail->CharSet    = 'UTF-8';
    $mail->SMTPAuth   = true;
    $mail->Username   = $config['username'];
    $mail->Password   = $config['password'];
    $mail->SMTPSecure = $config['secure']; // 'tls' ou 'ssl'

    // Remetente e destinatário
    $mail->setFrom($config['from_email'], $config['from_name']);

    // 🔧 Troque este e-mail para o seu endereço real de teste:
    $mail->addAddress('matheus.santossx@gmail.com', 'Teste Ecoleta');

    // Conteúdo do e-mail
    $mail->isHTML(true);
    $mail->Subject = 'Teste de SMTP - Ecoleta (Brevo)';
    $mail->Body    = '
        <h2 style="color:#223E2A;">Ecoleta - Teste de Envio ✅</h2>
        <p>Se você recebeu este e-mail, o servidor SMTP da Brevo está funcionando corretamente!</p>
        <p><b>Host:</b> ' . htmlspecialchars($config['host']) . '<br>
        <b>Porta:</b> ' . htmlspecialchars($config['port']) . '<br>
        <b>Segurança:</b> ' . htmlspecialchars($config['secure']) . '</p>
        <hr>
        <small>Enviado automaticamente pelo sistema Ecoleta.</small>
    ';
    $mail->AltBody = 'Ecoleta - Teste de Envio (modo texto).';

    // Envia o e-mail
    $mail->send();

    echo "<h2 style='color:green; font-family:Arial;'>✅ E-mail enviado com sucesso!</h2>";
    echo "<p>Verifique sua caixa de entrada (ou Spam) do endereço usado no addAddress().</p>";

} catch (Exception $e) {
    echo "<h2 style='color:red; font-family:Arial;'>❌ Erro ao enviar o e-mail:</h2>";
    echo "<pre>" . htmlspecialchars($mail->ErrorInfo) . "</pre>";
}