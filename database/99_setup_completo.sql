-- =====================================================
-- PETAMIGO - Setup Completo do Banco de Dados
-- =====================================================
-- INSTRUÇÕES:
-- 1. Abra o phpMyAdmin (http://localhost/phpmyadmin)
-- 2. Clique em "SQL" no topo
-- 3. Cole TODO este conteúdo
-- 4. Clique em "Executar"
-- =====================================================

CREATE DATABASE IF NOT EXISTS petamigo 
    CHARACTER SET utf8mb4 
    COLLATE utf8mb4_general_ci;

USE petamigo;

-- =====================================================
-- TABELA: usuarios
-- =====================================================
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    telefone VARCHAR(20),
    endereco TEXT,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- TABELA: doadores
-- =====================================================
CREATE TABLE IF NOT EXISTS doadores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    cpf VARCHAR(14) UNIQUE,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- TABELA: animais
-- =====================================================
CREATE TABLE IF NOT EXISTS animais (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    especie VARCHAR(50) NOT NULL,
    raca VARCHAR(100),
    idade INT,
    porte VARCHAR(20),
    descricao TEXT,
    imagem VARCHAR(255),
    disponivel BOOLEAN DEFAULT TRUE,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    doador_id INT,
    sexo ENUM('M', 'F'),
    vacinado BOOLEAN DEFAULT FALSE,
    castrado BOOLEAN DEFAULT FALSE,
    observacoes TEXT,
    FOREIGN KEY (doador_id) REFERENCES doadores(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- TABELA: adocoes
-- =====================================================
CREATE TABLE IF NOT EXISTS adocoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    animal_id INT,
    data_adocao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pendente', 'aprovada', 'rejeitada') DEFAULT 'pendente',
    motivacao TEXT,
    experiencia VARCHAR(50),
    data_pedido TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (animal_id) REFERENCES animais(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- TABELA: avaliacoes
-- =====================================================
CREATE TABLE IF NOT EXISTS avaliacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    animal_id INT,
    nota INT,
    comentario TEXT,
    data_avaliacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (animal_id) REFERENCES animais(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- DADOS DE EXEMPLO — USUÁRIOS
-- =====================================================
-- E-mail: admin@petamigo.com | Senha: password
INSERT INTO usuarios (nome, email, senha, telefone, endereco) VALUES 
('Administrador', 'admin@petamigo.com', 
'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
'(11) 9999-9999', 'Rua dos Animais, 123');

-- =====================================================
-- DADOS DE EXEMPLO — ANIMAIS
-- (12 animais com imagens reais em assets/img/animais/)
-- =====================================================
INSERT INTO animais (nome, especie, raca, idade, porte, descricao, imagem, sexo, vacinado, castrado, disponivel) VALUES
-- Cachorros
('Tobias', 'Cachorro', 'Golden Retriever', 1, 'Médio', 
 'Filhote dócil e brincalhão. Adora crianças e já sabe alguns truques. Adora uma flor!', 
 'animais/golden_flor.png', 'M', 1, 1, 1),

('Lobo', 'Cachorro', 'Husky Siberiano', 3, 'Grande', 
 'Energético e muito companheiro. Precisa de espaço para correr e muito exercício.', 
 'animais/husky.png', 'M', 1, 1, 1),

('Bidu', 'Cachorro', 'Pug', 4, 'Pequeno', 
 'Tranquilo, adora dormir no colo e é excelente para apartamento. Sociável com todos.', 
 'animais/pug.png', 'M', 1, 1, 1),

('Mel', 'Cachorro', 'Pomerânia', 2, 'Pequeno', 
 'Fofa, cheia de personalidade e muito apegada ao dono. Ideal para ambientes internos.', 
 'animais/pomerania.png', 'F', 1, 0, 1),

('Pandora', 'Cachorro', 'Border Collie', 2, 'Médio', 
 'Super inteligente, obedece comandos básicos. Precisa de estímulo mental diário.', 
 'animais/border_collie.png', 'F', 1, 1, 1),

('Fred', 'Cachorro', 'Golden Retriever', 1, 'Médio', 
 'Brincalhão e cheio de energia. Perfeito para famílias com crianças.', 
 'animais/golden_filhote.png', 'M', 0, 0, 1),

('Charlie', 'Cachorro', 'Beagle', 2, 'Médio', 
 'Curioso e farejador nato. Adora brincar ao ar livre e é muito sociável.', 
 'animais/beagle.png', 'M', 1, 1, 1),

-- Gatos
('Bella', 'Gato', 'Persa', 3, 'Médio', 
 'Elegante e calma. Gosta de ambientes tranquilos e de ser escovada.', 
 'animais/persa.png', 'F', 1, 1, 1),

('Laranjinha', 'Gato', 'SRD', 1, 'Pequeno', 
 'Resgatado da rua, muito carinhoso. Adora brincar com bolinhas e dormir no sol.', 
 'animais/gato_laranja.png', 'M', 1, 0, 1),

-- Aves
('Kiko', 'Ave', 'Calopsita', 2, 'Pequeno', 
 'Muito manso, já é acostumado com humanos. Adora assobiar e imitar sons.', 
 'animais/calopsita.png', 'M', 0, 0, 1),

('Verde', 'Ave', 'Papagaio', 5, 'Médio', 
 'Fala algumas palavras e é muito apegado ao dono. Precisa de atenção diária.', 
 'animais/papagaio.png', 'M', 0, 0, 1),

-- Equino
('Trovão', 'Equino', 'Mula', 6, 'Grande', 
 'Animal de trabalho, manso e obediente. Indicado para quem tem espaço e experiência.', 
 'animais/mula.png', 'M', 0, 0, 1);

-- =====================================================
-- FIM
-- =====================================================
SELECT '✅ Banco petamigo criado com sucesso!' AS Mensagem;
SELECT COUNT(*) AS total_usuarios FROM usuarios;
SELECT COUNT(*) AS total_animais FROM animais;
SELECT COUNT(*) AS total_doadores FROM doadores;