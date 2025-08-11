<?php
@session_start();

class Connection
{
    private $host = 'localhost';
    private $port = '3306';
    private $username = 'root';
    private $password = '';
    private $dbname = 'doc_approval2_db'; // <-- แก้ชื่อฐานข้อมูลของคุณที่นี่

    public $myConnect;

    function __construct()
    {
        $this->myConnect = null;
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4";

            $pdoObj = new PDO($dsn, $this->username, $this->password);
            $pdoObj->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdoObj->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $pdoObj->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

            $this->myConnect = $pdoObj;
        } catch (PDOException $e) {
            throw new PDOException("Connection failed: " . $e->getMessage());
        }
    }

    public function getDbConnection()
    {
        return $this->myConnect;
    }
}