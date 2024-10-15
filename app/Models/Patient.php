<?php

class Patient {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Récupérer tous les patients
    public function getAll() {
        $query = "SELECT * FROM patients";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Rechercher des patients
    public function searchPatients($search) {
        // Préparer la requête pour rechercher les patients selon le terme
        $query = "SELECT * FROM patients WHERE first_name LIKE :search OR last_name LIKE :search OR email LIKE :search OR phone LIKE :search";
        $stmt = $this->conn->prepare($query);
        $searchTerm = '%' . $search . '%'; // Pour la recherche avec wildcard
        $stmt->bindParam(':search', $searchTerm);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Créer un nouveau patient
    public function create($data) {
    try {
        // Vérification de l'unicité de l'email
        if ($this->getByEmail($data['email'])) {
            return false; // Email déjà utilisé
        }

        $query = "INSERT INTO patients (first_name, last_name, email, phone, password, role) VALUES (:first_name, :last_name, :email, :phone, :password, :role)";
        $stmt = $this->conn->prepare($query);

        // Hacher le mot de passe avant de l'insérer
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        $stmt->bindParam(':first_name', $data['first_name']);
        $stmt->bindParam(':last_name', $data['last_name']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':phone', $data['phone']);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':role', $data['role']); // Corrected

        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("Erreur lors de l'insertion du patient : " . $e->getMessage());
        return false;
    }
    }

    // Récupérer un patient par son email
    public function getByEmail($email) {
        $query = "SELECT * FROM patients WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Récupérer un patient par son ID
    public function getById($id) {
        $query = "SELECT * FROM patients WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Récupérer les données du compte d'un patient
    public function getPatientAccountData($patientId) {
        $patientData = $this->getById($patientId);

        if ($patientData) {
            unset($patientData['password']); // Ne pas inclure le mot de passe
        }

        return $patientData;
    }

    // Mise à jour des données du patient
    public function update($id, $data) {
        try {
            // Vérification de l'unicité de l'email
            if ($this->getByEmail($data['email']) && $this->getByEmail($data['email'])['id'] !== $id) {
                return false; // L'email est déjà utilisé par un autre patient
            }

            if (isset($_POST['change_password'])) {
                $this->changePassword();
                return;
            }

            $query = "UPDATE patients SET first_name = :first_name, last_name = :last_name, email = :email, phone = :phone WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':first_name', $data['first_name']);
            $stmt->bindParam(':last_name', $data['last_name']);
            $stmt->bindParam(':email', $data['email']);
            $stmt->bindParam(':phone', $data['phone']);
            $stmt->bindParam(':id', $id);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erreur lors de la mise à jour du patient : " . $e->getMessage());
            return false;
        }
    }

    // Authentifier un patient
    public function authenticate($username, $password) {
        $stmt = $this->conn->prepare("SELECT * FROM patients WHERE email = :email");
        $stmt->bindParam(':email', $username);
        $stmt->execute();
        $patient = $stmt->fetch(PDO::FETCH_OBJ);

        if ($patient && password_verify($password, $patient->password)) {
            return $patient; // Retourne l'objet patient
        }

        return false; // Échec de l'authentification
    }

    // Mettre à jour le mot de passe du patient
    public function updatePassword($id, $newPassword) {
        try {
            $query = "UPDATE patients SET password = :password WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            
            // Hacher le nouveau mot de passe avant de l'insérer
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':id', $id);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erreur lors de la mise à jour du mot de passe : " . $e->getMessage());
            return false;
        }
    }

    // Vérifier si le patient est un admin
    public function isAdmin($patient) {
        return $patient['role'] === 'admin';
    }

    // Supprimer un patient
    public function delete($id) {
        if (!$this->getById($id)) {
            return false; // Patient n'existe pas
        }

        $query = "DELETE FROM patients WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function getAppointments($patientId) {
        // Jointure entre les rendez-vous et les services pour récupérer le nom du service
        $query = "
            SELECT a.*, s.name as service_name
            FROM appointments a
            JOIN services s ON a.service_id = s.id
            WHERE a.patient_id = :patient_id
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':patient_id', $patientId);
        $stmt->execute();
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
}