<?php
// Inclure le header et la connexion à la base de données
include 'header.php';
include_once 'config/db.php';

session_start();

$database = new Database();
$db = $database->getConnection();

// Vérifier si l'utilisateur est déjà connecté
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    // Redirection en fonction du rôle de l'utilisateur connecté
    switch ($_SESSION['role']) {
        case 'admin':
            header("Location: admin/index.php");
            exit;
        case 'medecin':
            header("Location: medecin/index.php");
            exit;
        case 'infirmier':
            header("Location: infirmier/index.php");
            exit;
        case 'aide-soignant':
            header("Location: aide-soignant/index.php");
            exit;
        case 'secretaire':
            header("Location: secretaire/index.php");
            exit;
        default:
            header("Location: autre/index.php");
            exit;
    }
}

// Variables pour gérer les messages d'erreur ou de succès
$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $ip_adresse = $_SERVER['REMOTE_ADDR']; // Adresse IP de l'utilisateur
    $role_table = 'personnel'; // Tous les rôles sont désormais dans la table `personnel` ou similaire

    if (!empty($email) && !empty($password)) {
        // Requête pour récupérer les informations de l'utilisateur
        $query = "SELECT p.id, p.mail_pro, p.role, pass.password_hash, pass.compte_verrouille, pass.tentative_connexion_echouee 
                  FROM $role_table p 
                  JOIN password pass ON pass.id_personnel = p.id 
                  WHERE p.mail_pro = :email LIMIT 1";

        $stmt = $db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Vérifier si le compte est verrouillé
            if ($user['compte_verrouille']) {
                $error_message = "Compte verrouillé suite à plusieurs tentatives échouées.";
            } else {
                // Vérifier le mot de passe
                if (password_verify($password, $user['password_hash'])) {
                    // Connexion réussie, réinitialiser les tentatives échouées et inscrire dans logs_connexion
                    $query_reset_attempts = "UPDATE password SET tentative_connexion_echouee = 0 WHERE id_personnel = :id";
                    $stmt_reset = $db->prepare($query_reset_attempts);
                    $stmt_reset->bindParam(':id', $user['id']);
                    $stmt_reset->execute();

                    // Enregistrement de la connexion réussie
                    $query_log = "INSERT INTO logs_connexion (id_personnel, ip_adresse, resultat) VALUES (:id, :ip_adresse, 'succès')";
                    $stmt_log = $db->prepare($query_log);
                    $stmt_log->bindParam(':id', $user['id']);
                    $stmt_log->bindParam(':ip_adresse', $ip_adresse);
                    $stmt_log->execute();

                    // Démarrer une session pour l'utilisateur
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['role'] = $user['role'];

                    // Redirection en fonction du rôle
                    switch ($user['role']) {
                        case 'admin':
                            header("Location: admin/index.php");
                            exit;
                        case 'medecin':
                            header("Location: medecin/index.php");
                            exit;
                        case 'infirmier':
                            header("Location: infirmier/index.php");
                            exit;
                        case 'aide-soignant':
                            header("Location: aide-soignant/index.php");
                            exit;
                        case 'secretaire':
                            header("Location: secretaire/index.php");
                            exit;
                        default:
                            header("Location: autre/index.php");
                            exit;
                    }
                } else {
                    // Échec de la vérification du mot de passe, incrémenter les tentatives échouées
                    $query_fail = "UPDATE password SET tentative_connexion_echouee = tentative_connexion_echouee + 1 WHERE id_personnel = :id";
                    $stmt_fail = $db->prepare($query_fail);
                    $stmt_fail->bindParam(':id', $user['id']);
                    $stmt_fail->execute();

                    // Verrouiller le compte après 3 tentatives échouées
                    if ($user['tentative_connexion_echouee'] >= 2) {
                        $query_lock = "UPDATE password SET compte_verrouille = 1 WHERE id_personnel = :id";
                        $stmt_lock = $db->prepare($query_lock);
                        $stmt_lock->bindParam(':id', $user['id']);
                        $stmt_lock->execute();
                        $error_message = "Compte verrouillé après plusieurs tentatives échouées.";
                    } else {
                        $error_message = "Mot de passe incorrect.";
                    }

                    // Enregistrement de la connexion échouée
                    $query_log_fail = "INSERT INTO logs_connexion (id_personnel, ip_adresse, resultat) VALUES (:id, :ip_adresse, 'échec')";
                    $stmt_log_fail = $db->prepare($query_log_fail);
                    $stmt_log_fail->bindParam(':id', $user['id']);
                    $stmt_log_fail->bindParam(':ip_adresse', $ip_adresse);
                    $stmt_log_fail->execute();
                }
            }
        } else {
            $error_message = "Utilisateur non trouvé.";
        }
    } else {
        $error_message = "Veuillez remplir tous les champs.";
    }
}
?>

<div class="container mt-5">
    <h2 class="text-center">Connexion</h2>

    <!-- Afficher les messages d'erreur ou de succès -->
    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>
    <?php if (!empty($success_message)): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>

    <!-- Formulaire de connexion -->
    <form method="POST" action="login.php">
        <div class="form-group">
            <label for="email">Adresse e-mail</label>
            <input type="email" name="email" class="form-control" id="email" placeholder="Entrez votre adresse e-mail" required>
        </div>
        <div class="form-group">
            <label for="password">Mot de passe</label>
            <input type="password" name="password" class="form-control" id="password" placeholder="Entrez votre mot de passe" required>
        </div>
        <button type="submit" class="btn btn-primary">Se connecter</button>
    </form>
</div>

<?php include 'footer.php'; ?>
