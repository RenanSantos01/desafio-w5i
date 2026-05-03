CREATE DATABASE IF NOT EXISTS controle_atendimentos
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE controle_atendimentos;

CREATE TABLE IF NOT EXISTS setores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS prioridades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL UNIQUE,
    tempo_estimado INT NOT NULL 
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS chamados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setor_id INT NOT NULL,
    prioridade_id INT NOT NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'Aberto',
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP, 
    data_inicio DATETIME NULL,
    data_fim DATETIME NULL,
    solucao TEXT NULL,
    

    CONSTRAINT fk_chamado_setor 
        FOREIGN KEY (setor_id) REFERENCES setores(id) ON DELETE RESTRICT,
        
    CONSTRAINT fk_chamado_prioridade 
        FOREIGN KEY (prioridade_id) REFERENCES prioridades(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO setores (nome) VALUES 
('Suporte TI'), 
('Recursos Humanos'), 
('Financeiro'), 
('Manutenção Predial');

-- Inserindo Prioridades e seus tempos estimados (em horas)
INSERT INTO prioridades (nome, tempo_estimado) VALUES 
('Baixa', 48), 
('Média', 24), 
('Alta', 4),
('Urgente', 1);