-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 20/11/2025 às 08:33
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
  `id_gerador` int(11) DEFAULT NULL,
  `data_coleta` datetime DEFAULT NULL,
  `status` enum('pendente','agendada','concluida','cancelada') DEFAULT 'pendente',
  `quantidade_oleo` decimal(10,2) DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `periodo` set('Manhã','Tarde','','') NOT NULL,
  `cep` varchar(9) NOT NULL,
  `rua` varchar(100) NOT NULL,
  `numero` varchar(10) NOT NULL,
  `bairro` varchar(50) NOT NULL,
  `cidade` varchar(50) NOT NULL,
  `complemento` varchar(100) DEFAULT NULL,
  `data_solicitacao` datetime NOT NULL,
  `estado` char(2) NOT NULL,
  `id_coletor` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `coletas`
--

INSERT INTO `coletas` (`id`, `id_gerador`, `data_coleta`, `status`, `quantidade_oleo`, `observacoes`, `created_at`, `updated_at`, `periodo`, `cep`, `rua`, `numero`, `bairro`, `cidade`, `complemento`, `data_solicitacao`, `estado`, `id_coletor`) VALUES
(1, 9, '0000-00-00 00:00:00', 'pendente', 5.00, 'k', '2025-11-20 07:08:19', '2025-11-20 07:08:19', 'Manhã', '01001-000', 'Praça da Sé', '212', 'Sé', 'São Paulo', 'Casa', '2025-11-20 00:00:00', 'SP', NULL),
(2, 9, '2026-02-10 00:00:00', 'pendente', 1.00, 'Casa', '2025-11-20 07:14:02', '2025-11-20 07:14:02', 'Manhã', '01001-000', 'Praça da Sé', '32', 'Sé', 'São Paulo', 'ABC', '2025-11-20 00:00:00', 'SP', NULL),
(3, 9, '2025-11-24 00:00:00', 'pendente', 2.00, '', '2025-11-20 07:24:39', '2025-11-20 07:24:39', 'Tarde', '01001-000', 'Praça da Sé', '100', 'Sé', 'São Paulo', '', '2025-11-20 04:24:39', 'SP', NULL);

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
  `data_nasc` date NOT NULL,
  `genero` enum('M','F','O','') DEFAULT '',
  `foto_perfil` varchar(255) DEFAULT NULL,
  `id_endereco` int(11) DEFAULT NULL,
  `tipo_coletor` enum('pessoa_fisica','pessoa_juridica','') DEFAULT 'pessoa_fisica',
  `meio_transporte` enum('carro','moto','bicicleta','van','caminhao','') NOT NULL DEFAULT '',
  `experiencia` text DEFAULT NULL,
  `raio_atuacao` int(11) DEFAULT 5,
  `capacidade_coleta` decimal(10,2) DEFAULT NULL,
  `status` enum('ativo','inativo','pendente','suspenso') DEFAULT 'pendente',
  `avaliacao_media` decimal(3,2) DEFAULT 0.00,
  `total_avaliacoes` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `email_verificado` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `coletores`
--

INSERT INTO `coletores` (`id`, `email`, `senha`, `nome_completo`, `cpf_cnpj`, `telefone`, `data_nasc`, `genero`, `foto_perfil`, `id_endereco`, `tipo_coletor`, `meio_transporte`, `experiencia`, `raio_atuacao`, `capacidade_coleta`, `status`, `avaliacao_media`, `total_avaliacoes`, `created_at`, `updated_at`, `email_verificado`) VALUES
(1, 'coleti.lingeardi@gmail.com', '$2y$10$7BS6YKbLjqQJicB/gJct6Oh18zeCKyqwXNVThtH59gf.2fPmPTMjW', 'Pietro de Cesar Coimbra', '64571379030', '13982095384', '2001-01-20', '', NULL, 4, '', 'carro', NULL, 5, NULL, 'pendente', 0.00, 0, '2025-10-28 21:37:44', '2025-10-28 21:37:44', 0),
(2, 'pietrosar@gmail.com', '$2y$10$z7vLG7N97Xv1UnTnwEFITebIVvXfzPwxR3ZPSmQOIFXcsBRlMjez.', 'Pietro de Cesar Coimbra', '62935880021', '11923153201', '2000-01-20', '', NULL, 5, '', '', NULL, 5, NULL, 'pendente', 0.00, 0, '2025-10-28 21:46:14', '2025-10-28 21:46:14', 0),
(8, 'abd@gmail.com', '$2y$10$WAn2YOqRPccVHDC5nYiJ6OyFo2YmQyXpdfhVrxlcSwElKIgholeHi', 'Pietro de Coimbra Cesar', '92603116088', '91988854658', '1999-01-20', '', '8_69030b0184fac.jpg', 11, 'pessoa_fisica', '', NULL, 5, NULL, 'pendente', 0.00, 0, '2025-10-30 06:51:45', '2025-10-30 06:51:45', 0),
(9, 'abc@gmail.com', '$2y$10$cY5aSvCCdmwHYoyDMnoCT.YE0zzblBgOo/cZ9MxZdus1Zn0r7HiZu', 'Lucas Coleti Lingeardi', '47696880080', '13982095384', '2000-01-20', 'M', '9_69039ec776156.png', 12, 'pessoa_fisica', '', NULL, 5, NULL, 'pendente', 0.00, 0, '2025-10-30 17:22:15', '2025-10-30 17:22:15', 0),
(10, 'pietrocesar@gmail.com', '$2y$10$XRqTNt7B8.mup11C4dUIieTdFsIdo3i0myMgoS/tY00.mwrFVgw6.', 'Pietro Cesar', '52693545862', '1398183930', '2000-02-04', 'M', '10_6904f44480087.png', 16, 'pessoa_fisica', '', NULL, 5, NULL, 'pendente', 0.00, 0, '2025-10-31 17:39:16', '2025-10-31 17:39:16', 0),
(11, 'admin@gmail.com', '$2y$10$si3LC4LE3lJ5SDlqxpZtH.oIvKN5IbuP8TgNqS..Ei40Q4ZEjuaDK', 'Coletor Administrador', '94968076401', '68992089401', '2001-03-12', 'M', '11_6919496872fca.jpeg', 24, 'pessoa_fisica', '', NULL, 5, NULL, 'pendente', 0.00, 0, '2025-11-16 03:47:52', '2025-11-16 03:47:52', 0),
(12, 'coletor1@test.com', '3decd49a6c6dce88c16a85b9a8e42b51aa36f1e2', 'JoÒo Silva', '12345678901', '1133334444', '1990-05-15', 'M', NULL, 28, '', 'carro', NULL, 15, 100.00, 'ativo', 4.50, 10, '2025-11-18 20:26:25', '2025-11-18 20:26:25', 0),
(13, 'maria@test.com', '3decd49a6c6dce88c16a85b9a8e42b51aa36f1e2', 'Maria Santos', '98765432100', '1144445555', '1985-08-20', 'F', NULL, 29, '', '', NULL, 10, 50.00, 'ativo', 5.00, 15, '2025-11-18 20:26:36', '2025-11-18 20:26:36', 0),
(14, 'pedro@test.com', '3decd49a6c6dce88c16a85b9a8e42b51aa36f1e2', 'Pedro Oliveira', '11111111111', '1155556666', '1992-03-10', 'M', NULL, 30, '', 'van', NULL, 20, 150.00, 'ativo', 4.00, 8, '2025-11-18 20:26:36', '2025-11-18 20:26:36', 0);

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

--
-- Despejando dados para a tabela `configuracoes_usuario`
--

INSERT INTO `configuracoes_usuario` (`id`, `id_gerador`, `notificacoes_email`, `notificacoes_push`, `tema_escuro`, `privacidade_perfil`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 0, 'publico', '2025-10-31 16:27:15', '2025-10-31 16:27:15'),
(2, 2, 1, 1, 0, 'publico', '2025-10-31 16:27:15', '2025-10-31 16:27:15');

-- --------------------------------------------------------

--
-- Estrutura para tabela `disponibilidade_coletores`
--

CREATE TABLE `disponibilidade_coletores` (
  `id` int(11) NOT NULL,
  `id_coletor` int(11) NOT NULL,
  `dia_semana` enum('domingo','segunda','terca','quarta','quinta','sexta','sabado') NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fim` time NOT NULL,
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
(1, '222', 'Praça da S', 'Casa', 'Sé', 'São Paulo', 'SP', '01001-000', '2025-10-28 21:22:16', '2025-10-28 21:22:16'),
(2, '222', 'Praça da S', 'Casa', 'Sé', 'São Paulo', 'SP', '01001-000', '2025-10-28 21:22:16', '2025-10-28 21:22:16'),
(3, '312', 'Rua Direit', 'Casa', 'Sé', 'São Paulo', 'SP', '01002-000', '2025-10-28 21:34:54', '2025-10-28 21:34:54'),
(4, '312', 'Rua Direit', 'Casa', 'Sé', 'São Paulo', 'SP', '01002-000', '2025-10-28 21:37:43', '2025-10-28 21:37:43'),
(5, '20', 'Rua Direit', 'Casa', 'Sé', 'São Paulo', 'SP', '01002-000', '2025-10-28 21:46:14', '2025-10-28 21:46:14'),
(6, '201', 'Praça da S', 'Casa', 'Sé', 'São Paulo', 'SP', '01001-000', '2025-10-30 06:01:24', '2025-10-30 06:01:24'),
(7, '201', 'Praça da S', 'Casa', 'Sé', 'São Paulo', 'SP', '01001-000', '2025-10-30 06:04:52', '2025-10-30 06:04:52'),
(8, '201', 'Praça da S', 'Casa', 'Sé', 'São Paulo', 'SP', '01001-000', '2025-10-30 06:05:59', '2025-10-30 06:05:59'),
(9, '311', 'Rua Direit', 'Apto', 'Sé', 'São Paulo', 'SP', '01002-000', '2025-10-30 06:44:33', '2025-10-30 06:44:33'),
(10, '402', 'Praça da S', 'Apto', 'Sé', 'São Paulo', 'SP', '01001-000', '2025-10-30 06:47:35', '2025-10-30 06:47:35'),
(11, '873', 'Travessa S', 'Apto', 'Industrial', 'Aracaju', 'SE', '49066-243', '2025-10-30 06:51:45', '2025-10-30 06:51:45'),
(12, '941', 'Praça da S', 'Apto', 'Sé', 'São Paulo', 'SP', '01001-000', '2025-10-30 17:22:15', '2025-10-30 17:22:15'),
(13, '202', 'Rua Direit', 'Apto', 'Sé', 'São Paulo', 'SP', '01002-000', '2025-10-31 05:19:43', '2025-10-31 05:19:43'),
(14, '949', 'Travessa P', 'Apto', 'Barro Vermelho', 'Natal', 'RN', '59020-565', '2025-10-31 05:37:16', '2025-10-31 05:37:16'),
(15, '', 'Rua São Do', '', 'Caiçara', 'Praia Grande', 'SP', '11706-380', '2025-10-31 16:33:20', '2025-10-31 16:33:20'),
(16, '', 'Rua São Do', '', 'Caiçara', 'Praia Grande', 'SP', '11706-380', '2025-10-31 17:39:16', '2025-10-31 17:39:16'),
(17, '', 'Rua Vinte ', '', 'Princesa', 'Praia Grande', 'SP', '11711-000', '2025-11-13 01:50:50', '2025-11-13 01:50:50'),
(18, '', 'Rua Vinte ', '', 'Princesa', 'Praia Grande', 'SP', '11711-000', '2025-11-13 01:56:23', '2025-11-13 01:56:23'),
(19, '', 'Rua Vinte ', '', 'Princesa', 'Praia Grande', 'SP', '11711-000', '2025-11-13 02:00:21', '2025-11-13 02:00:21'),
(20, '', 'Rua Vinte ', '', 'Princesa', 'Praia Grande', 'SP', '11711-000', '2025-11-13 02:41:22', '2025-11-13 02:41:22'),
(21, '', 'Rua Vinte ', '', 'Princesa', 'Praia Grande', 'SP', '11711-000', '2025-11-13 14:17:31', '2025-11-13 14:17:31'),
(22, '', 'Rua Guabir', '', 'Alvorada', 'Manaus', 'AM', '69043-006', '2025-11-16 03:38:47', '2025-11-16 03:38:47'),
(23, '', 'Caminho 03', '', 'Cajazeiras X', 'Salvador', 'BA', '41340-270', '2025-11-16 03:42:51', '2025-11-16 03:42:51'),
(24, 'Rua Oswaldo Coelho', '', '', 'Areal', 'Rio Branco', 'AC', '69906-086', '2025-11-16 03:47:52', '2025-11-16 03:47:52'),
(25, 'Rua Odair Costa Ferreira', '471', 'Casa', 'Vila Industrial', 'Anápolis', 'GO', '75115-090', '2025-11-18 18:06:37', '2025-11-18 18:06:37'),
(26, 'Rua Padre Reus', '923', 'Casa', 'São Tomé', 'Viamão', 'RS', '94460-220', '2025-11-18 18:34:42', '2025-11-18 18:34:42'),
(27, 'Rua Esportista Roni Rocha', '920', 'Casa', 'Novo Horizonte', 'Itajubá', 'MG', '37505-452', '2025-11-18 18:48:46', '2025-11-18 18:48:46'),
(28, 'Rua das Flores', '123', NULL, 'Centro', 'Praia Grande', 'SP', '11700-000', '2025-11-18 20:26:24', '2025-11-18 20:26:24'),
(29, 'Avenida Paulista', '456', NULL, 'Bela Vista', 'SÒo Paulo', 'SP', '01311-100', '2025-11-18 20:26:36', '2025-11-18 20:26:36'),
(30, 'Rua Augusta', '789', NULL, 'ConsolaþÒo', 'SÒo Paulo', 'SP', '01305-100', '2025-11-18 20:26:36', '2025-11-18 20:26:36');

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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `email_verificado` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `geradores`
--

INSERT INTO `geradores` (`id`, `email`, `senha`, `nome_completo`, `cpf`, `telefone`, `data_nasc`, `genero`, `foto_perfil`, `id_endereco`, `status`, `created_at`, `updated_at`, `email_verificado`) VALUES
(1, 'jfg@gmail.com', 'senhasecura123$', 'Super Admin', '504.394.860-41', '13997584650', '1998-10-22', 'M', NULL, NULL, 'pendente', '2025-10-31 05:34:53', '2025-10-31 05:34:53', 0),
(2, 'matheus.santossx@gmail.com', 'MelhorProf321@', 'Lucas Coleti Lingeardi', '47696880080', '13982095384', '2000-08-30', 'M', NULL, 14, 'pendente', '2025-10-31 05:37:16', '2025-11-16 03:44:10', 0),
(4, 'kip75777@gmail.com', '$2y$10$nckZkTckndVZdhXeA5IL7OmvG2sxrIRnckTDYM11WxG6j2OLrImfy', 'Pietro Cesar', '05148016973', '11981840505', '2000-12-25', 'M', NULL, 17, 'pendente', '2025-11-13 01:50:50', '2025-11-13 01:50:50', 0),
(7, 'pietrovxvv@gmail.com', '$2y$10$F1DlBYGK3Qmp7.36WRH9KevXiqOmAbi9jHOj53H7dd9l4KSbFyryS', 'Pietro Cesar', '52693545862', '13981850565', '2000-05-04', 'M', NULL, 21, 'pendente', '2025-11-13 14:17:32', '2025-11-13 14:17:32', 0),
(8, 'theteus.ssx@gmail.com', '$2y$10$gbqmyr9pRJIX/MFZIgJSuehMtx/ErnnWvVv8UR0cDjGnLG2Er2Uzu', 'Alberto Vargaz', '34274681009', '9225116148', '2000-02-19', 'M', NULL, 22, 'pendente', '2025-11-16 03:38:47', '2025-11-16 03:38:47', 0),
(9, 'admin@gmail.com', '$2y$10$99Emq3kQDKtpxHgl.8aL1OvrEd3nUBBDOCeMGS4kJaMVHj5ANLEGS', 'Gerador Administrador', '94118572052', '71981811999', '2000-01-20', 'M', NULL, 23, 'pendente', '2025-11-16 03:42:52', '2025-11-16 03:42:52', 0),
(10, 'calebe.manuel.costa@eletrovip.com', '$2y$10$MI/aJepdOpBe732fTzjnyefIrr.LiPnOG/zwa8m5JjdSk9kH6D0c2', 'Calebe Manuel Vicente Costa', '56758584909', '62981316643', '2000-01-20', 'F', NULL, 25, 'pendente', '2025-11-18 18:06:37', '2025-11-18 18:06:37', 0),
(11, 'nina_mariah_barros@edepbr.com.br', '$2y$10$94IsJTjJDKT7ZJ2i.OdUd.As7Vc78/U5S5wKwaAHthWS442nwozie', 'Nina Mariah Barros', '43605015956', '51983765453', '1994-10-20', 'M', NULL, 26, 'pendente', '2025-11-18 18:34:42', '2025-11-18 18:34:42', 0),
(12, 'yago_diogo_almeida@montcalm.com.br', '$2y$10$qy478CUDS3F4sknuvJoLDOMGIaJ7Xu5gPemH9Tj8.7Hh1uqlAZIXK', 'Yago Diogo Almeida', '10146589777', '35997185746', '1999-03-05', '', '12_691cbf8e26568.jpeg', 27, 'pendente', '2025-11-18 18:48:46', '2025-11-18 18:48:46', 0);

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
(5, 'pietro.123pietro4@gmail.com', 'c1d746f6b437f54ae18e1c183e6a52e99b7a16eeeb94e5a32dfdf0b0dc0d6c2b', 'e4543d6840875e33ed642e9b8ffa0e0e', '2025-11-13 00:17:56', 5, '2025-11-12 20:07:56', '2025-11-12 20:07:56', 0, 'cadastro', 0, '::1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36 OPR/95.0.0.0'),
(6, 'pietro.123pietro4@gmail.com', '87dc8d908a839d14b56083497cada03fc0b3e26a9aad9afbce1368e05d31e3d0', '5deea4e889d24c3874a2b9a46228dd15', '2025-11-13 00:18:05', 5, '2025-11-12 20:08:05', '2025-11-12 20:08:05', 0, 'cadastro', 0, '::1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36 OPR/95.0.0.0'),
(7, 'kipo4223@gmail.com', '496955247f7071abff2ea0db7a3eeb0e514fb1d6426596a2645023ca08816644', '2a18130add04e3433322b670b8495be8', '2025-11-13 00:18:21', 5, '2025-11-12 20:08:21', '2025-11-12 20:08:21', 0, 'cadastro', 0, '::1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36 OPR/95.0.0.0'),
(8, 'kipo4223@gmail.com', '0787f32ef53ec533350b6c2f542b0614dc68d7d7f3bb3968dc6ae746c4ebaab4', '335ad6bcd5a0eb2c2b73f67a8b82b6df', '2025-11-13 00:19:03', 5, '2025-11-12 20:09:03', '2025-11-12 20:09:03', 0, 'cadastro', 0, '::1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36 OPR/95.0.0.0'),
(9, 'kipo4223@gmail.com', 'e9b00d374319836881a3bf23e66d00a1f4aa94fad53423078ddc981cb14adcd0', '0be621c95991e1fd88c6c44c3b24fada', '2025-11-13 00:22:53', 5, '2025-11-12 20:12:53', '2025-11-12 20:12:53', 0, 'cadastro', 0, '::1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36 OPR/95.0.0.0'),
(10, 'kipo4223@gmail.com', 'c92362bd8e485421f5c608d14d23003630eb887261c50b8f71f2cf835945cf4d', '4861b7874ebd74d1cb503ce39a8e6f42', '2025-11-13 00:27:11', 5, '2025-11-12 20:17:11', '2025-11-12 20:17:11', 0, 'cadastro', 0, '::1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36 OPR/95.0.0.0'),
(11, 'kipo4223@gmail.com', '3fa21f35e63a2429681248a3d60a7af41f6764f6f0e6fd5f8db0d716b8ca7679', 'f23f4e5d3060eeb68a45faafe16a4b0d', '2025-11-13 00:36:45', 5, '2025-11-12 20:26:45', '2025-11-12 20:26:45', 0, 'cadastro', 0, '::1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36 OPR/95.0.0.0'),
(12, 'pietro.123pietro4@gmail.com', '6022f0cfead8015f97194c0f246c28bd024bb54293492c8542a9472d534ddf79', 'ed1a23c1a426435c139a41237cc23164', '2025-11-13 00:50:35', 5, '2025-11-12 20:40:35', '2025-11-12 20:40:35', 0, 'cadastro', 0, '::1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36 OPR/95.0.0.0'),
(13, 'kipodela@gmail.com', '948561eeca794cc8101833463b97f31c964b00cb838a4d51a107433f99c22e94', 'fdec93e4d1f1c7aecbf34f9c93addad0', '2025-11-13 01:24:49', 5, '2025-11-12 21:14:49', '2025-11-12 21:14:49', 0, 'cadastro_coletor', 0, '::1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36 OPR/95.0.0.0'),
(14, 'kipo4777@gmail.com', 'e97afa078320153d91d4328d6aa7d507f00e710d519ebc8ed23ae354a8441b84', 'cad8ddb39031327993d0a71584046a86', '2025-11-13 02:07:32', 5, '2025-11-12 21:57:32', '2025-11-12 21:57:32', 0, 'cadastro_gerador', 1, '::1', ''),
(15, 'kipo4777@gmail.com', '39918c21bc8c924ce288c9c7460ac18cd224878a0bcabf4ab953c7760e5857a4', '1c0250db7b96fc78d058aef19533081e', '2025-11-13 02:17:54', 5, '2025-11-12 22:07:54', '2025-11-12 22:07:54', 0, 'cadastro_gerador', 0, '::1', ''),
(16, 'kipo4777@gmail.com', '7a1bfdaefc562e31192d4bb9790578ee84dd4fdc75c4ea39b720303c6ae2c572', 'dc306974eb3f1adab0ba85878e7f0e24', '2025-11-13 02:18:21', 5, '2025-11-12 22:08:21', '2025-11-12 22:08:21', 0, 'cadastro_gerador', 0, '::1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36 OPR/95.0.0.0'),
(17, 'kipo4777@gmail.com', '0da457175bc4f2fc92496b7df2df5fae48625431bbff2b03f82af45d259e50ed', 'd500f962950f9182221ff9b7ae0dc2e3', '2025-11-13 02:25:26', 5, '2025-11-12 22:15:26', '2025-11-12 22:15:26', 0, 'cadastro_gerador', 0, '::1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36 OPR/95.0.0.0'),
(18, 'kipo4777@gmail.com', 'bbb7a2e4b681c8be89c8b3d1fefe2186637c8d83847b32020386ce494f1d456b', 'a9c2679ae7554568d0eed32b05d77939', '2025-11-13 02:28:58', 5, '2025-11-12 22:18:58', '2025-11-12 22:18:58', 0, 'cadastro_gerador', 1, '::1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36 OPR/95.0.0.0'),
(19, 'pietrocesar@gmail.com', 'dd5921aafe696920405202ede2916979ebb5aac23d94fed0ce014102ab7be696', 'a9bf269ad394dd0dbfeb0f944610be67', '2025-11-13 02:29:30', 5, '2025-11-12 22:19:30', '2025-11-12 22:19:30', 0, 'cadastro_gerador', 0, '::1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36 OPR/95.0.0.0'),
(20, 'pietrocesr@gmail.com', '957953a5226b5a3d2bcfc84c0d530d28a4d39156e25ae968b82e4ec297b8ed00', '73e442c7e900d081a6b8976846832747', '2025-11-13 02:49:54', 5, '2025-11-12 22:39:54', '2025-11-12 22:39:54', 0, 'cadastro_gerador', 0, '::1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36 OPR/95.0.0.0'),
(21, 'kip75777@gmail.com', '9460cefa852799e36dc3df3b4f98a7769eacc12dcfb3bd8f282f57e80cc32cfd', '7eab84bb277cd4f454f0f01b4043e4fe', '2025-11-13 02:51:09', 5, '2025-11-12 22:41:09', '2025-11-12 22:41:09', 0, 'cadastro_gerador', 1, '::1', ''),
(22, 'kip75777@gmail.com', 'f5ec73417e3c1d61e3806df5a1138c1d47ef7297ff36712403e12cb09a685269', '15b4051481804ed19ebd3a9c134775e1', '2025-11-13 02:55:19', 4, '2025-11-12 22:45:19', '2025-11-12 22:45:19', 0, 'cadastro_gerador', 1, '::1', ''),
(23, 'kip75777@gmail.com', '305eb93b706e516b7d71ea0001b39d7232941e15beef996539b07dca71f2573f', 'a61ccc0bdee5b4537d304287d94b083f', '2025-11-13 02:59:39', 5, '2025-11-12 22:49:39', '2025-11-12 22:49:39', 0, 'cadastro_gerador', 1, '::1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36 OPR/95.0.0.0'),
(24, 'kipdelax@gmail.com', '39477dc19f6aabd536cb044f9452c94d73afffb1fa5768e56a426b0ec8c60ea7', 'aa80815ce43d0e9d8ab48d7f1e33dcbc', '2025-11-13 03:09:33', 5, '2025-11-12 22:59:33', '2025-11-12 22:59:33', 0, 'cadastro_gerador', 1, '::1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36 OPR/95.0.0.0'),
(25, 'kipdelax@gmail.com', '6b3baef657adddab2685026d06ab88b8811c2feb5c46e1bbac6800565fef873f', '7b5433249e61dd71ecceafeca4bd06b6', '2025-11-13 03:27:12', 5, '2025-11-12 23:17:12', '2025-11-12 23:17:12', 0, 'cadastro_gerador', 1, '::1', ''),
(26, 'pietrovxvv@gmail.com', '728897ec5756985728904372288a345b94a8dd52063c62400e8318948f1ee762', '7a3b47ce91d8f5de45d0930d53150904', '2025-11-13 15:26:25', 5, '2025-11-13 11:16:25', '2025-11-13 11:16:25', 0, 'cadastro_gerador', 1, '::1', ''),
(27, 'theteus.ssx@gmail.com', 'a32f8c33fa515e976171ec4d4b499d5cfae22f5092e0870a6bf0148a4c31ddad', 'f37f28648a7c7fe30188a77f8aaaf8b8', '2025-11-16 03:33:27', 5, '2025-11-15 23:23:27', '2025-11-15 23:23:27', 0, 'cadastro_gerador', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 CCleaner/141.0.0.0'),
(28, 'theteus.ssx@gmail.com', 'dbbe7a49cf8fe270b7fdbc438a2a79a48f31df327c470fb652fb676d32a1ebc8', 'd31ba84b28dbccb6535a3e1a760e8772', '2025-11-16 03:50:34', 5, '2025-11-15 23:40:34', '2025-11-15 23:40:34', 0, 'cadastro_gerador', 0, '::1', ''),
(29, 'theteus.ssx@gmail.com', '1dda0f0efc6393e8e0f745473cb9e81beff0199ee42d32e8eba3667b30a58497', 'ec17b6e579510f0759ab13c024b8979f', '2025-11-16 04:45:50', 5, '2025-11-16 00:35:50', '2025-11-16 00:35:50', 0, 'cadastro_gerador', 0, '::1', ''),
(30, 'theteus.ssx@gmail.com', 'bff79cac397ce8073ac23d5963d2b76d9602be271e0bd960de96c37f5a1994c4', '2aabc1439a49375c6d380a8b321b3872', '2025-11-16 04:46:09', 5, '2025-11-16 00:36:09', '2025-11-16 00:36:09', 0, 'cadastro_gerador', 0, '::1', ''),
(31, 'admin@gmail.com', '0a056fdecff5be823cd18b0b14dd679c92d9861a83dcf7186684c5b10dff95c3', '1eff5fc0bb471037cb4a6758418e3276', '2025-11-16 04:51:10', 5, '2025-11-16 00:41:10', '2025-11-16 00:41:10', 0, 'cadastro_gerador', 0, '::1', ''),
(32, 'admin@gmail.com', '304a80ff2e5f169be82a893637b9147d0569b4fa557cd0af67960f07765d26b6', 'b0383360b767756f8f6d5777a20b9b13', '2025-11-16 04:51:38', 5, '2025-11-16 00:41:38', '2025-11-16 00:41:38', 0, 'cadastro_gerador', 0, '::1', ''),
(33, 'calebe.manuel.costa@eletrovip.com', 'a2d0a5834c52b794b4c0d28907e222a203487e68e43810904a77d076aa9ae09b', '3935e6115958da49275e0cfcd238d960', '2025-11-18 19:15:38', 5, '2025-11-18 15:05:38', '2025-11-18 15:05:38', 0, 'cadastro_gerador', 0, '::1', ''),
(34, 'nina_mariah_barros@edepbr.com.br', '092acd9772a6fea9884098f3dc43abc7e18a0574dbd9d4150d0e0da7b28a17a3', 'd4f087800f67202dc8549d4c01031203', '2025-11-18 19:43:44', 5, '2025-11-18 15:33:44', '2025-11-18 15:33:44', 0, 'cadastro_gerador', 0, '::1', ''),
(35, 'yago_diogo_almeida@montcalm.com.br', '883a87e3530fb0b740170ea6ab2788fd4844b9fd8c7c0cb9304510c579f211f6', '2a4beff91c36a4a5513b11b53d8f2335', '2025-11-18 19:57:17', 5, '2025-11-18 15:47:17', '2025-11-18 15:47:17', 0, 'cadastro_gerador', 0, '::1', '');

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
  ADD KEY `id_gerador` (`id_gerador`),
  ADD KEY `idx_coletas_data` (`data_coleta`),
  ADD KEY `idx_coletas_status` (`status`),
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
  ADD KEY `idx_coletores_cpf_cnpj` (`cpf_cnpj`),
  ADD KEY `idx_coletores_status` (`status`);

--
-- Índices de tabela `configuracoes_usuario`
--
ALTER TABLE `configuracoes_usuario`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_gerador` (`id_gerador`);

--
-- Índices de tabela `disponibilidade_coletores`
--
ALTER TABLE `disponibilidade_coletores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_disponibilidade` (`id_coletor`,`dia_semana`),
  ADD KEY `idx_disponibilidade_dia` (`dia_semana`);

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
-- Índices de tabela `otp_tokens`
--
ALTER TABLE `otp_tokens`
  ADD PRIMARY KEY (`id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `coletas`
--
ALTER TABLE `coletas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `coletores`
--
ALTER TABLE `coletores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de tabela `configuracoes_usuario`
--
ALTER TABLE `configuracoes_usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `disponibilidade_coletores`
--
ALTER TABLE `disponibilidade_coletores`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de tabela `ganhos_coletores`
--
ALTER TABLE `ganhos_coletores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `geradores`
--
ALTER TABLE `geradores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de tabela `historico_coletas`
--
ALTER TABLE `historico_coletas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `otp_tokens`
--
ALTER TABLE `otp_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

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
  ADD CONSTRAINT `coletas_ibfk_1` FOREIGN KEY (`id_gerador`) REFERENCES `geradores` (`id`),
  ADD CONSTRAINT `fk_coletas_coletor` FOREIGN KEY (`id_coletor`) REFERENCES `coletores` (`id`);

--
-- Restrições para tabelas `coletores`
--
ALTER TABLE `coletores`
  ADD CONSTRAINT `coletores_ibfk_1` FOREIGN KEY (`id_endereco`) REFERENCES `enderecos` (`id`);

--
-- Restrições para tabelas `configuracoes_usuario`
--
ALTER TABLE `configuracoes_usuario`
  ADD CONSTRAINT `configuracoes_usuario_ibfk_1` FOREIGN KEY (`id_gerador`) REFERENCES `geradores` (`id`);

--
-- Restrições para tabelas `disponibilidade_coletores`
--
ALTER TABLE `disponibilidade_coletores`
  ADD CONSTRAINT `disponibilidade_coletores_ibfk_1` FOREIGN KEY (`id_coletor`) REFERENCES `coletores` (`id`);

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
