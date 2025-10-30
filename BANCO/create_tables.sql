-- Criação do banco de dados
CREATE DATABASE IF NOT EXISTS ecoleta;
USE ecoleta;

-- Tabela de endereços
CREATE TABLE IF NOT EXISTS enderecos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rua VARCHAR(100) NOT NULL,
    numero VARCHAR(10) NOT NULL,
    complemento VARCHAR(100),
    bairro VARCHAR(50) NOT NULL,
    cidade VARCHAR(50) NOT NULL,
    estado CHAR(2) NOT NULL,
    cep VARCHAR(9) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabela de geradores
CREATE TABLE IF NOT EXISTS geradores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    nome_completo VARCHAR(100) NOT NULL,
    cpf VARCHAR(14) NOT NULL UNIQUE,
    telefone VARCHAR(15) NOT NULL,
    data_nasc DATE NOT NULL,
    genero ENUM('M', 'F', 'O', '') DEFAULT '',
    foto_perfil VARCHAR(255),
    id_endereco INT,
    status ENUM('ativo', 'inativo', 'pendente') DEFAULT 'pendente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_endereco) REFERENCES enderecos(id)
);

-- Tabela de configurações do usuário
CREATE TABLE IF NOT EXISTS configuracoes_usuario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_gerador INT,
    notificacoes_email BOOLEAN DEFAULT TRUE,
    notificacoes_push BOOLEAN DEFAULT TRUE,
    tema_escuro BOOLEAN DEFAULT FALSE,
    privacidade_perfil ENUM('publico', 'privado') DEFAULT 'publico',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_gerador) REFERENCES geradores(id)
);

-- Tabela de coletas
CREATE TABLE IF NOT EXISTS coletas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_gerador INT,
    data_solicitacao DATETIME NOT NULL,
    data_coleta DATETIME,
    status ENUM('pendente', 'agendada', 'concluida', 'cancelada') DEFAULT 'pendente',
    quantidade_oleo DECIMAL(10,2),
    observacoes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_gerador) REFERENCES geradores(id)
);

-- Tabela de avaliações
CREATE TABLE IF NOT EXISTS avaliacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_coleta INT,
    id_gerador INT,
    nota INT NOT NULL CHECK (nota >= 1 AND nota <= 5),
    comentario TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_coleta) REFERENCES coletas(id),
    FOREIGN KEY (id_gerador) REFERENCES geradores(id)
);

-- Índices para otimização
CREATE INDEX idx_geradores_email ON geradores(email);
CREATE INDEX idx_geradores_cpf ON geradores(cpf);
CREATE INDEX idx_coletas_data ON coletas(data_coleta);
CREATE INDEX idx_coletas_status ON coletas(status);

-- Inserir configurações padrão do sistema
INSERT INTO configuracoes_usuario (id_gerador, notificacoes_email, notificacoes_push, tema_escuro, privacidade_perfil)
SELECT id, TRUE, TRUE, FALSE, 'publico'
FROM geradores
WHERE id NOT IN (SELECT id_gerador FROM configuracoes_usuario);