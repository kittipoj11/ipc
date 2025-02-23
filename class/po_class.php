<?php
// require_once 'config.php';
require_once 'connection_class.php';

class Po extends Connection
{
    public function getAllRecord()
    {
        $sql = <<<EOD
                SELECT `po_id`, `po_no`, `project_name`, `po_main`.`supplier_id`, `po_main`.`location_id`, `working_name_th`
                , `working_name_en`, `is_include_vat`, `contract_value`, `contract_value_before`, `vat`, `is_deposit`
                , `deposit_percent`, `deposit_value`, `create_by`, `create_date`, `number_of_period`
                , `suppliers`.`supplier_name`
                , `locations`.`location_name`
                FROM `po_main`
                INNER JOIN `suppliers`
                    ON `suppliers`.`supplier_id` = `po_main`.`supplier_id`
                INNER JOIN `locations`
                    ON `locations`.`location_id` = `po_main`.`location_id`
                EOD;
        $stmt = $this->myConnect->prepare($sql);
        $stmt->execute();
        $rs = $stmt->fetchAll();
        return $rs;
    }

    public function getRecordById($id)
    {
        $sql = <<<EOD
                SELECT `po_id`, `po_no`, `project_name`, `po_main`.`supplier_id`, `po_main`.`location_id`, `working_name_th`
                , `working_name_en`, `is_include_vat`, `contract_value`, `contract_value_before`, `vat`, `is_deposit`
                , `deposit_percent`, `deposit_value`, `create_by`, `create_date`, `number_of_period`
                , `suppliers`.`supplier_name`
                , `locations`.`location_name`
                FROM `po_main`
                INNER JOIN `suppliers`
                    ON `suppliers`.`supplier_id` = `po_main`.`supplier_id`
                INNER JOIN `locations`
                    ON `locations`.`location_id` = `po_main`.`location_id`
                EOD;

        $stmt = $this->myConnect->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $rs = $stmt->fetch();
        return $rs;
    }

    public function insertData($getData)
    {
        @session_start();

        // $_SESSION['getData'] = $getData;

        try {
            // $this->myConnect->beginTransaction();
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
                        INSERT INTO `po_main`(`po_no`, `project_name`, `supplier_id`, `location_id`, `working_name_th`, `working_name_en`
                        , `is_include_vat`, `contract_value`, `contract_value_before`, `vat`, `is_deposit`, `deposit_percent`, `deposit_value`
                        , `working_date_from`, `working_date_to`, `working_day`, `create_by`, `number_of_period`) 
                        VALUES(:po_no, :project_name, :supplier_id, :location_id, :working_name_th, :working_name_en
                        , :is_include_vat, :contract_value, :contract_value_before, :vat, :is_deposit, :deposit_percent, :deposit_value
                        , :working_date_from, :working_date_to, :working_day, :create_by, :number_of_period)
                    EOD;
            $stmt = $this->myConnect->prepare($sql);
            // $stmt->bindParam(':id', $headerId, PDO::PARAM_STR);
            $stmt->bindParam(':po_no', $po_no, PDO::PARAM_STR);
            $stmt->bindParam(':project_name', $project_name, PDO::PARAM_STR);
            $stmt->bindParam(':supplier_id', $supplier_id,  PDO::PARAM_INT);
            $stmt->bindParam(':location_id', $location_id, PDO::PARAM_INT);
            $stmt->bindParam(':working_name_th', $working_name_th, PDO::PARAM_STR);
            $stmt->bindParam(':working_name_en', $working_name_en, PDO::PARAM_STR);
            $stmt->bindParam(':is_include_vat', $is_include_vat, PDO::PARAM_BOOL);
            $stmt->bindParam(':contract_value_before', $contract_value_before, PDO::PARAM_STR);
            $stmt->bindParam(':contract_value', $contract_value, PDO::PARAM_STR);
            $stmt->bindParam(':vat', $vat, PDO::PARAM_STR);
            $stmt->bindParam(':is_deposit', $is_deposit, PDO::PARAM_BOOL);
            $stmt->bindParam(':deposit_percent', $deposit_percent, PDO::PARAM_STR);
            $stmt->bindParam(':deposit_value', $deposit_value, PDO::PARAM_STR);
            $stmt->bindParam(':working_date_from', $working_date_from, PDO::PARAM_STR);
            $stmt->bindParam(':working_date_to', $working_date_to, PDO::PARAM_STR);
            $stmt->bindParam(':working_day', $working_day, PDO::PARAM_INT);
            $stmt->bindParam(':number_of_period', $number_of_period, PDO::PARAM_INT);
            $stmt->bindParam(':create_by', $create_by, PDO::PARAM_STR);
            // $stmt->bindParam(':is_active', $is_active, PDO::PARAM_BOOL);
            // $affected = $stmt->execute();
            // $_SESSION['getData'] = $getData;
            // $_SESSION['sqldump'] = $stmt->debugDumpParams();
            // $stmt->debugDumpParams();
            // exit;
            if ($stmt->execute()) {
                $stmt->closeCursor();
                $po_id = $this->myConnect->lastInsertId();

                // INSERT INTO inspect_main
                $remain_value_interim_payment = $contract_value;
                $inspect_status = 1; //1:รอตรวจ
                $sql = <<<EOD
                        INSERT INTO `inspect_main`(`po_id`, `remain_value_interim_payment`, `inspect_status`, `create_by`) 
                        VALUES(:po_id, :remain_value_interim_payment, :inspect_status, :create_by)
                    EOD;
                $stmtInspect = $this->myConnect->prepare($sql);
                // $stmtInspect->bindParam(':id', $headerId, PDO::PARAM_STR);
                $stmtInspect->bindParam(':po_id', $po_id, PDO::PARAM_INT);
                $stmtInspect->bindParam(':remain_value_interim_payment', $remain_value_interim_payment, PDO::PARAM_STR);
                $stmtInspect->bindParam(':inspect_status', $inspect_status, PDO::PARAM_INT);
                $stmtInspect->bindParam(':create_by', $create_by, PDO::PARAM_STR);

                $stmtInspect->execute();
                $stmtInspect->closeCursor();

                $inspect_id = $this->myConnect->lastInsertId();

                // INSERT INTO po_period
                $sql = <<<EOD
                        INSERT INTO `po_period`(`po_id`, `period`, `interim_payment`, `interim_payment_percent`, `remark`) 
                        VALUES (:po_id, :period, :interim_payment, :interim_payment_percent, :remark)
                    EOD;
                $stmtPoPeriod = $this->myConnect->prepare($sql);

                // INSERT INTO inspect_period
                $sql = <<<EOD
                        INSERT INTO `inspect_period`(`inspect_id`, `period`, `plan_status`, `is_paid`, `is_retention`, `workflow_id`, `current_status`, `current_level`) 
                        VALUES (:inspect_id, :period, :plan_status, :is_paid, :is_retention, :workflow_id, :current_status, :current_level)
                    EOD;
                $stmtInspectPeriod = $this->myConnect->prepare($sql);

                // $stmtPoPeriod->bindParam(':id', $headerId, PDO::PARAM_STR);
                for ($i = 0; $i < $number_of_period; $i++) {
                    $stmtPoPeriod->bindParam(':po_id', $po_id, PDO::PARAM_INT);
                    $stmtPoPeriod->bindParam(':period', $periods[$i], PDO::PARAM_STR);
                    $stmtPoPeriod->bindParam(':interim_payment', $interim_payments[$i],  PDO::PARAM_STR);
                    $stmtPoPeriod->bindParam(':interim_payment_percent', $interim_payment_percents[$i], PDO::PARAM_STR);
                    $stmtPoPeriod->bindParam(':remark', $remarks[$i], PDO::PARAM_STR);

                    $stmtPoPeriod->execute();
                    $stmtPoPeriod->closeCursor();

                    $plan_status=1;
                    $is_paid=0;
                    $is_retention=0;
                    $workflow_id=1;
                    $current_status=1;
                    $current_level=1;//จะใช้เป็นอะไร: level_order หรือ level_id

                    $stmtInspectPeriod->bindParam(':inspect_id', $inspect_id, PDO::PARAM_INT);
                    $stmtInspectPeriod->bindParam(':period', $periods[$i], PDO::PARAM_STR);
                    $stmtInspectPeriod->bindParam(':plan_status', $plan_status,  PDO::PARAM_INT);
                    $stmtInspectPeriod->bindParam(':is_paid', $is_paid, PDO::PARAM_BOOL);
                    $stmtInspectPeriod->bindParam(':is_retention', $is_retention, PDO::PARAM_BOOL);
                    $stmtInspectPeriod->bindParam(':workflow_id', $workflow_id,  PDO::PARAM_INT);
                    $stmtInspectPeriod->bindParam(':current_status', $current_status,  PDO::PARAM_INT);
                    $stmtInspectPeriod->bindParam(':current_level', $current_level,  PDO::PARAM_INT);

                    $stmtInspectPeriod->execute();
                    $stmtInspectPeriod->closeCursor();
                }

                $_SESSION['message'] =  'data has been created successfully.';
                // $this->myConnect->commit();
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $_SESSION['message'] =  'This item could not be added.Because the data has duplicate values!!!';
            } else {
                $_SESSION['message'] =  $e->getCode() + ' '+ $e->getMessage();
            }
            // $this->myConnect->rollBack();
            
        } finally {
            // $stmt->closeCursor();
            // $stmtPoPeriod->closeCursor();
            // $stmtSubs->closeCursor();
            // unset($stmt);
            // unset($stmtPoPeriod);
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
// $stmt->bindParam(':supplier_id', $supplier_id, PDO::PARAM_STR);
// $stmt->bindParam(':building_name', $building_name, PDO::PARAM_STR);
// $stmt->bindParam(':is_active', $is_active, PDO::PARAM_BOOL);
// $stmt->execute();
// $rs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// function calculateVATExcludingVAT($amount, $vatRate)
// {
//     $vatAmount = $amount * ($vatRate / 100);
//     return $vatAmount;
// }

// function calculateTotalIncludingVAT($amount, $vatRate)
// {
//     $totalAmount = $amount * (1 + ($vatRate / 100));
//     return $totalAmount;
// }

// $amount_exclude_vat = 1000;
// $vat_rate = 7;

// $vat = calculateVATExcludingVAT($amount_exclude_vat, $vat_rate);
// $total_include_vat = calculateTotalIncludingVAT($amount_exclude_vat, $vat_rate);