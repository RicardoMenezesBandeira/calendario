-- Tabela principal de usuários
CREATE TABLE usuario (
    ID_Usuario INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    Tipo_Usuario ENUM('colaborador', 'lider', 'gerente') NOT NULL
);

-- Tabela de gerenteistradores (relaciona com usuario)
CREATE TABLE gerente (
    ID_Gerente INT AUTO_INCREMENT PRIMARY KEY,
    fk_Usuario_ID_Usuario INT NOT NULL,
    FOREIGN KEY (fk_Usuario_ID_Usuario)
        REFERENCES usuario(ID_Usuario)
        ON DELETE CASCADE
);

-- Tabela de lider (relaciona com usuario)
CREATE TABLE lider (
    ID_Lider INT AUTO_INCREMENT PRIMARY KEY,
    fk_Usuario_ID_Usuario INT NOT NULL,
    FOREIGN KEY (fk_Usuario_ID_Usuario)
        REFERENCES usuario(ID_Usuario)
        ON DELETE CASCADE
);

-- Tabela de equipes
CREATE TABLE equipe (
    Numero INT AUTO_INCREMENT PRIMARY KEY,
    Nome_Equipe VARCHAR(100) NOT NULL
);

-- Tabela de colaboradores (relaciona com usuario e equipe)
CREATE TABLE colaboradores (
    ID_Colaborador INT AUTO_INCREMENT PRIMARY KEY,
    fk_Equipe_Numero INT NOT NULL,
    fk_Usuario_ID_Usuario INT NOT NULL,
    FOREIGN KEY (fk_Equipe_Numero)
        REFERENCES equipe(Numero)
        ON DELETE CASCADE,
    FOREIGN KEY (fk_Usuario_ID_Usuario)
        REFERENCES usuario(ID_Usuario)
        ON DELETE CASCADE
);

-- Observação: o conceito de "disciplina" foi mesclado em "equipe" neste modelo.
-- (A tabela `disciplina` e suas relações foram removidas. Valores de disciplina
--  foram movidos para registros de `equipe` mais abaixo no script.)

-- Relação: lider gerencia equipe
CREATE TABLE lidera (
    fk_Lider_ID_Lider INT NOT NULL,
    fk_Equipe_Numero INT NOT NULL,
    PRIMARY KEY (fk_Lider_ID_Lider, fk_Equipe_Numero),
    FOREIGN KEY (fk_Lider_ID_Lider)
        REFERENCES lider(ID_Lider)
        ON DELETE CASCADE,
    FOREIGN KEY (fk_Equipe_Numero)
        REFERENCES equipe(Numero)
        ON DELETE CASCADE
);

-- Relação líder-disciplina removida: agora líderes se relacionam diretamente com equipes via
-- a tabela `lidera` (fk_Lider_ID_Lider <-> fk_Equipe_Numero). Se quiser recuperar
-- relacionamento similar a leciona, use `lidera` ou crie uma nova tabela específica.

-- Relação disciplina-<->equipe removida, pois disciplinas foram convertidas para registros em `equipe`.

-- Tabela de marcações (eventos, aulas, etc.)
CREATE TABLE marcacao (
    ID_Marcacao INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(100) NOT NULL,
    fk_Equipe_Numero INT NOT NULL,
    Data DATE NOT NULL,
    Hora TIME NOT NULL,
    Descricao TEXT,
    fk_Lider_ID_Lider INT NOT NULL,
    FOREIGN KEY (fk_Equipe_Numero)
        REFERENCES equipe(Numero)
        ON DELETE CASCADE,
    FOREIGN KEY (fk_Lider_ID_Lider)
        REFERENCES lider(ID_Lider)
        ON DELETE CASCADE
);


-- ------------------------------
-- Inserir usuários
-- ------------------------------

-- Gerente
INSERT INTO usuario (nome, email, senha, Tipo_Usuario) VALUES
('Gerente', 'gerente@test.com', MD5('123456'), 'gerente');
