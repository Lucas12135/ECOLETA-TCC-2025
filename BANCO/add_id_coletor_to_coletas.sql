-- Adicionar coluna id_coletor à tabela coletas
-- Esta coluna armazenará o ID do coletor que aceitou a solicitação

ALTER TABLE `coletas`
ADD COLUMN `id_coletor` int(11) DEFAULT NULL AFTER `id_gerador`,
ADD KEY `idx_coletas_coletor` (`id_coletor`),
ADD CONSTRAINT `coletas_ibfk_coletor` FOREIGN KEY (`id_coletor`) REFERENCES `coletores` (`id`);
