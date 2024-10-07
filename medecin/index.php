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

// Si l'utilisateur est connecté avec le bon rôle, récupérer les informations de session
// $medecin_nom = "Dupont"; // Vous pouvez récupérer ces informations depuis la BDD si nécessaire
// $medecin_prenom = "Jean";
?>

<div class="container mt-5">
    <h2>Bienvenue, Dr. <?php echo $medecin_prenom . " " . $medecin_nom; ?></h2>
    
    <div class="row mt-4">
        <!-- Tableau de bord : Rendez-vous du jour -->
        <div class="col-md-6">
            <h4>Consultations du jour</h4>
            <ul class="list-group">
                <li class="list-group-item">10:00 - Patient 1 (Consultation générale)</li>
                <li class="list-group-item">11:30 - Patient 2 (Suivi post-opératoire)</li>
                <li class="list-group-item">14:00 - Patient 3 (Consultation spécialisée)</li>
                <li class="list-group-item">16:00 - Patient 4 (Diagnostic radiologique)</li>
            </ul>
        </div>

        <!-- Tableau de bord : Patients hospitalisés -->
        <div class="col-md-6">
            <h4>Patients hospitalisés sous votre charge</h4>
            <ul class="list-group">
                <li class="list-group-item"><a href="#">Patient A - Dossier médical</a> (Chambre 101)</li>
                <li class="list-group-item"><a href="#">Patient B - Dossier médical</a> (Chambre 102)</li>
                <li class="list-group-item"><a href="#">Patient C - Dossier médical</a> (Chambre 105)</li>
                <li class="list-group-item"><a href="#">Patient D - Dossier médical</a> (Chambre 108)</li>
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
                    <a href="#" class="btn btn-secondary">Voir les prescriptions</a>
                    <a href="#" class="btn btn-info">Consulter les actes médicaux</a>
                </div>
                <p class="mt-3">Accédez rapidement aux outils pour gérer les ordonnances, prescriptions et actes médicaux liés aux patients sous votre responsabilité.</p>
            </div>
        </div>
    </div>

    <!-- Agenda personnel -->
    <div class="row mt-5">
        <div class="col-md-12">
            <h4>Mon Agenda</h4>
            <div class="card">
                <div class="card-body">
                    <p><strong>Consultations prévues :</strong> 5 aujourd'hui</p>
                    <p><strong>Prochaine intervention :</strong> Opération à 14h00</p>
                    <p><strong>Réunion :</strong> Conférence médicale à 16h00</p>
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
                    <p><strong>Consultations réalisées ce mois :</strong> 45</p>
                    <p><strong>Interventions chirurgicales :</strong> 10</p>
                    <p><strong>Patients suivis :</strong> 25</p>
                    <p><strong>Rendez-vous annulés :</strong> 3</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Inclure le footer
include '../footer.php';
?>
