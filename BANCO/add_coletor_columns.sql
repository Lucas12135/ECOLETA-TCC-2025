ALTER TABLE coletores
ADD COLUMN status ENUM('ativo', 'inativo') DEFAULT 'ativo',
ADD COLUMN avaliacao DECIMAL(3,1) DEFAULT 0.0,
ADD COLUMN total_coletas INT DEFAULT 0;