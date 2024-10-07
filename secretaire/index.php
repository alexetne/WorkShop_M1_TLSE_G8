<?php
// Inclure le header
include '../header.php';

// Simuler une session de secrétaire connecté
session_start();
$secretaire_nom = "Doe";
$secretaire_prenom = "Jane";
?>

<div class="container mt-5">
    <h2>Bienvenue, <?php echo $secretaire_prenom . " " . $secretaire_nom; ?></h2>
    
    <div class="row mt-4">
        <!-- Tableau de bord : Rendez-vous et admissions -->
        <div class="col-md-6">
            <h4>Rendez-vous du jour</h4>
            <ul class="list-group">
                <li class="list-group-item">10:00 - Consultation avec Dr. Dupont - Patient 1</li>
                <li class="list-group-item">11:30 - Consultation avec Dr. Martin - Patient 2</li>
                <li class="list-group-item">14:00 - Consultation avec Dr. Durand - Patient 3</li>
                <li class="list-group-item">16:00 - Consultation avec Dr. Renault - Patient 4</li>
            </ul>
        </div>
        
        <div class="col-md-6">
            <h4>Admissions à traiter</h4>
            <ul class="list-group">
                <li class="list-group-item">Patient A - Arrivée pour hospitalisation à 09:00</li>
                <li class="list-group-item">Patient B - Arrivée pour consultation à 10:30</li>
                <li class="list-group-item">Patient C - Pré-admission à 13:00</li>
            </ul>
        </div>
    </div>

    <!-- Section gestion des rendez-vous -->
    <div class="row mt-5">
        <div class="col-md-12">
            <h4>Gestion des rendez-vous</h4>
            <div class="card">
                <div class="card-body">
                    <a href="#" class="btn btn-primary">Créer un rendez-vous</a>
                    <a href="#" class="btn btn-secondary">Modifier un rendez-vous</a>
                    <a href="#" class="btn btn-info">Annuler un rendez-vous</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Section gestion des patients -->
    <div class="row mt-5">
        <div class="col-md-12">
            <h4>Gestion des patients et dossiers</h4>
            <div class="card">
                <div class="card-body">
                    <a href="#" class="btn btn-primary">Créer un dossier patient</a>
                    <a href="#" class="btn btn-secondary">Mettre à jour un dossier</a>
                    <a href="#" class="btn btn-info">Accéder aux dossiers archivés</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques et rapports -->
    <div class="row mt-5">
        <div class="col-md-12">
            <h4>Statistiques et Rapports</h4>
            <div class="card">
                <div class="card-body">
                    <p><strong>Patients admis aujourd'hui :</strong> 10</p>
                    <p><strong>Consultations réalisées :</strong> 8</p>
                    <p><strong>Chambres disponibles :</strong> 5</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Inclure le footer
include '../footer.php';
?>
