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

// Variable pour les messages d'erreur ou de succès
$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $id_stock = $_POST['id_stock'];
    $quantite_commande = $_POST['quantite_commande'];
    $prix_unitaire = $_POST['prix_unitaire'];

    if (!empty($id_stock) && !empty($quantite_commande) && !empty($prix_unitaire)) {
        // Requête pour insérer la commande dans la table `commandes`
        $query = "INSERT INTO commandes (id_stock, quantite_commande, statut, prix_unitaires, dernier_utilisateur_modif) 
                  VALUES (:id_stock, :quantite_commande, 'en cours', :prix_unitaire, :dernier_utilisateur_modif)";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id_stock', $id_stock, PDO::PARAM_INT);
        $stmt->bindParam(':quantite_commande', $quantite_commande, PDO::PARAM_INT);
        $stmt->bindParam(':prix_unitaire', $prix_unitaire, PDO::PARAM_STR);
        $stmt->bindParam(':dernier_utilisateur_modif', $_SESSION['user_id']);

        if ($stmt->execute()) {
            $success_message = "Commande passée avec succès!";
        } else {
            $error_message = "Erreur lors de la commande.";
        }
    } else {
        $error_message = "Veuillez remplir tous les champs.";
    }
}

// Récupérer la liste des articles disponibles dans le stock
$query_stock = "SELECT id, nom, prix_unitaire FROM stock";
$stmt_stock = $db->prepare($query_stock);
$stmt_stock->execute();
$articles = $stmt_stock->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-5">
    <h2>Passer une commande</h2>

    <!-- Afficher les messages d'erreur ou de succès -->
    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>
    <?php if (!empty($success_message)): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>

    <!-- Formulaire pour passer une commande -->
    <form method="POST" action="passer_commande">
        <div class="form-group">
            <label for="id_stock">Article à commander</label>
            <select name="id_stock" class="form-control" id="id_stock" required>
                <option value="">Sélectionnez un article</option>
                <?php foreach ($articles as $article): ?>
                    <option value="<?php echo $article['id']; ?>"><?php echo $article['nom']; ?> (Prix unitaire : <?php echo $article['prix_unitaire']; ?> €)</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="quantite_commande">Quantité à commander</label>
            <input type="number" name="quantite_commande" class="form-control" id="quantite_commande" placeholder="Entrez la quantité" required>
        </div>
        <div class="form-group">
            <label for="prix_unitaire">Prix unitaire</label>
            <input type="text" name="prix_unitaire" class="form-control" id="prix_unitaire" placeholder="Entrez le prix unitaire" required>
        </div>
        <button type="submit" class="btn btn-primary">Passer la commande</button>
    </form>
</div>

<?php
// Inclure le footer
include '../footer.php';
?>
