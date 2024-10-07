<?php
// Inclure le header
include '../header.php';

// Simuler une session de gestionnaire de stocks connecté (remplacer par une session réelle en production)
session_start();
$gestionnaire_nom = "Lemoine";
$gestionnaire_prenom = "Sophie";
?>

<div class="container mt-5">
    <h2>Bienvenue, <?php echo $gestionnaire_prenom . " " . $gestionnaire_nom; ?></h2>
    
    <div class="row mt-4">
        <!-- Vue d'ensemble des stocks -->
        <div class="col-md-6">
            <h4>Vue d'ensemble des stocks</h4>
            <ul class="list-group">
                <li class="list-group-item">Médicaments en stock : 150</li>
                <li class="list-group-item">Matériel médical en stock : 500</li>
                <li class="list-group-item">Articles en rupture : 10</li>
                <li class="list-group-item">Commandes en cours : 5</li>
            </ul>
        </div>

        <!-- Alertes de réapprovisionnement -->
        <div class="col-md-6">
            <h4>Alertes de réapprovisionnement</h4>
            <ul class="list-group">
                <li class="list-group-item">Paracétamol 500mg (Stock restant : 50)</li>
                <li class="list-group-item">Gants stériles (Stock restant : 100)</li>
                <li class="list-group-item">Masques chirurgicaux (Stock restant : 30)</li>
            </ul>
        </div>
    </div>

    <!-- Gestion des réapprovisionnements -->
    <div class="row mt-5">
        <div class="col-md-12">
            <h4>Gestion des réapprovisionnements</h4>
            <div class="card">
                <div class="card-body">
                    <a href="#" class="btn btn-primary">Passer une commande</a>
                    <a href="#" class="btn btn-secondary">Suivre les commandes en cours</a>
                    <a href="#" class="btn btn-info">Consulter l'historique des commandes</a>
                </div>
                <p class="mt-3">Accédez aux outils pour passer de nouvelles commandes, suivre les livraisons en cours, et consulter l'historique.</p>
            </div>
        </div>
    </div>

    <!-- Gestion des stocks de médicaments -->
    <div class="row mt-5">
        <div class="col-md-12">
            <h4>Gestion des stocks de médicaments</h4>
            <div class="card">
                <div class="card-body">
                    <p><strong>Médicaments actuellement en stock :</strong> 150 types</p>
                    <p><strong>Médicaments proches de la péremption :</strong> 5</p>
                    <p><strong>Médicaments à commander :</strong> 3</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques et rapports -->
    <div class="row mt-5">
        <div class="col-md-12">
            <h4>Statistiques et rapports</h4>
            <div class="card">
                <div class="card-body">
                    <p><strong>Consommation mensuelle de médicaments :</strong> 1200 unités</p>
                    <p><strong>Coût total des commandes ce mois :</strong> 50,000 €</p>
                    <p><strong>Nombre d'articles en rupture ce mois :</strong> 5</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Inclure le footer
include '../footer.php';
?>
