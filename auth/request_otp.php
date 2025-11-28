<?php
session_start();
require __DIR__ . '/../BANCO/conexao.php';

// tenta usar o autoload do Composer, se existir
$composerAutoload = __DIR__ . '/../vendor/autoload.php';
if (file_exists($composerAutoload)) {
    require $composerAutoload;
}

// se a classe do PHPMailer ainda não existir, carrega manualmente da pasta PHPMailer/
if (!class_exists(\PHPMailer\PHPMailer\PHPMailer::class)) {
    require __DIR__ . '/../PHPMailer/src/Exception.php';
    require __DIR__ . '/../PHPMailer/src/PHPMailer.php';
    require __DIR__ . '/../PHPMailer/src/SMTP.php';
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// --------- FUNÇÕES AUXILIARES ---------
function random_digits($len = 6) {
    $min = (int) str_pad('1', $len, '0');
    $max = (int) str_pad('', $len, '9');
    return (string) random_int($min, $max);
}

function hash_code($code, $salt) {
    return hash('sha256', $salt . $code);
}

// Sempre vamos responder em JSON
header('Content-Type: application/json; charset=utf-8');

// Só aceita POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Método inválido'
    ]);
    exit;
}

// Entrada
$email   = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$purpose = $_POST['purpose'] ?? 'cadastro_gerador'; // padrão pro fluxo do gerador

if (!$email) {
    http_response_code(400);
    echo json_encode([
        'status'  => 'error',
        'message' => 'E-mail inválido'
    ]);
    exit;
}

// Validar purpose
$validPurposes = ['cadastro_gerador', 'cadastro_coletor', 'reset_password', 'login_otp'];
if (!in_array($purpose, $validPurposes, true)) {
    http_response_code(400);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Propósito inválido'
    ]);
    exit;
}

// Limites / regras
$COOLDOWN = 90;  // 90 segundos = 1m30s
$MAX_DAILY = 100;  // máximo de 10 códigos por dia (por e-mail)

// ================== LIMITE DIÁRIO ==================
try {
    $stmt = $conn->prepare("
        SELECT COUNT(*) AS c 
        FROM otp_tokens 
        WHERE email = :e 
          AND DATE(sent_at) = CURDATE()
    ");
    $stmt->execute([':e' => $email]);
    $countRow = $stmt->fetch();
    $count    = (int) ($countRow['c'] ?? 0);

    if ($count >= $MAX_DAILY) {
        http_response_code(429);
        echo json_encode([
            'status'  => 'error',
            'code'    => 'daily_limit',
            'message' => 'Você atingiu o limite de envios de código para hoje.'
        ]);
        exit;
    }

    // ================== COOLDOWN 90s ==================
    $stmt = $conn->prepare("
        SELECT last_resend_at 
        FROM otp_tokens 
        WHERE email = :e 
          AND purpose = :p
          AND used = 0 
          AND expires_at > NOW()
        ORDER BY id DESC 
        LIMIT 1
    ");
    $stmt->execute([
        ':e' => $email,
        ':p' => $purpose
    ]);
    $last = $stmt->fetch();

    if ($last && !empty($last['last_resend_at'])) {
        $lastTime = strtotime($last['last_resend_at']);
        $diff     = time() - $lastTime;

        if ($diff < $COOLDOWN) {
            $remaining = $COOLDOWN - $diff;

            http_response_code(429);
            echo json_encode([
                'status'    => 'error',
                'code'      => 'cooldown',
                'remaining' => $remaining,
                'message'   => "Aguarde {$remaining} segundos para reenviar um novo código."
            ]);
            exit;
        }
    }

    // ================== GERA E GRAVA NOVO OTP ==================
    // Vários códigos por e-mail: cada requisição gera um novo registro
    $code    = random_digits(6);
    $salt    = bin2hex(random_bytes(16));
    $hash    = hash_code($code, $salt);
    $expires = (new DateTime('+10 minutes'))->format('Y-m-d H:i:s');

    $stmt = $conn->prepare("
        INSERT INTO otp_tokens (
            email, otp_hash, otp_salt, expires_at, attempts_left, 
            sent_at, last_resend_at, resend_count, purpose, used, 
            ip, user_agent
        ) VALUES (
            :e, :h, :s, :x, 5,
            NOW(), NOW(), 0, :p, 0, 
            :ip, :ua
        )
    ");
    $stmt->execute([
        ':e'  => $email,
        ':h'  => $hash,
        ':s'  => $salt,
        ':x'  => $expires,
        ':p'  => $purpose,
        ':ip' => $_SERVER['REMOTE_ADDR'] ?? null,
        ':ua' => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255),
    ]);

    // ================== ENVIO DE E-MAIL ==================
    $config = require __DIR__ . '/smtp_config.php';

    // Renderiza template do e-mail com o código
    ob_start();
    $purposeSafe = $purpose;
    $codeSafe    = $code;
    $code        = $codeSafe;
    $purpose     = $purposeSafe;
    include __DIR__ . '/mail/otp_template.html.php';
    $html = ob_get_clean();

    $mail = new PHPMailer(true);

    try {
        // 🔐 IGNORAR VERIFICAÇÃO DO CERTIFICADO (APENAS DEV)
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer'       => false,
                'verify_peer_name'  => false,
                'allow_self_signed' => true,
            ],
        ];

        // Logs SMTP (opcional):
        // $mail->SMTPDebug  = SMTP::DEBUG_SERVER;
        // $mail->Debugoutput = 'html';

        $mail->isSMTP();
        $mail->Host       = $config['host'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $config['username'];
        $mail->Password   = $config['password'];
        $mail->SMTPSecure = $config['secure']; // 'tls'
        $mail->Port       = $config['port'];   // 587
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom($config['from_email'], $config['from_name']);
        $mail->addAddress($email);
        $mail->Subject = 'Seu código de verificação';
        $mail->isHTML(true);
        $mail->Body    = $html;
        $mail->AltBody = "Seu código: {$code}\nVálido por 10 minutos.";

        $mail->send();

        echo json_encode([
            'status'  => 'ok',
            'message' => 'Código enviado para seu e-mail.'
        ]);
        exit;

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'status'  => 'error',
            'code'    => 'mail_error',
            'message' => 'Falha ao enviar e-mail.',
            // Se quiser debugar muito, descomenta abaixo (mas cuidado em produção):
            // 'debug'   => $mail->ErrorInfo,
        ]);
        exit;
    } catch (Throwable $e) {
        http_response_code(500);
        echo json_encode([
            'status'  => 'error',
            'code'    => 'unexpected_error',
            'message' => 'Falha ao enviar e-mail (erro inesperado).'
            // 'debug'   => $e->getMessage(),
        ]);
        exit;
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'code'    => 'server_error',
        'message' => 'Erro interno ao gerar o código.'
        // 'debug'   => $e->getMessage(),
    ]);
    exit;
}
