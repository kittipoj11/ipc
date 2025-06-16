<?php
// require_once 'config.php';
require_once 'connection_class.php';

class Approval_type extends Connection
{
    public function fetchAll()
    {
        $sql = <<<EOD
                SELECT `approval_type_id`, `approval_type_name` 
                from approval_type 
                where is_deleted = false
                EOD;
        $stmt = $this->myConnect->prepare($sql);
        $stmt->execute();

        // สำหรับใช้ตรวจสอบ SQL Statement เสมือนเป็นการ debug คำสั่ง
        // echo "sql = {$sql}<br>";
        // $stmt->debugDumpParams();
        // exit;

        $rs = $stmt->fetchAll();


        return $rs;
    }

    public function fetchById($id)
    {
        $sql = <<<EOD
                SELECT `approval_type_id`, `approval_type_name` 
                from approval_type
                where approval_type_id = :id
                EOD;

        $stmt = $this->myConnect->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $rs = $stmt->fetch();
        return $rs;
    }

    public function insertData($getData)
    {
        $approval_type_name = $getData['approval_type_name'];

        $sql = "insert into approval_type(approval_type_name) 
                values(:approval_type_name)";
        $stmt = $this->myConnect->prepare($sql);
        $stmt->bindParam(':approval_type_name', $approval_type_name, PDO::PARAM_STR);

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
    public function updateData($getData)
    {
        $approval_type_id = $getData['approval_type_id'];
        $approval_type_name = $getData['approval_type_name'];
        $sql = "update approval_type 
                set approval_type_name = :approval_type_name
                where approval_type_id = :approval_type_id";
        // , update_datetime = CURRENT_TIMESTAMP()
        $stmt = $this->myConnect->prepare($sql);
        $stmt->bindParam(':approval_type_id', $approval_type_id, PDO::PARAM_INT);
        $stmt->bindParam(':approval_type_name', $approval_type_name, PDO::PARAM_STR);

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
    public function deleteData($getData)
    {
        $approval_type_id = $getData['approval_type_id'];
        // $is_active = isset($getData['is_active']) ? 1 : 0;
        $sql = "update approval_type 
                set is_deleted = 1
                where approval_type_id = :approval_type_id";
        $stmt = $this->myConnect->prepare($sql);
        $stmt->bindParam(':approval_type_id', $approval_type_id, PDO::PARAM_INT);

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
        $sql = "select approval_type_id, approval_type_name, is_deleted 
                from approval_type 
                where is_deleted = false";

        $stmt = $this->myConnect->prepare($sql);
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
            $html .=  "<td>{$row['approval_type_id']}</td>";
            $html .=  "<td>{$row['approval_type_name']}</td>";
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