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

// Récupérer l'ID du patient depuis l'URL ou la requête POST
$id_patient = isset($_GET['id_patient']) ? $_GET['id_patient'] : null;

if (!$id_patient) {
    echo "<div class='container mt-5'><div class='alert alert-danger'>ID du patient manquant.</div></div>";
    include '../footer.php';
    exit;
}

// Requête pour récupérer les informations du patient
$query_patient = "SELECT nom, prenom FROM patient WHERE id = :id_patient";
$stmt_patient = $db->prepare($query_patient);
$stmt_patient->bindParam(':id_patient', $id_patient, PDO::PARAM_INT);
$stmt_patient->execute();
$patient = $stmt_patient->fetch(PDO::FETCH_ASSOC);

if (!$patient) {
    echo "<div class='container mt-5'><div class='alert alert-danger'>Patient introuvable.</div></div>";
    include '../footer.php';
    exit;
}

// Requête pour obtenir les actes médicaux du patient
$query_actes = "SELECT a.id, a.description, a.date_acte, p.nom AS medecin_nom, p.prenom AS medecin_prenom
                FROM actes_medicaux a
                JOIN personnel p ON a.id_medecin = p.id
                WHERE a.id_patient = :id_patient
                ORDER BY a.date_acte DESC";
$stmt_actes = $db->prepare($query_actes);
$stmt_actes->bindParam(':id_patient', $id_patient, PDO::PARAM_INT);
$stmt_actes->execute();
$actes = $stmt_actes->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-5">
    <h2>Actes médicaux pour <?php echo htmlspecialchars($patient['prenom'] . ' ' . $patient['nom']); ?></h2>

    <!-- Vérifier s'il y a des actes médicaux -->
    <?php if (count($actes) > 0): ?>
        <table class="table table-striped mt-4">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Médecin</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($actes as $acte): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($acte['description']); ?></td>
                        <td><?php echo htmlspecialchars($acte['medecin_prenom'] . ' ' . $acte['medecin_nom']); ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($acte['date_acte'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info mt-4">Aucun acte médical trouvé pour ce patient.</div>
    <?php endif; ?>
</div>

<?php
// Inclure le footer
include '../footer.php';
?>
