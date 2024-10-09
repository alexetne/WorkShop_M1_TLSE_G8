<?php
// Inclure le header et démarrer la session
include '../header.php';
session_start();

// Vérifier si l'utilisateur est connecté et a le rôle approprié
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'medecin') {
    // Si l'utilisateur n'est pas connecté ou n'a pas le bon rôle, redirection vers la page de connexion
    header("Location: ../login");
    exit;
}

// Connexion à la base de données
include_once '../config/db.php';
$database = new Database();
$db = $database->getConnection();

// Vérifier si un patient a été sélectionné
if (!isset($_GET['id_patient'])) {
    echo "<p>Patient non sélectionné.</p>";
    include '../footer.php';
    exit;
}

// Récupérer l'ID du patient depuis l'URL
$id_patient = $_GET['id_patient'];

// Requête pour obtenir les informations du patient
$query_patient = "SELECT nom, prenom FROM patient WHERE id = :id_patient";
$stmt_patient = $db->prepare($query_patient);
$stmt_patient->bindParam(':id_patient', $id_patient);
$stmt_patient->execute();
$patient = $stmt_patient->fetch(PDO::FETCH_ASSOC);

// Si le patient n'existe pas
if (!$patient) {
    echo "<p>Le patient sélectionné n'existe pas.</p>";
    include '../footer.php';
    exit;
}

// Requête pour obtenir les prescriptions du patient
$query_prescriptions = "SELECT p.date_prescription, p.medicament, p.dosage, p.frequence, p.duree, p.notes
                        FROM prescriptions p
                        WHERE p.id_patient = :id_patient";
$stmt_prescriptions = $db->prepare($query_prescriptions);
$stmt_prescriptions->bindParam(':id_patient', $id_patient);
$stmt_prescriptions->execute();
$prescriptions = $stmt_prescriptions->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-5">
    <h2>Prescriptions pour <?php echo htmlspecialchars($patient['prenom']) . ' ' . htmlspecialchars($patient['nom']); ?></h2>
    
    <!-- Afficher les prescriptions du patient -->
    <?php if (count($prescriptions) > 0): ?>
        <table class="table table-striped mt-4">
            <thead>
                <tr>
                    <th>Date de prescription</th>
                    <th>Médicament</th>
                    <th>Dosage</th>
                    <th>Fréquence</th>
                    <th>Durée</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($prescriptions as $prescription): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($prescription['date_prescription']); ?></td>
                        <td><?php echo htmlspecialchars($prescription['medicament']); ?></td>
                        <td><?php echo htmlspecialchars($prescription['dosage']); ?></td>
                        <td><?php echo htmlspecialchars($prescription['frequence']); ?></td>
                        <td><?php echo htmlspecialchars($prescription['duree']); ?></td>
                        <td><?php echo htmlspecialchars($prescription['notes']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Aucune prescription disponible pour ce patient.</p>
    <?php endif; ?>

    <!-- Bouton de retour -->
    <a href="javascript:history.back()" class="btn btn-secondary mt-3">Retour</a>
</div>

<?php
// Inclure le footer
include '../footer.php';
?>
