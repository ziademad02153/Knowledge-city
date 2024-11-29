<?php

class CategoryController {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }

    public function getAll() {
        $query = "WITH RECURSIVE category_tree AS (
            SELECT 
                c.*, 
                0 as level,
                CAST(id as CHAR(255)) as path
            FROM categories c
            WHERE parent_id IS NULL
            
            UNION ALL
            
            SELECT 
                c.*, 
                ct.level + 1,
                CONCAT(ct.path, ',', c.id)
            FROM categories c
            INNER JOIN category_tree ct ON c.parent_id = ct.id
            WHERE ct.level < 3
        )
        SELECT 
            ct.*,
            (
                SELECT COUNT(DISTINCT co.id)
                FROM courses co
                JOIN category_tree sub ON co.category_id = sub.id
                WHERE FIND_IN_SET(ct.id, sub.path) > 0
            ) as course_count
        FROM category_tree ct
        ORDER BY path;";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT c.*, 
            (
                SELECT COUNT(co.id) 
                FROM courses co 
                WHERE co.category_id = c.id
            ) as course_count
        FROM categories c
        WHERE c.id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
