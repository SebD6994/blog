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
                   a.appointment_date, 
                   a.status 
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

    // Mettre à jour le statut d'un rendez-vous
    public function updateStatus($id, $status) {
        $query = "UPDATE appointments SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
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

    // Récupérer les créneaux horaires disponibles pour une date donnée
    public function getAvailableTimeSlots($today) {
        $startTime = new DateTime("$today 09:00");
        $endTime = new DateTime("$today 16:40");
        $interval = new DateInterval('PT20M'); // Créneaux de 20 minutes
        $timeSlots = [
            'booked' => [],
            'available' => []
        ];
        
        // Récupérer les rendez-vous déjà pris pour cette date
        $query = "SELECT appointment_date FROM appointments WHERE DATE(appointment_date) = :date";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':date', $today);
        $stmt->execute();
        $bookedAppointments = $stmt->fetchAll(PDO::FETCH_COLUMN); // Récupérer uniquement les dates de rendez-vous
        
        // Créer une liste de créneaux horaires disponibles
        while ($startTime <= $endTime) {
            $slot = $startTime->format('H:i');
            // Combiner la date et le slot pour la vérification
            $combinedSlot = "$today $slot:00"; // Ajoutez les secondes pour correspondre à l'heure
            
            // Vérifier si le créneau est réservé
            if (in_array($combinedSlot, $bookedAppointments)) {
                $timeSlots['booked'][] = $slot; // Ajouter le créneau réservé
            } else {
                $timeSlots['available'][] = $slot; // Ajouter le créneau disponible
            }
            
            $startTime->add($interval); // Ajouter 20 minutes
        }
        
        return $timeSlots; // Retourner les créneaux avec une structure contenant les réservés et les disponibles
    }
}
?>