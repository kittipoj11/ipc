<?php
// class/connection_class.php

class Connection
{
    private $host = 'localhost';
    private $port = '3306';
    private $username = 'root';
    private $password = '';
    private $dbname = 'test_db'; // ★★★ กรุณาเปลี่ยนเป็นชื่อฐานข้อมูลของคุณ ★★★
    
    private $pdo;

    public function __construct()
    {
        try {
            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->dbname};charset=utf8mb4";
            $this->pdo = new PDO($dsn, $this->username, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // ในระบบงานจริง ควรจะ log error แทนการ die
            die("Connection failed: " . $e->getMessage());
        }
    }

    public function getDbConnection(): PDO
    {
        return $this->pdo;
    }
}