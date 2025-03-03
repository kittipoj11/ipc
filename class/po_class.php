<?php
// require_once 'config.php';
require_once 'connection_class.php';

class Po extends Connection
{
    public function getAllRecord()
    {
        $sql = <<<EOD
                SELECT `po_id`, `po_no`, `project_name`, `po_main`.`supplier_id`, `po_main`.`location_id`
                , `working_name_th`, `working_name_en`, `is_include_vat`, `contract_value`, `contract_value_before`, `vat`
                , `is_deposit`, `deposit_percent`, `deposit_value`, `working_date_from`, `working_date_to`, `working_day`
                , `create_by`, `create_date`, `number_of_period`
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

    public function getRecordById($getPoId)
    {
        $po_id = $getPoId;
        $sql = <<<EOD
                SELECT `po_id`, `po_no`, `project_name`, `po_main`.`supplier_id`, `po_main`.`location_id`
                , `working_name_th`, `working_name_en`, `is_include_vat`, `contract_value`, `contract_value_before`, `vat`
                , `is_deposit`, `deposit_percent`, `deposit_value`, `working_date_from`, `working_date_to`, `working_day`
                , `create_by`, `create_date`, `number_of_period`
                , `suppliers`.`supplier_name`
                , `locations`.`location_name`
                FROM `po_main`
                INNER JOIN `suppliers`
                    ON `suppliers`.`supplier_id` = `po_main`.`supplier_id`
                INNER JOIN `locations`
                    ON `locations`.`location_id` = `po_main`.`location_id`
                WHERE `po_id` = :po_id
                EOD;

        $stmt = $this->myConnect->prepare($sql);
        $stmt->bindParam(':po_id', $po_id, PDO::PARAM_INT);
        $stmt->execute();
        $rs = $stmt->fetch();
        return $rs;
    }

    public function getPeriodByPoId($getPoId)
    {
        $sql = <<<EOD
                SELECT `po_period_id`, `po_id`, `period`, `interim_payment`, `interim_payment_percent`, `remark`
                FROM `po_period`
                WHERE `po_id` = :po_id
                ORDER BY `po_id`, `period`
                EOD;
        $stmt = $this->myConnect->prepare($sql);
        $stmt->bindParam(':po_id', $getPoId, PDO::PARAM_INT);
        $stmt->execute();
        $rs = $stmt->fetchAll();
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
                $stmtInspectMain = $this->myConnect->prepare($sql);
                // $stmtInspectMain->bindParam(':id', $headerId, PDO::PARAM_STR);
                $stmtInspectMain->bindParam(':po_id', $po_id, PDO::PARAM_INT);
                $stmtInspectMain->bindParam(':remain_value_interim_payment', $remain_value_interim_payment, PDO::PARAM_STR);
                $stmtInspectMain->bindParam(':inspect_status', $inspect_status, PDO::PARAM_INT);
                $stmtInspectMain->bindParam(':create_by', $create_by, PDO::PARAM_STR);

                $stmtInspectMain->execute();
                $stmtInspectMain->closeCursor();

                // $inspect_id = $this->myConnect->lastInsertId();//ไม่ใช้แล้ว  ให้เปลี่ยนไปใช้ po_id แทนทั้งหมด

                // INSERT INTO po_period
                $sql = <<<EOD
                        INSERT INTO `po_period`(`po_id`, `period`, `interim_payment`, `interim_payment_percent`, `remark`) 
                        VALUES (:po_id, :period, :interim_payment, :interim_payment_percent, :remark)
                    EOD;
                $stmtPoPeriod = $this->myConnect->prepare($sql);

                // INSERT INTO inspect_period
                $sql = <<<EOD
                        INSERT INTO `inspect_period`(`po_id`,`po_period_id`, `period`, `plan_status`, `is_paid`, `is_retention`, `workflow_id`, `current_status`, `current_level`) 
                        VALUES (:po_id, :po_period_id, :period, :plan_status, :is_paid, :is_retention, :workflow_id, :current_status, :current_level)
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

                    $po_period_id = $this->myConnect->lastInsertId();

                    $plan_status = 1;
                    $is_paid = 0;
                    $is_retention = 0;
                    $workflow_id = 1;
                    $current_status = 1;
                    $current_level = 1; //จะใช้เป็นอะไร: level_order หรือ level_id

                    $stmtInspectPeriod->bindParam(':po_id', $po_id, PDO::PARAM_INT);
                    $stmtInspectPeriod->bindParam(':po_period_id', $po_period_id, PDO::PARAM_INT);
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
                $_SESSION['message'] =  $e->getCode() + ' ' + $e->getMessage();
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
        @session_start();

        try {
            $_SESSION['Begin'] =  'Begin';
            // $this->myConnect->beginTransaction();

            // parameters ในส่วน po_main
            $po_id = $getData['po_id'];
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

            // parameters ในส่วน po_period
            $periods = $getData['period'];
            $numberOfRows = count($periods); //นับจำนวนแถวทั้งหมดที่มีการเพิ่มหรือลบ
            $interim_payments = $getData['interim_payment'];
            $interim_payment_percents = $getData['interim_payment_percent'];
            $remarks = $getData['remark'];
            $cruds = $getData['crud'];
            $po_period_ids = $getData['po_period_id'];

            //ตัวแปร array สำหรับเก็บค่า index ของ element(class crud) แยกตาม value ของ crud ลงในแต่ละ array
            $insert_indexs = [];
            $update_indexs = [];
            $delete_indexs = [];

            //ตรวจสอบว่า valaue ของ crud แต่ละตัวมีค่าเป็นอะไรและจัดเก็บ index นั้นๆลงแต่ละตัวแปร array
            for ($i = 0; $i < $numberOfRows; $i++) {
                if ($cruds[$i] === 'i') {
                    $insert_indexs[] = $i;
                } elseif ($cruds[$i] === 's' || $cruds[$i] === 'u') {
                    $update_indexs[] = $i;
                } elseif ($cruds[$i] === 'd') {
                    $delete_indexs[] = $i;
                }
            }
            $number_of_period = count($insert_indexs) + count($update_indexs);

            // $_SESSION['insert']=$insert_indexs;
            // $_SESSION['update']=$update_indexs;
            // $_SESSION['delete']=$delete_indexs;

            //UPDATE po_main
            $sql = <<<EOD
                        UPDATE `po_main`
                        SET `project_name`= :project_name
                        , `supplier_id`= :supplier_id
                        , `location_id`= :location_id
                        , `working_name_th`= :working_name_th
                        , `working_name_en`= :working_name_en
                        , `is_include_vat`= :is_include_vat
                        , `contract_value`= :contract_value
                        , `contract_value_before`= :contract_value_before
                        , `vat`= :vat
                        , `is_deposit`= :is_deposit
                        , `deposit_percent`= :deposit_percent
                        , `deposit_value`= :deposit_value
                        , `working_date_from`= :working_date_from
                        , `working_date_to`= :working_date_to
                        , `working_day`= :working_day
                        , `number_of_period` = :number_of_period
                        WHERE `po_id` = :po_id
                    EOD;
            $stmt = $this->myConnect->prepare($sql);
            // $stmt->bindParam(':po_no', $po_no, PDO::PARAM_STR);
            $stmt->bindParam(':po_id', $po_id, PDO::PARAM_INT);
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

            if ($stmt->execute()) {
                $stmt->closeCursor();

                // UPDATE inspect_main
                $remain_value_interim_payment = $contract_value;
                $inspect_status = 1; //1:รอตรวจ
                $sql = <<<EOD
                        UPDATE `inspect_main`
                        SET `remain_value_interim_payment` = :remain_value_interim_payment
                        , `inspect_status` = :inspect_status
                        WHERE `po_id` = :po_id 
                    EOD;
                $stmtInspectMain = $this->myConnect->prepare($sql);
                $stmtInspectMain->bindParam(':po_id', $po_id, PDO::PARAM_INT);
                $stmtInspectMain->bindParam(':remain_value_interim_payment', $remain_value_interim_payment, PDO::PARAM_STR);
                $stmtInspectMain->bindParam(':inspect_status', $inspect_status, PDO::PARAM_INT);

                $stmtInspectMain->execute();
                $stmtInspectMain->closeCursor();

                // INSERT po_period
                $sql = <<<EOD
                            INSERT INTO `po_period`(`po_id`, `period`, `interim_payment`, `interim_payment_percent`, `remark`) 
                            VALUES (:po_id, :period, :interim_payment, :interim_payment_percent, :remark)
                        EOD;
                $stmtPoPeriod = $this->myConnect->prepare($sql);

                // INSERT inspect_period
                $sql = <<<EOD
                            INSERT INTO `inspect_period`(`po_id`,`po_period_id`, `period`, `plan_status`, `is_paid`, `is_retention`, `workflow_id`, `current_status`, `current_level`) 
                            VALUES (:po_id, :po_period_id, :period, :plan_status, :is_paid, :is_retention, :workflow_id, :current_status, :current_level)
                        EOD;
                $stmtInspectPeriod = $this->myConnect->prepare($sql);

                $plan_status = 1;
                $is_paid = 0;
                $is_retention = 0;
                $workflow_id = 1;
                $current_status = 1;
                $current_level = 1; //จะใช้เป็นอะไร: level_order หรือ level_id

                foreach ($insert_indexs as $i) { //ถ้าต้องการใช้ key ด้วย foreach($insert_indexs as $key=> $value){
                    $stmtPoPeriod->bindParam(':po_id', $po_id, PDO::PARAM_INT);
                    $stmtPoPeriod->bindParam(':period', $periods[$i], PDO::PARAM_STR);
                    $stmtPoPeriod->bindParam(':interim_payment', $interim_payments[$i],  PDO::PARAM_STR);
                    $stmtPoPeriod->bindParam(':interim_payment_percent', $interim_payment_percents[$i], PDO::PARAM_STR);
                    $stmtPoPeriod->bindParam(':remark', $remarks[$i], PDO::PARAM_STR);

                    $stmtPoPeriod->execute();
                    $stmtPoPeriod->closeCursor();

                    $po_period_id = $this->myConnect->lastInsertId();

                    $stmtInspectPeriod->bindParam(':po_id', $po_id, PDO::PARAM_INT);
                    $stmtInspectPeriod->bindParam(':po_period_id', $po_period_id, PDO::PARAM_INT);
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

                // UPDATE po_period
                $sql = <<<EOD
                            UPDATE `po_period`
                            SET `interim_payment` = :interim_payment
                            , `interim_payment_percent` = :interim_payment_percent
                            , `remark` = :remark
                            WHERE `po_id` = :po_id
                                AND `po_period_id` = :po_period_id
                        EOD;
                $stmtPoPeriod = $this->myConnect->prepare($sql);

                $sql = <<<EOD
                            UPDATE `inspect_period`
                            SET `plan_status` = :plan_status
                            , `is_paid` = :is_paid
                            , `is_retention` = :is_retention
                            , `workflow_id` = :workflow_id
                            , `current_status` = :current_status
                            , `current_level` = :current_level
                            WHERE `po_id` = :po_id
                                AND `po_period_id` = :po_period_id
                        EOD;
                $stmtInspectPeriod = $this->myConnect->prepare($sql);

                foreach ($update_indexs as $i) {
                    $stmtPoPeriod->bindParam(':po_id', $po_id, PDO::PARAM_INT);
                    $stmtPoPeriod->bindParam(':interim_payment', $interim_payments[$i],  PDO::PARAM_STR);
                    $stmtPoPeriod->bindParam(':interim_payment_percent', $interim_payment_percents[$i], PDO::PARAM_STR);
                    $stmtPoPeriod->bindParam(':remark', $remarks[$i], PDO::PARAM_STR);
                    $stmtPoPeriod->bindParam(':po_period_id', $po_period_ids[$i], PDO::PARAM_INT);

                    $stmtPoPeriod->execute();
                    $stmtPoPeriod->closeCursor();

                    $stmtInspectPeriod->bindParam(':po_id', $po_id, PDO::PARAM_INT);
                    $stmtInspectPeriod->bindParam(':po_period_id', $po_period_ids[$i], PDO::PARAM_INT);
                    $stmtInspectPeriod->bindParam(':plan_status', $plan_status,  PDO::PARAM_INT);
                    $stmtInspectPeriod->bindParam(':is_paid', $is_paid, PDO::PARAM_BOOL);
                    $stmtInspectPeriod->bindParam(':is_retention', $is_retention, PDO::PARAM_BOOL);
                    $stmtInspectPeriod->bindParam(':workflow_id', $workflow_id,  PDO::PARAM_INT);
                    $stmtInspectPeriod->bindParam(':current_status', $current_status,  PDO::PARAM_INT);
                    $stmtInspectPeriod->bindParam(':current_level', $current_level,  PDO::PARAM_INT);

                    $stmtInspectPeriod->execute();
                    $stmtInspectPeriod->closeCursor();
                }

                // DELETE po_period
                $sql = <<<EOD
                            DELETE FROM `po_period`
                            WHERE `po_id` = :po_id
                                AND `po_period_id` = :po_period_id
                        EOD;
                $stmtPoPeriod = $this->myConnect->prepare($sql);

                $sql = <<<EOD
                            DELETE FROM `inspect_period`
                            WHERE `po_id` = :po_id
                                AND `po_period_id` = :po_period_id
                        EOD;
                $stmtInspectPeriod = $this->myConnect->prepare($sql);

                foreach ($delete_indexs as $i) {
                    $stmtPoPeriod->bindParam(':po_id', $po_id, PDO::PARAM_INT);
                    $stmtPoPeriod->bindParam(':po_period_id', $po_period_ids[$i], PDO::PARAM_STR);

                    $stmtPoPeriod->execute();
                    $stmtPoPeriod->closeCursor();

                    $stmtInspectPeriod->bindParam(':po_id', $po_id, PDO::PARAM_INT);
                    $stmtInspectPeriod->bindParam(':po_period_id', $po_period_ids[$i], PDO::PARAM_STR);

                    $stmtInspectPeriod->execute();
                    $stmtInspectPeriod->closeCursor();
                }

                $_SESSION['message'] =  'data has been created successfully.';
            }
        } catch (PDOException $e) {
            $_SESSION['message'] =  $e->getCode() + ' : ' + $e->getMessage();
        }
    }
    public function deleteData($getData)
    {
        try {
            $po_id = $getData['po_id'];
            // $is_active = isset($getData['is_active']) ? 1 : 0;
            // $sql = "update po
            //         set is_deleted = 1
            //         where po_id = :po_id";
            $sql = <<<EOD
                    DELETE FROM `inspect_period` 
                    WHERE po_id = :po_id;
                    EOD;
            $stmt = $this->myConnect->prepare($sql);
            $stmt->bindParam(':po_id', $po_id, PDO::PARAM_INT);
            $stmt->execute();

            $sql = <<<EOD
                    DELETE FROM `po_period` 
                    WHERE po_id = :po_id;
                    EOD;
            $stmt = $this->myConnect->prepare($sql);
            $stmt->bindParam(':po_id', $po_id, PDO::PARAM_INT);
            $stmt->execute();

            $sql = <<<EOD
                    DELETE FROM `inspect_main` 
                    WHERE po_id = :po_id;
                    EOD;
            $stmt = $this->myConnect->prepare($sql);
            $stmt->bindParam(':po_id', $po_id, PDO::PARAM_INT);
            $stmt->execute();
            
            $sql = <<<EOD
                    DELETE FROM `po_main` 
                    WHERE po_id = :po_id;
                    EOD;
            $stmt = $this->myConnect->prepare($sql);
            $stmt->bindParam(':po_id', $po_id, PDO::PARAM_INT);
            $stmt->execute();



        } catch (PDOException $e) {
            echo 'error';
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