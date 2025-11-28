<?php
session_start();

// tenta usar o autoload do Composer
$composerAutoload = __DIR__ . '/../vendor/autoload.php';
if (file_exists($composerAutoload)) {
    require $composerAutoload;
}

// se a classe do PHPMailer ainda não existir, carrega manualmente
if (!class_exists(\PHPMailer\PHPMailer\PHPMailer::class)) {
    require __DIR__ . '/../PHPMailer/src/Exception.php';
    require __DIR__ . '/../PHPMailer/src/PHPMailer.php';
    require __DIR__ . '/../PHPMailer/src/SMTP.php';
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

$config = require __DIR__ . '/smtp_config.php';

$mail = new PHPMailer(true);

try {
    // Ativar debug
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;
    $mail->Debugoutput = 'html';

    // 🔐 IGNORAR VERIFICAÇÃO DO CERTIFICADO (APENAS DEV)
    $mail->SMTPOptions = [
        'ssl' => [
            'verify_peer'       => false,
            'verify_peer_name'  => false,
            'allow_self_signed' => true,
        ],
    ];

    $mail->isSMTP();
    $mail->Host       = $config['host'];
    $mail->SMTPAuth   = true;
    $mail->Username   = $config['username'];
    $mail->Password   = $config['password'];
    $mail->SMTPSecure = $config['secure'];
    $mail->Port       = $config['port'];
    $mail->CharSet    = 'UTF-8';

    echo "<h2>🔄 Testando conexão SMTP...</h2>";
    echo "<p><strong>Host:</strong> " . htmlspecialchars($config['host']) . "</p>";
    echo "<p><strong>Port:</strong> " . $config['port'] . "</p>";
    echo "<p><strong>Username:</strong> " . htmlspecialchars($config['username']) . "</p>";
    echo "<p><strong>Secure:</strong> " . $config['secure'] . "</p>";
    echo "<hr>";

    $mail->setFrom($config['from_email'], $config['from_name']);
    $mail->addAddress('your-test-email@gmail.com'); // MUDE PARA SEU E-MAIL
    $mail->Subject = 'Teste SMTP Ecoleta';
    $mail->isHTML(true);
    $mail->Body = '<h3>✅ Teste de conexão SMTP bem-sucedido!</h3>';
    $mail->AltBody = 'Teste de conexão SMTP bem-sucedido!';

    if ($mail->send()) {
        echo "<h2 style='color: green;'>✅ E-mail enviado com sucesso!</h2>";
    } else {
        echo "<h2 style='color: red;'>❌ Falha ao enviar e-mail</h2>";
    }

} catch (Exception $e) {
    echo "<h2 style='color: red;'>❌ Erro ao conectar SMTP:</h2>";
    echo "<pre style='background: #f0f0f0; padding: 10px; border-radius: 5px;'>";
    echo htmlspecialchars($mail->ErrorInfo);
    echo "</pre>";
    echo "<h3>Detalhes da exceção:</h3>";
    echo "<pre style='background: #f0f0f0; padding: 10px; border-radius: 5px;'>";
    echo htmlspecialchars($e->getMessage());
    echo "</pre>";
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Teste SMTP</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        pre { color: #333; }
    </style>
</head>
<body>
</body>
</html>
