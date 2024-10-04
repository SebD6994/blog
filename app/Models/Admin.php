<?php
class Admin {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Récupère tous les rendez-vous avec les informations du patient et du service
    public function getAppointments() {
        $stmt = $this->db->query("
            SELECT a.id, 
                   CONCAT(p.first_name, ' ', p.last_name) AS patient_name, 
                   s.name AS service_name, 
                   a.appointment_date, 
                   a.status 
            FROM appointments a
            LEFT JOIN patients p ON a.patient_id = p.id
            LEFT JOIN services s ON a.service_id = s.id
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStatus() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['appointment_id'])) {
            $appointmentId = $_POST['appointment_id'];
            $status = $_POST['status'];
    
            // Mettre à jour le statut du rendez-vous
            if ($this->adminModel->updateAppointmentStatus($appointmentId, $status)) {
                $_SESSION['message'] = "Statut du rendez-vous mis à jour avec succès.";
            } else {
                $_SESSION['message'] = "Erreur lors de la mise à jour du statut.";
            }
    
            // Redirigez vers la page d'administration
            header('Location: index.php?page=admin'); // Ou la page d'origine
            exit;
        }
    }

    // Récupère tous les patients
    public function getPatients() {
        $stmt = $this->db->query("SELECT * FROM patients");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
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
        
        // Définir le rôle par défaut à "patient"
        $role = 'patient';
        $stmt->bindParam(':role', $role);
        
        return $stmt->execute();
    }

    // Supprime un patient
    public function deletePatient($id) {
        $stmt = $this->db->prepare("DELETE FROM patients WHERE id = ?");
        $stmt->execute([$id]);
    }

    // Récupère tous les services
    public function getServices()
    {
        $stmt = $this->db->prepare("SELECT id, name, description FROM services"); // Ajout de description
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Ajoute un service
    public function addService($data) {
        $stmt = $this->db->prepare("INSERT INTO services (name, description) VALUES (?, ?, ?)");
        $stmt->execute([$data['name'], $data['description']]);
    }

    // Met à jour un service
    public function updateService($id, $data) {
        $stmt = $this->db->prepare("UPDATE services SET name = ?, description = ? WHERE id = ?"); // Retirer price car il n'existe pas
        $stmt->execute([$data['name'], $data['description'], $id]); // Passer tous les paramètres nécessaires
    }


    // Supprime un service
    public function deleteService($id) {
        $stmt = $this->db->prepare("DELETE FROM services WHERE id = ?");
        $stmt->execute([$id]);
    }

    // Récupère toutes les actualités
    public function getNews() {
        $stmt = $this->db->query("SELECT * FROM news");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Ajoute une actualité
    public function addNews($data) {
        $stmt = $this->db->prepare("INSERT INTO news (title, content) VALUES (?, ?)");
        $stmt->execute([$data['title'], $data['content']]);
    }

    // Met à jour une actualité
    public function updateNews($id, $data) {
        $stmt = $this->db->prepare("UPDATE news SET title = ?, content = ? WHERE id = ?");
        $stmt->execute([$data['title'], $data['content'], $id]);
    }

    // Supprime une actualité
    public function deleteNews($id) {
        $stmt = $this->db->prepare("DELETE FROM news WHERE id = ?");
        $stmt->execute([$id]);
    }

    // Récupère les horaires d'ouverture
    public function getOpeningHours() {
        $stmt = $this->db->query("SELECT * FROM opening_hours");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Met à jour les horaires d'ouverture
    public function updateOpeningHours($data) {
        $stmt = $this->db->prepare("UPDATE opening_hours SET start_time = ?, end_time = ? WHERE id = 1");
        $stmt->execute([$data['start_time'], $data['end_time']]);
    }
}