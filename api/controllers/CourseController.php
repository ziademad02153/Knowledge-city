<?php

class CourseController {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }

    public function getAll($categoryId = null) {
        $query = "SELECT 
            c.*,
            cat.name as category_name
        FROM courses c
        JOIN categories cat ON c.category_id = cat.id";
        
        if ($categoryId) {
            $query .= " WHERE c.category_id = :category_id";
        }

        $stmt = $this->conn->prepare($query);
        
        if ($categoryId) {
            $stmt->bindParam(":category_id", $categoryId);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT 
            c.*,
            cat.name as category_name
        FROM courses c
        JOIN categories cat ON c.category_id = cat.id
        WHERE c.id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
