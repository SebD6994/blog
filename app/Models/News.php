<?php

class News {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM news");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM news WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        if (empty($data['title']) || empty($data['content'])) {
            throw new InvalidArgumentException('Le titre et le contenu ne peuvent pas être vides.');
        }

        try {
            $this->db->beginTransaction();
            $stmt = $this->db->prepare("INSERT INTO news (title, content, image_path) VALUES (?, ?, ?)");
            $stmt->execute([$data['title'], $data['content'], $data['image_path']]);
            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();
            throw new Exception("Erreur lors de la création de l'actualité: " . $e->getMessage());
        }
    }

    public function update($id, $data) {
        if (empty($data['title']) || empty($data['content'])) {
            throw new InvalidArgumentException('Title and content cannot be empty.');
        }
    
        try {
            $this->db->beginTransaction();
            $stmt = $this->db->prepare("UPDATE news SET title = ?, content = ?, image_path = ? WHERE id = ?");
            $stmt->execute([$data['title'], $data['content'], $data['image_path'], $id]);
            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();
            throw new Exception("Erreur lors de la mise à jour de l'actualité: " . $e->getMessage());
        }
    }
    
    public function delete($id) {
        try {
            $this->db->beginTransaction();
            $news = $this->find($id);
    
            if (!$news) {
                throw new InvalidArgumentException("Actualité introuvable.");
            }
    
            if (!empty($news['image_path']) && file_exists($news['image_path'])) {
                if (!unlink($news['image_path'])) {
                    throw new RuntimeException("Erreur lors de la suppression de l'image.");
                }
            }
    
            $stmt = $this->db->prepare("DELETE FROM news WHERE id = ?");
            $stmt->execute([$id]);
            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();
            throw new Exception("Erreur lors de la suppression de l'actualité: " . $e->getMessage());
        }
    }

    private function handleImageUpload($file) {
        $targetDir = "../assets/images/news/";
        $targetFile = $targetDir . basename($file['name']);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    
        $check = getimagesize($file['tmp_name']);
        if ($check === false) {
            throw new Exception("Le fichier téléchargé n'est pas une image.");
        }
    
        if ($file['size'] > 2000000) {
            throw new Exception("Désolé, votre fichier est trop grand.");
        }
    
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($imageFileType, $allowedTypes)) {
            throw new Exception("Désolé, seuls les fichiers JPG, JPEG, PNG & GIF sont autorisés.");
        }
    
        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            return $targetFile;
        } else {
            throw new Exception("Désolé, une erreur est survenue lors du téléchargement de votre fichier.");
        }
    }
}