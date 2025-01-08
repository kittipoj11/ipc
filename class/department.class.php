<?php

require_once  'myPdo.class.php';

// namespace ropa;

// use ropa\Db;

class Department extends MyConnection
{
    public function getAllRecord()
    {
        $sql = "select * 
                from tbldepartment 
                where is_deleted = false";

        $stmt = $this->myPdo->prepare($sql);
        $stmt->execute();
        $rs = $stmt->fetchAll();
        return $rs;
    }

    public function getRecordById($id)
    {
        $sql = "select * 
                from tbldepartment
                where is_deleted = false
                and department_code = :id";

        $stmt = $this->myPdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_STR);
        $stmt->execute();
        $rs = $stmt->fetch();
        return $rs;
    }

    public function insertData($getData)
    {
        $department_code = $getData['department_code'];
        $department_name = $getData['department_name'];
        // $is_active = isset($getData['is_active']) ? 1 : 0;
        $sql = "insert into tbldepartment(department_code, department_name) 
                values(:department_code, :department_name)";
        $stmt = $this->myPdo->prepare($sql);
        $stmt->bindParam(':department_code', $department_code, PDO::PARAM_STR);
        $stmt->bindParam(':department_name', $department_name, PDO::PARAM_STR);
        // $stmt->bindParam(':is_active', $is_active, PDO::PARAM_BOOL);
        // $affected = $stmt->execute();

        try {
            if ($stmt->execute()) {
                $_SESSION['message'] =  'data has been created successfully.';
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $_SESSION['message'] =  'This item could not be added.Because the data has duplicate values!!!';
            } else {
                $_SESSION['message'] =  'Something is wrong.Can not add data.';
            }
        }
    }
    public function updateData($getData)
    {
        $department_code = $getData['department_code'];
        $department_name = $getData['department_name'];
        // $is_active = isset($getData['is_active']) ? 1 : 0;
        $sql = "update tbldepartment 
                set department_name = :department_name
                where department_code = :department_code";
        // , update_datetime = CURRENT_TIMESTAMP()
        $stmt = $this->myPdo->prepare($sql);
        $stmt->bindParam(':department_code', $department_code, PDO::PARAM_STR);
        $stmt->bindParam(':department_name', $department_name, PDO::PARAM_STR);
        // $stmt->bindParam(':is_active', $is_active, PDO::PARAM_BOOL);

        try {
            if ($stmt->execute()) {
                $_SESSION['message'] =  'data has been update successfully.';
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $_SESSION['message'] =  'This item could not be added.Because the data has duplicate values!!!';
            } else {
                $_SESSION['message'] =  'Something is wrong.Can not add data.';
            }
        }
    }
    public function deleteData($getData)
    {
        $department_code = $getData['delete_id'];
        // $is_active = isset($getData['is_active']) ? 1 : 0;
        $sql = "update tbldepartment 
                set is_deleted = 1
                where department_code = :department_code";
        $stmt = $this->myPdo->prepare($sql);
        $stmt->bindParam(':department_code', $department_code, PDO::PARAM_STR);

        try {
            if ($stmt->execute()) {
                $_SESSION['message'] =  'data has been delete successfully.';
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $_SESSION['message'] =  'This item could not be added.Because the data has duplicate values!!!';
            } else {
                $_SESSION['message'] =  'Something is wrong.Can not add data.';
            }
        }
    }

    public function getHtmlData()
    {
        $sql = "select * 
                from tbldepartment 
                where is_deleted = false";

        $stmt = $this->myPdo->prepare($sql);
        $stmt->execute();
        $rs = $stmt->fetchAll();

        $html = "<p>รายงาน...ทั้งหมด</p>";

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
        $html .= "<th align='center' bgcolor='F2F2F2'>รหัสอาคาร</th>";
        $html .= "<th align='center' bgcolor='F2F2F2'>อาคาร</th>";
        $html .= "</tr>";
        foreach ($rs as $row) :
            $html .=  "<tr bgcolor='#c7c7c7'>";
            $html .=  "<td>{$row['department_code']}</td>";
            $html .=  "<td>{$row['department_name']}</td>";
            $html .=  "</tr>";
        endforeach;

        $html .= "</table>";

        return $html;
    }
}

// $stmt = $conn->prepare('select ...where :');
// $stmt->bindParam(':department_code', $department_code, PDO::PARAM_STR);
// $stmt->bindParam(':department_name', $department_name, PDO::PARAM_STR);
// $stmt->bindParam(':is_active', $is_active, PDO::PARAM_BOOL);
// $stmt->execute();
// $rs = $stmt->fetchAll(PDO::FETCH_ASSOC);