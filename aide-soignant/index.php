<?php
// Inclure le header et démarrer la session
include '../header.php';
session_start();

// Vérifier si l'utilisateur est connecté et a le rôle approprié
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'aide-soignant') {
    // Si l'utilisateur n'est pas connecté ou n'a pas le bon rôle, redirection vers la page de connexion
    header("Location: ../login.php");
    exit;
}

// Si l'utilisateur est connecté avec le bon rôle, récupérer les informations de session
// $aide_soignant_nom = "Martin"; // Vous pouvez récupérer ces informations depuis la BDD si nécessaire
// $aide_soignant_prenom = "Lucas";
?>

<div class="container mt-5">
    <h2>Bienvenue, <?php echo $aide_soignant_prenom . " " . $aide_soignant_nom; ?></h2>
    
    <div class="row mt-4">
        <!-- Soins de base à prodiguer aujourd'hui -->
        <div class="col-md-6">
            <h4>Soins de base à prodiguer aujourd'hui</h4>
            <ul class="list-group">
                <li class="list-group-item">08:30 - Patient 1 (Toilette et habillage)</li>
                <li class="list-group-item">10:00 - Patient 2 (Aide à l’alimentation)</li>
                <li class="list-group-item">11:00 - Patient 3 (Changement de position)</li>
                <li class="list-group-item">15:30 - Patient 4 (Surveillance et hydratation)</li>
            </ul>
        </div>

        <!-- Mobilisation des patients -->
        <div class="col-md-6">
            <h4>Mobilisation des patients</h4>
            <ul class="list-group">
                <li class="list-group-item">09:00 - Patient A (Changer de position)</li>
                <li class="list-group-item">11:30 - Patient B (Mobilisation assistance)</li>
                <li class="list-group-item">14:00 - Patient C (Aide au fauteuil roulant)</li>
            </ul>
        </div>
    </div>

    <!-- Gestion des soins quotidiens -->
    <div class="row mt-5">
        <div class="col-md-12">
            <h4>Gestion des soins quotidiens</h4>
            <div class="card">
                <div class="card-body">
                    <a href="#" class="btn btn-primary">Ajouter un soin réalisé</a>
                    <a href="#" class="btn btn-secondary">Voir l'historique des soins</a>
                </div>
                <p class="mt-3">Enregistrez les soins prodigués et accédez à l'historique pour chaque patient sous votre charge.</p>
            </div>
        </div>
    </div>

    <!-- Suivi des patients hospitalisés -->
    <div class="row mt-5">
        <div class="col-md-12">
            <h4>Suivi des patients hospitalisés</h4>
            <div class="card">
                <div class="card-body">
                    <p><strong>Patients sous votre charge aujourd'hui :</strong> 8</p>
                    <p><strong>Nombre de soins de confort réalisés :</strong> 20</p>
                    <p><strong>Patients nécessitant une surveillance particulière :</strong> 2</p>
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
                    <p><strong>Patients aidés à la mobilité :</strong> 5</p>
                    <p><strong>Paramètres surveillés :</strong> 12</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Inclure le footer
include '../footer.php';
?>
