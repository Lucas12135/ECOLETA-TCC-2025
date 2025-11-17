-- =====================================================
-- Tabela para armazenar tokens OTP (One-Time Password)
-- =====================================================

CREATE TABLE IF NOT EXISTS `otp_tokens` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
