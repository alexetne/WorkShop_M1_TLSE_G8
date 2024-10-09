<?php
// Inclure le header et démarrer la session
include '../header.php';
session_start();

// Vérifier si l'utilisateur est connecté et a le rôle approprié
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // Si l'utilisateur n'est pas connecté ou n'a pas le bon rôle, redirection vers la page de connexion
    header("Location: ../login");
    exit;
}

// Si l'utilisateur est connecté avec le bon rôle, récupérer les informations de session
// $admin_nom = "Legrand"; // Vous pouvez récupérer ces informations depuis la BDD si nécessaire
// $admin_prenom = "Marie";
?>

<div class="container mt-5">
    <h2>Bienvenue, <?php echo $admin_prenom . " " . $admin_nom; ?></h2>
    
    <div class="row mt-4">
        <!-- Vue d'ensemble des opérations -->
        <div class="col-md-6">
            <h4>Vue d'ensemble des opérations</h4>
            <ul class="list-group">
                <li class="list-group-item">Patients hospitalisés : 45</li>
                <li class="list-group-item">Consultations prévues aujourd'hui : 20</li>
                <li class="list-group-item">Chambres disponibles : 10</li>
                <li class="list-group-item">Personnel présent : 85</li>
            </ul>
        </div>

        <!-- Performances financières -->
        <div class="col-md-6">
            <h4>Performances financières</h4>
            <ul class="list-group">
                <li class="list-group-item">Revenus ce mois : 120,000 €</li>
                <li class="list-group-item">Dépenses ce mois : 90,000 €</li>
                <li class="list-group-item">Paiements en attente : 15,000 €</li>
                <li class="list-group-item">Factures impayées : 8,000 €</li>
            </ul>
        </div>
    </div>

    <!-- Gestion des ressources humaines -->
    <div class="row mt-5">
        <div class="col-md-12">
            <h4>Gestion des ressources humaines</h4>
            <div class="card">
                <div class="card-body">
                    <a href="#" class="btn btn-primary">Suivre le personnel</a>
                    <a href="#" class="btn btn-secondary">Gérer les congés et absences</a>
                    <a href="#" class="btn btn-info">Voir les plannings du personnel</a>
                </div>
                <p class="mt-3">Gérez les employés, suivez les absences et gérez les plannings en fonction des besoins de l'établissement.</p>
            </div>
        </div>
    </div>

    <!-- Gestion financière et rapports -->
    <div class="row mt-5">
        <div class="col-md-12">
            <h4>Gestion financière et rapports</h4>
            <div class="card">
                <div class="card-body">
                    <a href="#" class="btn btn-primary">Suivi des paiements et factures</a>
                    <a href="#" class="btn btn-secondary">Voir les rapports financiers</a>
                    <a href="#" class="btn btn-info">Suivre les budgets et prévisions</a>
                </div>
                <p class="mt-3">Accédez aux paiements, factures et rapports financiers pour gérer les finances de l'établissement.</p>
            </div>
        </div>
    </div>

    <!-- Statistiques sur les performances -->
    <div class="row mt-5">
        <div class="col-md-12">
            <h4>Statistiques des performances</h4>
            <div class="card">
                <div class="card-body">
                    <p><strong>Consultations réalisées ce mois :</strong> 300</p>
                    <p><strong>Interventions chirurgicales :</strong> 40</p>
                    <p><strong>Taux d'occupation des chambres :</strong> 85%</p>
                    <p><strong>Taux d'absentéisme :</strong> 5%</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Inclure le footer
include '../footer.php';
?>
