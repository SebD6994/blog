<?php

class Home {
    private $dbConnection;
    private $serviceModel;

    public function __construct($dbConnection) {
        $this->dbConnection = $dbConnection;
        $this->db = $dbConnection;
        $this->serviceModel = new Service($dbConnection); // Créer une instance du modèle Service
    }

    // Récupérer tous les services via le modèle Service
    public function getServices() {
        return $this->serviceModel->getAll(); // Appeler la méthode getAll du modèle Service
    }

    // Récupérer tous les horaires d'ouverture
    public function getOpeningHours() {
        $stmt = $this->dbConnection->prepare("SELECT * FROM opening_hours ORDER BY day_of_week ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Mettre à jour les horaires d'ouverture
    public function updateOpeningHours($data) {
        // Boucle à travers chaque jour et met à jour les horaires
        foreach ($data['hours'] as $dayOfWeek => $hours) {
            $startTime = isset($hours['start_time']) ? $hours['start_time'] : null;
            $endTime = isset($hours['end_time']) ? $hours['end_time'] : null;

            // Préparez la requête pour mettre à jour les horaires d'ouverture pour chaque jour
            $stmt = $this->dbConnection->prepare("UPDATE opening_hours SET start_time = ?, end_time = ? WHERE day_of_week = ?");
            $stmt->execute([$startTime, $endTime, $dayOfWeek]);
        }
    }

        // Generate 20-minute time slots based on opening hours
        public function generateTimeSlots($day) {
            $stmt = $this->dbConnection->prepare("SELECT * FROM opening_hours WHERE day_of_week = :day");
            $stmt->bindParam(':day', $day);
            $stmt->execute();
            $hours = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($hours) {
                $startTime = new DateTime($hours['start_time']);
                $endTime = new DateTime($hours['end_time']);
                $timeSlots = [];
    
                // Create time slots
                while ($startTime < $endTime) {
                    $timeSlots[] = $startTime->format('H:i');
                    $startTime->modify('+20 minutes'); // Increment by 20 minutes
                }
    
                return $timeSlots;
            }
    
            return []; // Return an empty array if no hours found
        }

    // Récupérer l'image de la bannière depuis la table settings
    public function getBannerImage() {
        $query = $this->db->prepare("SELECT image_path FROM settings WHERE description = 'banner_image'");
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['image_path'] : null;
    }

    public function updateBanner($imagePath, $description) {
        try {
            // Démarrer une transaction
            $this->db->beginTransaction();
            
            // Créer une nouvelle entrée pour la bannière
            $stmtInsert = $this->db->prepare("INSERT INTO settings (description, image_path) VALUES (:description, :image_path)");
            $stmtInsert->bindParam(':description', $description);
            $stmtInsert->bindParam(':image_path', $imagePath);
            $stmtInsert->execute();
    
            // Récupérer l'ID de l'ancienne entrée à supprimer
            $stmtSelect = $this->db->prepare("SELECT id FROM settings WHERE description = 'banner_image'");
            $stmtSelect->execute();
            $oldEntry = $stmtSelect->fetch(PDO::FETCH_ASSOC);
    
            // Si une ancienne entrée existe, la supprimer
            if ($oldEntry) {
                $stmtDelete = $this->db->prepare("DELETE FROM settings WHERE id = :id");
                $stmtDelete->bindParam(':id', $oldEntry['id']);
                $stmtDelete->execute();
            }
    
            // Valider la transaction
            $this->db->commit();
        } catch (Exception $e) {
            // Annuler la transaction en cas d'erreur
            $this->db->rollBack();
            throw $e; // Relancer l'exception pour gestion ultérieure
        }
    }
    

    // Méthode pour gérer les images de la clinique
    public function getClinicImages() {
        // Sélectionnez également l'id de l'image
        $query = $this->db->prepare("SELECT id, image_path, description FROM clinic_images ORDER BY id ASC");
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC); // Récupérer toutes les lignes sous forme de tableau associatif
    }
    
        // Ajouter une nouvelle image de la clinique
    public function addClinicImage($imagePath, $description = null) {
        $query = $this->db->prepare("INSERT INTO clinic_images (image_path, description) VALUES (:image_path, :description)");
        $query->bindParam(':image_path', $imagePath);
        $query->bindParam(':description', $description);
        return $query->execute();
    }

    public function updateClinicImage($id, $imagePath, $description) {
        $stmt = $this->db->prepare("UPDATE clinic_images SET image_path = :image_path, description = :description WHERE id = :id");
        $stmt->bindParam(':image_path', $imagePath);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }

    // Supprimer une image de la clinique
    public function deleteClinicImage($imageId) {
        $query = $this->db->prepare("DELETE FROM clinic_images WHERE id = :id");
        $query->bindParam(':id', $imageId);
        return $query->execute();
    }
}