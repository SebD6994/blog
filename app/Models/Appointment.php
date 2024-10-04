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
        $query = "SELECT * FROM appointments";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $query = "INSERT INTO appointments (patient_id, appointment_date, service_id) VALUES (:patient_id, :appointment_date, :service_id)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':patient_id', $data['patient_id']);
        $stmt->bindParam(':appointment_date', $data['appointment_date']);
        $stmt->bindParam(':service_id', $data['service_id']);
        return $stmt->execute();
    }

    public function update($id, $data) {
        $query = "UPDATE appointments SET appointment_date = :appointment_date, service_id = :service_id WHERE id = :id";
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

    // Récupérer les rendez-vous d'un patient par son ID
    public function getAppointmentsByPatientId($patientId) {
        $query = $this->conn->prepare("SELECT a.id, 
                                               a.appointment_date, 
                                               a.service_id, 
                                               s.name AS service_name 
                                        FROM appointments a
                                        LEFT JOIN services s ON a.service_id = s.id
                                        WHERE a.patient_id = :patient_id");
        $query->bindParam(':patient_id', $patientId);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer un rendez-vous par son ID
    public function getById($id) {
        $query = $this->conn->prepare("SELECT * FROM appointments WHERE id = :id");
        $query->bindParam(':id', $id);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }
}