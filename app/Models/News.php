<?php

class News {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO news (title, content, created_at) VALUES (?, ?, NOW())");
        $stmt->execute([$data['title'], $data['content']]);
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
        $stmt = $this->db->prepare("UPDATE news SET title = ?, content = ? WHERE id = ?");
        $stmt->execute([$data['title'], $data['content'], $id]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM news WHERE id = ?");
        $stmt->execute([$id]);
    }
}