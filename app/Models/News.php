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
        $stmt = $this->db->prepare("SELECT * FROM news WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        if (empty($data['title']) || empty($data['content'])) {
            throw new InvalidArgumentException('Le titre et le contenu ne peuvent pas être vides.');
        }

        try {
            // Début d'une transaction
            $this->db->beginTransaction();

            // Prépare et exécute la requête pour insérer une nouvelle actualité
            $stmt = $this->db->prepare("INSERT INTO news (title, content, image_path) VALUES (?, ?, ?)");
            $stmt->execute([$data['title'], $data['content'], $data['image_path']]); // L'image est ajoutée ici si disponible

            // Commit de la transaction
            $this->db->commit();
        } catch (Exception $e) {
            // Rollback si une erreur survient
            $this->db->rollBack();
            throw new Exception("Erreur lors de la création de l'actualité: " . $e->getMessage());
        }
    }
    
    private function handleImageUpload($file) {
        // Définir le dossier où les images seront stockées
        $targetDir = "../assets/images/news/";
        $targetFile = $targetDir . basename($file['name']);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    
        // Vérifiez si le fichier est une image réelle ou une fausse image
        $check = getimagesize($file['tmp_name']);
        if ($check === false) {
            throw new Exception("Le fichier téléchargé n'est pas une image.");
        }
    
        // Vérifiez la taille du fichier (ex : max 2Mo)
        if ($file['size'] > 2000000) {
            throw new Exception("Désolé, votre fichier est trop grand.");
        }
    
        // Autoriser certains formats de fichiers
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($imageFileType, $allowedTypes)) {
            throw new Exception("Désolé, seuls les fichiers JPG, JPEG, PNG & GIF sont autorisés.");
        }
    
        // Essayez de déplacer le fichier téléchargé
        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            return $targetFile; // Retourne le chemin de l'image
        } else {
            throw new Exception("Désolé, une erreur est survenue lors du téléchargement de votre fichier.");
        }
    }
    

    public function update($id, $data) {
        if (empty($data['title']) || empty($data['content'])) {
            throw new InvalidArgumentException('Title and content cannot be empty.');
        }
    
        try {
            // Begin a transaction
            $this->db->beginTransaction();
    
            // Update the article with the new title, content, and image path
            $stmt = $this->db->prepare("UPDATE news SET title = ?, content = ?, image_path = ? WHERE id = ?");
            $stmt->execute([$data['title'], $data['content'], $data['image_path'], $id]); // Ensure image_path is used
    
            // Commit the transaction
            $this->db->commit();
        } catch (Exception $e) {
            // Rollback the transaction if an error occurs
            $this->db->rollBack();
            throw new Exception("Erreur lors de la mise à jour de l'actualité: " . $e->getMessage());
        }
    }
    

    public function delete($id) {
        try {
            // Begin a transaction
            $this->db->beginTransaction();
    
            // Retrieve the news item to get the image path
            $news = $this->find($id);
    
            if (!$news) {
                throw new InvalidArgumentException("Actualité introuvable.");
            }
    
            // Check if the image exists and delete it
            if (!empty($news['image_path']) && file_exists($news['image_path'])) {
                if (!unlink($news['image_path'])) {
                    throw new RuntimeException("Erreur lors de la suppression de l'image.");
                }
            }
    
            // Delete the news article
            $stmt = $this->db->prepare("DELETE FROM news WHERE id = ?");
            $stmt->execute([$id]);
    
            // Commit the transaction
            $this->db->commit();
        } catch (Exception $e) {
            // Rollback the transaction if an error occurs
            $this->db->rollBack();
            throw new Exception("Erreur lors de la suppression de l'actualité: " . $e->getMessage());
        }
    }
}