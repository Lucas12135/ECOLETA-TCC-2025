-- Adicionar coluna id_coletor à tabela coletas (se não existir)
ALTER TABLE `coletas` ADD COLUMN `id_coletor` INT DEFAULT NULL AFTER `id_gerador`;
ALTER TABLE `coletas` ADD CONSTRAINT `fk_coletas_coletor` FOREIGN KEY (`id_coletor`) REFERENCES `coletores`(`id`) ON DELETE SET NULL;

-- Adicionar colunas de endereço à tabela coletas (se não existirem)
ALTER TABLE `coletas` ADD COLUMN `cep` VARCHAR(9) DEFAULT NULL AFTER `periodo`;
ALTER TABLE `coletas` ADD COLUMN `rua` VARCHAR(255) DEFAULT NULL AFTER `cep`;
ALTER TABLE `coletas` ADD COLUMN `numero` VARCHAR(10) DEFAULT NULL AFTER `rua`;
ALTER TABLE `coletas` ADD COLUMN `complemento` VARCHAR(255) DEFAULT NULL AFTER `numero`;
ALTER TABLE `coletas` ADD COLUMN `bairro` VARCHAR(100) DEFAULT NULL AFTER `complemento`;
ALTER TABLE `coletas` ADD COLUMN `cidade` VARCHAR(100) DEFAULT NULL AFTER `bairro`;
ALTER TABLE `coletas` ADD COLUMN `estado` VARCHAR(2) DEFAULT NULL AFTER `cidade`;
ALTER TABLE `coletas` ADD COLUMN `latitude` DECIMAL(10, 8) DEFAULT NULL AFTER `estado`;
ALTER TABLE `coletas` ADD COLUMN `longitude` DECIMAL(11, 8) DEFAULT NULL AFTER `latitude`;

-- Adicionar coluna tipo_coleta à tabela coletas
ALTER TABLE `coletas` ADD COLUMN `tipo_coleta` ENUM('automatico', 'especifico') DEFAULT 'automatico' AFTER `status`;

-- Adicionar índices para melhor desempenho
ALTER TABLE `coletas` ADD INDEX `idx_id_coletor` (`id_coletor`);
ALTER TABLE `coletas` ADD INDEX `idx_tipo_coleta` (`tipo_coleta`);
