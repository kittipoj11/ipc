<?php
// require_once 'config.php';
require_once 'connection_class.php';

class Inspection extends Connection
{
    public function getAllRecord()
    {
        $sql = <<<EOD
                SELECT `inspect_id`, `inspect_main`.`po_id`, `working_date_from`, `working_date_to`, `working_day`
                , `remain_value_interim_payment`, `total_retention_value`, `inspect_status`, `inspect_main`.`create_by`
                ,`inspect_main`.`create_date`
                ,`po`.`po_no`, `po`.`project_name`, `po`.`working_name_th`, `po`.`contract_value`, `po`.`number_of_period`
                , `supplier_name`, `location_name`
                FROM `inspect_main` 
                INNER JOIN `po`
                    ON `inspect_main`.`po_id` = `po`.`po_id`
                INNER JOIN `suppliers`
                    ON `suppliers`.`supplier_id` = `po`.`supplier_id`
                INNER JOIN `locations`
                    ON `locations`.`location_id` = `po`.`location_id`
                EOD;
        $stmt = $this->myConnect->prepare($sql);
        $stmt->execute();
        $rs = $stmt->fetchAll();
        return $rs;
    }

    public function getRecordById($id)
    {
        $sql = <<<EOD
                SELECT `inspect_id`, `inspect_main`.`po_id`, `working_date_from`, `working_date_to`, `working_day`
                , `remain_value_interim_payment`, `total_retention_value`, `inspect_status`, `inspect_main`.`create_by`
                ,`inspect_main`.`create_date`
                ,`po`.`po_no`, `po`.`project_name`, `po`.`working_name_th`, `po`.`contract_value`, `po`.`number_of_period`
                , `supplier_name`, `location_name`
                FROM `inspect_main` 
                INNER JOIN `po`
                    ON `inspect_main`.`po_id` = `po`.`po_id`
                INNER JOIN `suppliers`
                    ON `suppliers`.`supplier_id` = `po`.`supplier_id`
                INNER JOIN `locations`
                    ON `locations`.`location_id` = `po`.`location_id`
                EOD;

        $stmt = $this->myConnect->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $rs = $stmt->fetch();
        return $rs;
    }

    public function insertData($getData)
    {
        $po_name = $getData['po_name'];

        $sql = "insert into pos(po_name) 
                values(:po_name)";
        $stmt = $this->myConnect->prepare($sql);
        $stmt->bindParam(':po_name', $po_name, PDO::PARAM_STR);

        try {
            if ($stmt->execute()) {
                echo  'data has been created successfully.';
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
        $po_id = $getData['po_id'];
        $po_name = $getData['po_name'];
        $sql = "update po
                set po_name = :po_name
                where po_id = :po_id";
        // , update_datetime = CURRENT_TIMESTAMP()
        $stmt = $this->myConnect->prepare($sql);
        $stmt->bindParam(':po_id', $po_id, PDO::PARAM_INT);
        $stmt->bindParam(':po_name', $po_name, PDO::PARAM_STR);

        try {
            if ($stmt->execute()) {
                echo 'data has been update successfully.';
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
        $po_id = $getData['delete_id'];
        // $is_active = isset($getData['is_active']) ? 1 : 0;
        $sql = "update po
                set is_deleted = 1
                where po_id = :po_id";
        $stmt = $this->myConnect->prepare($sql);
        $stmt->bindParam(':po_id', $po_id, PDO::PARAM_INT);

        try {
            if ($stmt->execute()) {
                echo 'data has been delete successfully.';
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
        $sql = "select po_id, po_name, is_deleted 
                from po
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
            $html .=  "<td>{$row['po_id']}</td>";
            $html .=  "<td>{$row['po_name']}</td>";
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