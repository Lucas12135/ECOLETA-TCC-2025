-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 21/11/2025 às 05:09
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `ecoleta`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `avaliacoes`
--

CREATE TABLE `avaliacoes` (
  `id` int(11) NOT NULL,
  `id_coleta` int(11) DEFAULT NULL,
  `id_gerador` int(11) DEFAULT NULL,
  `nota` int(11) NOT NULL CHECK (`nota` >= 1 and `nota` <= 5),
  `comentario` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `avaliacoes_coletores`
--

CREATE TABLE `avaliacoes_coletores` (
  `id` int(11) NOT NULL,
  `id_historico_coleta` int(11) NOT NULL,
  `id_gerador` int(11) NOT NULL,
  `id_coletor` int(11) NOT NULL,
  `nota` int(11) NOT NULL CHECK (`nota` >= 1 and `nota` <= 5),
  `comentario` text DEFAULT NULL,
  `pontualidade` int(11) DEFAULT NULL CHECK (`pontualidade` >= 1 and `pontualidade` <= 5),
  `profissionalismo` int(11) DEFAULT NULL CHECK (`profissionalismo` >= 1 and `profissionalismo` <= 5),
  `qualidade_servico` int(11) DEFAULT NULL CHECK (`qualidade_servico` >= 1 and `qualidade_servico` <= 5),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `avaliacoes_coletores`
--

INSERT INTO `avaliacoes_coletores` (`id`, `id_historico_coleta`, `id_gerador`, `id_coletor`, `nota`, `comentario`, `pontualidade`, `profissionalismo`, `qualidade_servico`, `created_at`) VALUES
(1, 2, 1, 1, 5, 'Coletou de forma eficiente', 5, 5, 5, '2025-11-21 02:49:41'),
(2, 1, 1, 1, 3, 'Coletou de forma regular', 4, 2, 4, '2025-11-21 02:50:32');

--
-- Acionadores `avaliacoes_coletores`
--
DELIMITER $$
CREATE TRIGGER `atualiza_avaliacao_coletor` AFTER INSERT ON `avaliacoes_coletores` FOR EACH ROW BEGIN
    UPDATE coletores c
    SET 
        c.avaliacao_media = (
            SELECT AVG((pontualidade + profissionalismo + qualidade_servico) / 3)
            FROM avaliacoes_coletores
            WHERE id_coletor = NEW.id_coletor
        ),
        c.total_avaliacoes = (
            SELECT COUNT(*)
            FROM avaliacoes_coletores
            WHERE id_coletor = NEW.id_coletor
        )
    WHERE c.id = NEW.id_coletor;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `coletas`
--

CREATE TABLE `coletas` (
  `id` int(11) NOT NULL,
  `id_gerador` int(11) NOT NULL,
  `id_coletor` int(11) DEFAULT NULL,
  `data_agendada` datetime DEFAULT NULL,
  `data_solicitacao` datetime NOT NULL DEFAULT current_timestamp(),
  `status` enum('solicitada','pendente','agendada','em_andamento','concluida','cancelada') NOT NULL DEFAULT 'solicitada',
  `periodo` enum('manha','tarde') DEFAULT NULL,
  `quantidade_oleo` decimal(10,2) DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `cep` varchar(9) NOT NULL,
  `rua` varchar(100) NOT NULL,
  `numero` varchar(10) NOT NULL,
  `bairro` varchar(50) NOT NULL,
  `cidade` varchar(50) NOT NULL,
  `estado` char(2) NOT NULL,
  `complemento` varchar(150) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `coletas`
--

INSERT INTO `coletas` (`id`, `id_gerador`, `id_coletor`, `data_agendada`, `data_solicitacao`, `status`, `periodo`, `quantidade_oleo`, `observacoes`, `cep`, `rua`, `numero`, `bairro`, `cidade`, `estado`, `complemento`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2025-12-01 00:00:00', '2025-11-20 22:54:01', 'concluida', 'tarde', 5.00, '', '01001-000', 'Praça da Sé', '123', 'Sé', 'São Paulo', 'SP', 'Casa', '2025-11-21 01:54:01', '2025-11-21 02:20:58'),
(2, 1, 1, '2026-03-10 00:00:00', '2025-11-20 23:15:08', 'concluida', 'manha', 10.00, '', '59020-565', 'Travessa Progresso', '920', 'Barro Vermelho', 'Natal', 'RN', 'abc', '2025-11-21 02:15:08', '2025-11-21 02:24:40');

-- --------------------------------------------------------

--
-- Estrutura para tabela `coletores`
--

CREATE TABLE `coletores` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `nome_completo` varchar(100) NOT NULL,
  `cpf_cnpj` varchar(14) NOT NULL,
  `telefone` varchar(15) NOT NULL,
  `data_nasc` date DEFAULT NULL,
  `genero` enum('M','F','O','') DEFAULT '',
  `foto_perfil` varchar(255) DEFAULT NULL,
  `id_endereco` int(11) DEFAULT NULL,
  `tipo_coletor` enum('pessoa_fisica','pessoa_juridica') DEFAULT 'pessoa_fisica',
  `meio_transporte` enum('carro','moto','bicicleta','van','caminhao') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `avaliacao_media` float NOT NULL DEFAULT 0,
  `total_avaliacoes` int(6) NOT NULL DEFAULT 0,
  `coletas` int(5) NOT NULL DEFAULT 0,
  `total_oleo` float NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `coletores`
--

INSERT INTO `coletores` (`id`, `email`, `senha`, `nome_completo`, `cpf_cnpj`, `telefone`, `data_nasc`, `genero`, `foto_perfil`, `id_endereco`, `tipo_coletor`, `meio_transporte`, `created_at`, `updated_at`, `avaliacao_media`, `total_avaliacoes`, `coletas`, `total_oleo`) VALUES
(1, 'admin@gmail.com', '$2y$10$zzE2dnJfRH62kEjolUcAQe28j6TejdZY1EohXRSiCQkTRs2wRHGme', 'Coletor Administrador', '54548576614', '13982095384', '1994-05-08', 'M', '1_691fb6f0e139d.jpeg', 2, 'pessoa_fisica', 'carro', '2025-11-21 00:48:48', '2025-11-21 02:50:32', 4.16667, 2, 0, 0);

-- --------------------------------------------------------

--
-- Estrutura para tabela `coletores_config`
--

CREATE TABLE `coletores_config` (
  `id` int(11) NOT NULL,
  `id_coletor` int(11) NOT NULL,
  `disponibilidade` enum('disponivel','indisponivel') NOT NULL DEFAULT 'disponivel',
  `raio_atuacao` int(11) NOT NULL DEFAULT 5 CHECK (`raio_atuacao` between 1 and 50),
  `meio_transporte` enum('carro','bicicleta','motocicleta','carroca','a_pe') NOT NULL DEFAULT 'a_pe'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `coletores_config`
--

INSERT INTO `coletores_config` (`id`, `id_coletor`, `disponibilidade`, `raio_atuacao`, `meio_transporte`) VALUES
(2, 1, 'disponivel', 5, 'carro');

-- --------------------------------------------------------

--
-- Estrutura para tabela `configuracoes_usuario`
--

CREATE TABLE `configuracoes_usuario` (
  `id` int(11) NOT NULL,
  `id_gerador` int(11) DEFAULT NULL,
  `notificacoes_email` tinyint(1) DEFAULT 1,
  `notificacoes_push` tinyint(1) DEFAULT 1,
  `tema_escuro` tinyint(1) DEFAULT 0,
  `privacidade_perfil` enum('publico','privado') DEFAULT 'publico',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `documentos_coletor`
--

CREATE TABLE `documentos_coletor` (
  `id` int(11) NOT NULL,
  `id_coletor` int(11) NOT NULL,
  `tipo_documento` enum('cnh','licenca_ambiental','alvara','documento_veiculo','outro') NOT NULL,
  `numero_documento` varchar(50) NOT NULL,
  `data_emissao` date NOT NULL,
  `data_validade` date DEFAULT NULL,
  `arquivo_path` varchar(255) DEFAULT NULL,
  `status` enum('pendente','aprovado','rejeitado') DEFAULT 'pendente',
  `observacoes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `enderecos`
--

CREATE TABLE `enderecos` (
  `id` int(11) NOT NULL,
  `rua` varchar(100) NOT NULL,
  `numero` varchar(10) NOT NULL,
  `complemento` varchar(100) DEFAULT NULL,
  `bairro` varchar(50) NOT NULL,
  `cidade` varchar(50) NOT NULL,
  `estado` char(2) NOT NULL,
  `cep` varchar(9) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `enderecos`
--

INSERT INTO `enderecos` (`id`, `rua`, `numero`, `complemento`, `bairro`, `cidade`, `estado`, `cep`, `created_at`, `updated_at`) VALUES
(1, 'Praça da Sé', '732', 'Casa', 'Sé', 'São Paulo', 'SP', '01001-000', '2025-11-21 00:46:38', '2025-11-21 00:46:38'),
(2, 'Praça da Sé', '732', 'Casa', 'Sé', 'São Paulo', 'SP', '01001-000', '2025-11-21 00:48:48', '2025-11-21 00:48:48');

-- --------------------------------------------------------

--
-- Estrutura para tabela `ganhos_coletores`
--

CREATE TABLE `ganhos_coletores` (
  `id` int(11) NOT NULL,
  `id_coletor` int(11) NOT NULL,
  `id_historico_coleta` int(11) NOT NULL,
  `valor_ganho` decimal(10,2) NOT NULL,
  `data_pagamento` date DEFAULT NULL,
  `status_pagamento` enum('pendente','processando','pago','cancelado') DEFAULT 'pendente',
  `metodo_pagamento` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `ganhos_coletores`
--

INSERT INTO `ganhos_coletores` (`id`, `id_coletor`, `id_historico_coleta`, `valor_ganho`, `data_pagamento`, `status_pagamento`, `metodo_pagamento`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 2.50, NULL, 'pendente', NULL, '2025-11-21 02:20:58', '2025-11-21 02:20:58'),
(2, 1, 2, 5.00, NULL, 'pendente', NULL, '2025-11-21 02:24:40', '2025-11-21 02:24:40');

-- --------------------------------------------------------

--
-- Estrutura para tabela `geradores`
--

CREATE TABLE `geradores` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `nome_completo` varchar(100) NOT NULL,
  `cpf` varchar(14) NOT NULL,
  `telefone` varchar(15) NOT NULL,
  `data_nasc` date NOT NULL,
  `genero` enum('M','F','O','') DEFAULT '',
  `foto_perfil` varchar(255) DEFAULT NULL,
  `id_endereco` int(11) DEFAULT NULL,
  `status` enum('ativo','inativo','pendente') DEFAULT 'pendente',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `geradores`
--

INSERT INTO `geradores` (`id`, `email`, `senha`, `nome_completo`, `cpf`, `telefone`, `data_nasc`, `genero`, `foto_perfil`, `id_endereco`, `status`, `created_at`, `updated_at`) VALUES
(1, 'admin@gmail.com', '$2y$10$BDwbhBFzX3pF1COhEFZ/V.Bryom6oAqC4hYh8CzLzjDVXCmxizpzS', 'Gerador Administrador', '94721255578', '13982095384', '1994-03-19', 'M', '1_691fb66eb8add.jpeg', 1, 'pendente', '2025-11-21 00:46:38', '2025-11-21 00:46:38');

-- --------------------------------------------------------

--
-- Estrutura para tabela `historico_coletas`
--

CREATE TABLE `historico_coletas` (
  `id` int(11) NOT NULL,
  `id_coleta` int(11) NOT NULL,
  `id_coletor` int(11) NOT NULL,
  `id_gerador` int(11) NOT NULL,
  `data_inicio` datetime NOT NULL,
  `data_conclusao` datetime DEFAULT NULL,
  `quantidade_coletada` decimal(10,2) NOT NULL,
  `observacoes` text DEFAULT NULL,
  `status` enum('em_andamento','concluida','cancelada') DEFAULT 'em_andamento',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `historico_coletas`
--

INSERT INTO `historico_coletas` (`id`, `id_coleta`, `id_coletor`, `id_gerador`, `data_inicio`, `data_conclusao`, `quantidade_coletada`, `observacoes`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, '2025-12-01 00:00:00', '2025-11-20 23:20:58', 5.00, '', 'concluida', '2025-11-21 02:20:58', '2025-11-21 02:20:58'),
(2, 2, 1, 1, '2026-03-10 00:00:00', '2025-11-20 23:24:40', 10.00, 'Bao', 'concluida', '2025-11-21 02:24:40', '2025-11-21 02:24:40');

-- --------------------------------------------------------

--
-- Estrutura para tabela `horarios_funcionamento`
--

CREATE TABLE `horarios_funcionamento` (
  `id` int(11) NOT NULL,
  `id_coletor` int(11) NOT NULL,
  `dia_semana` enum('segunda','terca','quarta','quinta','sexta','sabado','domingo') NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 0,
  `hora_abertura` time DEFAULT NULL,
  `hora_fechamento` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `horarios_funcionamento`
--

INSERT INTO `horarios_funcionamento` (`id`, `id_coletor`, `dia_semana`, `ativo`, `hora_abertura`, `hora_fechamento`) VALUES
(1, 1, 'segunda', 1, '13:00:00', '17:00:00'),
(2, 1, 'terca', 1, '13:00:00', '17:00:00'),
(3, 1, 'quarta', 1, '13:00:00', '17:00:00'),
(4, 1, 'quinta', 1, '13:00:00', '17:00:00'),
(5, 1, 'sexta', 1, '13:00:00', '17:00:00'),
(6, 1, 'sabado', 0, NULL, NULL),
(7, 1, 'domingo', 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `otp_tokens`
--

CREATE TABLE `otp_tokens` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `otp_hash` varchar(64) NOT NULL,
  `otp_salt` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `attempts_left` int(11) DEFAULT 5,
  `sent_at` datetime NOT NULL,
  `last_resend_at` datetime DEFAULT NULL,
  `resend_count` int(11) DEFAULT 0,
  `purpose` varchar(30) DEFAULT 'cadastro',
  `used` tinyint(1) DEFAULT 0,
  `ip` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `otp_tokens`
--

INSERT INTO `otp_tokens` (`id`, `email`, `otp_hash`, `otp_salt`, `expires_at`, `attempts_left`, `sent_at`, `last_resend_at`, `resend_count`, `purpose`, `used`, `ip`, `user_agent`) VALUES
(1, 'pietro.123pietro4@gmail.com', '8f0ddbe8f32d92710bad46883028e5cbb047d3a5cdba93440ef52c8ec731e0fe', '5a9bf7f4a8ec1ef392bdb4d3b05ac520', '2025-11-13 00:06:36', 5, '2025-11-12 19:56:36', '2025-11-12 19:56:36', 0, 'cadastro', 0, '::1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36 OPR/95.0.0.0'),
(2, 'pietro.123pietro4@gmail.com', 'bff06d893b2f3ad4625647fe362481eb8759bfda5036cfe5709c28d6f37bb959', 'e0918d9b9a813e020338c9c74c3b889a', '2025-11-13 00:08:14', 5, '2025-11-12 19:58:14', '2025-11-12 19:58:14', 0, 'cadastro', 0, '::1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36 OPR/95.0.0.0'),
(3, 'pietrocesar@gmail.com', 'c51fe29499e9287b73f6f5f7e0fe9ef1e4dd20a12710075666327930c3edb194', '3587ae75c8479cac3e210653f1ac89ed', '2025-11-13 00:17:16', 5, '2025-11-12 20:07:16', '2025-11-12 20:07:16', 0, 'cadastro', 0, '::1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36 OPR/95.0.0.0'),
(4, 'pietrocesar@gmail.com', '4f7307e069ab7aca59e6474cd915e508af6571e832ca5f082c3b7e84ec473169', 'e37e7e9285ecc2168dfc44a1566bc974', '2025-11-13 00:17:43', 5, '2025-11-12 20:07:43', '2025-11-12 20:07:43', 0, 'cadastro', 0, '::1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36 OPR/95.0.0.0'),
(0, 'admin@gmail.com', 'ea71c65da064288675c0b58101f920f1e703d0aba8cc4ee500079e6a5a3a78b5', '37c636b954aec3918386b5f2b5ae7aae', '2025-11-20 21:54:58', 5, '2025-11-20 21:44:58', '2025-11-20 21:44:58', 0, 'cadastro_gerador', 0, '::1', ''),
(0, 'admin2@gmail.com', 'd34b4278b5be8ec857e1b4e0feabf1256d5cb6d684d91281636f322275912f2d', 'a14d92f172650080a3346eadcf7356ed', '2025-11-21 00:18:04', 5, '2025-11-21 00:08:04', '2025-11-21 00:08:04', 0, 'cadastro_gerador', 0, '::1', '');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `avaliacoes`
--
ALTER TABLE `avaliacoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_coleta` (`id_coleta`),
  ADD KEY `id_gerador` (`id_gerador`);

--
-- Índices de tabela `avaliacoes_coletores`
--
ALTER TABLE `avaliacoes_coletores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_historico_coleta` (`id_historico_coleta`),
  ADD KEY `id_gerador` (`id_gerador`),
  ADD KEY `idx_avaliacoes_coletor` (`id_coletor`);

--
-- Índices de tabela `coletas`
--
ALTER TABLE `coletas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_coletas_gerador` (`id_gerador`),
  ADD KEY `fk_coletas_coletor` (`id_coletor`);

--
-- Índices de tabela `coletores`
--
ALTER TABLE `coletores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `cpf_cnpj` (`cpf_cnpj`),
  ADD KEY `id_endereco` (`id_endereco`),
  ADD KEY `idx_coletores_email` (`email`),
  ADD KEY `idx_coletores_cpf_cnpj` (`cpf_cnpj`);

--
-- Índices de tabela `coletores_config`
--
ALTER TABLE `coletores_config`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_coletor` (`id_coletor`);

--
-- Índices de tabela `configuracoes_usuario`
--
ALTER TABLE `configuracoes_usuario`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_gerador` (`id_gerador`);

--
-- Índices de tabela `documentos_coletor`
--
ALTER TABLE `documentos_coletor`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_coletor` (`id_coletor`);

--
-- Índices de tabela `enderecos`
--
ALTER TABLE `enderecos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `ganhos_coletores`
--
ALTER TABLE `ganhos_coletores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_coletor` (`id_coletor`),
  ADD KEY `id_historico_coleta` (`id_historico_coleta`);

--
-- Índices de tabela `geradores`
--
ALTER TABLE `geradores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `cpf` (`cpf`),
  ADD KEY `id_endereco` (`id_endereco`),
  ADD KEY `idx_geradores_email` (`email`),
  ADD KEY `idx_geradores_cpf` (`cpf`);

--
-- Índices de tabela `historico_coletas`
--
ALTER TABLE `historico_coletas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_coleta` (`id_coleta`),
  ADD KEY `id_coletor` (`id_coletor`),
  ADD KEY `id_gerador` (`id_gerador`),
  ADD KEY `idx_historico_coletas_datas` (`data_inicio`,`data_conclusao`);

--
-- Índices de tabela `horarios_funcionamento`
--
ALTER TABLE `horarios_funcionamento`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_coletor` (`id_coletor`,`dia_semana`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `avaliacoes`
--
ALTER TABLE `avaliacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `avaliacoes_coletores`
--
ALTER TABLE `avaliacoes_coletores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `coletas`
--
ALTER TABLE `coletas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `coletores`
--
ALTER TABLE `coletores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `coletores_config`
--
ALTER TABLE `coletores_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `configuracoes_usuario`
--
ALTER TABLE `configuracoes_usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `documentos_coletor`
--
ALTER TABLE `documentos_coletor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `enderecos`
--
ALTER TABLE `enderecos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `ganhos_coletores`
--
ALTER TABLE `ganhos_coletores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `geradores`
--
ALTER TABLE `geradores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `historico_coletas`
--
ALTER TABLE `historico_coletas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `horarios_funcionamento`
--
ALTER TABLE `horarios_funcionamento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `avaliacoes`
--
ALTER TABLE `avaliacoes`
  ADD CONSTRAINT `avaliacoes_ibfk_1` FOREIGN KEY (`id_coleta`) REFERENCES `coletas` (`id`),
  ADD CONSTRAINT `avaliacoes_ibfk_2` FOREIGN KEY (`id_gerador`) REFERENCES `geradores` (`id`);

--
-- Restrições para tabelas `avaliacoes_coletores`
--
ALTER TABLE `avaliacoes_coletores`
  ADD CONSTRAINT `avaliacoes_coletores_ibfk_1` FOREIGN KEY (`id_historico_coleta`) REFERENCES `historico_coletas` (`id`),
  ADD CONSTRAINT `avaliacoes_coletores_ibfk_2` FOREIGN KEY (`id_gerador`) REFERENCES `geradores` (`id`),
  ADD CONSTRAINT `avaliacoes_coletores_ibfk_3` FOREIGN KEY (`id_coletor`) REFERENCES `coletores` (`id`);

--
-- Restrições para tabelas `coletas`
--
ALTER TABLE `coletas`
  ADD CONSTRAINT `fk_coletas_coletor` FOREIGN KEY (`id_coletor`) REFERENCES `coletores` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_coletas_gerador` FOREIGN KEY (`id_gerador`) REFERENCES `geradores` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `coletores`
--
ALTER TABLE `coletores`
  ADD CONSTRAINT `coletores_ibfk_1` FOREIGN KEY (`id_endereco`) REFERENCES `enderecos` (`id`);

--
-- Restrições para tabelas `coletores_config`
--
ALTER TABLE `coletores_config`
  ADD CONSTRAINT `coletores_config_ibfk_1` FOREIGN KEY (`id_coletor`) REFERENCES `coletores` (`id`);

--
-- Restrições para tabelas `configuracoes_usuario`
--
ALTER TABLE `configuracoes_usuario`
  ADD CONSTRAINT `configuracoes_usuario_ibfk_1` FOREIGN KEY (`id_gerador`) REFERENCES `geradores` (`id`);

--
-- Restrições para tabelas `documentos_coletor`
--
ALTER TABLE `documentos_coletor`
  ADD CONSTRAINT `documentos_coletor_ibfk_1` FOREIGN KEY (`id_coletor`) REFERENCES `coletores` (`id`);

--
-- Restrições para tabelas `ganhos_coletores`
--
ALTER TABLE `ganhos_coletores`
  ADD CONSTRAINT `ganhos_coletores_ibfk_1` FOREIGN KEY (`id_coletor`) REFERENCES `coletores` (`id`),
  ADD CONSTRAINT `ganhos_coletores_ibfk_2` FOREIGN KEY (`id_historico_coleta`) REFERENCES `historico_coletas` (`id`);

--
-- Restrições para tabelas `geradores`
--
ALTER TABLE `geradores`
  ADD CONSTRAINT `geradores_ibfk_1` FOREIGN KEY (`id_endereco`) REFERENCES `enderecos` (`id`);

--
-- Restrições para tabelas `historico_coletas`
--
ALTER TABLE `historico_coletas`
  ADD CONSTRAINT `historico_coletas_ibfk_1` FOREIGN KEY (`id_coleta`) REFERENCES `coletas` (`id`),
  ADD CONSTRAINT `historico_coletas_ibfk_2` FOREIGN KEY (`id_coletor`) REFERENCES `coletores` (`id`),
  ADD CONSTRAINT `historico_coletas_ibfk_3` FOREIGN KEY (`id_gerador`) REFERENCES `geradores` (`id`);

--
-- Restrições para tabelas `horarios_funcionamento`
--
ALTER TABLE `horarios_funcionamento`
  ADD CONSTRAINT `horarios_funcionamento_ibfk_1` FOREIGN KEY (`id_coletor`) REFERENCES `coletores` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
