<?php

class Service {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function create($data) {
        if (empty($data['name']) || empty($data['description'])) {
            throw new InvalidArgumentException('Name and description cannot be empty.');
        }

        $stmt = $this->db->prepare("INSERT INTO services (name, description, image) VALUES (?, ?, ?)");
        $stmt->execute([$data['name'], $data['description'], $data['image']]);
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM services");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM services WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $data) {
        if (empty($data['name']) || empty($data['description'])) {
            throw new InvalidArgumentException('Name and description cannot be empty.');
        }

        $stmt = $this->db->prepare("UPDATE services SET name = ?, description = ?, image = ? WHERE id = ?");
        $stmt->execute([$data['name'], $data['description'], $data['image'], $id]);
    }

    public function delete($id) {
        $service = $this->find($id);
    
        if (!$service) {
            throw new InvalidArgumentException("Service introuvable."); 
        }
    
        if (!empty($service['image']) && file_exists($service['image'])) {
            if (!unlink($service['image'])) {
                throw new RuntimeException("Erreur lors de la suppression de l'image.");
            }
        }
    
        $stmt = $this->db->prepare("DELETE FROM services WHERE id = ?");
        $stmt->execute([$id]);
    }    
    
    
}