<?php
session_start();
require __DIR__ . '/../config/conexao.php';

function timing_safe_equals($a,$b){ if(function_exists('hash_equals')) return hash_equals($a,$b); if(strlen($a)!==strlen($b)) return false; $r=0; for($i=0;$i<strlen($a);$i++) $r|=ord($a[$i])^ord($b[$i]); return $r===0; }
function hash_code($code,$salt){ return hash('sha256',$salt.$code); }

if ($_SERVER['REQUEST_METHOD']!=='POST'){ http_response_code(405); exit('Método inválido'); }

$email   = filter_input(INPUT_POST,'email',FILTER_VALIDATE_EMAIL);
$code    = preg_replace('/\D/','', $_POST['code'] ?? '');
$purpose = $_POST['purpose'] ?? 'cadastro';
if(!$email || strlen($code)!==6){ http_response_code(400); exit('Dados inválidos'); }

// busca OTP válido
$stmt=$conn->prepare("SELECT * FROM otp_tokens WHERE email=:e AND purpose=:p AND used=0 AND expires_at>NOW() ORDER BY id DESC LIMIT 1");
$stmt->execute([':e'=>$email, ':p'=>$purpose]); $otp=$stmt->fetch();
if(!$otp){ http_response_code(400); exit('Código expirado ou inexistente'); }
if((int)$otp['attempts_left']===0){ http_response_code(423); exit('Muitas tentativas'); }

$calc = hash_code($code,$otp['otp_salt']);
if(!timing_safe_equals($calc,$otp['otp_hash'])){
  $conn->prepare("UPDATE otp_tokens SET attempts_left=attempts_left-1 WHERE id=:id")->execute([':id'=>$otp['id']]);
  http_response_code(401); exit('Código incorreto');
}

// marca como usado
$conn->prepare("UPDATE otp_tokens SET used=1 WHERE id=:id")->execute([':id'=>$otp['id']]);

// >>> INTEGRAÇÃO: marque o e-mail como verificado na sua tabela de usuários <<<
// TODO: ajuste o nome da tabela/coluna de usuários
$up=$conn->prepare("UPDATE usuarios SET email_verified_at=NOW() WHERE email=:e AND email_verified_at IS NULL");
$up->execute([':e'=>$email]);

echo 'VERIFICADO';
