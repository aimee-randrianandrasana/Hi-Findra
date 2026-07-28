-- Mi-Findra — Schema de la base de donnees (MySQL / MariaDB)

CREATE DATABASE IF NOT EXISTS mi_findra
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE mi_findra;

-- UTILISATEUR — Comptes de connexion
CREATE TABLE utilisateur (
    id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom               VARCHAR(100)  NOT NULL,
    prenom            VARCHAR(100)  NOT NULL,
    email             VARCHAR(150)  NOT NULL,
    mot_de_passe      VARCHAR(255)  NOT NULL,
    photo             VARCHAR(255)  DEFAULT NULL,
    role              ENUM('developpeur', 'administrateur') NOT NULL DEFAULT 'administrateur',
    statut            ENUM('actif', 'inactif') NOT NULL DEFAULT 'actif',
    date_creation     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    date_modification DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY uq_email (email),
    KEY idx_role (role),
    KEY idx_statut (statut)
);

-- TENTATIVE_CONNEXION — Protection contre force brute
CREATE TABLE tentative_connexion (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email        VARCHAR(150) NOT NULL,
    adresse_ip   VARCHAR(45)  NOT NULL,
    reussie      TINYINT(1)   NOT NULL DEFAULT 0,
    tentee_le    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,

    KEY idx_email (email),
    KEY idx_date (tentee_le)
);

-- REINITIALISATION_MDP — Jetons de reinitialisation
CREATE TABLE reinitialisation_mdp (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id  INT UNSIGNED NOT NULL,
    jeton           VARCHAR(255) NOT NULL,
    expire_le       DATETIME     NOT NULL,
    utilise         TINYINT(1)   NOT NULL DEFAULT 0,
    cree_le         DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    KEY idx_jeton (jeton)
);

-- JETON_CONNEXION — Se souvenir de moi
CREATE TABLE jeton_connexion (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id  INT UNSIGNED NOT NULL,
    selecteur       VARCHAR(24)  NOT NULL,
    validateur_hash VARCHAR(255) NOT NULL,
    expire_le       DATETIME     NOT NULL,
    cree_le         DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY uq_selecteur (selecteur),
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(id)
        ON DELETE CASCADE ON UPDATE CASCADE
);

-- LIEU — Sites d'affectation
CREATE TABLE lieu (
    id_lieu           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    designation       VARCHAR(150) NOT NULL,
    province          VARCHAR(100) NOT NULL,
    date_creation     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    date_modification DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY uq_designation_province (designation, province),
    KEY idx_province (province)
);

-- EMPLOYE — Employes rattaches a un lieu
CREATE TABLE employe (
    num_emp           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    civilite          ENUM('Mr', 'Mlle', 'Mme') NOT NULL,
    nom               VARCHAR(100) NOT NULL,
    prenom            VARCHAR(100) NOT NULL,
    mail              VARCHAR(150) NOT NULL,
    poste             VARCHAR(150) NOT NULL,
    photo             VARCHAR(255) DEFAULT NULL,
    id_lieu           INT UNSIGNED NOT NULL,
    date_creation     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    date_modification DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY uq_mail (mail),
    FOREIGN KEY (id_lieu) REFERENCES lieu(id_lieu)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    KEY idx_nom (nom),
    KEY idx_prenom (prenom),
    KEY idx_lieu (id_lieu)
);

-- AFFECTER — Historique des affectations
CREATE TABLE affecter (
    num_affect         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    numero_arrete      VARCHAR(50)  NOT NULL,
    num_emp            INT UNSIGNED NOT NULL,
    ancien_lieu_id     INT UNSIGNED DEFAULT NULL,
    nouveau_lieu_id    INT UNSIGNED NOT NULL,
    date_affect        DATE NOT NULL,
    date_prise_service DATE NOT NULL,
    raison             VARCHAR(255) DEFAULT NULL,
    notifie_par_mail   TINYINT(1)   NOT NULL DEFAULT 0,
    supprime           TINYINT(1)   NOT NULL DEFAULT 0,
    cree_le            DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY uq_numero_arrete (numero_arrete, num_emp),
    FOREIGN KEY (num_emp) REFERENCES employe(num_emp)
        ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (ancien_lieu_id) REFERENCES lieu(id_lieu)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (nouveau_lieu_id) REFERENCES lieu(id_lieu)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CHECK (date_prise_service >= date_affect),
    KEY idx_emp (num_emp),
    KEY idx_dates (date_affect, date_prise_service)
);

-- TRIGGER — Met a jour le lieu de l'employe apres affectation
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

-- COMPTE ADMIN PAR DEFAUT
-- Email : joker@gmail.com  /  Mot de passe : joker@test
INSERT IGNORE INTO utilisateur (nom, prenom, email, mot_de_passe, role, statut)
VALUES ('Joker', 'Admin', 'joker@gmail.com',
        '$2y$12$DIAYuwQe58YRtifFkgTkOujsIsSBdxvIwN0K6b9Al3/ktM54aD9SK',
        'administrateur', 'actif');
