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

    /**
     * @var PDO|null ตัวแปรสำหรับเก็บ object PDO connection
     */
    public $myConnect;

    /**
     * Constructor ของคลาส
     * จะทำงานอัตโนมัติเมื่อมีการสร้าง object ใหม่ (new Connection())
     * เพื่อเชื่อมต่อกับฐานข้อมูลทันที
     */
    // __construct ไม่จำเป็นต้อง return ค่าใดๆ หน้าที่ของมันคือการกำหนดค่าเริ่มต้นให้กับ object
    function __construct()
    {
        $this->myConnect = null;
        try {
            // $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->dbname};charset=utf8mb4";
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4";

            // สร้าง PDO object และเก็บไว้ใน property ของคลาส
            $pdoObj = new PDO($dsn, $this->username, $this->password);
            $pdoObj->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdoObj->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $pdoObj->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

            $this->myConnect = $pdoObj;
        } catch (PDOException $e) {
            // หากการเชื่อมต่อล้มเหลว ให้โยน Exception ออกไป
            // เพื่อให้โค้ดที่เรียกใช้สามารถดักจับ Error ได้
            throw new PDOException("Connection failed: " . $e->getMessage());
        }
    }

    /**
     * ฟังก์ชันสำหรับดึง PDO connection object (แนะนำให้ใช้)
     * @return PDO|null
     */
    // เป็นวิธีที่ดีในการเข้าถึง connection object แทนที่จะเข้าถึง property $myConnect โดยตรง (หลักการ Encapsulation)
    public function getDbConnection()
    {
        return $this->myConnect;
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