<?php
// require_once 'config.php';
require_once 'connection_class.php';

class Po_status extends Connection
{
    public function fetchAll()
    {
        $sql = <<<EOD
                select po_status_id, po_status_name, is_deleted 
                from po_status 
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
                select po_status_id, po_status_name, is_deleted 
                from po_status
                where is_deleted = false
                and po_status_id = :id
                EOD;

        $stmt = $this->myConnect->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $rs = $stmt->fetch();
        return $rs;
    }

    public function insertData($getData)
    {
        $po_status_name = $getData['po_status_name'];

        $sql = "insert into po_status(po_status_name) 
                values(:po_status_name)";
        $stmt = $this->myConnect->prepare($sql);
        $stmt->bindParam(':po_status_name', $po_status_name, PDO::PARAM_STR);

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
        $po_status_id = $getData['po_status_id'];
        $po_status_name = $getData['po_status_name'];
        $sql = "update po_status 
                set po_status_name = :po_status_name
                where po_status_id = :po_status_id";
        // , update_datetime = CURRENT_TIMESTAMP()
        $stmt = $this->myConnect->prepare($sql);
        $stmt->bindParam(':po_status_id', $po_status_id, PDO::PARAM_INT);
        $stmt->bindParam(':po_status_name', $po_status_name, PDO::PARAM_STR);

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
        $po_status_id = $getData['po_status_id'];
        // $is_active = isset($getData['is_active']) ? 1 : 0;
        $sql = "update po_status 
                set is_deleted = 1
                where po_status_id = :po_status_id";
        $stmt = $this->myConnect->prepare($sql);
        $stmt->bindParam(':po_status_id', $po_status_id, PDO::PARAM_INT);

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
        $sql = "select po_status_id, po_status_name, is_deleted 
                from po_status 
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
            $html .=  "<td>{$row['po_status_id']}</td>";
            $html .=  "<td>{$row['po_status_name']}</td>";
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