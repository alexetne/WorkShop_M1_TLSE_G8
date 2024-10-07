<?php
// Inclure le header
include 'header.php';

// Initialiser les variables
$error_message = '';
$success_message = '';

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    // Validation simple des champs
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error_message = "Tous les champs sont requis.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Adresse e-mail invalide.";
    } else {
        // Destinataire de l'e-mail (adresse de l'entreprise)
        $to = "contact@entreprise.com"; // Remplacer par l'adresse réelle de l'entreprise
        $headers = "From: $email\r\n";
        $headers .= "Reply-To: $email\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

        // Contenu du mail
        $body = "
            <html>
            <head>
                <title>$subject</title>
            </head>
            <body>
                <h2>Demande de contact</h2>
                <p><strong>Nom :</strong> $name</p>
                <p><strong>Email :</strong> $email</p>
                <p><strong>Sujet :</strong> $subject</p>
                <p><strong>Message :</strong><br>$message</p>
            </body>
            </html>
        ";

        // Envoi de l'email
        if (mail($to, $subject, $body, $headers)) {
            $success_message = "Votre message a été envoyé avec succès ! Nous vous répondrons sous peu.";
        } else {
            $error_message = "Une erreur est survenue lors de l'envoi du message. Veuillez réessayer plus tard.";
        }
    }
}
?>

<div class="container mt-5">
    <h2>Contactez-nous</h2>

    <!-- Afficher les messages d'erreur ou de succès -->
    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <?php if (!empty($success_message)): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>

    <!-- Formulaire de contact -->
    <form method="POST" action="contact.php">
        <div class="form-group">
            <label for="name">Nom</label>
            <input type="text" name="name" class="form-control" id="name" placeholder="Entrez votre nom" required>
        </div>
        <div class="form-group">
            <label for="email">Adresse e-mail</label>
            <input type="email" name="email" class="form-control" id="email" placeholder="Entrez votre adresse e-mail" required>
        </div>
        <div class="form-group">
            <label for="subject">Sujet</label>
            <input type="text" name="subject" class="form-control" id="subject" placeholder="Entrez le sujet de votre message" required>
        </div>
        <div class="form-group">
            <label for="message">Message</label>
            <textarea name="message" class="form-control" id="message" rows="5" placeholder="Entrez votre message" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Envoyer</button>
    </form>
</div>

<?php
// Inclure le footer
include 'footer.php';
?>
