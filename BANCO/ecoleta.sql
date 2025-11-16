-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 31/10/2025 às 07:06
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
  `data_solicitacao` datetime NOT NULL,
  `data_coleta` datetime DEFAULT NULL,
  `status` enum('pendente','agendada','concluida','cancelada') DEFAULT 'pendente',
  `quantidade_oleo` decimal(10,2) DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `tipo_coletor` enum('pessoa_fisica','pessoa_juridica') DEFAULT 'pessoa_fisica',
  `meio_transporte` enum('carro','moto','bicicleta','van','caminhao') NOT NULL,
  `raio_atuacao` int(11) DEFAULT 5,
  `capacidade_coleta` decimal(10,2) DEFAULT NULL,
  `status` enum('ativo','inativo','pendente','suspenso') DEFAULT 'pendente',
  `avaliacao_media` decimal(3,2) DEFAULT 0.00,
  `total_avaliacoes` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `coletores`
--

INSERT INTO `coletores` (`id`, `email`, `senha`, `nome_completo`, `cpf_cnpj`, `telefone`, `data_nasc`, `genero`, `foto_perfil`, `id_endereco`, `tipo_coletor`, `meio_transporte`, `raio_atuacao`, `capacidade_coleta`, `status`, `avaliacao_media`, `total_avaliacoes`, `created_at`, `updated_at`) VALUES
(1, 'coleti.lingeardi@gmail.com', '$2y$10$7BS6YKbLjqQJicB/gJct6Oh18zeCKyqwXNVThtH59gf.2fPmPTMjW', 'Pietro de Cesar Coimbra', '64571379030', '13982095384', '2001-01-20', '', NULL, 4, '', 'carro', 5, NULL, 'pendente', 0.00, 0, '2025-10-28 21:37:44', '2025-10-28 21:37:44'),
(2, 'pietrosar@gmail.com', '$2y$10$z7vLG7N97Xv1UnTnwEFITebIVvXfzPwxR3ZPSmQOIFXcsBRlMjez.', 'Pietro de Cesar Coimbra', '62935880021', '11923153201', '2000-01-20', '', NULL, 5, '', '', 5, NULL, 'pendente', 0.00, 0, '2025-10-28 21:46:14', '2025-10-28 21:46:14'),
(8, 'abd@gmail.com', '$2y$10$WAn2YOqRPccVHDC5nYiJ6OyFo2YmQyXpdfhVrxlcSwElKIgholeHi', 'Pietro de Coimbra Cesar', '92603116088', '91988854658', '1999-01-20', '', '8_69030b0184fac.jpg', 11, 'pessoa_fisica', '', 5, NULL, 'pendente', 0.00, 0, '2025-10-30 06:51:45', '2025-10-30 06:51:45'),
(9, 'abc@gmail.com', '$2y$10$cY5aSvCCdmwHYoyDMnoCT.YE0zzblBgOo/cZ9MxZdus1Zn0r7HiZu', 'Lucas Coleti Lingeardi', '47696880080', '13982095384', '2000-01-20', 'M', '9_69039ec776156.png', 12, 'pessoa_fisica', '', 5, NULL, 'pendente', 0.00, 0, '2025-10-30 17:22:15', '2025-10-30 17:22:15');

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
(14, '949', 'Travessa P', 'Apto', 'Barro Vermelho', 'Natal', 'RN', '59020-565', '2025-10-31 05:37:16', '2025-10-31 05:37:16');

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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `geradores`
--

INSERT INTO `geradores` (`id`, `email`, `senha`, `nome_completo`, `cpf`, `telefone`, `data_nasc`, `genero`, `foto_perfil`, `id_endereco`, `status`, `created_at`, `updated_at`) VALUES
(1, 'jfg@gmail.com', 'senhasecura123$', 'Super Admin', '504.394.860-41', '13997584650', '1998-10-22', 'M', NULL, NULL, 'pendente', '2025-10-31 05:34:53', '2025-10-31 05:34:53'),
(2, 'matheus.santossx@gmail.com', 'Meutec2023$', 'Lucas Coleti Lingeardi', '47696880080', '13982095384', '2000-08-30', 'M', NULL, 14, 'pendente', '2025-10-31 05:37:16', '2025-10-31 05:37:16');

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
  ADD KEY `idx_coletas_status` (`status`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `coletores`
--
ALTER TABLE `coletores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `configuracoes_usuario`
--
ALTER TABLE `configuracoes_usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de tabela `ganhos_coletores`
--
ALTER TABLE `ganhos_coletores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `geradores`
--
ALTER TABLE `geradores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `historico_coletas`
--
ALTER TABLE `historico_coletas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
  ADD CONSTRAINT `coletas_ibfk_1` FOREIGN KEY (`id_gerador`) REFERENCES `geradores` (`id`);

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
