<?php

class Appointment {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getConnection() {
        return $this->conn;
    }

    public function getAll() {
        $query = "
            SELECT a.id, 
                   CONCAT(p.first_name, ' ', p.last_name) AS patient_name, 
                   s.name AS service_name, 
                   a.appointment_date
            FROM appointments a
            LEFT JOIN patients p ON a.patient_id = p.id
            LEFT JOIN services s ON a.service_id = s.id
            ORDER BY a.appointment_date ASC
        ";

        $stmt = $this->conn->query($query);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    public function create($data) {
        // Crée un objet DateTime en combinant la date et le time slot
        try {
            $appointmentDateTime = new DateTime($data['appointment_date'] . ' ' . $data['appointment_time']);
        } catch (Exception $e) {
            throw new Exception("La date ou l'heure du rendez-vous est invalide.");
        }
    
        $now = new DateTime();
        
        // Vérifie si la date et l'heure du rendez-vous sont dans le futur
        if ($appointmentDateTime <= $now) {
            throw new Exception("La date et l'heure du rendez-vous doivent être dans le futur.");
        }
    
        // Prépare et exécute la requête d'insertion
        $query = "
            INSERT INTO appointments (patient_id, appointment_date, service_id) 
            VALUES (:patient_id, :appointment_date, :service_id)
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':patient_id', $data['patient_id']);
        $stmt->bindParam(':appointment_date', $appointmentDateTime->format('Y-m-d H:i:s'));
        $stmt->bindParam(':service_id', $data['service_id']);
        
        if (!$stmt->execute()) {
            throw new Exception("Erreur lors de la création du rendez-vous.");
        }
        
        return true;
    }
    
    

    public function update($id, $data) {
        $query = "
            UPDATE appointments 
            SET appointment_date = :appointment_date, 
                service_id = :service_id 
            WHERE id = :id
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':appointment_date', $data['appointment_date']);
        $stmt->bindParam(':service_id', $data['service_id']);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM appointments WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function getAppointmentsByPatientId($patientId) {
        $query = "
            SELECT a.id, 
                   a.appointment_date, 
                   a.service_id, 
                   s.name AS service_name 
            FROM appointments a
            LEFT JOIN services s ON a.service_id = s.id
            WHERE a.patient_id = :patient_id
            ORDER BY a.appointment_date ASC
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':patient_id', $patientId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT * FROM appointments WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getTimeSlots() {
        $stmt = $this->conn->prepare("SELECT slot_start FROM time_slots");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    }
}
?>