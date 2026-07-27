-- GESTION DES AFFECTATIONS DES EMPLOYES - Schema de la base (MariaDB)

CREATE DATABASE IF NOT EXISTS mi_findra
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE mi_findra;

-- TABLE : utilisateur - Comptes pour se connecter a l'application
CREATE TABLE utilisateur (
    id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom                 VARCHAR(100)  NOT NULL,
    prenom              VARCHAR(100)  NOT NULL,
    email               VARCHAR(150)  NOT NULL,
    mot_de_passe        VARCHAR(255)  NOT NULL,
    photo               VARCHAR(255)  DEFAULT NULL,
    role                ENUM('administrateur', 'gestionnaire') NOT NULL DEFAULT 'gestionnaire',
    statut              ENUM('actif', 'inactif') NOT NULL DEFAULT 'actif',
    date_creation       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    date_modification   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT uq_utilisateur_email UNIQUE (email),
    INDEX idx_utilisateur_role (role),
    INDEX idx_utilisateur_statut (statut)
) ENGINE=InnoDB;

-- TABLE : tentative_connexion - Protection brute force
CREATE TABLE tentative_connexion (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email           VARCHAR(150) NOT NULL,
    adresse_ip      VARCHAR(45)  NOT NULL,
    reussie         TINYINT(1)   NOT NULL DEFAULT 0,
    tentee_le       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_tentative_email (email),
    INDEX idx_tentative_date (tentee_le)
) ENGINE=InnoDB;

-- TABLE : reinitialisation_mdp - Jetons pour reinitialisation du mot de passe
CREATE TABLE reinitialisation_mdp (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id  INT UNSIGNED NOT NULL,
    jeton           VARCHAR(255) NOT NULL,
    expire_le       DATETIME     NOT NULL,
    utilise         TINYINT(1)   NOT NULL DEFAULT 0,
    cree_le         DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_reinit_utilisateur FOREIGN KEY (utilisateur_id)
        REFERENCES utilisateur(id) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_reinit_jeton (jeton)
) ENGINE=InnoDB;

-- TABLE : jeton_connexion - "Se souvenir de moi"
CREATE TABLE jeton_connexion (
    id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id    INT UNSIGNED NOT NULL,
    selecteur         VARCHAR(24)  NOT NULL COMMENT 'Partie publique du jeton (lookup)',
    validateur_hash   VARCHAR(255) NOT NULL COMMENT 'SHA-256 de la partie secrete',
    expire_le         DATETIME     NOT NULL,
    cree_le           DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT uq_jeton_selecteur UNIQUE (selecteur),
    CONSTRAINT fk_jeton_utilisateur FOREIGN KEY (utilisateur_id)
        REFERENCES utilisateur(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- TABLE : lieu - Sites d'affectation possibles pour les employes
CREATE TABLE lieu (
    id_lieu             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    designation         VARCHAR(150) NOT NULL,
    province            VARCHAR(100) NOT NULL,
    date_creation       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    date_modification   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT uq_lieu_designation_province UNIQUE (designation, province),
    INDEX idx_lieu_province (province)
) ENGINE=InnoDB;

-- TABLE : employe - Employes rattaches a leur lieu d'affectation actuel
CREATE TABLE employe (
    num_emp             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    civilite            ENUM('Mr', 'Mlle', 'Mme') NOT NULL,
    nom                 VARCHAR(100) NOT NULL,
    prenom              VARCHAR(100) NOT NULL,
    mail                VARCHAR(150) NOT NULL,
    poste               VARCHAR(150) NOT NULL,
    photo               VARCHAR(255) DEFAULT NULL,
    id_lieu             INT UNSIGNED NOT NULL COMMENT 'Lieu d affectation actuel',
    date_creation       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    date_modification   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT uq_employe_mail UNIQUE (mail),
    CONSTRAINT fk_employe_lieu FOREIGN KEY (id_lieu)
        REFERENCES lieu(id_lieu) ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX idx_employe_nom (nom),
    INDEX idx_employe_prenom (prenom),
    INDEX idx_employe_lieu (id_lieu)
) ENGINE=InnoDB;

-- TABLE : affecter - Historique des affectations
CREATE TABLE affecter (
    num_affect          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    numero_arrete       VARCHAR(50)  NOT NULL COMMENT 'Ex: 2341',
    num_emp             INT UNSIGNED NOT NULL,
    ancien_lieu_id      INT UNSIGNED DEFAULT NULL COMMENT 'NULL si premiere affectation',
    nouveau_lieu_id     INT UNSIGNED NOT NULL,
    date_affect         DATE NOT NULL COMMENT 'Date de l arrete',
    date_prise_service  DATE NOT NULL,
    notifie_par_mail    TINYINT(1)   NOT NULL DEFAULT 0,
    cree_le             DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT uq_affecter_numero_arrete UNIQUE (numero_arrete),
    CONSTRAINT fk_affecter_employe FOREIGN KEY (num_emp)
        REFERENCES employe(num_emp) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_affecter_ancien_lieu FOREIGN KEY (ancien_lieu_id)
        REFERENCES lieu(id_lieu) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_affecter_nouveau_lieu FOREIGN KEY (nouveau_lieu_id)
        REFERENCES lieu(id_lieu) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT chk_affecter_dates CHECK (date_prise_service >= date_affect),
    INDEX idx_affecter_emp (num_emp),
    INDEX idx_affecter_dates (date_affect, date_prise_service)
) ENGINE=InnoDB;

-- Trigger : met a jour le lieu courant de l'employe apres chaque affectation
DELIMITER $$

CREATE TRIGGER trg_affecter_after_insert
AFTER INSERT ON affecter
FOR EACH ROW
BEGIN
    UPDATE employe
    SET id_lieu = NEW.nouveau_lieu_id
    WHERE num_emp = NEW.num_emp;
END$$

DELIMITER ;

-- COMPTE ADMIN PAR DEFAUT (email: joker@gmail.com / mdp: joker@test)
INSERT IGNORE INTO utilisateur (nom, prenom, email, mot_de_passe, role, statut)
VALUES ('Joker', 'Admin', 'joker@gmail.com',
        '$2y$12$DIAYuwQe58YRtifFkgTkOujsIsSBdxvIwN0K6b9Al3/ktM54aD9SK',
        'administrateur', 'actif');
