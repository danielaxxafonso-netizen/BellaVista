CREATE DATABASE IF NOT EXISTS hotel_db CHARACTER SET utf8mb4;
USE hotel_db;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) UNIQUE,
    senha VARCHAR(50)
);

INSERT INTO usuarios (usuario, senha) VALUES ('admin', 'admin123');

CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50),
    descricao VARCHAR(200),
    preco DECIMAL(10,2),
    icone VARCHAR(50) DEFAULT 'bi-house'
);

INSERT INTO categorias (nome, descricao, preco, icone) VALUES 
('Standard', 'Quarto simples e confortável', 150.00, 'bi-house-door'),
('Luxo', 'Quarto com amenities premium', 300.00, 'bi-star'),
('Suíte', 'Suíte com vista panorâmica', 500.00, 'bi-gem');

CREATE TABLE quartos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero VARCHAR(10),
    categoria_id INT,
    andar VARCHAR(10),
    capacidade INT DEFAULT 2,
    status ENUM('livre','ocupado','manutencao') DEFAULT 'livre',
    foto VARCHAR(255) DEFAULT 'default.jpg',
    descricao TEXT,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id)
);

INSERT INTO quartos (numero, categoria_id, andar, capacidade, descricao, foto) VALUES 
('101', 1, '1', 2, 'Quarto standard com cama de casal', 'quarto1.jpg'),
('102', 1, '1', 2, 'Quarto standard com duas camas', 'quarto2.jpg'),
('201', 2, '2', 2, 'Quarto luxo com vista', 'quarto3.jpg'),
('202', 2, '2', 3, 'Quarto luxo familiar', 'quarto4.jpg'),
('301', 3, '3', 2, 'Suíte presidencial', 'quarto5.jpg');

CREATE TABLE hospedes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100),
    cpf VARCHAR(14),
    telefone VARCHAR(20),
    email VARCHAR(100),
    cidade VARCHAR(100),
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE servicos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100),
    descricao VARCHAR(200),
    preco DECIMAL(10,2),
    icone VARCHAR(50) DEFAULT 'bi-gift'
);

INSERT INTO servicos (nome, descricao, preco, icone) VALUES 
('Café da manhã', 'Buffet completo', 30.00, 'bi-cup-hot'),
('Lavanderia', 'Lavagem e passagem', 25.00, 'bi-basket'),
('Frigobar', 'Bebidas e snacks', 50.00, 'bi-snow'),
('Estacionamento', 'Vaga coberta', 20.00, 'bi-car-front'),
('Spa', 'Massagem relaxante', 120.00, 'bi-heart');

CREATE TABLE reservas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    hospede_id INT,
    quarto_id INT,
    data_entrada DATE,
    data_saida DATE,
    status ENUM('reservada','checkin','checkout','cancelada') DEFAULT 'reservada',
    valor_total DECIMAL(10,2),
    observacoes TEXT,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (hospede_id) REFERENCES hospedes(id),
    FOREIGN KEY (quarto_id) REFERENCES quartos(id)
);