<?php
class Database {
    private static $instance = null;
    private $connection;

    private $host = 'localhost';
    private $db_name = 'your_db'; // **เปลี่ยนเป็นชื่อฐานข้อมูลของคุณ**
    private $username = 'your_user'; // **เปลี่ยนเป็น username ของคุณ**
    private $password = 'your_pass'; // **เปลี่ยนเป็น password ของคุณ**

    private function __construct() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4";
            $this->connection = new PDO($dsn, $this->username, $this->password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            die("Connection error: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }
}