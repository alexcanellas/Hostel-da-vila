SET NAMES utf8mb4;
SET time_zone = 'America/Sao_Paulo';

-- Usuários do sistema administrativo
CREATE TABLE IF NOT EXISTS usuarios (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome        VARCHAR(100)        NOT NULL,
    email       VARCHAR(150)        NOT NULL UNIQUE,
    senha       VARCHAR(255)        NOT NULL,
    perfil      ENUM('admin','staff') NOT NULL DEFAULT 'staff',
    ativo       TINYINT(1)          NOT NULL DEFAULT 1,
    criado_em   DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em DATETIME          NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tipos de acomodação (quarto privê, dormitório misto, etc.)
CREATE TABLE IF NOT EXISTS tipos_acomodacao (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome        VARCHAR(100)        NOT NULL,
    descricao   TEXT,
    capacidade  TINYINT UNSIGNED    NOT NULL DEFAULT 1,
    criado_em   DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Acomodações / quartos
CREATE TABLE IF NOT EXISTS acomodacoes (
    id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tipo_id             INT UNSIGNED,
    nome                VARCHAR(100)        NOT NULL,
    descricao           TEXT,
    capacidade          TINYINT UNSIGNED    NOT NULL DEFAULT 1,
    preco_base          DECIMAL(10,2)       NOT NULL,
    moeda               CHAR(3)             NOT NULL DEFAULT 'BRL',
    fotos               JSON,
    comodidades         JSON,
    cloudbeds_room_id   VARCHAR(100),
    ativo               TINYINT(1)          NOT NULL DEFAULT 1,
    criado_em           DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em       DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (tipo_id) REFERENCES tipos_acomodacao(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Clientes / hóspedes
CREATE TABLE IF NOT EXISTS clientes (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome            VARCHAR(150)        NOT NULL,
    email           VARCHAR(150)        NOT NULL,
    telefone        VARCHAR(30),
    documento       VARCHAR(30),
    pais            CHAR(2)             DEFAULT 'BR',
    kommo_lead_id   VARCHAR(100),
    criado_em       DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em   DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_kommo (kommo_lead_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Descontos / cupons
CREATE TABLE IF NOT EXISTS descontos (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    codigo          VARCHAR(50)         NOT NULL UNIQUE,
    tipo            ENUM('percentual','fixo') NOT NULL DEFAULT 'percentual',
    valor           DECIMAL(10,2)       NOT NULL,
    usos_maximos    INT UNSIGNED,
    usos_atuais     INT UNSIGNED        NOT NULL DEFAULT 0,
    valido_de       DATE,
    valido_ate      DATE,
    ativo           TINYINT(1)          NOT NULL DEFAULT 1,
    criado_em       DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Reservas
CREATE TABLE IF NOT EXISTS reservas (
    id                      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    codigo                  VARCHAR(20)         NOT NULL UNIQUE,
    cliente_id              INT UNSIGNED        NOT NULL,
    acomodacao_id           INT UNSIGNED        NOT NULL,
    checkin                 DATE                NOT NULL,
    checkout                DATE                NOT NULL,
    adultos                 TINYINT UNSIGNED    NOT NULL DEFAULT 1,
    criancas                TINYINT UNSIGNED    NOT NULL DEFAULT 0,
    status                  ENUM('pendente','confirmada','cancelada','no_show','concluida') NOT NULL DEFAULT 'pendente',
    origem                  ENUM('site','cloudbeds','admin') NOT NULL DEFAULT 'site',
    preco_total             DECIMAL(10,2)       NOT NULL,
    desconto_id             INT UNSIGNED,
    desconto_valor          DECIMAL(10,2)       NOT NULL DEFAULT 0,
    preco_final             DECIMAL(10,2)       NOT NULL,
    observacoes             TEXT,
    cloudbeds_reservation_id VARCHAR(100),
    hsystem_reservation_id  VARCHAR(100),
    criado_em               DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em           DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id)    REFERENCES clientes(id),
    FOREIGN KEY (acomodacao_id) REFERENCES acomodacoes(id),
    FOREIGN KEY (desconto_id)   REFERENCES descontos(id) ON DELETE SET NULL,
    INDEX idx_checkin (checkin),
    INDEX idx_checkout (checkout),
    INDEX idx_status (status),
    INDEX idx_cloudbeds (cloudbeds_reservation_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pagamentos
CREATE TABLE IF NOT EXISTS pagamentos (
    id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    reserva_id          INT UNSIGNED        NOT NULL,
    valor               DECIMAL(10,2)       NOT NULL,
    status              ENUM('pendente','aprovado','recusado','cancelado','estornado') NOT NULL DEFAULT 'pendente',
    gateway             ENUM('getnet','pix','dinheiro','outro') NOT NULL DEFAULT 'getnet',
    getnet_link_id      VARCHAR(200),
    getnet_payment_id   VARCHAR(200),
    link_pagamento      TEXT,
    link_expira_em      DATETIME,
    pago_em             DATETIME,
    dados_gateway       JSON,
    criado_em           DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em       DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (reserva_id) REFERENCES reservas(id),
    INDEX idx_status (status),
    INDEX idx_getnet_payment (getnet_payment_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Log de webhooks recebidos
CREATE TABLE IF NOT EXISTS webhooks_log (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    origem      ENUM('cloudbeds','getnet','kommo') NOT NULL,
    evento      VARCHAR(100)        NOT NULL,
    payload     JSON,
    status      ENUM('processado','erro','ignorado') NOT NULL DEFAULT 'processado',
    erro_msg    TEXT,
    recebido_em DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed: usuário admin padrão (senha: admin123)
-- Senha: admin123
INSERT INTO usuarios (nome, email, senha, perfil) VALUES
('Administrador', 'admin@hosteldavila.com.br', '$2y$12$6c1FnFmifUYz.wYdFxN3leicW7vLOlwF2/guwTvPaf853Z/qdBrc.', 'admin');

-- Seed: tipos de acomodação
INSERT INTO tipos_acomodacao (nome, descricao, capacidade) VALUES
('Dormitório Misto', 'Dormitório compartilhado misto com beliches', 8),
('Dormitório Feminino', 'Dormitório exclusivo para mulheres', 6),
('Quarto Privê Duplo', 'Quarto privativo com cama de casal', 2),
('Quarto Privê Individual', 'Quarto privativo com cama de solteiro', 1);
