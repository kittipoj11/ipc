<?php
// require_once 'config.php';
require_once 'connection_class.php';

class Workflows extends Connection
{
    public function getRecordAll()
    {
        $sql = <<<EOD
                select workflow_id, workflow_name, is_deleted 
                from workflows 
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

    public function getRecordById($id)
    {
        $sql = <<<EOD
                select workflow_id, workflow_name, is_deleted 
                from workflows
                where is_deleted = false
                and workflow_id = :id
                EOD;

        $stmt = $this->myConnect->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $rs = $stmt->fetch();
        return $rs;
    }

    public function insertData($getData)
    {
        $workflow_name = $getData['workflow_name'];

        $sql = "insert into workflows(workflow_name) 
                values(:workflow_name)";
        $stmt = $this->myConnect->prepare($sql);
        $stmt->bindParam(':workflow_name', $workflow_name, PDO::PARAM_STR);

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
        $workflow_id = $getData['workflow_id'];
        $workflow_name = $getData['workflow_name'];
        $sql = "update workflows 
                set workflow_name = :workflow_name
                where workflow_id = :workflow_id";
        // , update_datetime = CURRENT_TIMESTAMP()
        $stmt = $this->myConnect->prepare($sql);
        $stmt->bindParam(':workflow_id', $workflow_id, PDO::PARAM_INT);
        $stmt->bindParam(':workflow_name', $workflow_name, PDO::PARAM_STR);

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
        $workflow_id = $getData['workflow_id'];
        // $is_active = isset($getData['is_active']) ? 1 : 0;
        $sql = "update workflows 
                set is_deleted = 1
                where workflow_id = :workflow_id";
        $stmt = $this->myConnect->prepare($sql);
        $stmt->bindParam(':workflow_id', $workflow_id, PDO::PARAM_INT);

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
        $sql = "select workflow_id, workflow_name, is_deleted 
                from workflows 
                where is_deleted = false";

        $stmt = $this->myConnect->prepare($sql);
        $stmt->execute();
        $rs = $stmt->fetchAll();

        $html = "<p>รายงาน Workflow ทั้งหมด</p>";

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
        $html .= "<th align='center' bgcolor='F2F2F2'>รหัส Workflow </th>";
        $html .= "<th align='center' bgcolor='F2F2F2'> Workflow </th>";
        $html .= "</tr>";
        foreach ($rs as $row) :
            $html .=  "<tr bgcolor='#c7c7c7'>";
            $html .=  "<td>{$row['workflow_id']}</td>";
            $html .=  "<td>{$row['workflow_name']}</td>";
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