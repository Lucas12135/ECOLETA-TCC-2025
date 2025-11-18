-- Tabela de Solicitações de Coleta
CREATE TABLE IF NOT EXISTS solicitacoes_coleta (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_gerador INT NOT NULL,
    id_coletor INT,
    volume DECIMAL(10, 2) NOT NULL,
    tipo_coleta ENUM('automatico', 'especifico') NOT NULL,
    cep VARCHAR(9),
    rua VARCHAR(255),
    numero VARCHAR(10),
    complemento VARCHAR(255),
    bairro VARCHAR(100),
    cidade VARCHAR(100),
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    data_solicitacao DATETIME NOT NULL,
    data_coleta DATE NOT NULL,
    periodo ENUM('manha', 'tarde') NOT NULL,
    observacoes TEXT,
    status ENUM('pendente', 'aceita', 'em_andamento', 'concluida', 'cancelada') DEFAULT 'pendente',
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (id_gerador) REFERENCES geradores(id) ON DELETE CASCADE,
    FOREIGN KEY (id_coletor) REFERENCES coletores(id) ON DELETE SET NULL,
    
    INDEX idx_id_gerador (id_gerador),
    INDEX idx_id_coletor (id_coletor),
    INDEX idx_status (status),
    INDEX idx_data_coleta (data_coleta)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
