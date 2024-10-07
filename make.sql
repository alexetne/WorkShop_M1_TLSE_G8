create database mediflow;
use mediflow;

CREATE TABLE patient (
  id INT PRIMARY KEY AUTO_INCREMENT,
  nom VARCHAR(100) NOT NULL,
  prenom VARCHAR(100) NOT NULL,
  date_naissance DATE NOT NULL,
  profession ENUM(
    'Agriculteurs exploitants',
    'Artisans, commerçants et chefs d\'entreprise',
    'Cadres et professions intellectuelles supérieures',
    'Professions intermédiaires',
    'Employés',
    'autres'
  ),
  num_secu_social CHAR(15) NOT NULL UNIQUE,
  medecin_traitant VARCHAR(100),
  adresse_rue VARCHAR(150),
  adresse_ville VARCHAR(100),
  adresse_code_postal VARCHAR(10),
  mail VARCHAR(200),
  telephone_port VARCHAR(15),
  telephone_fixe VARCHAR(15),
  num_mutuelle BIGINT(30),
  date_creation DATETIME DEFAULT NOW(),
  date_derniere_modif DATETIME DEFAULT NOW() ON UPDATE NOW()
);

CREATE TABLE personnel (
  id INT PRIMARY KEY AUTO_INCREMENT,
  nom VARCHAR(100) NOT NULL,
  prenom VARCHAR(100) NOT NULL,
  mail_pro VARCHAR(200) NOT NULL,
  mail_perso VARCHAR(200),
  tel_pro VARCHAR(20),
  tel_perso VARCHAR(20),
  categorie VARCHAR(100),
  poste_occupe VARCHAR(100),
  role ENUM('admin', 'medecin', 'infirmier', 'aide-soignant', 'secretaire', 'autre') NOT NULL,
  statut ENUM('actif', 'suspendu', 'désactivé') DEFAULT 'actif',
  date_entree TIMESTAMP DEFAULT NOW(),
  derniere_connexion TIMESTAMP DEFAULT NULL,
  date_modif_password TIMESTAMP DEFAULT NULL,
  date_derniere_modif TIMESTAMP DEFAULT NOW() ON UPDATE NOW()
);

CREATE TABLE medecins (
  id INT PRIMARY KEY AUTO_INCREMENT,
  nom VARCHAR(255) NOT NULL,
  prenom VARCHAR(255) NOT NULL,
  mail_pro VARCHAR(255) NOT NULL,
  mail_perso VARCHAR(255),
  tel_pro VARCHAR(20),
  tel_perso VARCHAR(20),
  num_diplome VARCHAR(100) NOT NULL UNIQUE,
  service VARCHAR(255),
  specialite VARCHAR(255),
  statut ENUM('libre', 'occupé', 'intervention') DEFAULT 'libre',
  derniere_connexion TIMESTAMP DEFAULT NULL,
  date_modif_password TIMESTAMP DEFAULT NULL,
  date_creation TIMESTAMP DEFAULT NOW(),
  date_derniere_modif TIMESTAMP DEFAULT NOW() ON UPDATE NOW()
);

CREATE TABLE stock (
  id INT PRIMARY KEY AUTO_INCREMENT,
  quantite INT NOT NULL,
  nom VARCHAR(255) NOT NULL,
  type VARCHAR(100),
  service VARCHAR(100),
  informations TEXT,
  notes TEXT,
  code_barre VARCHAR(50),
  date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  date_derniere_modif TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  dernier_utilisateur_modif VARCHAR(100)
);

CREATE TABLE compta (
  id INT PRIMARY KEY AUTO_INCREMENT,
  type_flux ENUM('entrant', 'sortant') NOT NULL,
  montant DECIMAL(10, 2) NOT NULL,
  date_transaction TIMESTAMP DEFAULT NOW()
);

CREATE TABLE salle_hopital (
  id INT PRIMARY KEY AUTO_INCREMENT,
  nom VARCHAR(255) NOT NULL,
  service VARCHAR(100),
  lieu VARCHAR(255),
  statut ENUM('disponible', 'occupée', 'en maintenance') DEFAULT 'disponible',
  type_salle VARCHAR(100),
  capacite_max INT,
  date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  date_derniere_modif TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE planning (
  id INT PRIMARY KEY AUTO_INCREMENT,
  type VARCHAR(10) NOT NULL,
  jour DATE NOT NULL,
  id_salle INT,
  id_personnel INT,
  FOREIGN KEY (id_salle) REFERENCES salle_hopital(id),
  FOREIGN KEY (id_personnel) REFERENCES personnel(id)
);

CREATE TABLE password (
  id INT PRIMARY KEY AUTO_INCREMENT,
  id_personnel INT NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  expiration_password TIMESTAMP DEFAULT NULL,
  tentative_connexion_echouee INT DEFAULT 0,
  compte_verrouille BOOL DEFAULT FALSE,
  FOREIGN KEY (id_personnel) REFERENCES personnel(id)
);

CREATE TABLE logs_connexion (
  id INT PRIMARY KEY AUTO_INCREMENT,
  id_personnel INT NOT NULL,
  ip_adresse VARCHAR(45) NOT NULL,
  date_connexion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  resultat ENUM('succès', 'échec') NOT NULL,
  FOREIGN KEY (id_personnel) REFERENCES personnel(id)
);