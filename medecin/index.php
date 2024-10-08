<?php
// Inclure le header et démarrer la session
include '../header.php';
session_start();

// Vérifier si l'utilisateur est connecté et a le rôle approprié
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'medecin') {
    // Si l'utilisateur n'est pas connecté ou n'a pas le bon rôle, redirection vers la page de connexion
    header("Location: ../login.php");
    exit;
}

// Connexion à la base de données
include_once '../config/db.php';
$database = new Database();
$db = $database->getConnection();

// Récupérer les informations du médecin à partir de la session
$medecin_nom = $_SESSION['nom'];
$medecin_prenom = $_SESSION['prenom'];
?>

<div class="container mt-5">
    <h2>Bienvenue, Dr. <?php echo htmlspecialchars($medecin_prenom) . " " . htmlspecialchars($medecin_nom); ?></h2>
    
    <div class="row mt-4">
        <!-- Tableau de bord : Consultations du jour -->
        <div class="col-md-6">
            <h4>Consultations du jour</h4>
            <ul class="list-group">
                <?php
                // Récupérer la date actuelle
                $today = date('Y-m-d');

                // Requête pour obtenir les consultations du jour (type = 'consultation')
                $query = "SELECT p.jour, p.type, p.heure, s.nom AS salle_nom, pers.nom AS medecin_nom, pers.prenom AS medecin_prenom 
                          FROM planning p
                          JOIN salle_hopital s ON p.id_salle = s.id
                          JOIN personnel pers ON p.id_personnel = pers.id
                          WHERE p.jour = :today AND p.type = 'consultation' AND p.id_personnel = :id_personnel";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':today', $today);
                $stmt->bindParam(':id_personnel', $_SESSION['user_id']);
                $stmt->execute();
                $consultations = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Affichage des consultations
                if (count($consultations) > 0) {
                    foreach ($consultations as $consultation) {
                        echo '<li class="list-group-item">';
                        echo htmlspecialchars($consultation['heure']) . ' - ';
                        echo 'Salle : ' . htmlspecialchars($consultation['salle_nom']);
                        echo '</li>';
                    }
                } else {
                    echo '<li class="list-group-item">Aucune consultation prévue aujourd\'hui.</li>';
                }
                ?>
            </ul>
        </div>

        <!-- Tableau de bord : Patients hospitalisés -->
        <div class="col-md-6">
            <h4>Patients hospitalisés sous votre charge</h4>
            <ul class="list-group">
                <?php
                // Requête pour obtenir les patients hospitalisés sous la charge du médecin
                $query = "SELECT p.id, p.nom AS patient_nom, p.prenom AS patient_prenom, s.nom AS salle_nom, s.numero_chambre 
                          FROM hospitalisation h
                          JOIN patient p ON h.id_patient = p.id
                          JOIN salle_hopital s ON h.id_salle = s.id
                          WHERE h.id_medecin = :id_medecin AND h.statut = 'hospitalisé'";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':id_medecin', $_SESSION['user_id']);
                $stmt->execute();
                $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Affichage des patients hospitalisés
                if (count($patients) > 0) {
                    foreach ($patients as $patient) {
                        echo '<li class="list-group-item">';
                        echo '<a href="dossier_medical.php?id_patient=' . htmlspecialchars($patient['id']) . '">';
                        echo htmlspecialchars($patient['prenom']) . ' ' . htmlspecialchars($patient['nom']);
                        echo '</a> (Chambre ' . htmlspecialchars($patient['numero_chambre']) . ')';
                        echo '</li>';
                    }
                } else {
                    echo '<li class="list-group-item">Aucun patient hospitalisé sous votre charge.</li>';
                }
                ?>
            </ul>
        </div>
    </div>

    <!-- Gestion des soins et prescriptions -->
    <div class="row mt-5">
        <div class="col-md-12">
            <h4>Gestion des soins et ordonnances</h4>
            <div class="card">
                <div class="card-body">
                    <a href="#" class="btn btn-primary">Ajouter une ordonnance</a>
                    <!-- Bouton pour ouvrir la popup de sélection de patient pour la prescription -->
                    <button class="btn btn-secondary" data-toggle="modal" data-target="#selectPatientPrescriptionModal">Voir les prescriptions</button>
                    <!-- Bouton pour ouvrir la popup de sélection de patient pour les actes médicaux -->
                    <button class="btn btn-info" data-toggle="modal" data-target="#selectPatientActesModal">Consulter les actes médicaux</button>
                </div>
                <p class="mt-3">Accédez rapidement aux outils pour gérer les ordonnances, prescriptions et actes médicaux liés aux patients sous votre responsabilité.</p>
            </div>
        </div>
    </div>

    <!-- Modal pour sélectionner un patient pour les actes médicaux -->
    <div class="modal fade" id="selectPatientActesModal" tabindex="-1" role="dialog" aria-labelledby="selectPatientActesModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="selectPatientActesModalLabel">Sélectionner un patient pour les actes médicaux</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <ul class="list-group">
                        <?php
                        // Requête pour obtenir la liste des patients sous la charge du médecin
                        $query_patients = "SELECT p.id, p.nom, p.prenom 
                                           FROM hospitalisation h
                                           JOIN patient p ON h.id_patient = p.id
                                           WHERE h.id_medecin = :id_medecin";
                        $stmt_patients = $db->prepare($query_patients);
                        $stmt_patients->bindParam(':id_medecin', $_SESSION['user_id']);
                        $stmt_patients->execute();
                        $patients = $stmt_patients->fetchAll(PDO::FETCH_ASSOC);

                        // Affichage des patients
                        foreach ($patients as $patient) {
                            echo '<li class="list-group-item">';
                            echo '<a href="consulter_actes.php?id_patient=' . htmlspecialchars($patient['id']) . '">';
                            echo htmlspecialchars($patient['prenom']) . ' ' . htmlspecialchars($patient['nom']);
                            echo '</a>';
                            echo '</li>';
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal pour sélectionner un patient pour les prescriptions -->
    <div class="modal fade" id="selectPatientPrescriptionModal" tabindex="-1" role="dialog" aria-labelledby="selectPatientPrescriptionModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="selectPatientPrescriptionModalLabel">Sélectionner un patient pour les prescriptions</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <ul class="list-group">
                        <?php
                        // Requête pour obtenir la liste des patients sous la charge du médecin
                        $stmt_patients->execute();
                        $patients = $stmt_patients->fetchAll(PDO::FETCH_ASSOC);

                        // Affichage des patients
                        foreach ($patients as $patient) {
                            echo '<li class="list-group-item">';
                            echo '<a href="consulter_prescriptions.php?id_patient=' . htmlspecialchars($patient['id']) . '">';
                            echo htmlspecialchars($patient['prenom']) . ' ' . htmlspecialchars($patient['nom']);
                            echo '</a>';
                            echo '</li>';
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Agenda personnel -->
    <div class="row mt-5">
        <div class="col-md-12">
            <h4>Mon Agenda</h4>
            <div class="card">
                <div class="card-body">
                    <?php
                    // Récupérer les consultations, interventions et réunions du jour
                    $query_agenda = "SELECT COUNT(*) as total_consultations, 
                                            (SELECT heure FROM planning WHERE type = 'intervention' AND jour = :today AND id_personnel = :id_personnel ORDER BY heure ASC LIMIT 1) AS intervention_heure, 
                                            (SELECT heure FROM planning WHERE type = 'réunion' AND jour = :today AND id_personnel = :id_personnel ORDER BY heure ASC LIMIT 1) AS reunion_heure
                                     FROM planning 
                                     WHERE type = 'consultation' AND jour = :today AND id_personnel = :id_personnel";
                    $stmt_agenda = $db->prepare($query_agenda);
                    $stmt_agenda->bindParam(':today', $today);
                    $stmt_agenda->bindParam(':id_personnel', $_SESSION['user_id']);
                    $stmt_agenda->execute();
                    $agenda = $stmt_agenda->fetch(PDO::FETCH_ASSOC);
                    ?>

                    <p><strong>Consultations prévues :</strong> <?php echo $agenda['total_consultations']; ?> aujourd'hui</p>
                    <p><strong>Prochaine intervention :</strong> 
                        <?php echo $agenda['intervention_heure'] ? 'Opération à ' . htmlspecialchars($agenda['intervention_heure']) : 'Aucune intervention prévue'; ?>
                    </p>
                    <p><strong>Réunion :</strong> 
                        <?php echo $agenda['reunion_heure'] ? 'Conférence médicale à ' . htmlspecialchars($agenda['reunion_heure']) : 'Aucune réunion prévue'; ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques des consultations -->
    <div class="row mt-5">
        <div class="col-md-12">
            <h4>Statistiques</h4>
            <div class="card">
                <div class="card-body">
                    <?php
                    // Récupérer les statistiques mensuelles
                    $first_day_of_month = date('Y-m-01');
                    $last_day_of_month = date('Y-m-t');

                    $query_stats = "SELECT (SELECT COUNT(*) FROM planning WHERE type = 'consultation' AND jour BETWEEN :first_day AND :last_day AND id_personnel = :id_personnel) AS consultations,
                                           (SELECT COUNT(*) FROM planning WHERE type = 'intervention' AND jour BETWEEN :first_day AND :last_day AND id_personnel = :id_personnel) AS interventions,
                                           (SELECT COUNT(DISTINCT id_patient) FROM planning WHERE type IN ('consultation', 'intervention') AND jour BETWEEN :first_day AND :last_day AND id_personnel = :id_personnel) AS patients_suivis,
                                           (SELECT COUNT(*) FROM planning WHERE type = 'consultation' AND statut = 'annulé' AND jour BETWEEN :first_day AND :last_day AND id_personnel = :id_personnel) AS annulations";
                    $stmt_stats = $db->prepare($query_stats);
                    $stmt_stats->bindParam(':first_day', $first_day_of_month);
                    $stmt_stats->bindParam(':last_day', $last_day_of_month);
                    $stmt_stats->bindParam(':id_personnel', $_SESSION['user_id']);
                    $stmt_stats->execute();
                    $stats = $stmt_stats->fetch(PDO::FETCH_ASSOC);
                    ?>

                    <p><strong>Consultations réalisées ce mois :</strong> <?php echo $stats['consultations']; ?></p>
                    <p><strong>Interventions chirurgicales :</strong> <?php echo $stats['interventions']; ?></p>
                    <p><strong>Patients suivis :</strong> <?php echo $stats['patients_suivis']; ?></p>
                    <p><strong>Rendez-vous annulés :</strong> <?php echo $stats['annulations']; ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Inclure le footer
include '../footer.php';
?>
