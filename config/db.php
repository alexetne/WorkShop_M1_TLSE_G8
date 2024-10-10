<?php
class Database {
    // Paramètres de connexion
    private $host = "localhost"; // L'hôte de la base de données
    private $db_name = "mediflow"; // Le nom de la base de données
    private $username = "mediflow"; // Le nom d'utilisateur de la base de données
    private $password = "passroot"; // Le mot de passe de la base de données
    public $conn;

    // Connexion à la base de données
    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            // Définir le mode d'erreur de PDO sur Exception
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            echo "Erreur de connexion : " . $exception->getMessage();
        }

        return $this->conn;
    }
}
?>
