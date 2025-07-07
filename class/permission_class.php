<?php
// require_once 'config.php';
require_once 'connection_class.php';

class Permission {
    private $db; 
    public function __construct(PDO $pdoConnection)
    {
        $this->db = $pdoConnection;
    }
    public function getAll()
    {
        $sql = <<<EOD
                select `permission_id`, `permission_name`, `menu_name`, `content_filename`, `function_name`, `is_deleted` 
                from permissions 
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

    public function getById($id):?array
    {
        $sql = <<<EOD
                select `permission_id`, `permission_name`, `menu_name`, `content_filename`, `function_name`, `is_deleted` 
                from permissions
                where is_deleted = false
                and permission_id = :id
                EOD;

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $rs = $stmt->fetch();
        
        if (!$rs) {
            return null; // ไม่พบข้อมูล
        }
        return $rs;
    }

    public function create($getData)
    {
        $permission_name = $getData['permission_name'];
        $menu_name = $getData['menu_name'];
        $content_filename = $getData['content_filename'];
        $function_name = $getData['function_name'];

        $sql = "insert into permissions(permission_name, menu_name, content_filename, function_name) 
                values(:permission_name, :menu_name, :content_filename, :function_name)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':permission_name', $permission_name, PDO::PARAM_STR);
        $stmt->bindParam(':menu_name', $menu_name, PDO::PARAM_STR);
        $stmt->bindParam(':content_filename', $content_filename, PDO::PARAM_STR);
        $stmt->bindParam(':function_name', $function_name, PDO::PARAM_STR);

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
        $permission_id = $getData['permission_id'];
        $permission_name = $getData['permission_name'];
        $menu_name = $getData['menu_name'];
        $content_filename = $getData['content_filename'];
        $function_name = $getData['function_name'];
        $sql = "update permissions 
                set permission_name = :permission_name
                , menu_name = :menu_name
                , content_filename = :content_filename
                , function_name = :function_name
                where permission_id = :permission_id";
        // , update_datetime = CURRENT_TIMESTAMP()
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':permission_id', $permission_id, PDO::PARAM_INT);
        $stmt->bindParam(':permission_name', $permission_name, PDO::PARAM_STR);
        $stmt->bindParam(':menu_name', $menu_name, PDO::PARAM_STR);
        $stmt->bindParam(':content_filename', $content_filename, PDO::PARAM_STR);
        $stmt->bindParam(':function_name', $function_name, PDO::PARAM_STR);

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
        $permission_id = $getData['permission_id'];
        // $is_active = isset($getData['is_active']) ? 1 : 0;
        $sql = "update permissions 
                set is_deleted = 1
                where permission_id = :permission_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':permission_id', $permission_id, PDO::PARAM_INT);

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
        $sql = "select `permission_id`, `permission_name`, `menu_name`, `content_filename`, `function_name`, `is_deleted` 
                from permissions 
                where is_deleted = false";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $rs = $stmt->fetchAll();

        $html = "<p>รายงาน Permission ทั้งหมด</p>";

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
        $html .= "<th align='center' bgcolor='F2F2F2'>รหัส Permission </th>";
        $html .= "<th align='center' bgcolor='F2F2F2'> Permission </th>";
        $html .= "</tr>";
        foreach ($rs as $row) :
            $html .=  "<tr bgcolor='#c7c7c7'>";
            $html .=  "<td>{$row['permission_id']}</td>";
            $html .=  "<td>{$row['permission_name']}</td>";
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