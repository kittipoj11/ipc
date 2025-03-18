<?php
// require_once 'config.php';
// header('Content-Type: application/json');
require_once 'connection_class.php';

class Po extends Connection
{
    public function getExampleRecord()
    {
        $sql = <<<EOD
                    SELECT * FROM your_table_name
                EOD;
        $stmt = $this->myConnect->prepare($sql);
        $stmt->execute();
        $rs = $stmt->fetchAll();
        return $rs;
    }
    public function getRecordAll()
    {
        $sql = <<<EOD
                SELECT `po_id`, `po_number`, `project_name`, `po_main`.`supplier_id`, `po_main`.`location_id`
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
                ORDER BY `po_id`
                EOD;
        $stmt = $this->myConnect->prepare($sql);
        $stmt->execute();
        $rs = $stmt->fetchAll();
        return $rs;
    }

    public function getRecordByPoId($getPoId)
    {
        $po_id = $getPoId;
        $sql = <<<EOD
                SELECT `po_id`, `po_number`, `project_name`, `po_main`.`supplier_id`, `po_main`.`location_id`
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
                SELECT `period_id`, `po_id`, `period_number`, `interim_payment`, `interim_payment_percent`, `remark`
                FROM `po_period`
                WHERE `po_id` = :po_id
                ORDER BY `po_id`, `period_number`
                EOD;
        $stmt = $this->myConnect->prepare($sql);
        $stmt->bindParam(':po_id', $getPoId, PDO::PARAM_INT);
        $stmt->execute();
        $rs = $stmt->fetchAll();
        return $rs;
    }

    public function getInspectionAllPeriod($getPoId)
    {
        $sql = <<<EOD
                    SELECT `inspection_id`, `inspection_periods`.`period_id`, `workload_planned_percent`, `workload_actual_completed_percent`, `workload_remaining_percent`
                    , `inspection_periods`.`interim_payment`, `inspection_periods`.`interim_payment_percent`
                    , `interim_payment_less_previous`, `interim_payment_less_previous_percent`
                    , `interim_payment_accumulated`, `interim_payment_accumulated_percent`
                    , `interim_payment_remain`, `interim_payment_remain_percent`
                    , `retention_value`, `plan_status`, `is_paid`, `is_retention`, `inspection_periods`.`remark`, `workflow_id`, `current_status`, `current_approval_level` 
                    , `po_period`.`period_id`, `po_id`, `period_number`, `po_period`.`interim_payment`, `po_period`.`interim_payment_percent`, `period_status`, `po_period`.`remark` as po_period_remark
                    FROM `inspection_periods`
                    INNER JOIN `po_period`
                        ON `inspection_periods`.`period_id` = `po_period`.`period_id`
                    WHERE `po_id` = :po_id
                    ORDER BY `period_number`
                EOD;
        $stmt = $this->myConnect->prepare($sql);
        $stmt->bindParam(':po_id', $getPoId, PDO::PARAM_INT);
        $stmt->execute();
        $rs = $stmt->fetchAll();
        return $rs;
    }

    public function getInspectionOnePeriod($getPoId, $getPeriodId)
    {
        $sql = <<<EOD
                    SELECT `inspection_id`, `inspection_periods`.`period_id`, `workload_planned_percent`, `workload_actual_completed_percent`, `workload_remaining_percent`
                    , `inspection_periods`.`interim_payment`, `inspection_periods`.`interim_payment_percent`
                    , `interim_payment_less_previous`, `interim_payment_less_previous_percent`
                    , `interim_payment_accumulated`, `interim_payment_accumulated_percent`
                    , `interim_payment_remain`, `interim_payment_remain_percent`
                    , `retention_value`, `plan_status`, `is_paid`, `is_retention`, `inspection_periods`.`remark`, `workflow_id`, `current_status`, `current_approval_level` 
                    , `po_period`.`period_id`, `po_period`.`po_id`, `period_number`, `po_period`.`interim_payment`, `po_period`.`interim_payment_percent`, `period_status`, `po_period`.`remark` as po_period_remark
                    , `po_number`, `project_name`, `working_name_th`, `working_name_en`, `is_include_vat`, `contract_value`, `contract_value_before`, `vat`
                    , `is_deposit`, `deposit_percent`, `deposit_value`, `working_date_from`, `working_date_to`, `working_day`
                    , `po_main`.`supplier_id`, `suppliers`.`supplier_name`
                    , `po_main`.`location_id`, `locations`.`location_name`
                    FROM `inspection_periods`
                    INNER JOIN `po_period`
                        ON `inspection_periods`.`period_id` = `po_period`.`period_id`
                    INNER JOIN `po_main`
                        ON `po_period`.`po_id` = `po_main`.`po_id`
                    INNER JOIN `suppliers`
                        ON `suppliers`.`supplier_id` = `po_main`.`supplier_id`
                    INNER JOIN `locations`
                        ON `locations`.`location_id` = `po_main`.`location_id`
                    WHERE `po_period`.`po_id` = :po_id
                        AND `inspection_periods`.`period_id` = :period_id
                    ORDER BY `po_id`, `period_number`
                EOD;
        $stmt = $this->myConnect->prepare($sql);
        $stmt->bindParam(':po_id', $getPoId, PDO::PARAM_INT);
        $stmt->bindParam(':period_id', $getPeriodId, PDO::PARAM_INT);
        $stmt->execute();
        $rs = $stmt->fetch();
        return $rs;
    }

    public function getInspectionPeriodDetail($getPoId, $getPeriodId)
    {
        $sql = <<<EOD
                    SELECT `rec_id`, `inspection_period_details`.`inspection_id`, `order_no`, `details`, `inspection_period_details`.`remark`
                    FROM `inspection_period_details`
                    INNER JOIN `inspection_periods`
                        ON `inspection_periods`.`inspection_id` = `inspection_period_details`.`inspection_id`
                    INNER JOIN `po_period`
                        ON `po_period`.`period_id` = `inspection_periods`.`period_id`
                    WHERE `po_id` = :po_id
                        AND `inspection_periods`.`period_id` = :period_id
                    ORDER BY `order_no`
                EOD;
        $stmt = $this->myConnect->prepare($sql);
        $stmt->bindParam(':po_id', $getPoId, PDO::PARAM_INT);
        $stmt->bindParam(':period_id', $getPeriodId, PDO::PARAM_INT);
        $stmt->execute();
        $rs = $stmt->fetchAll();
        return $rs;
    }

    public function getInspectionFiles($getPoId, $getPeriodId, $getInspectionId)
    {
        $sql = <<<EOD
                    SELECT `file_id`, `inspection_files`.`inspection_id`, `file_name`, `file_path`, `file_type`, `uploaded_at` 
                    FROM `inspection_files` 
                    INNER JOIN `inspection_periods`
                        ON `inspection_periods`.`inspection_id` = `inspection_files`.`inspection_id`
                    INNER JOIN `po_period`
                        ON `po_period`.`period_id` = `inspection_periods`.`period_id`
                    WHERE `inspection_periods`.`inspection_id` = :inspection_id
                        AND `inspection_periods`.`period_id` = :period_id
                        AND `po_period`.`po_id` = :po_id
                    EOD;
        $stmt = $this->myConnect->prepare($sql);
        $stmt->bindParam(':po_id', $getPoId, PDO::PARAM_INT);
        $stmt->bindParam(':period_id', $getPeriodId, PDO::PARAM_INT);
        $stmt->bindParam(':inspection_id', $getInspectionId, PDO::PARAM_INT);
        $stmt->execute();
        $rs = $stmt->fetchAll();
        return $rs;
    }

    public function insertData($getData)
    {
        @session_start();

        // $_SESSION['getData'] = $getData;
        // exit;
        try {
            // $this->myConnect->beginTransaction();
            // สร้าง id มี prefix ในที่นี่ให้ prefix เป็น PO 
            // $po_id = uniqid('PO', true);
            // ดึงข้อมูล id ที่เป็น Auto Increment หลังจาก Insert ข้อมูล Header แล้ว
            // $po_id = $this->myConnect->lastInsertId();

            // parameters ในส่วน po_main
            // po_id=auto incremental
            $po_number = $getData['po_number'];
            $project_name = $getData['project_name'];
            $supplier_id = $getData['supplier_id'];
            $location_id = $getData['location_id'];
            $working_name_th = $getData['working_name_th'];
            $working_name_en = $getData['working_name_en'];
            $is_include_vat = 1;
            $contract_value = floatval($getData['contract_value'] ?? 0);
            $contract_value_before = floatval($getData['contract_value_before'] ?? 0);
            $vat = floatval($getData['vat'] ?? 0);
            $is_deposit = $getData['is_deposit'];
            $deposit_percent = floatval($getData['deposit_percent'] ?? 0);
            $deposit_value = ($deposit_percent * $contract_value) / 100;
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
            $period_numbers = $getData['period_numbers'];
            // $period_numbers = null; // ตัวอย่างกรณีที่ตัวแปรเป็น null

            if (is_array($period_numbers)) {
                $number_of_period = count($period_numbers);
                // echo "จำนวน elements ใน array คือ: " . $number_of_period;
            } else {
                // echo "ตัวแปร \$period_numbers ไม่ใช่ array หรือเป็น null";
                $number_of_period = 0; // กำหนดค่าเริ่มต้นให้ $count ในกรณีที่ไม่ใช่ array
            }
            $interim_payments = $getData['interim_payments'];
            $interim_payment_percents = $getData['interim_payment_percents'];
            $remarks = $getData['remarks'];

            $sql = <<<EOD
                        INSERT INTO `po_main`(`po_number`, `project_name`, `supplier_id`, `location_id`, `working_name_th`, `working_name_en`
                        , `is_include_vat`, `contract_value`, `contract_value_before`, `vat`, `is_deposit`, `deposit_percent`, `deposit_value`
                        , `working_date_from`, `working_date_to`, `working_day`, `create_by`, `number_of_period`) 
                        VALUES(:po_number, :project_name, :supplier_id, :location_id, :working_name_th, :working_name_en
                        , :is_include_vat, :contract_value, :contract_value_before, :vat, :is_deposit, :deposit_percent, :deposit_value
                        , :working_date_from, :working_date_to, :working_day, :create_by, :number_of_period)
                    EOD;

            $stmt = $this->myConnect->prepare($sql);
            // $stmt->bindParam(':id', $headerId, PDO::PARAM_STR);
            $stmt->bindParam(':po_number', $po_number, PDO::PARAM_STR);
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
            // $stmt->debugDumpParams();
            // exit;
            if ($stmt->execute()) {
                $stmt->closeCursor();
                $po_id = $this->myConnect->lastInsertId();

                // INSERT INTO po_period
                $sql = <<<EOD
                        INSERT INTO `po_period`(`po_id`, `period_number`, `interim_payment`, `interim_payment_percent`, `remark`) 
                        VALUES (:po_id, :period_number, :interim_payment, :interim_payment_percent, :remark)
                    EOD;
                $stmtPoPeriod = $this->myConnect->prepare($sql);

                // INSERT INTO inspection_periods
                $sql = <<<EOD
                        INSERT INTO `inspection_periods`(`period_id`, `plan_status`, `is_paid`, `is_retention`, `workflow_id`, `current_status`, `current_approval_level`) 
                        VALUES (:period_id, :plan_status, :is_paid, :is_retention, :workflow_id, :current_status, :current_approval_level)
                    EOD;
                $stmtInspectPeriod = $this->myConnect->prepare($sql);

                // INSERT inspection_period_details
                $sql = <<<EOD
                            INSERT INTO `inspection_period_details`(`inspection_id`) 
                            VALUES (:inspection_id)
                        EOD;
                $stmtInspectPeriodDetail = $this->myConnect->prepare($sql);

                $plan_status = 1;
                $is_paid = 0;
                $is_retention = 0;
                $workflow_id = 1;
                $current_status = 1;
                $current_approval_level = 1; //จะใช้เป็นอะไร: approved_level หรือ workflow_step_id
                for ($i = 0; $i < $number_of_period; $i++) {
                    // po_period
                    $stmtPoPeriod->bindParam(':po_id', $po_id, PDO::PARAM_INT);
                    $stmtPoPeriod->bindParam(':period_number', $period_numbers[$i], PDO::PARAM_INT);
                    $stmtPoPeriod->bindParam(':interim_payment', $interim_payments[$i],  PDO::PARAM_STR);
                    $stmtPoPeriod->bindParam(':interim_payment_percent', $interim_payment_percents[$i], PDO::PARAM_STR);
                    $stmtPoPeriod->bindParam(':remark', $remarks[$i], PDO::PARAM_STR);

                    $stmtPoPeriod->execute();
                    $stmtPoPeriod->closeCursor();

                    // inspection_periods
                    $period_id = $this->myConnect->lastInsertId();

                    $stmtInspectPeriod->bindParam(':period_id', $period_id, PDO::PARAM_INT);
                    $stmtInspectPeriod->bindParam(':plan_status', $plan_status,  PDO::PARAM_INT);
                    $stmtInspectPeriod->bindParam(':is_paid', $is_paid, PDO::PARAM_BOOL);
                    $stmtInspectPeriod->bindParam(':is_retention', $is_retention, PDO::PARAM_BOOL);
                    $stmtInspectPeriod->bindParam(':workflow_id', $workflow_id,  PDO::PARAM_INT);
                    $stmtInspectPeriod->bindParam(':current_status', $current_status,  PDO::PARAM_INT);
                    $stmtInspectPeriod->bindParam(':current_approval_level', $current_approval_level,  PDO::PARAM_INT);

                    $stmtInspectPeriod->execute();
                    $stmtInspectPeriod->closeCursor();

                    // inspection_period_details
                    $inspection_id = $this->myConnect->lastInsertId();
                    $stmtInspectPeriodDetail->bindParam(':inspection_id', $inspection_id, PDO::PARAM_INT);
                    // $stmtInspectPeriodDetail->bindParam(':order_no', 1,  PDO::PARAM_INT);
                    // $stmtInspectPeriodDetail->bindParam(':details', "",  PDO::PARAM_STR);
                    // $stmtInspectPeriodDetail->bindParam(':remark', "",  PDO::PARAM_STR);

                    $stmtInspectPeriodDetail->execute();
                    $stmtInspectPeriodDetail->closeCursor();
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
        } catch (Exception $e) {
            $_SESSION['message'] =  $e->getCode() + ' ' + $e->getMessage();
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
            // $_SESSION['Begin'] =  'Begin';
            // $this->myConnect->beginTransaction();

            // parameters ในส่วน po_main
            $po_id = $getData['po_id'];
            $po_number = $getData['po_number'];
            $project_name = $getData['project_name'];
            $supplier_id = $getData['supplier_id'];
            $location_id = $getData['location_id'];
            $working_name_th = $getData['working_name_th'];
            $working_name_en = $getData['working_name_en'];
            $is_include_vat = 1;
            $contract_value = floatval($getData['contract_value'] ?? 0);
            $contract_value_before = floatval($getData['contract_value_before'] ?? 0);
            $vat = floatval($getData['vat'] ?? 0);
            $is_deposit = $getData['is_deposit'];
            $deposit_percent = floatval($getData['deposit_percent'] ?? 0);
            $deposit_value = ($deposit_percent * $contract_value) / 100;
            $working_date_from = $getData['working_date_from'];
            $working_date_to = $getData['working_date_to'];
            // Create DateTime objects from the input strings
            $date1 = new DateTime($working_date_from);
            $date2 = new DateTime($working_date_to);
            // Calculate the difference between the two dates
            $interval = $date1->diff($date2);
            $working_day =  $interval->days + 1;
            // $update_by = $_SESSION['user_code'];

            $remain_value_interim_payment = $contract_value;
            $inspect_status = 1; //1:รอตรวจ

            // parameters ในส่วน po_period
            $period_numbers = $getData['period_numbers'];
            $interim_payments = $getData['interim_payments'];
            $interim_payment_percents = $getData['interim_payment_percents'];
            $remarks = $getData['remarks'];
            $cruds = $getData['cruds'];
            $period_ids = $getData['period_ids'];

            if (is_array($period_numbers)) {
                $number_of_period = count($period_numbers);
                // echo "จำนวน elements ใน array คือ: " . $number_of_period;
            } else {
                // echo "ตัวแปร \$period_numbers ไม่ใช่ array หรือเป็น null";
                $number_of_period = 0; // กำหนดค่าเริ่มต้นให้ $count ในกรณีที่ไม่ใช่ array
            }

            //ตัวแปร array สำหรับเก็บค่า index ของ element(class crud) แยกตาม value ของ crud ลงในแต่ละ array
            $insert_indexs = [];
            $update_indexs = [];
            $delete_indexs = [];


            //ตรวจสอบว่า valaue ของ crud แต่ละตัวมีค่าเป็นอะไรและจัดเก็บ index นั้นๆลงแต่ละตัวแปร array
            for ($i = 0; $i < $number_of_period; $i++) {
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
            $stmtPoMainUpdate = $this->myConnect->prepare($sql);
            // $stmtPoMainUpdate->bindParam(':po_number', $po_number, PDO::PARAM_STR);
            $stmtPoMainUpdate->bindParam(':po_id', $po_id, PDO::PARAM_INT);
            $stmtPoMainUpdate->bindParam(':project_name', $project_name, PDO::PARAM_STR);
            $stmtPoMainUpdate->bindParam(':supplier_id', $supplier_id,  PDO::PARAM_INT);
            $stmtPoMainUpdate->bindParam(':location_id', $location_id, PDO::PARAM_INT);
            $stmtPoMainUpdate->bindParam(':working_name_th', $working_name_th, PDO::PARAM_STR);
            $stmtPoMainUpdate->bindParam(':working_name_en', $working_name_en, PDO::PARAM_STR);
            $stmtPoMainUpdate->bindParam(':is_include_vat', $is_include_vat, PDO::PARAM_BOOL);
            $stmtPoMainUpdate->bindParam(':contract_value_before', $contract_value_before, PDO::PARAM_STR);
            $stmtPoMainUpdate->bindParam(':contract_value', $contract_value, PDO::PARAM_STR);
            $stmtPoMainUpdate->bindParam(':vat', $vat, PDO::PARAM_STR);
            $stmtPoMainUpdate->bindParam(':is_deposit', $is_deposit, PDO::PARAM_BOOL);
            $stmtPoMainUpdate->bindParam(':deposit_percent', $deposit_percent, PDO::PARAM_STR);
            $stmtPoMainUpdate->bindParam(':deposit_value', $deposit_value, PDO::PARAM_STR);
            $stmtPoMainUpdate->bindParam(':working_date_from', $working_date_from, PDO::PARAM_STR);
            $stmtPoMainUpdate->bindParam(':working_date_to', $working_date_to, PDO::PARAM_STR);
            $stmtPoMainUpdate->bindParam(':working_day', $working_day, PDO::PARAM_INT);
            $stmtPoMainUpdate->bindParam(':number_of_period', $number_of_period, PDO::PARAM_INT);
            // $stmtPoMainUpdate->bindParam(':remain_value_interim_payment', $remain_value_interim_payment, PDO::PARAM_STR);
            // $stmtPoMainUpdate->bindParam(':inspect_status', $inspect_status, PDO::PARAM_INT);

            if ($stmtPoMainUpdate->execute()) {
                $stmtPoMainUpdate->closeCursor();

                // INSERT po_period
                $sql = <<<EOD
                            INSERT INTO `po_period`(`po_id`, `period_number`, `interim_payment`, `interim_payment_percent`, `remark`) 
                            VALUES (:po_id, :period_number, :interim_payment, :interim_payment_percent, :remark)
                        EOD;
                $stmtPoPeriodInsert = $this->myConnect->prepare($sql);

                // INSERT inspection_periods
                $sql = <<<EOD
                            INSERT INTO `inspection_periods`(`period_id`, `plan_status`, `is_paid`, `is_retention`, `workflow_id`, `current_status`, `current_approval_level`) 
                            VALUES (:period_id, :plan_status, :is_paid, :is_retention, :workflow_id, :current_status, :current_approval_level)
                        EOD;
                $stmtInspectPeriodInsert = $this->myConnect->prepare($sql);

                // INSERT inspection_period_details
                $sql = <<<EOD
                            INSERT INTO `inspection_period_details`(`inspection_id`) 
                            VALUES (:inspection_id)
                        EOD;
                $stmtInspectPeriodDetailInsert = $this->myConnect->prepare($sql);

                $plan_status = 1;
                $period_status = 1;
                $is_paid = 0;
                $is_retention = 0;
                $workflow_id = 1;
                $current_status = 1;
                $current_approval_level = 1; //จะใช้เป็นอะไร: approved_level หรือ workflow_step_id

                foreach ($insert_indexs as $i) { //ถ้าต้องการใช้ค่าของ key ให้เขียนแบบนี้ foreach($insert_indexs as $key=> $value){
                    $stmtPoPeriodInsert->bindParam(':po_id', $po_id, PDO::PARAM_INT);
                    $stmtPoPeriodInsert->bindParam(':period_number', $period_numbers[$i], PDO::PARAM_INT);
                    $stmtPoPeriodInsert->bindParam(':interim_payment', $interim_payments[$i],  PDO::PARAM_STR);
                    $stmtPoPeriodInsert->bindParam(':interim_payment_percent', $interim_payment_percents[$i], PDO::PARAM_STR);
                    $stmtPoPeriodInsert->bindParam(':remark', $remarks[$i], PDO::PARAM_STR);

                    $stmtPoPeriodInsert->execute();
                    $stmtPoPeriodInsert->closeCursor();

                    $period_id = $this->myConnect->lastInsertId();

                    $stmtInspectPeriodInsert->bindParam(':period_id', $period_id, PDO::PARAM_INT);
                    $stmtInspectPeriodInsert->bindParam(':plan_status', $plan_status,  PDO::PARAM_INT);
                    $stmtInspectPeriodInsert->bindParam(':is_paid', $is_paid, PDO::PARAM_BOOL);
                    $stmtInspectPeriodInsert->bindParam(':is_retention', $is_retention, PDO::PARAM_BOOL);
                    $stmtInspectPeriodInsert->bindParam(':workflow_id', $workflow_id,  PDO::PARAM_INT);
                    $stmtInspectPeriodInsert->bindParam(':current_status', $current_status,  PDO::PARAM_INT);
                    $stmtInspectPeriodInsert->bindParam(':current_approval_level', $current_approval_level,  PDO::PARAM_INT);

                    $stmtInspectPeriodInsert->execute();
                    $stmtInspectPeriodInsert->closeCursor();

                    $inspection_id = $this->myConnect->lastInsertId();

                    $stmtInspectPeriodDetailInsert->bindParam(':inspection_id', $inspection_id, PDO::PARAM_INT);
                    // $stmtInspectPeriodDetailInsert->bindParam(':order_no', $order_no,  PDO::PARAM_INT);
                    // $stmtInspectPeriodDetailInsert->bindParam(':details', $details,  PDO::PARAM_STR);
                    // $stmtInspectPeriodDetailInsert->bindParam(':remark', $remark,  PDO::PARAM_STR);

                    $stmtInspectPeriodDetailInsert->execute();
                    $stmtInspectPeriodDetailInsert->closeCursor();
                }

                // UPDATE po_period
                $sql = <<<EOD
                            UPDATE `po_period`
                            SET `interim_payment` = :interim_payment
                            , `interim_payment_percent` = :interim_payment_percent
                            , `remark` = :remark
                            WHERE `po_id` = :po_id
                                AND `period_id` = :period_id
                        EOD;
                $stmtPoPeriodUpdate = $this->myConnect->prepare($sql);

                $sql = <<<EOD
                            UPDATE `inspection_periods`
                            SET `plan_status` = :plan_status
                            , `is_paid` = :is_paid
                            , `is_retention` = :is_retention
                            , `workflow_id` = :workflow_id
                            , `current_status` = :current_status
                            , `current_approval_level` = :current_approval_level
                            WHERE `period_id` = :period_id
                        EOD;
                $stmtInspectPeriodUpdate = $this->myConnect->prepare($sql);

                foreach ($update_indexs as $i) {
                    $stmtPoPeriodUpdate->bindParam(':po_id', $po_id, PDO::PARAM_INT);
                    $stmtPoPeriodUpdate->bindParam(':period_id', $period_ids[$i], PDO::PARAM_INT);
                    $stmtPoPeriodUpdate->bindParam(':interim_payment', $interim_payments[$i],  PDO::PARAM_STR);
                    $stmtPoPeriodUpdate->bindParam(':interim_payment_percent', $interim_payment_percents[$i], PDO::PARAM_STR);
                    $stmtPoPeriodUpdate->bindParam(':remark', $remarks[$i], PDO::PARAM_STR);

                    $stmtPoPeriodUpdate->execute();
                    $stmtPoPeriodUpdate->closeCursor();

                    $stmtInspectPeriodUpdate->bindParam(':period_id', $period_ids[$i], PDO::PARAM_INT);
                    $stmtInspectPeriodUpdate->bindParam(':plan_status', $plan_status,  PDO::PARAM_INT);
                    $stmtInspectPeriodUpdate->bindParam(':is_paid', $is_paid, PDO::PARAM_BOOL);
                    $stmtInspectPeriodUpdate->bindParam(':is_retention', $is_retention, PDO::PARAM_BOOL);
                    $stmtInspectPeriodUpdate->bindParam(':workflow_id', $workflow_id,  PDO::PARAM_INT);
                    $stmtInspectPeriodUpdate->bindParam(':current_status', $current_status,  PDO::PARAM_INT);
                    $stmtInspectPeriodUpdate->bindParam(':current_approval_level', $current_approval_level,  PDO::PARAM_INT);

                    $stmtInspectPeriodUpdate->execute();
                    $stmtInspectPeriodUpdate->closeCursor();
                }

                // DELETE po_period
                $sql = <<<EOD
                            DELETE FROM `po_period`
                            WHERE `po_id` = :po_id
                                AND `period_id` = :period_id
                        EOD;
                $stmtPoPeriodDelete = $this->myConnect->prepare($sql);

                // ใช้ ON DELETE CASCADE
                // $sql = <<<EOD
                //             DELETE FROM `inspection_periods`
                //             WHERE `period_id` = :period_id
                //         EOD;
                // $stmtInspectPeriodDelete = $this->myConnect->prepare($sql);

                foreach ($delete_indexs as $i) {
                    $period_id = $period_ids[$i];

                    // ดึงข้อมูลไฟล์ที่จะลบ
                    $sql = <<<EOD
                                SELECT file_path
                                FROM `inspection_files` 
                                INNER JOIN `inspection_periods`
                                    ON `inspection_files`.`inspection_id` = `inspection_periods`.`inspection_id`
                                WHERE `period_id` = :period_id
                            EOD;
                    $stmt = $this->myConnect->prepare($sql);
                    $stmt->bindParam(':period_id', $period_id, PDO::PARAM_INT);
                    $stmt->execute();
                    $rs = $stmt->fetchAll();

                    $stmtPoPeriodDelete->bindParam(':po_id', $po_id, PDO::PARAM_INT);
                    $stmtPoPeriodDelete->bindParam(':period_id', $period_id, PDO::PARAM_INT);

                    $stmtPoPeriodDelete->execute();
                    $stmtPoPeriodDelete->closeCursor();

                    // ลบไฟล์ออกจาก server
                    // $_SESSION['rs'] = $rs;
                    foreach ($rs as $row) {
                        $filePath = $row['file_path'];
                        if (file_exists($filePath)) {
                            unlink($filePath); // ลบไฟล์
                        }
                    }


                    // ใช้ ON DELETE CASCADE
                    // $stmtInspectPeriodDelete->bindParam(':period_id', $period_ids[$i], PDO::PARAM_INT);

                    // $stmtInspectPeriodDelete->execute();
                    // $stmtInspectPeriodDelete->closeCursor();
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

            // ใช้การกำหนด ON DELETE CASCADE ใน Table ลูก
            // $sql = <<<EOD
            //         DELETE FROM `inspection_periods` 
            //         WHERE `period_id` IN (SELECT `period_id` FROM `po_period` WHERE `po_id` = :po_id);
            //         EOD;
            // $stmt = $this->myConnect->prepare($sql);
            // $stmt->bindParam(':po_id', $po_id, PDO::PARAM_INT);
            // $stmt->execute();

            // $sql = <<<EOD
            //         DELETE FROM `po_period` 
            //         WHERE po_id = :po_id;
            //         EOD;
            // $stmt = $this->myConnect->prepare($sql);
            // $stmt->bindParam(':po_id', $po_id, PDO::PARAM_INT);
            // $stmt->execute();

            $sql = <<<EOD
                        SELECT file_path
                        FROM `inspection_files` 
                        INNER JOIN `inspection_periods`
                            ON `inspection_files`.`inspection_id` = `inspection_periods`.`inspection_id`
                        INNER JOIN `po_period`
                            ON `po_period`.`period_id` = `inspection_periods`.`period_id`
                        WHERE `po_id` = :po_id
                    EOD;
            $stmt = $this->myConnect->prepare($sql);
            $stmt->bindParam(':po_id', $po_id, PDO::PARAM_INT);
            $stmt->execute();
            $rs = $stmt->fetchAll();

            $sql = <<<EOD
                    DELETE FROM `po_main` 
                    WHERE po_id = :po_id;
                    EOD;
            $stmt = $this->myConnect->prepare($sql);
            $stmt->bindParam(':po_id', $po_id, PDO::PARAM_INT);
            $stmt->execute();

            // ลบไฟล์ออกจาก server
            foreach ($rs as $row) {
                $filePath = $row['file_path'];
                if (file_exists($filePath)) {
                    unlink($filePath); // ลบไฟล์
                }
            }

            echo 'success';
        } catch (PDOException $e) {
            echo 'error';
        }
    }

    public function updateInspectionPeriod($getData)
    {
        @session_start();

        try {
            // $_SESSION['getData'] =  $getData;
            // exit;
            // $this->myConnect->beginTransaction();

            // parameters ในส่วน main
            $po_id = $getData['po_id'];
            $period_id = $getData['period_id'];
            $inspection_id = $getData['inspection_id'];
            $po_number = $getData['po_number'];
            $interim_payment = floatval($getData['interim_payment'] ?? 0);
            $workload_actual_completed_percent = floatval($getData['workload_actual_completed_percent'] ?? 0);
            $workload_remaining_percent = floatval($getData['workload_remaining_percent'] ?? 0);

            // parameters ในส่วน period
            $order_nos = $getData['order_nos'];
            $details = $getData['details'];
            $remarks = $getData['remarks'];
            $cruds = $getData['cruds'];
            $rec_ids = $getData['rec_ids'];

            if (is_array($order_nos)) {
                $number_of_order = count($order_nos);
                // echo "จำนวน elements ใน array คือ: " . $number_of_order;
            } else {
                // echo "ตัวแปร \$order_nos ไม่ใช่ array หรือเป็น null";
                $number_of_order = 0; // กำหนดค่าเริ่มต้นให้ $count ในกรณีที่ไม่ใช่ array
            }

            //ตัวแปร array สำหรับเก็บค่า index ของ element(class crud) แยกตาม value ของ crud ลงในแต่ละ array
            $insert_indexs = [];
            $update_indexs = [];
            $delete_indexs = [];


            //ตรวจสอบว่า valaue ของ crud แต่ละตัวมีค่าเป็นอะไรและจัดเก็บ index นั้นๆลงแต่ละตัวแปร array
            for ($i = 0; $i < $number_of_order; $i++) {
                if ($cruds[$i] === 'i') {
                    $insert_indexs[] = $i;
                } elseif ($cruds[$i] === 's' || $cruds[$i] === 'u') {
                    $update_indexs[] = $i;
                } elseif ($cruds[$i] === 'd') {
                    $delete_indexs[] = $i;
                }
            }
            $number_of_order = count($insert_indexs) + count($update_indexs);

            // $_SESSION['insert'] = $insert_indexs;
            // $_SESSION['update'] = $update_indexs;
            // $_SESSION['delete'] = $delete_indexs;

            //UPDATE po_main
            $sql = <<<EOD
                        UPDATE `inspection_periods`
                            SET `interim_payment` = :interim_payment
                            , `workload_actual_completed_percent` = :workload_actual_completed_percent
                            , `workload_remaining_percent` = :workload_remaining_percent
                            WHERE `period_id` = :period_id
                                AND `inspection_id` = :inspection_id
                    EOD;
            $stmtInspectPeriod = $this->myConnect->prepare($sql);
            // $stmtInspectPeriod->bindParam(':po_number', $po_number, PDO::PARAM_STR);
            $stmtInspectPeriod->bindParam(':period_id', $period_id, PDO::PARAM_INT);
            $stmtInspectPeriod->bindParam(':inspection_id', $inspection_id, PDO::PARAM_INT);
            $stmtInspectPeriod->bindParam(':interim_payment', $interim_payment, PDO::PARAM_STR);
            $stmtInspectPeriod->bindParam(':workload_actual_completed_percent', $workload_actual_completed_percent, PDO::PARAM_STR);
            $stmtInspectPeriod->bindParam(':workload_remaining_percent', $workload_remaining_percent, PDO::PARAM_STR);

            if ($stmtInspectPeriod->execute()) {
                $stmtInspectPeriod->closeCursor();

                // INSERT inspection_period_details
                $sql = <<<EOD
                            INSERT INTO `inspection_period_details`(`inspection_id`, `order_no`, `details`, `remark`) 
                            VALUES (:inspection_id, :order_no, :details, :remark)
                        EOD;
                $stmtInspectPeriodDetail = $this->myConnect->prepare($sql);

                foreach ($insert_indexs as $i) { //ถ้าต้องการใช้ key ด้วย foreach($insert_indexs as $key=> $value){
                    $stmtInspectPeriodDetail->bindParam(':inspection_id', $inspection_id, PDO::PARAM_INT);
                    $stmtInspectPeriodDetail->bindParam(':order_no', $order_nos[$i], PDO::PARAM_INT);
                    $stmtInspectPeriodDetail->bindParam(':details', $details[$i],  PDO::PARAM_STR);
                    $stmtInspectPeriodDetail->bindParam(':remark', $remarks[$i], PDO::PARAM_STR);

                    $stmtInspectPeriodDetail->execute();
                    $stmtInspectPeriodDetail->closeCursor();
                }

                // UPDATE inspection_period_details
                $sql = <<<EOD
                            UPDATE `inspection_period_details`
                            SET `details` = :details
                            , `remark` = :remark
                            WHERE `inspection_id` = :inspection_id
                                AND `rec_id` = :rec_id
                        EOD;
                $stmtInspectPeriodDetail = $this->myConnect->prepare($sql);

                foreach ($update_indexs as $i) {
                    $stmtInspectPeriodDetail->bindParam(':inspection_id', $inspection_id, PDO::PARAM_INT);
                    $stmtInspectPeriodDetail->bindParam(':rec_id', $rec_ids[$i], PDO::PARAM_INT);
                    $stmtInspectPeriodDetail->bindParam(':details', $details[$i],  PDO::PARAM_STR);
                    $stmtInspectPeriodDetail->bindParam(':remark', $remarks[$i], PDO::PARAM_STR);

                    $stmtInspectPeriodDetail->execute();
                    $stmtInspectPeriodDetail->closeCursor();
                }

                // DELETE inspection_period_details
                $sql = <<<EOD
                            DELETE FROM `inspection_period_details`
                            WHERE `inspection_id` = :inspection_id
                                AND `rec_id` = :rec_id
                        EOD;
                $stmtInspectPeriodDetail = $this->myConnect->prepare($sql);

                foreach ($delete_indexs as $i) {
                    $stmtInspectPeriodDetail->bindParam(':inspection_id', $inspection_id, PDO::PARAM_INT);
                    $stmtInspectPeriodDetail->bindParam(':rec_id', $rec_ids[$i], PDO::PARAM_INT);

                    $stmtInspectPeriodDetail->execute();
                    $stmtInspectPeriodDetail->closeCursor();
                }

                $_SESSION['message'] =  'data has been created successfully.';
            }
        } catch (PDOException $e) {
            $_SESSION['message'] =  $e->getCode() + ' : ' + $e->getMessage();
        }
    }

    public function insertInspectionFile($getData)
    {
        @session_start();

        // $_SESSION['getData in po_class'] = $getData;
        // return;
        try {
            // เริ่ม transaction
            $this->myConnect->beginTransaction();

            $po_id = $getData['po_id'];
            $period_id = $getData['period_id'];
            $inspection_id = $getData['inspection_id'];

            // บันทึก inspection_files
            if (isset($_FILES['files'])) {
                // $_SESSION['File is selected'] = $_FILES['files'];
                // โฟลเดอร์สำหรับเก็บไฟล์
                $uploadDir = 'uploads/';

                // ตรวจสอบว่าโฟลเดอร์ uploads มีอยู่หรือไม่ ถ้าไม่มีให้สร้าง
                if (!file_exists($uploadDir)) {
                    if (!mkdir($uploadDir, 0777, true)) { // สร้างโฟลเดอร์และตั้ง permission (0777 คือ read, write, execute สำหรับทุก user)
                        throw new Exception("Failed to create uploads directory.");
                    }
                }

                $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
                foreach ($_FILES['files']['tmp_name'] as $key => $tmp_name) {
                    $file_type = $_FILES['files']['type'][$key];
                    if (!in_array($file_type, $allowedTypes)) {
                        throw new Exception("Invalid file type.");
                    }

                    $fileSize = $_FILES['files']['size'][$key];
                    if ($fileSize > 2000000) { // 2MB limit
                        throw new Exception("File size exceeds 2MB.");
                    }

                    $file_name = $_FILES['files']['name'][$key];
                    $fileExtension = pathinfo($file_name, PATHINFO_EXTENSION);
                    $newFileName = uniqid() . '.' . $fileExtension; //จำเป็นต้องเปลี่ยนชื่อหรือไม่?
                    $file_path = $uploadDir . $newFileName;

                    if (move_uploaded_file($tmp_name, $file_path)) {
                        // บันทึกข้อมูลไฟล์ลงฐานข้อมูล
                        $sql = <<<EOD
                                    INSERT INTO `inspection_files`(`inspection_id`, `file_name`, `file_path`, `file_type`) 
                                    VALUES(:inspection_id, :file_name, :file_path, :file_type)
                                EOD;
                        $stmt = $this->myConnect->prepare($sql);
                        $stmt->bindParam(':inspection_id', $inspection_id, PDO::PARAM_INT);
                        $stmt->bindParam(':file_name', $file_name, PDO::PARAM_STR);
                        $stmt->bindParam(':file_path', $file_path, PDO::PARAM_STR);
                        $stmt->bindParam(':file_type', $file_type, PDO::PARAM_STR);
                        $stmt->execute();
                        // $_SESSION['insert inspection_files'] = 'Completed';
                    } else {
                        // $_SESSION['insert inspection_files'] = 'Completed';
                        throw new Exception("Failed to upload file.");
                    }
                }
            }
            // commit transaction
            $this->myConnect->commit();
            // $_SESSION['commit'] = 'Completed';
            echo json_encode(['status' => 'success', 'message' => 'Record and files uploaded successfully.']);
            // echo json_encode(["a" => "A", "b" => "B"]);
        } catch (PDOException $e) {
            // $_SESSION['rollBack'] = 'Completed';
            // rollback transaction
            $this->myConnect->rollBack();
            if ($e->getCode() == 23000) {
                $_SESSION['message'] =  'This item could not be added.Because the data has duplicate values!!!';
            } else {
                $_SESSION['message'] =  $e->getCode() + ' ' + $e->getMessage();
            }

            echo json_encode(['status' => 'fail', 'message' => 'Record and files uploaded fail!!!.']);
        } catch (Exception $e) {
            // $_SESSION['Exception'] = 'ERROR!!!!!!!!!!!';
            $_SESSION['message'] =  $e->getCode() + ' ' + $e->getMessage();
            echo json_encode(['status' => 'fail', 'message' => 'Record and files uploaded fail!!!.']);
        }
        // finally {
        // $stmt->closeCursor();
        // $stmtPoPeriod->closeCursor();
        // $stmtSubs->closeCursor();
        // unset($stmt);
        // unset($stmtPoPeriod);
        // }
    }

    // Function นี้ลบที่ละ file_id
    // ต้องทำลบที่ละ inspection_id ด้วย เมื่อมีการลบ po_period จะต้องมาลบ inspection_id ที่ inspection_files ด้วย
    public function deleteInspectionFile($getFileId)
    {
        $file_id = $getFileId;
        // if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['file_id'])) {
        //     $file_id = $_POST['file_id'];

        // } else {
        //     echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
        // }
        try {
            // เริ่ม transaction
            $this->myConnect->beginTransaction();

            // ดึงข้อมูลไฟล์ที่จะลบ
            $sql = <<<EOD
                        SELECT file_path 
                        FROM `inspection_files`
                        WHERE file_id = :file_id
                    EOD;
            $stmt = $this->myConnect->prepare($sql);
            $stmt->bindParam(':file_id', $file_id, PDO::PARAM_INT);
            $stmt->execute();
            $rs = $stmt->fetchAll();

            // ลบไฟล์ออกจาก server
            foreach ($rs as $row) {
                $filePath = $row['file_path'];
                if (file_exists($filePath)) {
                    unlink($filePath); // ลบไฟล์
                }
            }

            // ลบข้อมูลไฟล์จาก inspection_files
            $sql = <<<EOD
                        DELETE FROM `inspection_files`
                        WHERE file_id = :file_id
                    EOD;
            $stmt = $this->myConnect->prepare($sql);
            $stmt->bindParam(':file_id', $file_id, PDO::PARAM_INT);
            $stmt->execute();

            // commit transaction
            $this->myConnect->commit();
            echo json_encode(['status' => 'success', 'message' => 'Record and associated files deleted successfully.']);
        } catch (Exception $e) {
            // rollback transaction
            $this->myConnect->rollBack();
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
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