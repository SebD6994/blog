<?php

class Service {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function create($data) {
        // Validate that 'name' and 'description' are not empty
        if (empty($data['name']) || empty($data['description'])) {
            throw new InvalidArgumentException('Name and description cannot be empty.');
        }

        $stmt = $this->db->prepare("INSERT INTO services (name, description) VALUES (?, ?)");
        $stmt->execute([$data['name'], $data['description']]);
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
        // Validate that 'name' and 'description' are not empty
        if (empty($data['name']) || empty($data['description'])) {
            throw new InvalidArgumentException('Name and description cannot be empty.');
        }

        $stmt = $this->db->prepare("UPDATE services SET name = ?, description = ? WHERE id = ?");
        $stmt->execute([$data['name'], $data['description'], $id]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM services WHERE id = ?");
        $stmt->execute([$id]);
    }
}