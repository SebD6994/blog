<?php

class Home {
    private $dbConnection;
    private $serviceModel;
    private $db;

    public function __construct($dbConnection) {
        $this->dbConnection = $dbConnection;
        $this->db = $dbConnection;
        $this->serviceModel = new Service($dbConnection);
    }

    public function getServices() {
        return $this->serviceModel->getAll();
    }

    public function getOpeningHours() {
        $stmt = $this->dbConnection->prepare("SELECT * FROM opening_hours ORDER BY day_of_week ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateOpeningHours($data) {
        foreach ($data['hours'] as $dayOfWeek => $hours) {
            $startTime = isset($hours['start_time']) ? $hours['start_time'] : null;
            $endTime = isset($hours['end_time']) ? $hours['end_time'] : null;

            $stmt = $this->dbConnection->prepare("UPDATE opening_hours SET start_time = ?, end_time = ? WHERE day_of_week = ?");
            $stmt->execute([$startTime, $endTime, $dayOfWeek]);
        }
    }    

    public function getBannerImage() {
        $query = $this->db->prepare("SELECT image_path FROM settings WHERE description = 'banner_image'");
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['image_path'] : null;
    }
    
    public function updateBanner($imagePath, $description) {
        try {
            $this->db->beginTransaction();
            
            $stmtInsert = $this->db->prepare("INSERT INTO settings (description, image_path) VALUES (:description, :image_path)");
            $stmtInsert->bindParam(':description', $description);
            $stmtInsert->bindParam(':image_path', $imagePath);
            $stmtInsert->execute();
    
            $stmtSelect = $this->db->prepare("SELECT id FROM settings WHERE description = 'banner_image'");
            $stmtSelect->execute();
            $oldEntry = $stmtSelect->fetch(PDO::FETCH_ASSOC);
    
            if ($oldEntry) {
                $stmtDelete = $this->db->prepare("DELETE FROM settings WHERE id = :id");
                $stmtDelete->bindParam(':id', $oldEntry['id']);
                $stmtDelete->execute();
            }
    
            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
    
    public function getApropos() {
        $stmt = $this->db->prepare("SELECT id, description FROM apropos ORDER BY created_at DESC LIMIT 1");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result : null; 
    }

    public function updateApropos($id, $newDescription) {
        $stmt = $this->db->prepare("UPDATE apropos SET description = :description WHERE id = :id");
        $stmt->bindParam(':description', $newDescription);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getClinicImages() {
        $query = $this->db->prepare("SELECT id, image_path, description FROM clinic_images ORDER BY id ASC");
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function addClinicImage($imagePath, $description = null) {
        $query = $this->db->prepare("INSERT INTO clinic_images (image_path, description) VALUES (:image_path, :description)");
        $query->bindParam(':image_path', $imagePath);
        $query->bindParam(':description', $description);
        return $query->execute();
    }
    
    public function updateClinicImage($id, $description, $imagePath = null) {
        if ($imagePath) {
            $stmt = $this->db->prepare("UPDATE clinic_images SET image_path = :image_path, description = :description WHERE id = :id");
            $stmt->bindParam(':image_path', $imagePath);
        } else {
            $stmt = $this->db->prepare("UPDATE clinic_images SET description = :description WHERE id = :id");
        }
        
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }    
    
    public function deleteClinicImage($imageId) {
        $query = $this->db->prepare("DELETE FROM clinic_images WHERE id = :id");
        $query->bindParam(':id', $imageId);
        return $query->execute();
    }
}