<?php
// Inclure le header
include '../header.php';

// Simuler une session de médecin connecté (remplacer par une session réelle en production)
session_start();
$medecin_nom = "Dupont";
$medecin_prenom = "Jean";
?>

<div class="container mt-5">
    <h2>Bienvenue, Dr. <?php echo $medecin_prenom . " " . $medecin_nom; ?></h2>
    
    <div class="row mt-4">
        <!-- Tableau de bord résumé -->
        <div class="col-md-4">
            <h4>Tâches du jour</h4>
            <ul class="list-group">
                <li class="list-group-item">Patient 1 - Consultation à 10h00</li>
                <li class="list-group-item">Patient 2 - Opération programmée à 14h00</li>
                <li class="list-group-item">Examiner les résultats des tests de Patient 3</li>
                <li class="list-group-item">Suivi post-opératoire de Patient 4</li>
            </ul>
        </div>
        
        <!-- Liste des patients -->
        <div class="col-md-4">
            <h4>Mes Patients</h4>
            <ul class="list-group">
                <li class="list-group-item"><a href="#">Patient A - Dossier médical</a></li>
                <li class="list-group-item"><a href="#">Patient B - Dossier médical</a></li>
                <li class="list-group-item"><a href="#">Patient C - Dossier médical</a></li>
                <li class="list-group-item"><a href="#">Patient D - Dossier médical</a></li>
            </ul>
        </div>
        
        <!-- Agenda personnel -->
        <div class="col-md-4">
            <h4>Mon Agenda</h4>
            <ul class="list-group">
                <li class="list-group-item">Consultations : 5 aujourd'hui</li>
                <li class="list-group-item">Prochaine intervention : 14h00</li>
                <li class="list-group-item">Réunion à 16h00</li>
            </ul>
        </div>
    </div>

    <!-- Section avancée pour le suivi des patients, gestion des ordonnances, actes médicaux -->
    <div class="row mt-5">
        <div class="col-md-12">
            <h4>Gestion des patients et ordonnances</h4>
            <div class="card">
                <div class="card-body">
                    <p>
                        <a href="#" class="btn btn-primary">Ajouter une ordonnance</a>
                        <a href="#" class="btn btn-secondary">Consulter les prescriptions</a>
                        <a href="#" class="btn btn-info">Voir les actes médicaux</a>
                    </p>
                    <p>Accédez rapidement aux outils pour gérer vos patients et leurs ordonnances.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques et rapports -->
    <div class="row mt-5">
        <div class="col-md-12">
            <h4>Statistiques</h4>
            <div class="card">
                <div class="card-body">
                    <p>
                        <strong>Consultations ce mois :</strong> 45<br>
                        <strong>Interventions chirurgicales :</strong> 10<br>
                        <strong>Patients suivis :</strong> 25
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Inclure le footer
include '../footer.php';
?>
