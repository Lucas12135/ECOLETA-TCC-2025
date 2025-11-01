<?php
session_start();
require __DIR__ . '/../BANCO/conexao.php';
require __DIR__ . '/../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;

function random_digits($len=6){ $min=(int)str_pad('1',$len,'0'); $max=(int)str_pad('', $len,'9'); return (string)random_int($min,$max); }
function hash_code($code,$salt){ return hash('sha256', $salt.$code); }

if ($_SERVER['REQUEST_METHOD']!=='POST'){ http_response_code(405); exit('Método inválido'); }

$email = filter_input(INPUT_POST,'email',FILTER_VALIDATE_EMAIL);
$purpose = $_POST['purpose'] ?? 'cadastro';
if(!$email){ http_response_code(400); exit('E-mail inválido'); }

// limite diário
$stmt=$conn->prepare("SELECT COUNT(*) c FROM otp_tokens WHERE email=:e AND DATE(sent_at)=CURDATE()");
$stmt->execute([':e'=>$email]); if((int)$stmt->fetch()['c']>=5){ http_response_code(429); exit('Muitas solicitações hoje'); }

// cooldown 60s
$stmt=$conn->prepare("SELECT last_resend_at FROM otp_tokens WHERE email=:e AND used=0 AND expires_at>NOW() ORDER BY id DESC LIMIT 1");
$stmt->execute([':e'=>$email]); $last=$stmt->fetch();
if($last && $last['last_resend_at']){ if(time()-strtotime($last['last_resend_at'])<60){ http_response_code(429); exit('Aguarde para reenviar'); }}

// gera e guarda
$code = random_digits(6);
$salt = bin2hex(random_bytes(16));
$hash = hash_code($code,$salt);
$expires = (new DateTime('+10 minutes'))->format('Y-m-d H:i:s');

$stmt=$conn->prepare("INSERT INTO otp_tokens(email,otp_hash,otp_salt,expires_at,attempts_left,sent_at,last_resend_at,resend_count,purpose,used,ip,user_agent)
VALUES(:e,:h,:s,:x,5,NOW(),NOW(),0,:p,0,:ip,:ua)");
$stmt->execute([
  ':e'=>$email, ':h'=>$hash, ':s'=>$salt, ':x'=>$expires, ':p'=>$purpose,
  ':ip'=>$_SERVER['REMOTE_ADDR']??null, ':ua'=>substr($_SERVER['HTTP_USER_AGENT']??'',0,255)
]);

// e-mail
$config = require __DIR__ . '/../config/smtp_config.php';
ob_start(); $purposeSafe=$purpose; $codeSafe=$code; $code=$codeSafe; $purpose=$purposeSafe;
include __DIR__ . '/mail/otp_template.html.php'; $html = ob_get_clean();

$mail = new PHPMailer(true);
try{
  $mail->isSMTP(); $mail->Host=$config['host']; $mail->SMTPAuth=true;
  $mail->Username=$config['username']; $mail->Password=$config['password'];
  $mail->SMTPSecure=$config['secure']; $mail->Port=$config['port'];
  $mail->CharSet='UTF-8';
  $mail->setFrom($config['from_email'],$config['from_name']);
  $mail->addAddress($email);
  $mail->Subject='Seu código de verificação';
  $mail->isHTML(true); $mail->Body=$html; $mail->AltBody="Seu código: {$code}\nVálido por 10 minutos.";
  $mail->send();
  echo 'OK';
}catch(Throwable $e){
  http_response_code(500); echo 'Falha ao enviar e-mail';
}
