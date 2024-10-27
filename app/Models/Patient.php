<?php

class Patient {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $query = "SELECT * FROM patients";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function searchPatients($search) {
        $query = "SELECT * FROM patients WHERE first_name LIKE :search OR last_name LIKE :search OR email LIKE :search";
        $stmt = $this->conn->prepare($query);
        $searchTerm = '%' . $search . '%';
        $stmt->bindParam(':search', $searchTerm);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        try {
            if ($this->getByEmail($data['email'])) {
                return false;
            }

            $query = "INSERT INTO patients (first_name, last_name, email, phone, password, role) VALUES (:first_name, :last_name, :email, :phone, :password, :role)";
            $stmt = $this->conn->prepare($query);
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            $stmt->bindParam(':first_name', $data['first_name']);
            $stmt->bindParam(':last_name', $data['last_name']);
            $stmt->bindParam(':email', $data['email']);
            $stmt->bindParam(':phone', $data['phone']);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':role', $data['role']);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erreur lors de l'insertion du patient : " . $e->getMessage());
            return false;
        }
    }

    public function getByEmail($email) {
        $query = "SELECT * FROM patients WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT * FROM patients WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getPatientAccountData($patientId) {
        $patientData = $this->getById($patientId);

        if ($patientData) {
            unset($patientData['password']);
        }

        return $patientData;
    }

    public function update($id, $data) {
        try {
            if ($this->getByEmail($data['email']) && $this->getByEmail($data['email'])['id'] !== $id) {
                return false;
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

    public function authenticate($username, $password) {
        $stmt = $this->conn->prepare("SELECT * FROM patients WHERE email = :email");
        $stmt->bindParam(':email', $username);
        $stmt->execute();
        $patient = $stmt->fetch(PDO::FETCH_OBJ);

        if ($patient && password_verify($password, $patient->password)) {
            return $patient;
        }

        return false;
    }

    public function isAdmin($patient) {
        return $patient['role'] === 'admin';
    }

    public function delete($id) {
        if (!$this->getById($id)) {
            return false;
        }

        $query = "DELETE FROM patients WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function getAppointments($patientId) {
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