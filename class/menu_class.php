<?php
// require_once 'config.php';
require_once 'connection_class.php';

class Menu extends Connection
{
    public function fetchAll()
    {
        $sql = <<<EOD
                SELECT `id`, `parent_id`, `title`, `url`, `icon`, `order_num`
                FROM menu_items 
                ORDER BY parent_id, order_num
                EOD;
        $stmt = $this->myConnect->prepare($sql);
        $stmt->execute();

        $rs = $stmt->fetchAll();

        return $rs;
    }

    public function fetchById($id)
    {
        $sql = <<<EOD
                select `id`, `parent_id`, `title`, `url`, `icon`, `order_num` 
                from menu_items
                where role_id = :id
                EOD;

        $stmt = $this->myConnect->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $rs = $stmt->fetch();
        return $rs;
    }

    public function insertData($getData)
    {
        $department_name = $getData['department_name'];

        $sql = "insert into menu_items(department_name) 
                values(:department_name)";
        $stmt = $this->myConnect->prepare($sql);
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
    public function updateData($getData)
    {
        $role_id = $getData['role_id'];
        $department_name = $getData['department_name'];
        $sql = "update menu_items 
                set department_name = :department_name
                where role_id = :role_id";
        // , update_datetime = CURRENT_TIMESTAMP()
        $stmt = $this->myConnect->prepare($sql);
        $stmt->bindParam(':role_id', $role_id, PDO::PARAM_INT);
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
    public function deleteData($getData)
    {
        $role_id = $getData['role_id'];
        // $is_active = isset($getData['is_active']) ? 1 : 0;
        $sql = "update menu_items 
                set is_deleted = 1
                where role_id = :role_id";
        $stmt = $this->myConnect->prepare($sql);
        $stmt->bindParam(':role_id', $role_id, PDO::PARAM_INT);

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
        $sql = "select role_id, department_name, is_deleted 
                from menu_items 
                where is_deleted = false";

        $stmt = $this->myConnect->prepare($sql);
        $stmt->execute();
        $rs = $stmt->fetchAll();

        $html = "<p>รายงาน Menu ทั้งหมด</p>";

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
        $html .= "<th align='center' bgcolor='F2F2F2'>รหัส Menu </th>";
        $html .= "<th align='center' bgcolor='F2F2F2'> Menu </th>";
        $html .= "</tr>";
        foreach ($rs as $row) :
            $html .=  "<tr bgcolor='#c7c7c7'>";
            $html .=  "<td>{$row['role_id']}</td>";
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