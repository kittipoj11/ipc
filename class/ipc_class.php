<?php
// require_once 'config.php';
require_once 'connection_class.php';

class Ipc extends Connection
{


    public function getPeriodAll($getPoId)
    {
        $sql = <<<EOD
                SELECT `po_period_id`, `po_id`, `period`, `workload_planned_percent`, `workload_actual_completed_percent`, `workload_remaining_percent`
                , `interim_payment`, `interim_payment_percent`, `interim_payment_less_previous`, `interim_payment_less_previous_percent`
                , `interim_payment_accumulated`, `interim_payment_accumulated_percent`, `interim_payment_remain`, `interim_payment_remain_percent`
                , `retention_value`, `plan_status`, `is_paid`, `is_retention`, `remark`, `workflow_id`, `current_status`, `current_approval_level` 
                FROM `inspect_period` 
                WHERE `po_id` = :po_id
                ORDER BY `po_id`, `period`
                EOD;
        $stmt = $this->myConnect->prepare($sql);
        $stmt->bindParam(':po_id', $getPoId, PDO::PARAM_INT);
        $stmt->execute();
        $rs = $stmt->fetchAll();
        return $rs;
    }

    

    public function getInspectionFilesByInspectionId($getPoId, $getPeriodId, $getInspectionId)
    {
        $sql = <<<EOD
                    SELECT * 
                    FROM files
                EOD;
        $stmt = $this->myConnect->prepare($sql);
        // $stmt->bindParam(':po_id', $getPoId, PDO::PARAM_INT);
        // $stmt->bindParam(':period_id', $getPeriodId, PDO::PARAM_INT);
        // $stmt->bindParam(':inspection_id', $getInspectionId, PDO::PARAM_INT);
        $stmt->execute();
        $rs = $stmt->fetch();
        return $rs;
    }



    // อาจจะไม่ใช้
    public function insertData($getData)
    {
        @session_start();

        // $_SESSION['getData'] = $getData;

        try {
            $this->myConnect->beginTransaction();
            // สร้าง id มี prefix ในที่นี่ให้ prefix เป็น PO 
            // $po_id = uniqid('PO', true);
            // ดึงข้อมูล id ที่เป็น Auto Increment หลังจาก Insert ข้อมูล Header แล้ว
            // $po_id = $pdo->lastInsertId();

            // parameters ในส่วน po_main
            $po_no = $getData['po_no'];
            $project_name = $getData['project_name'];
            $supplier_id = $getData['supplier_id'];
            $location_id = $getData['location_id'];
            $working_name_th = $getData['working_name_th'];
            $working_name_en = $getData['working_name_en'];
            $is_include_vat = 1;
            $contract_value_before = $getData['contract_value_before'];
            $contract_value = $getData['contract_value'];
            $vat = $getData['vat'];
            $is_deposit = $getData['is_deposit'];
            $deposit_percent = $getData['deposit_percent'];
            $deposit_value = $deposit_percent * $contract_value / 100;
            $working_date_from = $getData['working_date_from'];
            $working_date_to = $getData['working_date_to'];
            // Create DateTime objects from the input strings
            $date1 = new DateTime($working_date_from);
            $date2 = new DateTime($working_date_to);
            // Calculate the difference between the two dates
            $interval = $date1->diff($date2);
            $working_day =  $interval->days + 1;
            $create_by = $_SESSION['user_code'];
            // $is_active = isset($getData['is_active']) ? 1 : 0;

            // parameters ในส่วน po_period
            $periods = $getData['period'];
            $number_of_period = count($periods);
            $interim_payments = $getData['interim_payment'];
            $interim_payment_percents = $getData['interim_payment_percent'];
            $remarks = $getData['remark'];
            $sql = <<<EOD
                        INSERT INTO `inspect_main`(`po_id`, `remain_value_interim_payment`, `total_retention_value`, `po_status`, `create_by`) 
                        VALUES(:po_id, :remain_value_interim_payment, :total_retention_value, :po_status, :create_by)
                    EOD;
            $stmt = $this->myConnect->prepare($sql);
            // $stmt->bindParam(':id', $headerId, PDO::PARAM_STR);
            $stmt->bindParam(':po_id', $po_id, PDO::PARAM_INT);
            $stmt->bindParam(':remain_value_interim_payment', $remain_value_interim_payment, PDO::PARAM_STR);
            $stmt->bindParam(':total_retention_value', $total_retention_value,  PDO::PARAM_STR);
            $stmt->bindParam(':po_status', $po_status, PDO::PARAM_INT);
            $stmt->bindParam(':create_by', $create_by, PDO::PARAM_STR);

            if ($stmt->execute()) {
                $stmt->closeCursor();
                $po_id = $this->myConnect->lastInsertId();
                $_SESSION['xxx'] = $po_id;
                $sql = <<<EOD
                        INSERT INTO `po_period`(`po_id`, `period`, `interim_payment`, `interim_payment_percent`, `remark`) 
                        VALUES (:po_id, :period, :interim_payment, :interim_payment_percent, :remark)
                    EOD;
                $stmtPeriod = $this->myConnect->prepare($sql);
                // $stmtPeriod->bindParam(':id', $headerId, PDO::PARAM_STR);
                for ($i = 0; $i < $number_of_period; $i++) {
                    $stmtPeriod->bindParam(':po_id', $po_id, PDO::PARAM_INT);
                    $stmtPeriod->bindParam(':period', $periods[$i], PDO::PARAM_STR);
                    $stmtPeriod->bindParam(':interim_payment', $interim_payments[$i],  PDO::PARAM_STR);
                    $stmtPeriod->bindParam(':interim_payment_percent', $interim_payment_percents[$i], PDO::PARAM_STR);
                    $stmtPeriod->bindParam(':remark', $remarks[$i], PDO::PARAM_STR);

                    $stmtPeriod->execute();
                    $stmtPeriod->closeCursor();
                }

                $_SESSION['message'] =  'data has been created successfully.';
                $this->myConnect->commit();
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $_SESSION['message'] =  'This item could not be added.Because the data has duplicate values!!!';
            } else {
                $_SESSION['message'] =  'Something is wrong.Can not add data.';
            }
            $this->myConnect->rollBack();
        } finally {
            // $stmt->closeCursor();
            // $stmtPeriod->closeCursor();
            // $stmtSubs->closeCursor();
            // unset($stmt);
            // unset($stmtPeriod);
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

    // อาจจะไม่ใช้
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