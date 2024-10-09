<?php
// Inclure le header et la connexion à la base de données
include '../header.php';
include_once '../config/db.php';

// Fonction de chiffrement AES-256-CBC
function encryptData($data, $key) {
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
    $encrypted_data = openssl_encrypt($data, 'aes-256-cbc', $key, 0, $iv);
    return ['ciphertext' => $encrypted_data, 'iv' => bin2hex($iv)];
}

$encryption_key = 'ZDIb5ZBkJ2NuVeST2ofVY09PywR6v8O2EHWitxyA'; // Définir une clé de chiffrement sécurisée
$error_message = '';
$success_message = '';

// Connexion à la base de données
$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $date_naissance = $_POST['date_naissance'];
    $profession = $_POST['profession'];
    $num_secu_social = $_POST['num_secu_social'];
    $medecin_traitant = $_POST['medecin_traitant'];
    $adresse_rue = $_POST['adresse_rue'];
    $adresse_ville = $_POST['adresse_ville'];
    $adresse_code_postal = $_POST['adresse_code_postal'];
    $mail = $_POST['mail'];
    $telephone_port = $_POST['telephone_port'];
    $telephone_fixe = $_POST['telephone_fixe'];
    $num_mutuelle = !empty($_POST['num_mutuelle']) ? $_POST['num_mutuelle'] : NULL;  // Si vide, on remplace par NULL

    // Récupérer l'ID du médecin à partir de la session
    $id_medecin = $_SESSION['user_id'];

    // Vérifier si tous les champs obligatoires sont remplis
    if (!empty($nom) && !empty($prenom) && !empty($date_naissance) && !empty($num_secu_social)) {
        // Chiffrer le numéro de sécurité sociale avant l'insertion
        $encrypted_ssn = encryptData($num_secu_social, $encryption_key);

        // Requête d'insertion du patient avec l'ID du médecin
        $query = "INSERT INTO patient (
                    nom, prenom, date_naissance, profession, num_secu_social, medecin_traitant, 
                    adresse_rue, adresse_ville, adresse_code_postal, mail, telephone_port, 
                    telephone_fixe, num_mutuelle, id_medecin, date_creation
                  ) 
                  VALUES (
                    :nom, :prenom, :date_naissance, :profession, :num_secu_social, :medecin_traitant,
                    :adresse_rue, :adresse_ville, :adresse_code_postal, :mail, :telephone_port, 
                    :telephone_fixe, :num_mutuelle, :id_medecin, NOW()
                  )";
        $stmt = $db->prepare($query);
        
        // Lier les paramètres
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':prenom', $prenom);
        $stmt->bindParam(':date_naissance', $date_naissance);
        $stmt->bindParam(':profession', $profession);
        $stmt->bindParam(':num_secu_social', $encrypted_ssn['ciphertext']);
        $stmt->bindParam(':medecin_traitant', $medecin_traitant);
        $stmt->bindParam(':adresse_rue', $adresse_rue);
        $stmt->bindParam(':adresse_ville', $adresse_ville);
        $stmt->bindParam(':adresse_code_postal', $adresse_code_postal);
        $stmt->bindParam(':mail', $mail);
        $stmt->bindParam(':telephone_port', $telephone_port);
        $stmt->bindParam(':telephone_fixe', $telephone_fixe);
        $stmt->bindParam(':num_mutuelle', $num_mutuelle, PDO::PARAM_INT); // Accepter NULL
        $stmt->bindParam(':id_medecin', $id_medecin); // Lier l'ID du médecin

        // Exécuter la requête
        if ($stmt->execute()) {
            $success_message = "Patient ajouté avec succès.";
        } else {
            $error_message = "Erreur lors de l'ajout du patient.";
        }
    } else {
        $error_message = "Veuillez remplir tous les champs obligatoires.";
    }
}
?>

<div class="container mt-5">
    <h2>Ajouter un patient</h2>
    
    <!-- Afficher les messages d'erreur ou de succès -->
    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>
    <?php if (!empty($success_message)): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>
    
    <!-- Formulaire d'ajout de patient -->
    <form method="POST" action="ajouter_patient">
        <div class="form-group">
            <label for="nom">Nom</label>
            <input type="text" name="nom" class="form-control" id="nom" required>
        </div>
        <div class="form-group">
            <label for="prenom">Prénom</label>
            <input type="text" name="prenom" class="form-control" id="prenom" required>
        </div>
        <div class="form-group">
            <label for="date_naissance">Date de naissance</label>
            <input type="date" name="date_naissance" class="form-control" id="date_naissance" required>
        </div>
        <div class="form-group">
            <label for="profession">Profession</label>
            <select name="profession" class="form-control" id="profession">
                <option value="Agriculteurs exploitants">Agriculteurs exploitants</option>
                <option value="Artisans, commerçants et chefs d'entreprise">Artisans, commerçants et chefs d'entreprise</option>
                <option value="Cadres et professions intellectuelles supérieures">Cadres et professions intellectuelles supérieures</option>
                <option value="Professions intermédiaires">Professions intermédiaires</option>
                <option value="Employés">Employés</option>
                <option value="autres">Autres</option>
            </select>
        </div>
        <div class="form-group">
            <label for="num_secu_social">Numéro de sécurité sociale</label>
            <input type="text" name="num_secu_social" class="form-control" id="num_secu_social" required>
        </div>
        <div class="form-group">
            <label for="medecin_traitant">Médecin traitant</label>
            <input type="text" name="medecin_traitant" class="form-control" id="medecin_traitant">
        </div>
        <div class="form-group">
            <label for="adresse_rue">Adresse - Rue</label>
            <input type="text" name="adresse_rue" class="form-control" id="adresse_rue">
        </div>
        <div class="form-group">
            <label for="adresse_ville">Adresse - Ville</label>
            <input type="text" name="adresse_ville" class="form-control" id="adresse_ville">
        </div>
        <div class="form-group">
            <label for="adresse_code_postal">Code postal</label>
            <input type="text" name="adresse_code_postal" class="form-control" id="adresse_code_postal">
        </div>
        <div class="form-group">
            <label for="mail">Email</label>
            <input type="email" name="mail" class="form-control" id="mail">
        </div>
        <div class="form-group">
            <label for="telephone_port">Téléphone portable</label>
            <input type="text" name="telephone_port" class="form-control" id="telephone_port">
        </div>
        <div class="form-group">
            <label for="telephone_fixe">Téléphone fixe</label>
            <input type="text" name="telephone_fixe" class="form-control" id="telephone_fixe">
        </div>
        <div class="form-group">
            <label for="num_mutuelle">Numéro de mutuelle</label>
            <input type="text" name="num_mutuelle" class="form-control" id="num_mutuelle">
        </div>
        <button type="submit" class="btn btn-primary">Ajouter le patient</button>
    </form>
</div>

<?php
// Inclure le footer
include '../footer.php';
?>
