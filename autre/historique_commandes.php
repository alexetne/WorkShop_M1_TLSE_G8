<?php
// Inclure le header et démarrer la session
include '../header.php';
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login");
    exit;
}

// Connexion à la base de données
include_once '../config/db.php';
$database = new Database();
$db = $database->getConnection();

// Récupérer l'historique des commandes (livrées ou annulées)
$query = "SELECT c.id, s.nom AS article_nom, c.quantite_commande, c.prix_unitaires, c.date_commande, c.date_livraison, c.statut, s.type, s.service 
          FROM commandes c
          JOIN stock s ON c.id_stock = s.id
          WHERE c.statut IN ('livré', 'annulé')";
$stmt = $db->prepare($query);
$stmt->execute();
$commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-5">
    <h2>Historique des commandes</h2>

    <!-- Vérifier s'il y a des commandes dans l'historique -->
    <?php if (count($commandes) > 0): ?>
        <table class="table table-striped mt-4">
            <thead>
                <tr>
                    <th>Article</th>
                    <th>Quantité</th>
                    <th>Prix unitaire (€)</th>
                    <th>Total (€)</th>
                    <th>Date de commande</th>
                    <th>Date de livraison</th>
                    <th>Statut</th>
                    <th>Service</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($commandes as $commande): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($commande['article_nom']); ?></td>
                        <td><?php echo htmlspecialchars($commande['quantite_commande']); ?></td>
                        <td><?php echo number_format($commande['prix_unitaires'], 2, ',', ' '); ?></td>
                        <td><?php echo number_format($commande['quantite_commande'] * $commande['prix_unitaires'], 2, ',', ' '); ?></td>
                        <td><?php echo htmlspecialchars($commande['date_commande']); ?></td>
                        <td><?php echo ($commande['statut'] === 'livré') ? htmlspecialchars($commande['date_livraison']) : '-'; ?></td>
                        <td><?php echo htmlspecialchars($commande['statut']); ?></td>
                        <td><?php echo htmlspecialchars($commande['service']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Aucune commande dans l'historique à afficher.</p>
    <?php endif; ?>
</div>

<?php
// Inclure le footer
include '../footer.php';
?>
