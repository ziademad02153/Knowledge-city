<?php

class Database {
    private $host = 'database.cc.localhost';
    private $db_name = 'course_catalog';
    private $username = 'test_user';
    private $password = 'test_password';
    private $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            throw new Exception("Connection error: " . $e->getMessage());
        }

        return $this->conn;
    }
}
