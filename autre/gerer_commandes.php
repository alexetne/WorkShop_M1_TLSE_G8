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

// Variable pour les messages de succès ou d'erreur
$success_message = '';
$error_message = '';

// Si une mise à jour est demandée pour changer le statut d'une commande
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_commande']) && isset($_POST['action'])) {
    $id_commande = $_POST['id_commande'];
    $action = $_POST['action'];

    if ($action === 'livrer') {
        // Démarrer une transaction pour garantir la cohérence des données
        $db->beginTransaction();

        // Mettre à jour le statut de la commande à "livré"
        $query_update = "UPDATE commandes SET statut = 'livré', date_livraison = NOW(), dernier_utilisateur_modif = :user_id WHERE id = :id_commande";
        $stmt_update = $db->prepare($query_update);
        $stmt_update->bindParam(':user_id', $_SESSION['user_id']);
        $stmt_update->bindParam(':id_commande', $id_commande);

        if ($stmt_update->execute()) {
            // Récupérer la quantité commandée et l'ID de l'article dans le stock
            $query_commande = "SELECT id_stock, quantite_commande FROM commandes WHERE id = :id_commande";
            $stmt_commande = $db->prepare($query_commande);
            $stmt_commande->bindParam(':id_commande', $id_commande);
            $stmt_commande->execute();
            $commande = $stmt_commande->fetch(PDO::FETCH_ASSOC);

            if ($commande) {
                // Ajouter la quantité commandée au stock de l'article
                $query_update_stock = "UPDATE stock SET quantite = quantite + :quantite_commande WHERE id = :id_stock";
                $stmt_update_stock = $db->prepare($query_update_stock);
                $stmt_update_stock->bindParam(':quantite_commande', $commande['quantite_commande']);
                $stmt_update_stock->bindParam(':id_stock', $commande['id_stock']);

                if ($stmt_update_stock->execute()) {
                    // Si tout s'est bien passé, valider la transaction
                    $db->commit();
                    $success_message = "Commande mise à jour et stock ajusté avec succès.";
                } else {
                    // Si la mise à jour du stock échoue, annuler la transaction
                    $db->rollBack();
                    $error_message = "Erreur lors de la mise à jour du stock.";
                }
            } else {
                $db->rollBack();
                $error_message = "Commande introuvable.";
            }
        } else {
            $db->rollBack();
            $error_message = "Erreur lors de la mise à jour de la commande.";
        }
    } elseif ($action === 'annuler') {
        // Mettre à jour le statut de la commande à "annulé" sans toucher au stock
        $query_cancel = "UPDATE commandes SET statut = 'annulé', date_livraison = NULL, dernier_utilisateur_modif = :user_id WHERE id = :id_commande";
        $stmt_cancel = $db->prepare($query_cancel);
        $stmt_cancel->bindParam(':user_id', $_SESSION['user_id']);
        $stmt_cancel->bindParam(':id_commande', $id_commande);

        if ($stmt_cancel->execute()) {
            $success_message = "Commande annulée avec succès.";
        } else {
            $error_message = "Erreur lors de l'annulation de la commande.";
        }
    }
}

// Récupérer la liste des commandes en cours
$query = "SELECT c.id, s.nom AS article_nom, c.quantite_commande, c.prix_unitaires, c.date_commande, s.type, s.service 
          FROM commandes c 
          JOIN stock s ON c.id_stock = s.id 
          WHERE c.statut = 'en cours'";
$stmt = $db->prepare($query);
$stmt->execute();
$commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-5">
    <h2>Gestion des commandes en cours</h2>

    <!-- Afficher les messages de succès ou d'erreur -->
    <?php if (!empty($success_message)): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>
    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <!-- Vérifier s'il y a des commandes en cours -->
    <?php if (count($commandes) > 0): ?>
        <table class="table table-striped mt-4">
            <thead>
                <tr>
                    <th>Article</th>
                    <th>Quantité</th>
                    <th>Prix unitaire (€)</th>
                    <th>Total (€)</th>
                    <th>Date de commande</th>
                    <th>Service</th>
                    <th>Action</th>
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
                        <td><?php echo htmlspecialchars($commande['service']); ?></td>
                        <td>
                            <form method="POST" action="gerer_commandes">
                                <input type="hidden" name="id_commande" value="<?php echo $commande['id']; ?>">
                                <button type="submit" name="action" value="livrer" class="btn btn-success">Marquer comme livrée</button>
                                <button type="submit" name="action" value="annuler" class="btn btn-danger">Annuler la commande</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Aucune commande en cours à afficher.</p>
    <?php endif; ?>
</div>

<?php
// Inclure le footer
include '../footer.php';
?>
