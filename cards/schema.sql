-- ─── Criar banco ──────────────────────────────────────────────
CREATE DATABASE IF NOT EXISTS pokecatalog CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE pokecatalog;

-- ─── Usuários / Perfis ────────────────────────────────────────
CREATE TABLE IF NOT EXISTS usuarios (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nome        VARCHAR(100)  NOT NULL,
    apelido     VARCHAR(60)   DEFAULT NULL,
    email       VARCHAR(150)  UNIQUE NOT NULL,
    bio         TEXT          DEFAULT NULL,
    avatar      VARCHAR(255)  DEFAULT NULL,   -- caminho: uploads/xxx.png
    cargo       VARCHAR(80)   DEFAULT NULL,   -- ex: "Treinador Pokémon"
    criado_em   DATETIME      DEFAULT CURRENT_TIMESTAMP
);

-- ─── Cards ────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS cards (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id  INT           NOT NULL,
    codigo      VARCHAR(10)   NOT NULL,        -- ex: 001
    nome        VARCHAR(100)  NOT NULL,
    tipo        VARCHAR(40)   NOT NULL,        -- fogo, agua, planta, eletrico, custom
    cor_primaria   VARCHAR(7) DEFAULT '#ff4d00', -- hex
    cor_secundaria VARCHAR(7) DEFAULT '#ff8c42', -- hex
    hp          INT           DEFAULT 50,
    categoria   VARCHAR(60)   DEFAULT NULL,
    altura      VARCHAR(20)   DEFAULT NULL,
    peso        VARCHAR(20)   DEFAULT NULL,
    desc_curta  TEXT          DEFAULT NULL,
    desc_longa  TEXT          DEFAULT NULL,
    imagem      VARCHAR(255)  DEFAULT NULL,    -- caminho: uploads/xxx.png
    habilidades JSON          DEFAULT NULL,    -- ["Blaze", "Solar Power"]
    fraquezas   JSON          DEFAULT NULL,
    resistencias JSON         DEFAULT NULL,
    criado_em   DATETIME      DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- ─── Dados de exemplo ─────────────────────────────────────────
INSERT INTO usuarios (nome, apelido, email, bio, cargo) VALUES
('Ash Ketchum', 'Ash', 'ash@pokemon.com', 'Treinador em busca de se tornar o melhor do mundo.', 'Treinador Pokémon'),
('Misty Waterflower', 'Misty', 'misty@cerulean.com', 'Especialista em Pokémon do tipo Água da Academia Cerulean.', 'Líder de Ginásio');

INSERT INTO cards (usuario_id, codigo, nome, tipo, cor_primaria, cor_secundaria, hp, categoria, altura, peso, desc_curta, desc_longa, imagem, habilidades, fraquezas, resistencias) VALUES
(1, '001', 'Charmander', 'fogo', '#ff4d00', '#ff8c42', 52, 'Lagarto', '0.6m', '8.5kg',
 'Nasce com uma chama na ponta da cauda. Quando está feliz, a chama crepita suavemente.',
 'Charmander é um Pokémon do tipo Fogo introduzido na Geração I. Desde o nascimento, uma chama arde na ponta de sua cauda.',
 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/4.png',
 '["Blaze","Solar Power"]', '["Água","Pedra","Terra"]', '["Fogo","Planta","Gelo","Aço"]'),
(1, '002', 'Pikachu', 'eletrico', '#ffd600', '#ffe57f', 35, 'Camundongo', '0.4m', '6.0kg',
 'Guarda eletricidade nas bochechas e libera raios com até 100.000 volts.',
 'Pikachu é o Pokémon mais reconhecível do mundo. As bolsas vermelhas em suas bochechas são órgãos elétricos sofisticados.',
 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/25.png',
 '["Static","Lightning Rod"]', '["Terra"]', '["Elétrico","Voador","Aço"]'),
(2, '003', 'Squirtle', 'agua', '#00b4ff', '#6ee0ff', 44, 'Tartaruga', '0.5m', '9.0kg',
 'A concha protege-o de ataques. Esguicha água com precisão devastadora.',
 'Squirtle é um Pokémon do tipo Água cuja concha funciona como um reservatório de pressão hídrica.',
 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/7.png',
 '["Torrent","Rain Dish"]', '["Elétrico","Planta"]', '["Fogo","Água","Gelo","Aço"]');
