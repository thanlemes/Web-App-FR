CREATE DATABASE fr;
USE fr;

-- Tabela Cidade
CREATE TABLE cidade (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100),
    codigo_municipio VARCHAR(20),
    uf CHAR(2)
);

INSERT INTO cidade (nome, codigo_municipio, uf) VALUES 
('Mogi das Cruzes', '6713', 'SP'),
('Rio de Janeiro', '6714', 'RJ');

-- Tabela Empresa
CREATE TABLE empresa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100),
    cnpj VARCHAR(20),
    logradouro VARCHAR(150),
    cidade_id INT,
    FOREIGN KEY (cidade_id) REFERENCES cidade(id)
);

INSERT INTO empresa (nome, cnpj, logradouro, cidade_id) VALUES 
('Prefeitura Municipal de Mogi das Cruzes', '12.345.678/0001-99', 'Rua A, 123', 1),
('Prefeitura Municipal de Mogi das Cruzes', '12.345.678/0001-99', 'Rua B, 123', 1),
('Prefeitura Municipal de Mogi das Cruzes', '12.345.678/0001-99', 'Rua C, 123', 1),
('Prefeitura Municipal de Suzano', '98.765.432/0001-88', 'Avenida B, 456', 1);

-- Tabela Secretaria
CREATE TABLE secretaria (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100),
    empresa_id INT,
    FOREIGN KEY (empresa_id) REFERENCES empresa(id)
);

INSERT INTO secretaria (nome, empresa_id) VALUES 
('SMMU', 1),
('SMMU', 1),
('SMMU', 1),
('SMMU', 1),
('SMMU', 1),
('SMMU', 1),
('SMMU', 1),
('SMMU', 1),
('SMMU', 1),
('SMMU', 1),
('SMMU', 1),
('SMMU', 1),
('GAB', 2);

-- Tabela Departamento
CREATE TABLE departamento (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100),
    secretaria_id INT,
    FOREIGN KEY (secretaria_id) REFERENCES secretaria(id)
);

INSERT INTO departamento (nome, secretaria_id) VALUES 
('DID', 1),
('DID', 2),
('DID', 3),
('DID', 4),
('DID', 5),
('DID', 6),
('DID', 7),
('DID', 8),
('DID', 9),
('DID', 10),
('DGA', 11);

-- Tabela Cargo
CREATE TABLE cargo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100)
);

INSERT INTO cargo (nome) VALUES 
('Analista TI'),
('Assistente');

-- Tabela Cargo_Departamento (Relacionamento N:N entre Cargo e Departamento)
CREATE TABLE cargo_departamento (
    id INT AUTO_INCREMENT PRIMARY KEY,
    departamento_id INT,
    cargo_id INT,
    FOREIGN KEY (departamento_id) REFERENCES departamento(id),
    FOREIGN KEY (cargo_id) REFERENCES cargo(id)
);

INSERT INTO cargo_departamento (departamento_id, cargo_id) VALUES 
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(9, 1),
(10, 1);

-- Tabela Funcionario
CREATE TABLE funcionario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rgf VARCHAR(20),
    nome VARCHAR(100),
    email VARCHAR(100),
    telefone VARCHAR(20),
    endereco VARCHAR(150),
    registro_cnh VARCHAR(20),
    categoria_cnh CHAR(2),
    validade_cnh DATE,
    imagem_cnh VARCHAR(255),
    data_admissao DATE,
    status BOOLEAN,
    senha VARCHAR(255),
    reset_token VARCHAR(255),
    cargo_departamento_id INT,
    FOREIGN KEY (cargo_departamento_id) REFERENCES cargo_departamento(id)
);

-- Inserindo registros na tabela Funcionario
INSERT INTO funcionario (rgf, nome, email, telefone, endereco, registro_cnh, categoria_cnh, validade_cnh, imagem_cnh, data_admissao, status, senha, reset_token, cargo_departamento_id) VALUES 
('111111', 'Nathan Lemes', 'thanrocha99@outlook.com', '11995703619', 'Rua X, 100', '012345678910', 'B', '2025-12-31', '/img/cnh_joao.jpg', '2023-01-10', 1, '111111', NULL, 1),
('222222', 'Kaike Mateus', 'kaike0310@gmail.com', '11995879200', 'Rua Y, 200', '012345678910', 'B', '2026-06-30', '/img/cnh_joao.jpg', '2023-02-15', 1, '222222', NULL, 2),
('333333', 'Renan Martins', 'renanrodriguesmartins2003@gmail.com', '11990279949', 'Rua X, 100', '012345678910', 'B', '2025-12-31', '/img/cnh_joao.jpg', '2023-01-10', 1, '333333', NULL, 3),
('444444', 'Joktan Junior', 'juniorb72@hotmail.com', '11934768307', 'Rua X, 100', '012345678910', 'B', '2025-12-31', '/img/cnh_joao.jpg', '2023-01-10', 1, '444444', NULL, 4),
('555555', 'Gustavo Leite', 'ghmleite@gmail.com', '11974946423', 'Rua X, 100', '012345678910', 'B', '2025-12-31', '/img/cnh_joao.jpg', '2023-01-10', 1, '555555', NULL, 5);
('999999', 'Guilherme Vaz', 'thespaceg@gmail.com', '11952777596', 'Rua X, 100', '012345678910', 'B', '2025-12-31', '/img/cnh_joao.jpg', '2023-01-10', 1, '999999', NULL, 6);
('123456', 'Israel Lemos', 'thanrocha99@gmail.com', '11912345678', 'Rua X, 100', '012345678910', 'B', '2025-12-31', '/img/cnh_joao.jpg', '2023-01-10', 1, '123456', NULL, 7);

-- teste
INSERT INTO funcionario (rgf, nome, email, telefone, endereco, registro_cnh, categoria_cnh, validade_cnh, imagem_cnh, data_admissao, status, senha, reset_token, cargo_departamento_id) VALUES ('777777', 'Nathan Lemes', 'thanrocha99@gmail.com', '11995703619', 'Rua X, 100', '012345678910', 'B', '2025-12-31', '/img/cnh_joao.jpg', '2023-01-10', 1, '111111', NULL, 1);

-- Tabela Veiculo
CREATE TABLE veiculo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    placa VARCHAR(10),
    tombamento VARCHAR(20),
    marca VARCHAR(50),
    modelo VARCHAR(50),
    cor VARCHAR(20),
    tipo VARCHAR(20),
    especie VARCHAR(20),
    km INT,
    disponibilidade BOOLEAN,
    departamento_id INT,
    rota_funcionario_id INT,
    FOREIGN KEY (departamento_id) REFERENCES departamento(id)
);

-- Inserindo registros na tabela Veiculo
INSERT INTO veiculo (placa, tombamento, marca, modelo, cor, tipo, especie, km, disponibilidade, departamento_id, rota_funcionario_id) VALUES 
('ABC1D23', 'TMB001', 'Ford', 'Fiesta', 'Branco', 'Sedan', 'Passageiro', 50000, 1, 1, NULL),
('XYZ4E56', 'TMB002', 'Toyota', 'Corolla', 'Prata', 'Sedan', 'Passageiro', 30000, 1, 1, NULL),
('LMN7P89', 'TMB003', 'Honda', 'Civic', 'Azul', 'Sedan', 'Passageiro', 45000, 1, 1, NULL),
('PQR0S12', 'TMB004', 'Chevrolet', 'Onix', 'Preto', 'Hatchback', 'Passageiro', 20000, 1, 2, NULL),
('STU3V45', 'TMB005', 'Volkswagen', 'Gol', 'Vermelho', 'Hatchback', 'Passageiro', 35000, 1, 2, NULL),
('VWX6Y78', 'TMB006', 'Fiat', 'Palio', 'Verde', 'Hatchback', 'Passageiro', 25000, 1, 2, NULL),
('ABC9X12', 'TMB007', 'Hyundai', 'HB20', 'Amarelo', 'Hatchback', 'Passageiro', 28000, 1, 3, NULL),
('DEF3G45', 'TMB008', 'Renault', 'Sandero', 'Cinza', 'Hatchback', 'Passageiro', 22000, 1, 3, NULL),
('GHI6K89', 'TMB009', 'Nissan', 'March', 'Branco', 'Hatchback', 'Passageiro', 18000, 1, 3, NULL),
('JKL4M23', 'TMB010', 'Peugeot', '208', 'Azul', 'Hatchback', 'Passageiro', 16000, 1, 4, NULL),
('MNO7P56', 'TMB011', 'Fiat', 'Uno', 'Vermelho', 'Hatchback', 'Passageiro', 32000, 1, 4, NULL),
('PQR5J01', 'TMB012', 'Ford', 'Ka', 'Preto', 'Hatchback', 'Passageiro', 27000, 1, 4, NULL),
('STU8V34', 'TMB013', 'Volkswagen', 'Up!', 'Prata', 'Hatchback', 'Passageiro', 21000, 1, 5, NULL),
('ABC1D23', 'TMB001', 'Ford', 'Fiesta', 'Branco', 'Sedan', 'Passageiro', 50000, 1, 5, NULL),
('VWX9Y67', 'TMB014', 'Chevrolet', 'Cruze', 'Azul', 'Sedan', 'Passageiro', 40000, 1, 5, NULL),
('ABC1D23', 'TMB001', 'Ford', 'Fiesta', 'Branco', 'Sedan', 'Passageiro', 50000, 1, 6, NULL),
('XYZ4E56', 'TMB002', 'Toyota', 'Corolla', 'Prata', 'Sedan', 'Passageiro', 30000, 1, 6, NULL),
('LMN7P89', 'TMB003', 'Honda', 'Civic', 'Azul', 'Sedan', 'Passageiro', 45000, 1, 6, NULL),
('PQR0S12', 'TMB004', 'Chevrolet', 'Onix', 'Preto', 'Hatchback', 'Passageiro', 20000, 1, 7, NULL),
('STU3V45', 'TMB005', 'Volkswagen', 'Gol', 'Vermelho', 'Hatchback', 'Passageiro', 35000, 1, 7, NULL),
('VWX6Y78', 'TMB006', 'Fiat', 'Palio', 'Verde', 'Hatchback', 'Passageiro', 25000, 1, 7, NULL),
('ABC9X12', 'TMB007', 'Hyundai', 'HB20', 'Amarelo', 'Hatchback', 'Passageiro', 28000, 1, 8, NULL),
('DEF3G45', 'TMB008', 'Renault', 'Sandero', 'Cinza', 'Hatchback', 'Passageiro', 22000, 1, 8, NULL),
('GHI6K89', 'TMB009', 'Nissan', 'March', 'Branco', 'Hatchback', 'Passageiro', 18000, 1, 8, NULL),
('JKL4M23', 'TMB010', 'Peugeot', '208', 'Azul', 'Hatchback', 'Passageiro', 16000, 1, 9, NULL),
('MNO7P56', 'TMB011', 'Fiat', 'Uno', 'Vermelho', 'Hatchback', 'Passageiro', 32000, 1, 9, NULL),
('PQR5J01', 'TMB012', 'Ford', 'Ka', 'Preto', 'Hatchback', 'Passageiro', 27000, 1, 9, NULL),
('STU8V34', 'TMB013', 'Volkswagen', 'Up!', 'Prata', 'Hatchback', 'Passageiro', 21000, 1, 10, NULL),
('ABC1D23', 'TMB001', 'Ford', 'Fiesta', 'Branco', 'Sedan', 'Passageiro', 50000, 1, 10, NULL),
('VWX9Y67', 'TMB014', 'Chevrolet', 'Cruze', 'Azul', 'Sedan', 'Passageiro', 40000, 1, 10, NULL);

-- Tabela Rota
CREATE TABLE rota (
    id INT AUTO_INCREMENT PRIMARY KEY,
    veiculo_id INT,
    funcionario_id INT,
    data_inicial DATE,
    hora_inicial TIME,
    km_inicial INT,
    km_final INT,
    data_final DATE,
    hora_final TIME,
    local_partida VARCHAR(100),
    destino VARCHAR(100),
    protocolo INT UNIQUE,
    FOREIGN KEY (veiculo_id) REFERENCES veiculo(id),
    FOREIGN KEY (funcionario_id) REFERENCES funcionario(id)
);

CREATE TABLE abastecimento (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rota_id INT,
    litros NUMERIC(4,1),
    km_atual INT,
    comprovante_abastecimento VARCHAR(200),
    data_abastecimento TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (rota_id) REFERENCES rota(id)
);