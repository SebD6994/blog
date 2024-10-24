<?php

class Appointment {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getConnection() {
        return $this->conn;
    }

    // Récupérer tous les rendez-vous, triés par date
    public function getAll() {
        $query = "
            SELECT a.id, 
                   CONCAT(p.first_name, ' ', p.last_name) AS patient_name, 
                   s.name AS service_name, 
                   a.appointment_date
            FROM appointments a
            LEFT JOIN patients p ON a.patient_id = p.id
            LEFT JOIN services s ON a.service_id = s.id
            ORDER BY a.appointment_date ASC  -- Tri par date croissante
        ";

        $stmt = $this->conn->query($query);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : []; // Gestion des erreurs
    }

    // Créer un nouveau rendez-vous
    public function create($data) {
        $appointmentDateTime = new DateTime($data['appointment_date'] . ' ' . $data['appointment_time']);
        $now = new DateTime();
    
        // Vérifier si la date du rendez-vous est dans le futur
        if ($appointmentDateTime <= $now) {
            throw new Exception("La date et l'heure du rendez-vous doivent être dans le futur.");
        }

        $query = "
            INSERT INTO appointments (patient_id, appointment_date, service_id) 
            VALUES (:patient_id, :appointment_date, :service_id)
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':patient_id', $data['patient_id']);
        $stmt->bindParam(':appointment_date', $appointmentDateTime->format('Y-m-d H:i:s'));
        $stmt->bindParam(':service_id', $data['service_id']);
        
        // Exécution de la requête
        if (!$stmt->execute()) {
            throw new Exception("Erreur lors de la création du rendez-vous.");
        }
        return true; // Confirmation de la création
    }

    // Mettre à jour un rendez-vous
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

    // Supprimer un rendez-vous
    public function delete($id) {
        $query = "DELETE FROM appointments WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // Récupérer les rendez-vous d'un patient par son ID, triés par date
    public function getAppointmentsByPatientId($patientId) {
        $query = "
            SELECT a.id, 
                   a.appointment_date, 
                   a.service_id, 
                   s.name AS service_name 
            FROM appointments a
            LEFT JOIN services s ON a.service_id = s.id
            WHERE a.patient_id = :patient_id
            ORDER BY a.appointment_date ASC  -- Tri par date croissante
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':patient_id', $patientId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer un rendez-vous par son ID
    public function getById($id) {
        $query = "SELECT * FROM appointments WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getTimeSlots($day) {
        $stmt = $this->conn->prepare("SELECT slot_start FROM time_slots WHERE day_of_week = :day");
        $stmt->bindParam(':day', $day);
        $stmt->execute();
        
        // Commencer à construire le HTML des options
        $options = '<option value="">Sélectionnez un créneau</option>'; // Option par défaut
        
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $slot) {
            $options .= '<option value="' . htmlspecialchars($slot['slot_start']) . '">' . htmlspecialchars($slot['slot_start']) . '</option>';
        }
        
        return $options; // Retourner le HTML complet
    }
    
    
    }
?>