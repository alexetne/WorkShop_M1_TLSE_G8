<?php
// Inclure le header et démarrer la session
include '../header.php';
session_start();

// Vérifier si l'utilisateur est connecté et a le rôle approprié
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'autre') {
    // Si l'utilisateur n'est pas connecté ou n'a pas le bon rôle, redirection vers la page de connexion
    header("Location: ../login.php");
    exit;
}

// Si l'utilisateur est connecté avec le bon rôle, récupérer les informations de session
$gestionnaire_nom = $_SESSION['nom'] ?? 'Lemoine';  // À remplacer par les données réelles
$gestionnaire_prenom = $_SESSION['prenom'] ?? 'Sophie';

// Connexion à la base de données
include_once '../config/db.php';
$database = new Database();
$db = $database->getConnection();
?>

<div class="container mt-5">
    <h2>Bienvenue, <?php echo $gestionnaire_prenom . " " . $gestionnaire_nom; ?></h2>
    
    <div class="row mt-4">
        <!-- Vue d'ensemble des stocks -->
        <div class="col-md-6">
            <h4>Vue d'ensemble des stocks</h4>
            <ul class="list-group">
                <?php
                // Requête pour obtenir le nombre total de médicaments en stock
                $query_medications = "SELECT SUM(quantite) as total_medications FROM stock WHERE type = 'médicament'";
                $stmt_medications = $db->prepare($query_medications);
                $stmt_medications->execute();
                $row_medications = $stmt_medications->fetch(PDO::FETCH_ASSOC);
                $total_medications = $row_medications['total_medications'] ?? 0;

                // Requête pour obtenir le nombre total de matériel médical en stock
                $query_material = "SELECT SUM(quantite) as total_material FROM stock WHERE type = 'matériel médical'";
                $stmt_material = $db->prepare($query_material);
                $stmt_material->execute();
                $row_material = $stmt_material->fetch(PDO::FETCH_ASSOC);
                $total_material = $row_material['total_material'] ?? 0;

                // Requête pour obtenir le nombre d'articles en rupture de stock
                $query_out_of_stock = "SELECT COUNT(*) as out_of_stock FROM stock WHERE quantite = 0";
                $stmt_out_of_stock = $db->prepare($query_out_of_stock);
                $stmt_out_of_stock->execute();
                $row_out_of_stock = $stmt_out_of_stock->fetch(PDO::FETCH_ASSOC);
                $total_out_of_stock = $row_out_of_stock['out_of_stock'] ?? 0;

                // Requête pour obtenir le nombre de commandes en cours
                $query_orders = "SELECT COUNT(*) as pending_orders FROM commandes WHERE statut = 'en cours'";
                $stmt_orders = $db->prepare($query_orders);
                $stmt_orders->execute();
                $row_orders = $stmt_orders->fetch(PDO::FETCH_ASSOC);
                $pending_orders = $row_orders['pending_orders'] ?? 0;
                ?>

                <li class="list-group-item">Médicaments en stock : <?php echo $total_medications; ?></li>
                <li class="list-group-item">Matériel médical en stock : <?php echo $total_material; ?></li>
                <li class="list-group-item">Articles en rupture : <?php echo $total_out_of_stock; ?></li>
                <li class="list-group-item">Commandes en cours : <?php echo $pending_orders; ?></li>
            </ul>
        </div>

        <!-- Alertes de réapprovisionnement -->
        <div class="col-md-6">
            <h4>Alertes de réapprovisionnement</h4>
            <ul class="list-group">
                <?php
                // Définir le seuil de réapprovisionnement (par exemple, moins de 100 unités)
                $seuil_reapprovisionnement = 100;

                // Requête pour obtenir les articles dont la quantité est inférieure au seuil
                $query_reappro = "SELECT nom, quantite FROM stock WHERE quantite < :seuil";
                $stmt_reappro = $db->prepare($query_reappro);
                $stmt_reappro->bindParam(':seuil', $seuil_reapprovisionnement, PDO::PARAM_INT);
                $stmt_reappro->execute();
                $articles = $stmt_reappro->fetchAll(PDO::FETCH_ASSOC);

                // Vérifier s'il y a des articles à afficher
                if (count($articles) > 0) {
                    foreach ($articles as $article) {
                        echo '<li class="list-group-item">' . htmlspecialchars($article['nom']) . ' (Stock restant : ' . htmlspecialchars($article['quantite']) . ')</li>';
                    }
                } else {
                    echo '<li class="list-group-item">Aucun article ne nécessite de réapprovisionnement pour le moment.</li>';
                }
                ?>
            </ul>
        </div>
    </div>

    <!-- Gestion des réapprovisionnements -->
    <div class="row mt-5">
        <div class="col-md-12">
            <h4>Gestion des réapprovisionnements</h4>
            <div class="card">
                <div class="card-body">
                    <a href="passer_commande.php" class="btn btn-primary">Passer une commande</a>
                    <a href="gerer_commandes.php" class="btn btn-secondary">Suivre les commandes en cours</a>
                    <a href="historique_commandes.php" class="btn btn-info">Consulter l'historique des commandes</a>
                </div>
                <p class="mt-3">Accédez aux outils pour passer de nouvelles commandes, suivre les livraisons en cours, et consulter l'historique.</p>
            </div>
        </div>
    </div>

    <!-- Statistiques et rapports -->
    <div class="row mt-5">
        <div class="col-md-12">
            <h4>Statistiques et rapports</h4>
            <div class="card">
                <div class="card-body">
                    <?php
                    // Définir la période (ce mois-ci)
                    $debut_mois = date('Y-m-01'); // Premier jour du mois
                    $fin_mois = date('Y-m-t');    // Dernier jour du mois

                    // Consommation mensuelle de médicaments
                    $query_consumption = "SELECT SUM(quantite) as consommation_mensuelle FROM stock WHERE type = 'médicament'";
                    $stmt_consumption = $db->prepare($query_consumption);
                    $stmt_consumption->execute();
                    $row_consumption = $stmt_consumption->fetch(PDO::FETCH_ASSOC);
                    $consommation_mensuelle = $row_consumption['consommation_mensuelle'] ?? 0;

                    // Coût total des commandes ce mois-ci
                    $query_cost = "SELECT SUM(quantite_commande * prix_unitaires) as total_cost 
                                  FROM commandes c
                                  JOIN stock s ON c.id_stock = s.id
                                  WHERE c.date_commande BETWEEN :debut_mois AND :fin_mois AND c.statut = 'livré'";
                    $stmt_cost = $db->prepare($query_cost);
                    $stmt_cost->bindParam(':debut_mois', $debut_mois);
                    $stmt_cost->bindParam(':fin_mois', $fin_mois);
                    $stmt_cost->execute();
                    $row_cost = $stmt_cost->fetch(PDO::FETCH_ASSOC);
                    $total_cost = $row_cost['total_cost'] ?? 0;

                    // Nombre d'articles en rupture ce mois-ci
                    $query_out_of_stock = "SELECT COUNT(*) as articles_en_rupture FROM stock WHERE quantite = 0";
                    $stmt_out_of_stock = $db->prepare($query_out_of_stock);
                    $stmt_out_of_stock->execute();
                    $row_out_of_stock = $stmt_out_of_stock->fetch(PDO::FETCH_ASSOC);
                    $articles_en_rupture = $row_out_of_stock['articles_en_rupture'] ?? 0;
                    ?>

                    <p><strong>Consommation mensuelle de médicaments :</strong> <?php echo $consommation_mensuelle; ?> unités</p>
                    <p><strong>Coût total des commandes ce mois :</strong> <?php echo number_format($total_cost, 2, ',', ' '); ?> €</p>
                    <p><strong>Nombre d'articles en rupture ce mois :</strong> <?php echo $articles_en_rupture; ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Inclure le footer
include '../footer.php';
?>
