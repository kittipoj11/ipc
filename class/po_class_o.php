<?php
// require_once 'config.php';
require_once 'connection_class.php';

class Po
{
    private $db;
    public function __construct(PDO $pdoConnection)
    {
        $this->db = $pdoConnection;
    }
    public function getExampleRecord()
    {
        $sql = <<<EOD
                    SELECT * FROM your_table_name
                EOD;
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $rs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $rs;
    }
    public function getAll():array
    {
        $sql = <<<EOD
                    SELECT `po_id`, `po_number`, `project_name`, p.`supplier_id`, p.`location_id`
                    , `working_name_th`, `working_name_en`, `is_include_vat`, `contract_value`, `contract_value_before`, `vat`
                    , `is_deposit`, `deposit_percent`, `deposit_value`
                    , `working_date_from`, `working_date_to`, `working_day`
                    , `create_by`, `create_date`, `number_of_period`
                    , s.`supplier_name`
                    , l.`location_name`
                    FROM `po_main` p
                    INNER JOIN `suppliers` s
                        ON s.`supplier_id` = p.`supplier_id`
                    INNER JOIN `locations` l
                        ON l.`location_id` = p.`location_id`
                    ORDER BY `po_id`
                EOD;
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        $rs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $rs;
    }

    public function getByPoId_old($id):?array
    {
        $po_id = $id;
        $sql = <<<EOD
                    SELECT `po_id`, `po_number`, `project_name`, p.`supplier_id`, p.`location_id`
                    , `working_name_th`, `working_name_en`, `is_include_vat`, `contract_value`, `contract_value_before`, `vat`
                    , `is_deposit`, `deposit_percent`, `deposit_value`
                    , `working_date_from`, `working_date_to`, `working_day`
                    , `create_by`, `create_date`, `number_of_period`
                    , s.`supplier_name`
                    , l.`location_name`
                    FROM `po_main` p
                    INNER JOIN `suppliers` s
                        ON s.`supplier_id` = p.`supplier_id`
                    INNER JOIN `locations` l
                        ON l.`location_id` = p.`location_id`
                    WHERE `po_id` = :po_id
                EOD;

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':po_id', $po_id, PDO::PARAM_INT);
        $stmt->execute();

        $rs = $stmt->fetch();
        if (!$rs) {
            return null; // ไม่พบข้อมูล
        }
        return $rs;
    }

    public function getAllPeriodByPoId($id)
    {
        $sql = <<<EOD
                SELECT `period_id`, `po_id`, `period_number`, `workload_planned_percent`, `interim_payment`, `interim_payment_percent`, `remark`
                FROM `po_periods`
                WHERE `po_id` = :po_id
                ORDER BY `po_id`, `period_number`
                EOD;
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':po_id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $rs = $stmt->fetchAll();
        return $rs;
    }

    public function create($getData)
    {
        @session_start();
        $this->db->beginTransaction();
        try {
            // กำหนดค่า default สำหรับ workflow step ของ inspection และ ipc (อาจจะมีหน้าจอ config) โดยที่
            // 1. ทำการสร้าง inspection_period_approvals เมื่อมีการ save po เรียบร้อยแล้ว
            // 2. ทำการสร้าง ipc_period_approvals เมื่อมีการ approve ใน step สุดท้ายของ inspection ในแต่ละ period  
            // workflow_id = 1 สร้าง inspection_period_approvals
            // workflow_id = 2 สร้าง ipc_period_approvals
            $workflow_id = 1; //ในที่นี้กำหนด workflow_id = 1 ของการสร้าง inspection_period_approvals
            $sql = <<<EOD
                        SELECT `workflow_step_id`, `workflow_id`, `approval_level`, `approver_id`, `approval_type_id`, `approval_type_text`
                        FROM `workflow_steps`
                        WHERE `workflow_id` = :workflow_id
                        ORDER BY approval_level asc
                    EOD;
            $stmtWorkflowSteps = $this->db->prepare($sql);
            $stmtWorkflowSteps->bindParam(':workflow_id', $workflow_id, PDO::PARAM_INT);
            $stmtWorkflowSteps->execute();
            $rsWorkflowSteps = $stmtWorkflowSteps->fetchAll();

            // $this->db->beginTransaction();
            // สร้าง id มี prefix ในที่นี่ให้ prefix เป็น PO 
            // $po_id = uniqid('PO', true);
            // ดึงข้อมูล id ที่เป็น Auto Increment หลังจาก Insert ข้อมูล Header แล้ว
            // $po_id = $this->db->lastInsertId();

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
            // $date1 = new DateTime($working_date_from);
            // $date2 = new DateTime($working_date_to);
            // Calculate the difference between the two dates
            // $interval = $date1->diff($date2);
            // $working_day =  $interval->days + 1;
            $working_day =  $getData['working_day'];
            $create_by = $_SESSION['user_code'];

            // จัดการตรงจุดนี้ให้เหมือนกับ update function เพราะอาจจะมีการเพิ่ม-ลบ period
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
            $cruds = $getData['cruds'];
            $period_ids = $getData['period_ids'];

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
            // จัดการตรงจุดนี้
            $number_of_period = count($insert_indexs) + count($update_indexs);

            // INSERT INTO po_main
            $sql = <<<EOD
                        INSERT INTO `po_main`(`po_number`, `project_name`, `supplier_id`, `location_id`, `working_name_th`, `working_name_en`
                        , `is_include_vat`, `contract_value`, `contract_value_before`, `vat`, `is_deposit`, `deposit_percent`, `deposit_value`
                        , `working_date_from`, `working_date_to`, `working_day`, `create_by`, `number_of_period`, `workflow_id`) 
                        VALUES(:po_number, :project_name, :supplier_id, :location_id, :working_name_th, :working_name_en
                        , :is_include_vat, :contract_value, :contract_value_before, :vat, :is_deposit, :deposit_percent, :deposit_value
                        , :working_date_from, :working_date_to, :working_day, :create_by, :number_of_period, :workflow_id)
                    EOD;

            $stmtPoMainInsert = $this->db->prepare($sql);
            $stmtPoMainInsert->bindParam(':po_number', $po_number, PDO::PARAM_STR);
            $stmtPoMainInsert->bindParam(':project_name', $project_name, PDO::PARAM_STR);
            $stmtPoMainInsert->bindParam(':supplier_id', $supplier_id,  PDO::PARAM_INT);
            $stmtPoMainInsert->bindParam(':location_id', $location_id, PDO::PARAM_INT);
            $stmtPoMainInsert->bindParam(':working_name_th', $working_name_th, PDO::PARAM_STR);
            $stmtPoMainInsert->bindParam(':working_name_en', $working_name_en, PDO::PARAM_STR);
            $stmtPoMainInsert->bindParam(':is_include_vat', $is_include_vat, PDO::PARAM_BOOL);
            $stmtPoMainInsert->bindParam(':contract_value_before', $contract_value_before, PDO::PARAM_STR);
            $stmtPoMainInsert->bindParam(':contract_value', $contract_value, PDO::PARAM_STR);
            $stmtPoMainInsert->bindParam(':vat', $vat, PDO::PARAM_STR);
            $stmtPoMainInsert->bindParam(':is_deposit', $is_deposit, PDO::PARAM_BOOL);
            $stmtPoMainInsert->bindParam(':deposit_percent', $deposit_percent, PDO::PARAM_STR);
            $stmtPoMainInsert->bindParam(':deposit_value', $deposit_value, PDO::PARAM_STR);
            $stmtPoMainInsert->bindParam(':working_date_from', $working_date_from, PDO::PARAM_STR);
            $stmtPoMainInsert->bindParam(':working_date_to', $working_date_to, PDO::PARAM_STR);
            $stmtPoMainInsert->bindParam(':working_day', $working_day, PDO::PARAM_INT);
            $stmtPoMainInsert->bindParam(':number_of_period', $number_of_period, PDO::PARAM_INT);
            $stmtPoMainInsert->bindParam(':create_by', $create_by, PDO::PARAM_STR);
            $stmtPoMainInsert->bindParam(':workflow_id', $workflow_id, PDO::PARAM_INT);

            if ($stmtPoMainInsert->execute()) {
                $_SESSION['location_id'] = $location_id;

                // การเรียกใช้ closeCursor() จะช่วยคืนทรัพยากรการเชื่อมต่อฐานข้อมูลที่ถูกใช้โดย Statement นั้นๆ ทำให้การเชื่อมต่อพร้อมสำหรับคำสั่ง SQL อื่นๆ ต่อไป
                $stmtPoMainInsert->closeCursor();

                // ดึงข้อมูล po_id ที่เป็น Auto Increment หลังจาก Insert ข้อมูลใน po_main แล้ว
                $po_id = $this->db->lastInsertId();

                // INSERT INTO po_periods
                $sql = <<<EOD
                            INSERT INTO `po_periods`(`po_id`, `period_number`, `workload_planned_percent`, `interim_payment`, `interim_payment_percent`, `remark`) 
                            VALUES (:po_id, :period_number, :workload_planned_percent, :interim_payment, :interim_payment_percent, :remark)
                        EOD;
                $stmtPoPeriod = $this->db->prepare($sql);

                // INSERT INTO inspection_periods
                $sql = <<<EOD
                            INSERT INTO `inspection_periods`(`po_id`, `period_number`, `period_id`, `workload_planned_percent`, `interim_payment`, `interim_payment_percent`, `is_paid`, `is_retention`, `workflow_id`) 
                            VALUES (:po_id, :period_number, :period_id, :workload_planned_percent, :interim_payment, :interim_payment_percent, :is_paid, :is_retention, :workflow_id)
                        EOD;
                $stmtInspectionPeriods = $this->db->prepare($sql);

                // INSERT inspection_period_details
                $sql = <<<EOD
                            INSERT INTO `inspection_period_details`(`inspection_id`) 
                            VALUES (:inspection_id)
                        EOD;
                $stmtInspectionPeriodDetails = $this->db->prepare($sql);

                // INSERT inspection_period_approvals
                $sql = <<<EOD
                            INSERT INTO `inspection_period_approvals`(`inspection_id`, `period_id`, `po_id`, `period_number`, `approval_level`, `approver_id`, `approval_type_id`, `approval_type_text`, `approval_status_id`) 
                            VALUES (:inspection_id, :period_id, :po_id, :period_number, :approval_level, :approver_id, :approval_type_id, :approval_type_text, :approval_status_id)
                        EOD;
                $stmtInspectApprovals = $this->db->prepare($sql);

                foreach ($insert_indexs as $i) { //ถ้าต้องการใช้ค่าของ key ให้เขียนแบบนี้ foreach($insert_indexs as $key=> $value){
                    $stmtPoPeriod->bindParam(':po_id', $po_id, PDO::PARAM_INT);
                    $stmtPoPeriod->bindParam(':period_number', $period_numbers[$i], PDO::PARAM_INT);
                    $stmtPoPeriod->bindParam(':workload_planned_percent', $workload_planned_percents[$i],  PDO::PARAM_STR);
                    $stmtPoPeriod->bindParam(':interim_payment', $interim_payments[$i],  PDO::PARAM_STR);
                    $stmtPoPeriod->bindParam(':interim_payment_percent', $interim_payment_percents[$i], PDO::PARAM_STR);
                    $stmtPoPeriod->bindParam(':remark', $remarks[$i], PDO::PARAM_STR);

                    $stmtPoPeriod->execute();
                    $stmtPoPeriod->closeCursor();

                    $period_id = $this->db->lastInsertId();

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

                    $inspection_id = $this->db->lastInsertId();

                    $stmtInspectionPeriodDetails->bindParam(':inspection_id', $inspection_id, PDO::PARAM_INT);

                    $stmtInspectionPeriodDetails->execute();
                    $stmtInspectionPeriodDetails->closeCursor();

                    // inspection_period_approvals
                    $approval_status_id = 1;
                    foreach ($rsWorkflowSteps as $row) {
                        $approverId = $row['approver_id'];
                        $approvalLevel = $row['approval_level'];
                        $approvalTypeId = $row['approval_type_id'];
                        $approvalTypeText = $row['approval_type_text'];
                        $actionType = $row['action_type']; // สมมติว่ามี action_type เช่น 'approval', 'submit', 'confirm', 'verify'

                        $stmtInspectApprovals->bindParam(':inspection_id', $inspection_id, PDO::PARAM_INT);
                        $stmtInspectApprovals->bindParam(':period_id', $period_id, PDO::PARAM_INT);
                        $stmtInspectApprovals->bindParam(':po_id', $po_id, PDO::PARAM_INT);
                        $stmtInspectApprovals->bindParam(':period_number', $period_numbers[$i], PDO::PARAM_INT);
                        $stmtInspectApprovals->bindParam(':approval_level', $approvalLevel,  PDO::PARAM_INT);
                        $stmtInspectApprovals->bindParam(':approver_id', $approverId, PDO::PARAM_INT);
                        $stmtInspectApprovals->bindParam(':approval_type_id', $approvalTypeId, PDO::PARAM_INT);
                        $stmtInspectApprovals->bindParam(':approval_type_text', $approvalTypeText, PDO::PARAM_STR);
                        $stmtInspectApprovals->bindParam(':approval_status_id', $approval_status_id, PDO::PARAM_INT);

                        $stmtInspectApprovals->execute();
                    }
                    $stmtInspectApprovals->closeCursor();
                }

                $_SESSION['message'] =  'data has been created successfully.';
                $this->db->commit();
                return true;
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $_SESSION['message'] =  'This item could not be added.Because the data has duplicate values!!!';
            } else {
                $_SESSION['message'] =  $e->getCode() + ' ' + $e->getMessage();
            }
            $this->db->rollBack();
            return false;
        } finally {
            // $stmt->closeCursor();
            // $stmtPoPeriod->closeCursor();
            // $stmtSubs->closeCursor();
            // unset($stmt);
            // unset($stmtPoPeriod);
        }
    }

    public function update(int $getId, array $getData)
    {
        // ถ้ารายการผ่านขั้นตอนแรกใน inspection_period_approvals (เปลี่ยน approval_status_id จาก 1-pending เป็น 2-approved) แล้วจะต้องห้ามแก้ไขหรือลบ period นี้
        // แต่ถ้า approval_status_id เปลี่ยนจาก 1-pending เป็น 0-reject จะสามารถแก้ไขหรือลบได้
        // ในขั้นตอนเริ่มต้นของ approval_type ที่เป็น submit จะไม่สามารถ reject เอกสารของตัวเองได้่  ทำได้เพียงเปลี่ยนจาก 1-pending เป็น 2-approved 
        // เพื่อเปลี่ยน approval_type เป็นค่าอื่นที่ไม่ใช่ submit เพื่อส่งให้ผู้ดำเนินการในลำดับถัดไป เช่น จาก 1-submit เป็น verify, confirm หรือ approve ตามแต่ที่กำหนดใน inspection_period_approvals
        // และในการลบจะยังคงลบจากรายการสุดท้ายก่อนเสมอ
        @session_start();

        try {
            $this->db->beginTransaction();

            // กำหนดค่า default สำหรับ workflow step ของ inspection และ ipc (อาจจะมีหน้าจอ config) โดยที่
            // 1. ทำการสร้าง inspection_period_approvals เมื่อมีการ save po เรียบร้อยแล้ว
            // 2. ทำการสร้าง ipc_period_approvals เมื่อมีการ approve ใน step สุดท้ายของ inspection ในแต่ละ period  
            // workflow_id = 1 สร้าง inspection_period_approvals
            // workflow_id = 2 สร้าง ipc_period_approvals
            $workflow_id = 1; //ในที่นี้กำหนด workflow_id = 1 ของการสร้าง inspection_period_approvals
            $sql = <<<EOD
                        SELECT `workflow_step_id`, `workflow_id`, `approval_level`, `approver_id`, `approval_type_id`, `approval_type_text`
                        FROM `workflow_steps`
                        WHERE `workflow_id` = :workflow_id
                        ORDER BY approval_level asc
                    EOD;
            $stmtWorkflowSteps = $this->db->prepare($sql);
            $stmtWorkflowSteps->bindParam(':workflow_id', $workflow_id, PDO::PARAM_INT);
            $stmtWorkflowSteps->execute();
            $rsWorkflowSteps = $stmtWorkflowSteps->fetchAll();

            // parameters ในส่วน po_main
            $po_id = $getId;
            // $po_number = $getData['po_number'];
            $project_name = $getData['project_name'];
            $supplier_id = $getData['supplier_id'];
            $location_id = $getData['location_id'];
            $working_name_th = $getData['working_name_th'];
            $working_name_en = $getData['working_name_en'];
            $is_include_vat = 1;
            $contract_value = floatval($getData['contract_value'] ?? 0);
            $contract_value_before = floatval($getData['contract_value_before'] ?? 0);
            $vat = floatval($getData['vat'] ?? 0);
            $is_deposit = 1;//$getData['is_deposit'];
            $deposit_percent = floatval($getData['deposit_percent'] ?? 0);
            $deposit_value = ($deposit_percent * $contract_value) / 100;
            $working_date_from = $getData['working_date_from'];
            $working_date_to = $getData['working_date_to'];

            // Create DateTime objects from the input strings
            // $date1 = new DateTime($working_date_from);
            // $date2 = new DateTime($working_date_to);
            // Calculate the difference between the two dates
            // $interval = $date1->diff($date2);
            // $working_day =  $interval->days + 1;
            $working_day =  $getData['working_day'];
            // $update_by = $_SESSION['user_code'];

            $remain_value_interim_payment = $contract_value;
            $po_status = 1; //1:รอตรวจ

            // parameters ในส่วน po_periods
            $period_numbers = $getData['period_numbers'];

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
            $cruds = $getData['cruds'];
            $period_ids = $getData['period_ids'];

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
            // จัดการตรงจุดนี้
            $number_of_period = count($insert_indexs) + count($update_indexs);

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
            $stmtPoMainUpdate = $this->db->prepare($sql);
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

            if ($stmtPoMainUpdate->execute()) {
                $stmtPoMainUpdate->closeCursor();

                // INSERT po_periods
                $sql = <<<EOD
                            INSERT INTO `po_periods`(`po_id`, `period_number`, `workload_planned_percent`, `interim_payment`, `interim_payment_percent`, `remark`) 
                            VALUES (:po_id, :period_number, :workload_planned_percent, :interim_payment, :interim_payment_percent, :remark)
                        EOD;
                $stmtPoPeriodInsert = $this->db->prepare($sql);

                // INSERT inspection_periods
                $sql = <<<EOD
                            INSERT INTO `inspection_periods`(`po_id`, `period_number`, `period_id`, `workload_planned_percent`, `interim_payment`, `interim_payment_percent`, `is_paid`, `is_retention`,`workflow_id`) 
                            VALUES (:po_id, :period_number, :period_id, :workload_planned_percent, :interim_payment, :interim_payment_percent, :is_paid, :is_retention,:workflow_id)
                        EOD;
                $stmtInspectionPeriodInsert = $this->db->prepare($sql);

                // INSERT inspection_period_details
                $sql = <<<EOD
                            INSERT INTO `inspection_period_details`(`inspection_id`) 
                            VALUES (:inspection_id)
                        EOD;
                $stmtInspectionPeriodDetailInsert = $this->db->prepare($sql);

                // INSERT inspection_period_approvals
                $sql = <<<EOD
                            INSERT INTO `inspection_period_approvals`(`inspection_id`, `period_id`, `po_id`, `period_number`, `approval_level`, `approver_id`, `approval_type_id`, `approval_type_text`, `approval_status_id`) 
                            VALUES (:inspection_id, :period_id, :po_id, :period_number, :approval_level, :approver_id, :approval_type_id, :approval_type_text, :approval_status_id)
                        EOD;
                $stmtInspectApprovals = $this->db->prepare($sql);

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

                    $stmtPoPeriodInsert->execute();
                    $stmtPoPeriodInsert->closeCursor();

                    $period_id = $this->db->lastInsertId();

                    $stmtInspectionPeriodInsert->bindParam(':period_id', $period_id, PDO::PARAM_INT);
                    $stmtInspectionPeriodInsert->bindParam(':po_id', $po_id, PDO::PARAM_INT);
                    $stmtInspectionPeriodInsert->bindParam(':period_number', $period_numbers[$i], PDO::PARAM_INT);
                    $stmtInspectionPeriodInsert->bindParam(':workload_planned_percent', $workload_planned_percents[$i],  PDO::PARAM_STR);
                    $stmtInspectionPeriodInsert->bindParam(':interim_payment', $interim_payments[$i],  PDO::PARAM_STR);
                    $stmtInspectionPeriodInsert->bindParam(':interim_payment_percent', $interim_payment_percents[$i], PDO::PARAM_STR);
                    $stmtInspectionPeriodInsert->bindParam(':is_paid', $is_paid, PDO::PARAM_BOOL);
                    $stmtInspectionPeriodInsert->bindParam(':is_retention', $is_retention, PDO::PARAM_BOOL);
                    $stmtInspectionPeriodInsert->bindParam(':workflow_id', $workflow_id, PDO::PARAM_INT);

                    $stmtInspectionPeriodInsert->execute();
                    $stmtInspectionPeriodInsert->closeCursor();

                    $inspection_id = $this->db->lastInsertId();

                    $stmtInspectionPeriodDetailInsert->bindParam(':inspection_id', $inspection_id, PDO::PARAM_INT);

                    $stmtInspectionPeriodDetailInsert->execute();
                    $stmtInspectionPeriodDetailInsert->closeCursor();

                    // inspection_period_approvals
                    $approval_status_id = 1;
                    foreach ($rsWorkflowSteps as $row) {
                        $approverId = $row['approver_id'];
                        $approvalLevel = $row['approval_level'];
                        $approvalTypeId = $row['approval_type_id'];
                        $approvalTypeText = $row['approval_type_text'];
                        $actionType = $row['action_type']; // สมมติว่ามี action_type เช่น 'approval', 'submit', 'confirm', 'verify'

                        $stmtInspectApprovals->bindParam(':inspection_id', $inspection_id, PDO::PARAM_INT);
                        $stmtInspectApprovals->bindParam(':period_id', $period_id, PDO::PARAM_INT);
                        $stmtInspectApprovals->bindParam(':po_id', $po_id, PDO::PARAM_INT);
                        $stmtInspectApprovals->bindParam(':period_number', $period_numbers[$i], PDO::PARAM_INT);
                        $stmtInspectApprovals->bindParam(':approval_level', $approvalLevel,  PDO::PARAM_INT);
                        $stmtInspectApprovals->bindParam(':approver_id', $approverId, PDO::PARAM_INT);
                        $stmtInspectApprovals->bindParam(':approval_type_id', $approvalTypeId, PDO::PARAM_INT);
                        $stmtInspectApprovals->bindParam(':approval_type_text', $approvalTypeText, PDO::PARAM_STR);
                        $stmtInspectApprovals->bindParam(':approval_status_id', $approval_status_id, PDO::PARAM_INT);

                        $stmtInspectApprovals->execute();
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
                $stmtPoPeriodUpdate = $this->db->prepare($sql);

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
                $stmtInspectionPeriodUpdate = $this->db->prepare($sql);

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
                $stmtPoPeriodDelete = $this->db->prepare($sql);

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
                    $stmt = $this->db->prepare($sql);
                    $stmt->bindParam(':period_id', $period_id, PDO::PARAM_INT);
                    $stmt->execute();
                    $rs = $stmt->fetchAll();

                    $stmtPoPeriodDelete->bindParam(':po_id', $po_id, PDO::PARAM_INT);
                    $stmtPoPeriodDelete->bindParam(':period_id', $period_id, PDO::PARAM_INT);

                    $stmtPoPeriodDelete->execute();
                    $stmtPoPeriodDelete->closeCursor();

                    // ลบไฟล์ออกจาก server
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

                $_SESSION['message'] =  'Data has been created successfully.';
                $this->db->commit();
                return true;
            }
        } catch (PDOException $e) {
            $_SESSION['message'] =  $e->getCode() + ' : ' + $e->getMessage();
            $this->db->rollBack();
            return false;
        }
    }

    public function delete(int $getId)
    {
        try {
            $po_id = $getId;

            $sql = <<<EOD
                        SELECT file_path
                        FROM `inspection_files` 
                        INNER JOIN `inspection_periods`
                            ON `inspection_files`.`inspection_id` = `inspection_periods`.`inspection_id`
                        INNER JOIN `po_periods`
                            ON `po_periods`.`period_id` = `inspection_periods`.`period_id`
                        WHERE `po_periods`.`po_id` = :po_id
                    EOD;
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':po_id', $po_id, PDO::PARAM_INT);
            $stmt->execute();
            $rs = $stmt->fetchAll();

            $sql = <<<EOD
                    DELETE FROM `po_main` 
                    WHERE po_id = :po_id;
                    EOD;
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':po_id', $po_id, PDO::PARAM_INT);
            $stmt->execute();

            // ลบไฟล์ออกจาก server
            foreach ($rs as $row) {
                $filePath = $row['file_path'];
                if (file_exists($filePath)) {
                    unlink($filePath); // ลบไฟล์
                }
            }

            return true;
        } catch (PDOException $e) {
            return false;
        }
    }



    public function getHtmlData()
    {
        $sql = "select po_id, po_name, is_deleted 
                from po
                where is_deleted = false";

        $stmt = $this->db->prepare($sql);
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