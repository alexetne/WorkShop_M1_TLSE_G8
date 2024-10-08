<?php

class PatientDataMiddleware
{
    private $logFile = 'logs/patient_data.log';

    // Valider les données sortantes (vers l'API)
    public function validateOutgoingData($data)
    {
        // Valider que les champs obligatoires sont présents
        if (empty($data['patient_id']) || empty($data['name']) || empty($data['dob'])) {
            throw new Exception("Données patient incomplètes.");
        }

        // Valider les formats (exemple de vérification de date de naissance)
        if (!DateTime::createFromFormat('Y-m-d', $data['dob'])) {
            throw new Exception("Le format de la date de naissance est invalide.");
        }

        // Filtrer les données sensibles avant l'envoi
        unset($data['ssn']); // Retirer par exemple le numéro de sécurité sociale si présent

        return $data;
    }

    // Vérifier et filtrer les données entrantes (depuis l'API)
    public function validateIncomingData($data)
    {
        // Valider l'intégrité des données (par exemple, une signature ou un checksum)
        if (!isset($data['signature']) || !$this->verifySignature($data)) {
            throw new Exception("L'intégrité des données est compromise.");
        }

        // Filtrer les données inutiles ou sensibles reçues
        unset($data['internal_use_only']); // Ex. retirer des champs internes de l'API

        return $data;
    }

    // Exemple de vérification de signature (fonction fictive)
    private function verifySignature($data)
    {
        // Ex. vérifier que la signature correspond à une clé secrète partagée
        $calculatedSignature = hash_hmac('sha256', json_encode($data['data']), 'secret_key');
        return $data['signature'] === $calculatedSignature;
    }

    // Enregistrer les transactions dans un fichier log
    public function logTransaction($request, $response)
    {
        $logData = [
            'timestamp' => date('Y-m-d H:i:s'),
            'request' => json_encode($request),
            'response' => json_encode($response)
        ];
        file_put_contents($this->logFile, json_encode($logData) . PHP_EOL, FILE_APPEND);
    }

    // Middleware principal
    public function handle($request, callable $next)
    {
        try {
            // Valider et filtrer les données avant l'envoi à l'API
            $validatedRequest = $this->validateOutgoingData($request);

            // Appeler l'API (ou la fonction suivante dans la chaîne)
            $response = $next($validatedRequest);

            // Valider et filtrer les données reçues de l'API
            $validatedResponse = $this->validateIncomingData($response);

            // Journaliser la transaction
            $this->logTransaction($request, $validatedResponse);

            return $validatedResponse;

        } catch (Exception $e) {
            // Gérer les erreurs et journaliser
            error_log("Erreur de validation des données: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }
}
