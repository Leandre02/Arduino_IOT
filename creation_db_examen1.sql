-- Creation de la base de donnees
-- By Léandre Kanmegne - examen1
CREATE DATABASE IF NOT EXISTS examen1;
   
-- Utilisation de la base de donnees
USE examen1;

-- Creation de la table boutons
CREATE TABLE IF NOT EXISTS boutons (
    id                 INT AUTO_INCREMENT PRIMARY KEY,
    etat_bouton_rouge  TINYINT(1)   NOT NULL,
    etat_bouton_jaune  TINYINT(1)   NOT NULL,
    adresse_ip         VARCHAR(45)  NOT NULL,
    date_heure         DATETIME     NOT NULL DEFAULT NOW()
);

-- Creation de l'usager examen1
CREATE USER IF NOT EXISTS 'examen1'@'localhost' IDENTIFIED BY 'examen1';

-- Acces a la base de donnees examen1 seulement
GRANT ALL PRIVILEGES ON examen1.* TO 'examen1'@'localhost';

FLUSH PRIVILEGES;