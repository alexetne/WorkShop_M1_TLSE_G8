create database mediflow;
use mediflow;

select prenom, num_secu_social, mail, num_mutuelle, date_creation, date_derniere_modif from patient;


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
  num_secu_social CHAR(100) NOT NULL UNIQUE,
  medecin_traitant VARCHAR(100),
  adresse_rue VARCHAR(150),
  adresse_ville VARCHAR(100),
  adresse_code_postal VARCHAR(10),
  mail VARCHAR(200),
  telephone_port VARCHAR(15),
  id_medecin INT,
  telephone_fixe VARCHAR(15),
  num_mutuelle BIGINT(30) DEFAULT NULL,
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
  dernier_utilisateur_modif VARCHAR(100),
  prix_unitaire DECIMAL(10, 2) NOT NULL DEFAULT 0
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
  numero_chambre VARCHAR(50) NOT NULL,
  date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  date_derniere_modif TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE planning (
  id INT PRIMARY KEY AUTO_INCREMENT,
  type ENUM('consultation', 'intervention', 'réunion') NOT NULL, -- Enum pour gérer les différents types de planning
  jour DATE NOT NULL,
  heure TIME NOT NULL,
  statut ENUM('prévu', 'en cours', 'terminé', 'annulé') DEFAULT 'prévu',
  id_salle INT,
  id_personnel INT,
  id_patient INT, -- Ajouter une référence au patient pour lier la consultation à un patient spécifique
  FOREIGN KEY (id_salle) REFERENCES salle_hopital(id),
  FOREIGN KEY (id_personnel) REFERENCES personnel(id),
  FOREIGN KEY (id_patient) REFERENCES patient(id)
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
  resultat VARCHAR(10) NOT NULL,
  FOREIGN KEY (id_personnel) REFERENCES personnel(id)
);

CREATE TABLE commandes (
  id INT PRIMARY KEY AUTO_INCREMENT,
  id_stock INT NOT NULL, -- Référence à l'article dans la table stock
  quantite_commande INT NOT NULL, 
  statut ENUM('en cours', 'livré', 'annulé') DEFAULT 'en cours', 
  date_commande TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  date_livraison TIMESTAMP NULL, 
  dernier_utilisateur_modif VARCHAR(100), 
  prix_unitaires DECIMAL(10, 2) NOT NULL DEFAULT 0,
  FOREIGN KEY (id_stock) REFERENCES stock(id)
);


CREATE TABLE hospitalisation (
  id INT PRIMARY KEY AUTO_INCREMENT,
  id_patient INT NOT NULL,
  id_salle INT NOT NULL,
  id_medecin INT NOT NULL,
  date_admission DATE NOT NULL,
  date_sortie DATE DEFAULT NULL,
  statut ENUM('hospitalisé', 'sorti') DEFAULT 'hospitalisé',
  FOREIGN KEY (id_patient) REFERENCES patient(id),
  FOREIGN KEY (id_salle) REFERENCES salle_hopital(id),
  FOREIGN KEY (id_medecin) REFERENCES personnel(id)
);

CREATE TABLE actes_medicaux (
  id INT PRIMARY KEY AUTO_INCREMENT,
  id_patient INT NOT NULL,
  id_medecin INT NOT NULL,
  description TEXT NOT NULL,
  date_acte TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_patient) REFERENCES patient(id),
  FOREIGN KEY (id_medecin) REFERENCES personnel(id)
);


ADD CONSTRAINT fk_patient_medecin FOREIGN KEY (id_medecin) REFERENCES personnel(id);

INSERT INTO password (id_personnel, password_hash, expiration_password, tentative_connexion_echouee, compte_verrouille)
VALUES (6, '$2y$10$vvEX5d2dewiUY.vQZqmMh.IuIwll2RPKKzteOlBMp3D7tbyndUVBu', NULL, 0, FALSE);

INSERT INTO personnel (nom, prenom, mail_pro, mail_perso, tel_pro, tel_perso, categorie, poste_occupe, role, statut)
VALUES ('Dupont', 'Jean', 'autre@hopital.com', 'autre@gmail.com', '0123456789', '0987654321', 'Soins', 'autre', 'autre', 'actif');

-- ALTER TABLE commandes RENAME COLUMN prix_unitaire TO prix_unitaires;
