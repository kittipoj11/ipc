<?php
// require_once 'config.php';
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
    public function getPoMainAll()
    {
        $sql = <<<EOD
                SELECT `po_id`, `po_number`, `project_name`, `po_main`.`supplier_id`, `po_main`.`location_id`
                , `working_name_th`, `working_name_en`, `is_include_vat`, `contract_value`, `contract_value_before`, `vat`
                , `is_deposit`, `deposit_percent`, `deposit_value`
                , `working_date_from`, `working_date_to`, `working_day`
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

    public function getPoMainByPoId($getPoId)
    {
        $po_id = $getPoId;
        $sql = <<<EOD
                SELECT `po_id`, `po_number`, `project_name`, `po_main`.`supplier_id`, `po_main`.`location_id`
                , `working_name_th`, `working_name_en`, `is_include_vat`, `contract_value`, `contract_value_before`, `vat`
                , `is_deposit`, `deposit_percent`, `deposit_value`
                , `working_date_from`, `working_date_to`, `working_day`
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

    public function getPoPeriodByPoId($getPoId)
    {
        $sql = <<<EOD
                SELECT `period_id`, `po_id`, `period_number`, `workload_planned_percent`, `interim_payment`, `interim_payment_percent`, `remark`
                FROM `po_periods`
                WHERE `po_id` = :po_id
                ORDER BY `po_id`, `period_number`
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
        // exit;
        try {
            // $workflow_id=$getData['workflow_id'];
            $workflow_id = 2;
            $sql = <<<EOD
                        SELECT `workflow_step_id`, `workflow_id`, `approval_level`, `approver_id`, `action_type_id`, `first_status_id`
                        FROM `workflow_steps`
                        WHERE `workflow_id` = :workflow_id
                        ORDER BY approval_level asc
                    EOD;
            $stmtWorkflowSteps = $this->myConnect->prepare($sql);
            $stmtWorkflowSteps->bindParam(':workflow_id', $workflow_id, PDO::PARAM_INT);
            $stmtWorkflowSteps->execute();
            $rsWorkflowSteps = $stmtWorkflowSteps->fetchAll();

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
            // $workflow_id = 1; //$getData['workflow_id'];

            // // Create DateTime objects from the input strings
            // $date1 = new DateTime($working_date_from);
            // $date2 = new DateTime($working_date_to);
            // // Calculate the difference between the two dates
            // $interval = $date1->diff($date2);
            // $working_day =  $interval->days + 1;
            $working_day =  $getData['working_day'];
            $create_by = $_SESSION['user_code'];

            // parameters ในส่วน po_periods
            $period_numbers = $getData['period_numbers'];
            // $period_numbers = null; // ตัวอย่างกรณีที่ตัวแปรเป็น null

            if (is_array($period_numbers)) {
                $number_of_period = count($period_numbers);
                // echo "จำนวน elements ใน array คือ: " . $number_of_period;
            } else {
                // echo "ตัวแปร \$period_numbers ไม่ใช่ array หรือเป็น null";
                $number_of_period = 0; // กำหนดค่าเริ่มต้นให้ $count ในกรณีที่ไม่ใช่ array
            }
            $workload_planned_percents = $getData['workload_planned_percents'];
            $interim_payments = $getData['interim_payments'];
            $interim_payment_percents = $getData['interim_payment_percents'];
            $remarks = $getData['remarks'];

            $sql = <<<EOD
                        INSERT INTO `po_main`(`po_number`, `project_name`, `supplier_id`, `location_id`, `working_name_th`, `working_name_en`
                        , `is_include_vat`, `contract_value`, `contract_value_before`, `vat`, `is_deposit`, `deposit_percent`, `deposit_value`
                        , `working_date_from`, `working_date_to`, `working_day`, `create_by`, `number_of_period`, `workflow_id`) 
                        VALUES(:po_number, :project_name, :supplier_id, :location_id, :working_name_th, :working_name_en
                        , :is_include_vat, :contract_value, :contract_value_before, :vat, :is_deposit, :deposit_percent, :deposit_value
                        , :working_date_from, :working_date_to, :working_day, :create_by, :number_of_period, :workflow_id)
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
            $stmt->bindParam(':workflow_id', $workflow_id, PDO::PARAM_INT);

            $_SESSION['location_id'] = $location_id;
            // $stmt->bindParam(':is_active', $is_active, PDO::PARAM_BOOL);
            // $affected = $stmt->execute();
            // $stmt->debugDumpParams();
            // exit;
            if ($stmt->execute()) {
                // การเรียกใช้ closeCursor() จะช่วยคืนทรัพยากรการเชื่อมต่อฐานข้อมูลที่ถูกใช้โดย Statement นั้นๆ ทำให้การเชื่อมต่อพร้อมสำหรับคำสั่ง SQL อื่นๆ ต่อไป
                $stmt->closeCursor();

                // ดึงข้อมูล po_id ที่เป็น Auto Increment หลังจาก Insert ข้อมูลใน po_main แล้ว
                $po_id = $this->myConnect->lastInsertId();

                // INSERT INTO po_periods
                $sql = <<<EOD
                            INSERT INTO `po_periods`(`po_id`, `period_number`, `workload_planned_percent`, `interim_payment`, `interim_payment_percent`, `remark`) 
                            VALUES (:po_id, :period_number, :workload_planned_percent, :interim_payment, :interim_payment_percent, :remark)
                        EOD;
                $stmtPoPeriod = $this->myConnect->prepare($sql);

                // INSERT INTO inspection_periods
                $sql = <<<EOD
                            INSERT INTO `inspection_periods`(`po_id`, `period_number`, `period_id`, `workload_planned_percent`, `interim_payment`, `interim_payment_percent`, `is_paid`, `is_retention`, `workflow_id`) 
                            VALUES (:po_id, :period_number, :period_id, :workload_planned_percent, :interim_payment, :interim_payment_percent, :is_paid, :is_retention, :workflow_id)
                        EOD;
                $stmtInspectionPeriods = $this->myConnect->prepare($sql);

                // INSERT inspection_period_details
                $sql = <<<EOD
                            INSERT INTO `inspection_period_details`(`inspection_id`) 
                            VALUES (:inspection_id)
                        EOD;
                $stmtInspectionPeriodDetails = $this->myConnect->prepare($sql);

                // INSERT inspection_period_approvals
                $sql = <<<EOD
                            INSERT INTO `inspection_period_approvals`(`inspection_id`, `period_id`, `po_id`, `period_number`, `approval_level`, `approver_id`, `approval_status_id`) 
                            VALUES (:inspection_id, :period_id, :po_id, :period_number, :approval_level, :approver_id, :approval_status_id)
                        EOD;
                $stmtInspectApprovals = $this->myConnect->prepare($sql);

                // $_SESSION['number_of_period'] = $number_of_period;
                for ($i = 0; $i < $number_of_period; $i++) {
                    // po_periods
                    $stmtPoPeriod->bindParam(':po_id', $po_id, PDO::PARAM_INT);
                    $stmtPoPeriod->bindParam(':period_number', $period_numbers[$i], PDO::PARAM_INT);
                    $stmtPoPeriod->bindParam(':workload_planned_percent', $workload_planned_percents[$i],  PDO::PARAM_STR);
                    $stmtPoPeriod->bindParam(':interim_payment', $interim_payments[$i],  PDO::PARAM_STR);
                    $stmtPoPeriod->bindParam(':interim_payment_percent', $interim_payment_percents[$i], PDO::PARAM_STR);
                    $stmtPoPeriod->bindParam(':remark', $remarks[$i], PDO::PARAM_STR);

                    $stmtPoPeriod->execute();
                    $stmtPoPeriod->closeCursor();

                    // inspection_periods
                    $period_id = $this->myConnect->lastInsertId();

                    $stmtInspectionPeriods->bindParam(':period_id', $period_id, PDO::PARAM_INT);
                    $stmtInspectionPeriods->bindParam(':po_id', $po_id, PDO::PARAM_INT);
                    $stmtInspectionPeriods->bindParam(':period_number', $period_numbers[$i], PDO::PARAM_INT);
                    $stmtInspectionPeriods->bindParam(':workload_planned_percent', $workload_planned_percents[$i],  PDO::PARAM_STR);
                    $stmtInspectionPeriods->bindParam(':interim_payment', $interim_payments[$i],  PDO::PARAM_STR);
                    $stmtInspectionPeriods->bindParam(':interim_payment_percent', $interim_payment_percents[$i], PDO::PARAM_STR);
                    $stmtInspectionPeriods->bindParam(':is_paid', $is_paid, PDO::PARAM_BOOL);
                    $stmtInspectionPeriods->bindParam(':is_retention', $is_retention, PDO::PARAM_BOOL);
                    $stmtInspectionPeriods->bindParam(':workflow_id', $workflow_id, PDO::PARAM_INT);

                    $stmtInspectionPeriods->execute();
                    $stmtInspectionPeriods->closeCursor();


                    // inspection_period_details
                    // insert เริ่มต้น 1 รายการ
                    $inspection_id = $this->myConnect->lastInsertId();
                    $stmtInspectionPeriodDetails->bindParam(':inspection_id', $inspection_id, PDO::PARAM_INT);
                    // $stmtInspectionPeriodDetails->bindParam(':order_no', 1,  PDO::PARAM_INT);
                    // $stmtInspectionPeriodDetails->bindParam(':details', "",  PDO::PARAM_STR);
                    // $stmtInspectionPeriodDetails->bindParam(':remark', "",  PDO::PARAM_STR);

                    $stmtInspectionPeriodDetails->execute();
                    $stmtInspectionPeriodDetails->closeCursor();


                    // inspection_period_approvals
                    // เพิ่มรายการลำดับการอนุมัติจากข้อมูลในตาราง workflow_step ของ inspection_periods
                    // ในที่นี้ตาราง workflows จะใช้ workflow_id = 2(ตรวจรับงาน) และเชื่อมโยงกับลำดับการอนุมัติในตาราง workflow_step
                    // นำมา Loop เพื่อ insert ข้อมูลลง inspection_period_approvals ตามลำดับใน workflow_step ที่มี workflow_id = 2 
                    $approval_status_id = 1;
                    foreach ($rsWorkflowSteps as $row) {
                        $approverId = $row['approver_id'];
                        $approvalLevel = $row['approval_level'];
                        $actionType = $row['action_type']; // สมมติว่ามี action_type เช่น 'approval', 'submit', 'confirm', 'verify'

                        $approval_status_id = $row['first_status_id'];//จาก approval_status
                        // if ($actionType === 'approval') {
                        //     $approval_status_id = 11;
                        // } elseif ($actionType === 'submit') {
                        //     $approval_status_id = 21;
                        // } elseif ($actionType === 'confirm') {
                        //     $approval_status_id = 31;
                        // } elseif ($actionType === 'verify') {
                        //     $approval_status_id = 41;
                        // }

                        
                        $stmtInspectApprovals->bindParam(':inspection_id', $inspection_id, PDO::PARAM_INT);
                        $stmtInspectApprovals->bindParam(':period_id', $period_id, PDO::PARAM_INT);
                        $stmtInspectApprovals->bindParam(':po_id', $po_id, PDO::PARAM_INT);
                        $stmtInspectApprovals->bindParam(':period_number', $period_numbers[$i], PDO::PARAM_INT);
                        $stmtInspectApprovals->bindParam(':approval_level', $approvalLevel,  PDO::PARAM_INT);
                        $stmtInspectApprovals->bindParam(':approver_id', $approverId, PDO::PARAM_INT);
                        $stmtInspectApprovals->bindParam(':approval_status_id', $approval_status_id, PDO::PARAM_INT);

                        // $_SESSION['Loop'] = 'inspection_id='+$inspection_id+'approval_level='+$row['approval_level']+'approver_id='+$row['approver_id'];
                        // $_SESSION['After param Loop'] = 'After';
                        $stmtInspectApprovals->execute();
                        // $_SESSION['Execute Loop'] = 'Execute';

                    }
                    $stmtInspectApprovals->closeCursor();
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

            // $workflow_id=$getData['workflow_id'];
            $workflow_id = 2;
            $sql = <<<EOD
                        SELECT `workflow_step_id`, `workflow_id`, `approval_level`, `approver_id`, `action_type_id`, `first_status_id`
                        FROM `workflow_steps`
                        WHERE `workflow_id` = :workflow_id
                        ORDER BY approval_level asc
                    EOD;
            $stmtWorkflowSteps = $this->myConnect->prepare($sql);
            $stmtWorkflowSteps->bindParam(':workflow_id', $workflow_id, PDO::PARAM_INT);
            $stmtWorkflowSteps->execute();
            $rsWorkflowSteps = $stmtWorkflowSteps->fetchAll();

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
            // $workflow_id = 1; //$getData['workflow_id'];

            // // Create DateTime objects from the input strings
            // $date1 = new DateTime($working_date_from);
            // $date2 = new DateTime($working_date_to);
            // // Calculate the difference between the two dates
            // $interval = $date1->diff($date2);
            // $working_day =  $interval->days + 1;
            $working_day =  $getData['working_day'];

            // $update_by = $_SESSION['user_code'];

            $remain_value_interim_payment = $contract_value;
            $po_status = 1; //1:รอตรวจ

            // parameters ในส่วน po_periods
            $period_numbers = $getData['period_numbers'];
            $workload_planned_percents = $getData['workload_planned_percents'];
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
            // $stmtPoMainUpdate->bindParam(':po_status', $po_status, PDO::PARAM_INT);

            // $_SESSION['exe1'] = '1';
            if ($stmtPoMainUpdate->execute()) {
                // $_SESSION['exe2'] = '2';
                $stmtPoMainUpdate->closeCursor();

                // INSERT po_periods
                $sql = <<<EOD
                            INSERT INTO `po_periods`(`po_id`, `period_number`, `workload_planned_percent`, `interim_payment`, `interim_payment_percent`, `remark`) 
                            VALUES (:po_id, :period_number, :workload_planned_percent, :interim_payment, :interim_payment_percent, :remark)
                        EOD;
                $stmtPoPeriodInsert = $this->myConnect->prepare($sql);

                // INSERT inspection_periods
                $sql = <<<EOD
                            INSERT INTO `inspection_periods`(`po_id`, `period_number`, `period_id`, `workload_planned_percent`, `interim_payment`, `interim_payment_percent`, `is_paid`, `is_retention`,`workflow_id`) 
                            VALUES (:po_id, :period_number, :period_id, :workload_planned_percent, :interim_payment, :interim_payment_percent, :is_paid, :is_retention,:workflow_id)
                        EOD;
                $stmtInspectionPeriodInsert = $this->myConnect->prepare($sql);                

                // INSERT inspection_period_details
                $sql = <<<EOD
                            INSERT INTO `inspection_period_details`(`inspection_id`) 
                            VALUES (:inspection_id)
                        EOD;
                $stmtInspectionPeriodDetailInsert = $this->myConnect->prepare($sql);

                // INSERT inspection_period_approvals
                $sql = <<<EOD
                            INSERT INTO `inspection_period_approvals`(`inspection_id`, `period_id`, `po_id`, `period_number`, `approval_level`, `approver_id`, `approval_status_id`) 
                            VALUES (:inspection_id, :period_id, :po_id, :period_number, :approval_level, :approver_id, :approval_status_id)
                        EOD;
                $stmtInspectApprovals = $this->myConnect->prepare($sql);

                $period_status = 1;
                $is_paid = 0;
                $is_retention = 0;
                $inspection_status = 1;
                $current_approval_level = 1; //จะใช้เป็นอะไร: approval_level หรือ workflow_step_id

                foreach ($insert_indexs as $i) { //ถ้าต้องการใช้ค่าของ key ให้เขียนแบบนี้ foreach($insert_indexs as $key=> $value){
                    $stmtPoPeriodInsert->bindParam(':po_id', $po_id, PDO::PARAM_INT);
                    $stmtPoPeriodInsert->bindParam(':period_number', $period_numbers[$i], PDO::PARAM_INT);
                    $stmtPoPeriodInsert->bindParam(':workload_planned_percent', $workload_planned_percents[$i],  PDO::PARAM_STR);
                    $stmtPoPeriodInsert->bindParam(':interim_payment', $interim_payments[$i],  PDO::PARAM_STR);
                    $stmtPoPeriodInsert->bindParam(':interim_payment_percent', $interim_payment_percents[$i], PDO::PARAM_STR);
                    $stmtPoPeriodInsert->bindParam(':remark', $remarks[$i], PDO::PARAM_STR);
                    // $_SESSION['exe3'] = '3';
                    $stmtPoPeriodInsert->execute();
                    // $_SESSION['exe4'] = '4';
                    $stmtPoPeriodInsert->closeCursor();

                    $period_id = $this->myConnect->lastInsertId();

                    $stmtInspectionPeriodInsert->bindParam(':period_id', $period_id, PDO::PARAM_INT);
                    $stmtInspectionPeriodInsert->bindParam(':po_id', $po_id, PDO::PARAM_INT);
                    $stmtInspectionPeriodInsert->bindParam(':period_number', $period_numbers[$i], PDO::PARAM_INT);
                    $stmtInspectionPeriodInsert->bindParam(':workload_planned_percent', $workload_planned_percents[$i],  PDO::PARAM_STR);
                    $stmtInspectionPeriodInsert->bindParam(':interim_payment', $interim_payments[$i],  PDO::PARAM_STR);
                    $stmtInspectionPeriodInsert->bindParam(':interim_payment_percent', $interim_payment_percents[$i], PDO::PARAM_STR);
                    $stmtInspectionPeriodInsert->bindParam(':is_paid', $is_paid, PDO::PARAM_BOOL);
                    $stmtInspectionPeriodInsert->bindParam(':is_retention', $is_retention, PDO::PARAM_BOOL);
                    $stmtInspectionPeriodInsert->bindParam(':workflow_id', $workflow_id, PDO::PARAM_INT);
                    // $_SESSION['exe5'] = '5';
                    $stmtInspectionPeriodInsert->execute();
                    // $_SESSION['exe6'] = '6';
                    $stmtInspectionPeriodInsert->closeCursor();

                    $inspection_id = $this->myConnect->lastInsertId();

                    $stmtInspectionPeriodDetailInsert->bindParam(':inspection_id', $inspection_id, PDO::PARAM_INT);
                    // $stmtInspectionPeriodDetailInsert->bindParam(':order_no', $order_no,  PDO::PARAM_INT);
                    // $stmtInspectionPeriodDetailInsert->bindParam(':details', $details,  PDO::PARAM_STR);
                    // $stmtInspectionPeriodDetailInsert->bindParam(':remark', $remark,  PDO::PARAM_STR);
                    // $_SESSION['exe7'] = '7';
                    $stmtInspectionPeriodDetailInsert->execute();
                    // $_SESSION['exe8'] = '8';
                    $stmtInspectionPeriodDetailInsert->closeCursor();

                    // inspection_period_approvals
                    $approval_status_id = 1;
                    foreach ($rsWorkflowSteps as $row) {
                        $approverId = $row['approver_id'];
                        $approvalLevel = $row['approval_level'];
                        $actionType = $row['action_type']; // สมมติว่ามี action_type เช่น 'approval', 'submit', 'confirm', 'verify'

                        $approval_status_id = $row['first_status_id'];
                        // if ($actionType === 'approval') {
                        //     $approval_status_id = 11;
                        // } elseif ($actionType === 'submit') {
                        //     $approval_status_id = 21;
                        // } elseif ($actionType === 'confirm') {
                        //     $approval_status_id = 31;
                        // } elseif ($actionType === 'verify') {
                        //     $approval_status_id = 41;
                        // }

                        // $_SESSION['Before param Loop'] = 'Before';
                        $stmtInspectApprovals->bindParam(':inspection_id', $inspection_id, PDO::PARAM_INT);
                        $stmtInspectApprovals->bindParam(':period_id', $period_id, PDO::PARAM_INT);
                        $stmtInspectApprovals->bindParam(':po_id', $po_id, PDO::PARAM_INT);
                        $stmtInspectApprovals->bindParam(':period_number', $period_numbers[$i], PDO::PARAM_INT);
                        $stmtInspectApprovals->bindParam(':approval_level', $approvalLevel,  PDO::PARAM_INT);
                        $stmtInspectApprovals->bindParam(':approver_id', $approverId, PDO::PARAM_INT);
                        $stmtInspectApprovals->bindParam(':approval_status_id', $approval_status_id, PDO::PARAM_INT);

                        // $_SESSION['Loop'] = 'inspection_id='+$inspection_id+'approval_level='+$row['approval_level']+'approver_id='+$row['approver_id'];
                        // $_SESSION['After param Loop'] = 'After';
                        // $_SESSION['exe9'] = '9';
                        $stmtInspectApprovals->execute();
                        // $_SESSION['exe10'] = '10';
                        // $_SESSION['Execute Loop'] = 'Execute';

                    }
                    $stmtInspectApprovals->closeCursor();
                }

                // UPDATE po_periods
                $sql = <<<EOD
                            UPDATE `po_periods`
                            SET `workload_planned_percent` = :workload_planned_percent
                            , `interim_payment` = :interim_payment
                            , `interim_payment_percent` = :interim_payment_percent
                            , `remark` = :remark
                            WHERE `po_id` = :po_id
                                AND `period_id` = :period_id
                        EOD;
                $stmtPoPeriodUpdate = $this->myConnect->prepare($sql);

                // UPDATE inspection_periods
                $sql = <<<EOD
                            UPDATE `inspection_periods`
                            SET `workload_planned_percent` = :workload_planned_percent
                            , `interim_payment` = :interim_payment
                            , `interim_payment_percent` = :interim_payment_percent
                            , `is_paid` = :is_paid
                            , `is_retention` = :is_retention
                            WHERE `po_id` = :po_id
                                AND `period_id` = :period_id
                        EOD;
                $stmtInspectionPeriodUpdate = $this->myConnect->prepare($sql);

                foreach ($update_indexs as $i) {
                    $stmtPoPeriodUpdate->bindParam(':po_id', $po_id, PDO::PARAM_INT);
                    $stmtPoPeriodUpdate->bindParam(':period_id', $period_ids[$i], PDO::PARAM_INT);
                    $stmtPoPeriodUpdate->bindParam(':workload_planned_percent', $workload_planned_percents[$i],  PDO::PARAM_STR);
                    $stmtPoPeriodUpdate->bindParam(':interim_payment', $interim_payments[$i],  PDO::PARAM_STR);
                    $stmtPoPeriodUpdate->bindParam(':interim_payment_percent', $interim_payment_percents[$i], PDO::PARAM_STR);
                    $stmtPoPeriodUpdate->bindParam(':remark', $remarks[$i], PDO::PARAM_STR);

                    $stmtPoPeriodUpdate->execute();
                    $stmtPoPeriodUpdate->closeCursor();

                    $stmtInspectionPeriodUpdate->bindParam(':po_id', $po_id, PDO::PARAM_INT);
                    $stmtInspectionPeriodUpdate->bindParam(':period_id', $period_ids[$i], PDO::PARAM_INT);
                    $stmtInspectionPeriodUpdate->bindParam(':workload_planned_percent', $workload_planned_percents[$i],  PDO::PARAM_STR);
                    $stmtInspectionPeriodUpdate->bindParam(':interim_payment', $interim_payments[$i],  PDO::PARAM_STR);
                    $stmtInspectionPeriodUpdate->bindParam(':interim_payment_percent', $interim_payment_percents[$i], PDO::PARAM_STR);
                    $stmtInspectionPeriodUpdate->bindParam(':is_paid', $is_paid, PDO::PARAM_BOOL);
                    $stmtInspectionPeriodUpdate->bindParam(':is_retention', $is_retention, PDO::PARAM_BOOL);

                    $stmtInspectionPeriodUpdate->execute();
                    $stmtInspectionPeriodUpdate->closeCursor();
                }

                // DELETE po_periods
                $sql = <<<EOD
                            DELETE FROM `po_periods`
                            WHERE `po_id` = :po_id
                                AND `period_id` = :period_id
                        EOD;
                $stmtPoPeriodDelete = $this->myConnect->prepare($sql);

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
                    // $stmtInspectionPeriodDelete->bindParam(':period_id', $period_ids[$i], PDO::PARAM_INT);

                    // $stmtInspectionPeriodDelete->execute();
                    // $stmtInspectionPeriodDelete->closeCursor();
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
            //         WHERE `period_id` IN (SELECT `period_id` FROM `po_periods` WHERE `po_id` = :po_id);
            //         EOD;
            // $stmt = $this->myConnect->prepare($sql);
            // $stmt->bindParam(':po_id', $po_id, PDO::PARAM_INT);
            // $stmt->execute();

            // $sql = <<<EOD
            //         DELETE FROM `po_periods` 
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
                        INNER JOIN `po_periods`
                            ON `po_periods`.`period_id` = `inspection_periods`.`period_id`
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