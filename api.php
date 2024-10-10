<?php
// Configuration de la base de données des hôpitaux
$host_hospital = 'localhost';
$db_hospital = 'hospital_db';
$user_hospital = 'root';
$password_hospital = 'password';

// Configuration de la base de données MediFlow
$host_mediflow = 'localhost';
$db_mediflow = 'mediflow_db';
$user_mediflow = 'root';
$password_mediflow = 'password';

try {
    // Connexion à la base de données des hôpitaux
    $pdo_hospital = new PDO("mysql:host=$host_hospital;dbname=$db_hospital", $user_hospital, $password_hospital);
    $pdo_hospital->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Connexion à la base de données MediFlow
    $pdo_mediflow = new PDO("mysql:host=$host_mediflow;dbname=$db_mediflow", $user_mediflow, $password_mediflow);
    $pdo_mediflow->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupération des données des patients depuis la base de données des hôpitaux
    $sql_hospital = "SELECT * FROM patients";
    $stmt_hospital = $pdo_hospital->query($sql_hospital);

    // Boucle pour insérer ou mettre à jour les patients dans la base MediFlow
    while ($patient = $stmt_hospital->fetch(PDO::FETCH_ASSOC)) {
        $sql_mediflow = "
            INSERT INTO mediflow_patients (id, nom, prenom, num_secu_social)
            VALUES (:id, :nom, :prenom, :num_secu_social)
            ON DUPLICATE KEY UPDATE nom = :nom, prenom = :prenom, num_secu_social = :num_secu_social";
        
        $stmt_mediflow = $pdo_mediflow->prepare($sql_mediflow);
        $stmt_mediflow->execute([
            ':id' => $patient['id'],
            ':nom' => $patient['nom'],
            ':prenom' => $patient['prenom'],
            ':num_secu_social' => $patient['num_secu_social']
        ]);
    }

    echo "Synchronisation réussie avec MediFlow.";
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
