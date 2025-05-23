<?php
@session_start();

class Connection
{
    private $host = 'localhost';
    private $port = '3306';
    private $username = 'root';
    private $password = '';
    private $dbname = 'ipc_db';
    // private $dbname = 'inspection_db';

    public $myConnect;

    function __construct()
    {

        try {
            // $this->myConnect = $this->connection();
            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->dbname};charset=utf8mb4";

            $pdoObj = new PDO($dsn, $this->username, $this->password);
            $pdoObj->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdoObj->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $pdoObj->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

            // echo "Connection successfully: ";

            $this->myConnect = $pdoObj;
        } catch (PDOException $e) {
            throw new PDOException("Connection failed: " . $e->getMessage());
            // echo "Connection failed: " . $e->getMessage();
        }
    }

    protected function connection()
    {
        // $dsn1 = 'mysql:host=' . $this->host . '; port=' . $this->port . ';dbname=' . $this->dbname . ';charset=utf8mb4';
        // $_SESSION['dsn1'] = $dsn1;
        $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->dbname};charset=utf8mb4";
        // $_SESSION['dsn2'] = $dsn;

        $pdoObj = new PDO($dsn, $this->username, $this->password);
        $pdoObj->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $pdoObj->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdoObj->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        return $pdoObj;
    }
}

// DataType
// PDO::PARAM_BOOL  ข้อมูล Boolean
// PDO::PARAM_NULL  ข้อมูลค่าว่าง(Null)
// PDO::PARAM_INT   ข้อมูล Integer 
// PDO::PARAM_STR   ข้อมูล String เป็นการบอก PDO ว่าข้อมูลนี้ควรถูกจัดการในรูปแบบ String ซึ่ง PDO จะแปลงให้เหมาะสมกับฐานข้อมูล ถ้าเป็นจำนวนจริงให้ใช้ DataType นี้
// PDO::PARAM_LOB   ข้อมูล Object
// PDO::PARAM_STMT  ข้อมูล Recordset

//fetchAll(): Fetch Style
// PDO::FETCH_NUM   คืนค่าเป็น Array ใช้เลขลำดับของคอลัมน์เป็น Index(คอลัมน์แรกคือ 0)
// PDO::FETCH_ASSOC คืนค่าเป็น Array ใช้ชื่อคอลัมน์เป็น Index
// PDO::FETCH_BOTH  คืนค่าเป็น Array ใช้เลขลำดับของคอลัมน์หรือชื่อคอลัมน์เป็น Index
// PDO::FETCH_OBJ   คืนค่าเป็น Object ใช้ชื่อคอลัมน์เป็นชื่อ Property

// PDOStatement::debugDumpParams() เป็นฟังก์ชันใน PHP Data Objects (PDO) ที่ใช้สำหรับการดีบัก (debugging) prepared statements ในการทำงานกับฐานข้อมูล