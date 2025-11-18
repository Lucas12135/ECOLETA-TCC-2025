<?php
// Script para criar a tabela otp_tokens se não existir
// Execute este arquivo uma única vez: http://localhost/Ecoleta/BANCO/setup_otp_table.php

session_start();
require_once 'conexao.php';

try {
    // Verifica se a tabela já existe
    $stmt = $conn->prepare("SHOW TABLES LIKE 'otp_tokens'");
    $stmt->execute();
    $result = $stmt->fetch();
    
    if ($result) {
        echo "<h2 style='color: green;'>✓ Tabela 'otp_tokens' já existe no banco de dados!</h2>";
        echo "<p>Nenhuma ação necessária.</p>";
    } else {
        // Cria a tabela
        $sql = "CREATE TABLE IF NOT EXISTS `otp_tokens` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `email` varchar(100) NOT NULL,
          `otp_hash` varchar(64) NOT NULL COMMENT 'SHA256 hash do código + salt',
          `otp_salt` varchar(32) NOT NULL COMMENT 'Salt usado para hash',
          `expires_at` datetime NOT NULL COMMENT 'Quando o código expira (10 minutos)',
          `used` tinyint(1) DEFAULT 0 COMMENT 'Se o código já foi usado',
          `attempts_left` int(11) DEFAULT 5 COMMENT 'Tentativas restantes de verificação',
          `sent_at` datetime DEFAULT CURRENT_TIMESTAMP COMMENT 'Quando o código foi enviado',
          `last_resend_at` datetime DEFAULT CURRENT_TIMESTAMP COMMENT 'Último reenvio',
          `resend_count` int(11) DEFAULT 0 COMMENT 'Quantas vezes foi reenviado',
          `purpose` varchar(50) NOT NULL COMMENT 'Propósito (cadastro_gerador, login, etc)',
          `ip` varchar(45) DEFAULT NULL COMMENT 'IP que solicitou',
          `user_agent` varchar(255) DEFAULT NULL COMMENT 'User Agent do navegador',
          `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          KEY `idx_email_purpose` (`email`, `purpose`),
          KEY `idx_expires_at` (`expires_at`),
          KEY `idx_used` (`used`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
        
        $conn->exec($sql);
        
        echo "<h2 style='color: green;'>✓ Tabela 'otp_tokens' criada com sucesso!</h2>";
        echo "<p>O sistema de verificação de email agora está funcionando.</p>";
        echo "<p><a href='../CADASTRO_GERADOR/login.php'>Voltar para o cadastro</a></p>";
    }
    
} catch (PDOException $e) {
    echo "<h2 style='color: red;'>✗ Erro ao criar tabela:</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Setup - OTP Table</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        h2 { margin-top: 0; }
    </style>
</head>
<body>
</body>
</html>
