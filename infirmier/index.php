<?php
// Inclure le header
include '../header.php';

// Simuler une session d'infirmier connecté (remplacer par une session réelle en production)
session_start();
$infirmier_nom = "Durand";
$infirmier_prenom = "Julie";
?>

<div class="container mt-5">
    <h2>Bienvenue, <?php echo $infirmier_prenom . " " . $infirmier_nom; ?></h2>
    
    <div class="row mt-4">
        <!-- Soins à prodiguer aujourd'hui -->
        <div class="col-md-6">
            <h4>Soins à prodiguer aujourd'hui</h4>
            <ul class="list-group">
                <li class="list-group-item">09:00 - Patient 1 (Injection de médication)</li>
                <li class="list-group-item">10:30 - Patient 2 (Changement de pansement)</li>
                <li class="list-group-item">12:00 - Patient 3 (Prise de paramètres vitaux)</li>
                <li class="list-group-item">15:00 - Patient 4 (Surveillance post-opératoire)</li>
            </ul>
        </div>

        <!-- Médicaments à distribuer -->
        <div class="col-md-6">
            <h4>Médicaments à administrer</h4>
            <ul class="list-group">
                <li class="list-group-item">09:00 - Patient A (Paracétamol 500mg)</li>
                <li class="list-group-item">10:00 - Patient B (Antibiotique)</li>
                <li class="list-group-item">14:00 - Patient C (Anti-inflammatoire)</li>
                <li class="list-group-item">16:00 - Patient D (Injection d'insuline)</li>
            </ul>
        </div>
    </div>

    <!-- Gestion des soins -->
    <div class="row mt-5">
        <div class="col-md-12">
            <h4>Gestion des soins</h4>
            <div class="card">
                <div class="card-body">
                    <a href="#" class="btn btn-primary">Ajouter un soin</a>
                    <a href="#" class="btn btn-secondary">Voir l'historique des soins</a>
                    <a href="#" class="btn btn-info">Voir les patients assignés</a>
                </div>
                <p class="mt-3">Accédez aux outils pour gérer les soins, enregistrer les actes réalisés, et consulter les soins passés.</p>
            </div>
        </div>
    </div>

    <!-- Suivi des patients hospitalisés -->
    <div class="row mt-5">
        <div class="col-md-12">
            <h4>Suivi des patients hospitalisés</h4>
            <div class="card">
                <div class="card-body">
                    <p><strong>Nombre total de patients sous votre charge :</strong> 12</p>
                    <p><strong>Paramètres vitaux enregistrés aujourd'hui :</strong> 36</p>
                    <p><strong>Patients en post-opératoire :</strong> 3</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques des soins -->
    <div class="row mt-5">
        <div class="col-md-12">
            <h4>Statistiques des soins</h4>
            <div class="card">
                <div class="card-body">
                    <p><strong>Soins prodigués aujourd'hui :</strong> 25</p>
                    <p><strong>Médicaments administrés :</strong> 15</p>
                    <p><strong>Paramètres vitaux pris :</strong> 36</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Inclure le footer
include '../footer.php';
?>
