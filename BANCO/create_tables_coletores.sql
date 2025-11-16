-- Tabela de coletores
CREATE TABLE IF NOT EXISTS coletores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    nome_completo VARCHAR(100) NOT NULL,
    cpf_cnpj VARCHAR(14) NOT NULL UNIQUE,
    telefone VARCHAR(15) NOT NULL,
    data_nasc DATE NOT NULL,
    genero ENUM('M', 'F', 'O', '') DEFAULT '',
    foto_perfil VARCHAR(255),
    id_endereco INT,
    tipo_coletor ENUM('pessoa_fisica', 'pessoa_juridica') DEFAULT 'pessoa_fisica',
    meio_transporte ENUM('carro', 'moto', 'bicicleta', 'van', 'caminhao') NOT NULL,
    raio_atuacao INT DEFAULT 5, -- Raio de atuação em KM
    capacidade_coleta DECIMAL(10,2), -- Capacidade de coleta em litros
    status ENUM('ativo', 'inativo', 'pendente', 'suspenso') DEFAULT 'pendente',
    avaliacao_media DECIMAL(3,2) DEFAULT 0.00,
    total_avaliacoes INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_endereco) REFERENCES enderecos(id)
);

-- Tabela de disponibilidade dos coletores
CREATE TABLE IF NOT EXISTS disponibilidade_coletores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_coletor INT NOT NULL,
    dia_semana ENUM('domingo', 'segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado') NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fim TIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_coletor) REFERENCES coletores(id),
    UNIQUE KEY unique_disponibilidade (id_coletor, dia_semana)
);

-- Tabela de certificações e documentos dos coletores
CREATE TABLE IF NOT EXISTS documentos_coletor (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_coletor INT NOT NULL,
    tipo_documento ENUM('cnh', 'licenca_ambiental', 'alvara', 'documento_veiculo', 'outro') NOT NULL,
    numero_documento VARCHAR(50) NOT NULL,
    data_emissao DATE NOT NULL,
    data_validade DATE,
    arquivo_path VARCHAR(255),
    status ENUM('pendente', 'aprovado', 'rejeitado') DEFAULT 'pendente',
    observacoes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_coletor) REFERENCES coletores(id)
);

-- Tabela de histórico de coletas realizadas
CREATE TABLE IF NOT EXISTS historico_coletas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_coleta INT NOT NULL,
    id_coletor INT NOT NULL,
    id_gerador INT NOT NULL,
    data_inicio DATETIME NOT NULL,
    data_conclusao DATETIME,
    quantidade_coletada DECIMAL(10,2) NOT NULL,
    observacoes TEXT,
    status ENUM('em_andamento', 'concluida', 'cancelada') DEFAULT 'em_andamento',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_coleta) REFERENCES coletas(id),
    FOREIGN KEY (id_coletor) REFERENCES coletores(id),
    FOREIGN KEY (id_gerador) REFERENCES geradores(id)
);

-- Tabela de avaliações dos coletores
CREATE TABLE IF NOT EXISTS avaliacoes_coletores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_historico_coleta INT NOT NULL,
    id_gerador INT NOT NULL,
    id_coletor INT NOT NULL,
    nota INT NOT NULL CHECK (nota >= 1 AND nota <= 5),
    comentario TEXT,
    pontualidade INT CHECK (pontualidade >= 1 AND pontualidade <= 5),
    profissionalismo INT CHECK (profissionalismo >= 1 AND profissionalismo <= 5),
    qualidade_servico INT CHECK (qualidade_servico >= 1 AND qualidade_servico <= 5),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_historico_coleta) REFERENCES historico_coletas(id),
    FOREIGN KEY (id_gerador) REFERENCES geradores(id),
    FOREIGN KEY (id_coletor) REFERENCES coletores(id)
);

-- Tabela de ganhos dos coletores
CREATE TABLE IF NOT EXISTS ganhos_coletores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_coletor INT NOT NULL,
    id_historico_coleta INT NOT NULL,
    valor_ganho DECIMAL(10,2) NOT NULL,
    data_pagamento DATE,
    status_pagamento ENUM('pendente', 'processando', 'pago', 'cancelado') DEFAULT 'pendente',
    metodo_pagamento VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_coletor) REFERENCES coletores(id),
    FOREIGN KEY (id_historico_coleta) REFERENCES historico_coletas(id)
);

-- Índices para otimização
CREATE INDEX idx_coletores_email ON coletores(email);
CREATE INDEX idx_coletores_cpf_cnpj ON coletores(cpf_cnpj);
CREATE INDEX idx_coletores_status ON coletores(status);
CREATE INDEX idx_historico_coletas_datas ON historico_coletas(data_inicio, data_conclusao);
CREATE INDEX idx_disponibilidade_dia ON disponibilidade_coletores(dia_semana);
CREATE INDEX idx_avaliacoes_coletor ON avaliacoes_coletores(id_coletor);

-- Trigger para atualizar a média de avaliações do coletor
DELIMITER //
CREATE TRIGGER atualiza_avaliacao_coletor AFTER INSERT ON avaliacoes_coletores
FOR EACH ROW
BEGIN
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
END//
DELIMITER ;