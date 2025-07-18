<?php
// require_once 'config.php';
require_once 'connection_class.php';

class Department {
    private $db; 
    public function __construct(PDO $pdoConnection)
    {
        $this->db = $pdoConnection;
    }
    public function getAll()
    {
        $sql = <<<EOD
                select department_id, department_name, is_deleted 
                from departments 
                where is_deleted = false
                EOD;
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        // สำหรับใช้ตรวจสอบ SQL Statement เสมือนเป็นการ debug คำสั่ง
        // echo "sql = {$sql}<br>";
        // $stmt->debugDumpParams();
        // exit;

        $rs = $stmt->fetchAll();


        return $rs;
    }

    public function getById($id)
    {
        $sql = <<<EOD
                select department_id, department_name, is_deleted 
                from departments
                where is_deleted = false
                and department_id = :id
                EOD;

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $rs = $stmt->fetch();
        return $rs;
    }

    public function create($getData)
    {
        $department_name = $getData['department_name'];

        $sql = "insert into departments(department_name) 
                values(:department_name)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':department_name', $department_name, PDO::PARAM_STR);

        try {
            if ($stmt->execute()) {
                // echo  'Data has been created successfully.';
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                echo  'This item could not be added.Because the data has duplicate values!!!';
            } else {
                echo  'Something is wrong.Can not add data.';
            }
        }
    }
    public function update(int $getId, array $getData)
    {
        $department_id = $getData['department_id'];
        $department_name = $getData['department_name'];
        $sql = "update departments 
                set department_name = :department_name
                where department_id = :department_id";
        // , update_datetime = CURRENT_TIMESTAMP()
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':department_id', $department_id, PDO::PARAM_INT);
        $stmt->bindParam(':department_name', $department_name, PDO::PARAM_STR);

        try {
            if ($stmt->execute()) {
                // echo 'Data has been update successfully.';
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                echo 'This item could not be added.Because the data has duplicate values!!!';
            } else {
                echo 'Something is wrong.Can not add data.';
            }
        }
    }
    public function delete(int $getId)
    {
        $department_id = $getId;
        // $is_active = isset($getData['is_active']) ? 1 : 0;
        $sql = "update departments 
                set is_deleted = 1
                where department_id = :department_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':department_id', $department_id, PDO::PARAM_INT);

        try {
            if ($stmt->execute()) {
                echo 'Data has been delete successfully.';
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                echo 'This item could not be added.Because the data has duplicate values!!!';
            } else {
                echo 'Something is wrong.Can not add data.';
            }
        }
    }

    public function getHtmlData()
    {
        $sql = "select department_id, department_name, is_deleted 
                from departments 
                where is_deleted = false";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $rs = $stmt->fetchAll();

        $html = "<p>รายงาน Location ทั้งหมด</p>";

        // เรียกใช้งาน ฟังก์ชั่นดึงข้อมูลไฟล์มาใช้งาน
        $html .= "<style>";
        $html .= "table, th, td {";
        $html .= "border: 1px solid black;";
        $html .= "border-radius: 10px;";
        $html .= "background-color: #b3ffb3;";
        $html .= "padding: 5px;}";
        $html .= "</style>";
        $html .= "<table cellspacing='0' cellpadding='1' style='width:1100px;'>";
        $html .= "<tr>";
        $html .= "<th align='center' bgcolor='F2F2F2'>รหัส Location </th>";
        $html .= "<th align='center' bgcolor='F2F2F2'> Location </th>";
        $html .= "</tr>";
        foreach ($rs as $row) :
            $html .=  "<tr bgcolor='#c7c7c7'>";
            $html .=  "<td>{$row['department_id']}</td>";
            $html .=  "<td>{$row['department_name']}</td>";
            $html .=  "</tr>";
        endforeach;

        $html .= "</table>";

        return $html;
    }
}


// $stmt = $conn->prepare('select ...where :');
// $stmt->bindParam(':building_id', $building_id, PDO::PARAM_STR);
// $stmt->bindParam(':building_name', $building_name, PDO::PARAM_STR);
// $stmt->bindParam(':is_active', $is_active, PDO::PARAM_BOOL);
// $stmt->execute();
// $rs = $stmt->fetchAll(PDO::FETCH_ASSOC);