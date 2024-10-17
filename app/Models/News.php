<?php

class News {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function create($data) {
        try {
            // Begin a transaction
            $this->db->beginTransaction();

            // Insert the article into the news table
            $stmt = $this->db->prepare("INSERT INTO news (title, content, created_at) VALUES (?, ?, NOW())");
            $stmt->execute([$data['title'], $data['content']]);
            $newsId = $this->db->lastInsertId(); // Get the ID of the created article

            // Commit the transaction
            $this->db->commit();
            return $newsId; // Return the ID of the created news article
        } catch (Exception $e) {
            // Rollback the transaction if an error occurs
            $this->db->rollBack();
            throw new Exception("Erreur lors de la création de l'actualité: " . $e->getMessage());
        }
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

    public function update($id, $data) {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("UPDATE news SET title = ?, content = ? WHERE id = ?");
            $stmt->execute([$data['title'], $data['content'], $id]);

            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();
            throw new Exception("Erreur lors de la mise à jour de l'actualité: " . $e->getMessage());
        }
    }

    public function delete($id) {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("DELETE FROM news WHERE id = ?");
            $stmt->execute([$id]);

            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();
            throw new Exception("Erreur lors de la suppression de l'actualité: " . $e->getMessage());
        }
    }
}