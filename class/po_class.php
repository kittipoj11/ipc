<?php
@session_start();
// require_once 'config.php';
require_once 'connection_class.php';

class Po
{
    private $db;
    
    public function __construct(PDO $pdoConnection)
    {
        $this->db = $pdoConnection;
    }

    public function getAll(): array
    {
        $sql = "SELECT `po_id`, `po_number`, `project_name`, p.`supplier_id`, p.`location_id`
                    , `working_name_th`, `working_name_en`, `is_include_vat`, `contract_value`, `contract_value_before`, `vat`
                    , `deposit_percent`, `deposit_value`, `retention_percent`, `retention_value`
                    , `working_date_from`, `working_date_to`, `working_day`
                    , `create_by`, `create_date`, `number_of_period`
                    , s.`supplier_name`
                    , l.`location_name`
                    FROM `po_main` p
                    INNER JOIN `suppliers` s
                        ON s.`supplier_id` = p.`supplier_id`
                    INNER JOIN `locations` l
                        ON l.`location_id` = p.`location_id`
                    ORDER BY `po_id`";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        $rs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $rs;
    }

    public function getByPoId($poId): ?array
    {
        // ดึงข้อมูลจากตารางหลัก - po_main
        $sql = "SELECT `po_id`, `po_number`, `project_name`, p.`supplier_id`, p.`location_id`
                , `working_name_th`, `working_name_en`, `is_include_vat`, `contract_value`, `contract_value_before`, `vat`
                , `deposit_percent`, `deposit_value`, `retention_percent`, `retention_value`
                , `working_date_from`, `working_date_to`, `working_day`
                , `create_by`, `create_date`, `number_of_period`
                , s.`supplier_name`
                , l.`location_name`
                FROM `po_main` p
                INNER JOIN `suppliers` s
                    ON s.`supplier_id` = p.`supplier_id`
                INNER JOIN `locations` l
                    ON l.`location_id` = p.`location_id`
                WHERE `po_id` = :po_id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':po_id', $poId, PDO::PARAM_INT);
        $stmt->execute();
        $rs = $stmt->fetch();
        if (!$rs) {
            return null; // ไม่พบข้อมูล
        }
        // return $rs ?: null;

        // ดึงข้อมูลจากตารางรอง
        $rs['periods'] = $this->getAllPeriodByPoId($poId);

        return $rs;
    }

    public function getAllPeriodByPoId($poId): array
    {
        $sql = "SELECT `period_id`, `po_id`, `period_number`, `workload_planned_percent`, `interim_payment`, `interim_payment_percent`, `remark`
                FROM `po_periods`
                WHERE `po_id` = :po_id
                ORDER BY `po_id`, `period_number`";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':po_id', $poId, PDO::PARAM_INT);
        $stmt->execute();

        $rs = $stmt->fetchAll();
        return $rs;
    }

    public function save(array $data): int
    {
        $poId = $data['po_id'] ?? 0;
        if (empty($poId)) { //ถ้าไม่มีค่าหรือมีค่าเป็น 0
            // --- CREATE MODE ---
            // ถ้าจะสร้าง id มี prefix ด้วยตนเอง สมมติให้ prefix เป็น PO เช่น $poId = uniqid('PO', true);
            // INSERT INTO po_main"
            $sql = "INSERT INTO `po_main`(`po_number`, `project_name`, `supplier_id`, `location_id`, `working_name_th`, `working_name_en`
                    , `is_include_vat`, `contract_value`, `contract_value_before`, `vat`
                    , `deposit_percent`, `deposit_value`, `retention_percent`, `retention_value`
                    , `working_date_from`, `working_date_to`, `working_day`, `create_by`, `number_of_period`, `workflow_id`) 
                    VALUES(:po_number, :project_name, :supplier_id, :location_id, :working_name_th, :working_name_en
                    , :is_include_vat, :contract_value, :contract_value_before, :vat
                    , :deposit_percent, :deposit_value, :retention_percent, :retention_value
                    , :working_date_from, :working_date_to, :working_day, :create_by, :number_of_period, :workflow_id)";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':po_number', $data['po_number'], PDO::PARAM_STR);
            $stmt->bindParam(':project_name', $data['project_name'], PDO::PARAM_STR);
            $stmt->bindParam(':supplier_id', $data['supplier_id'],  PDO::PARAM_INT);
            $stmt->bindParam(':location_id', $data['location_id'], PDO::PARAM_INT);
            $stmt->bindParam(':working_name_th', $data['working_name_th'], PDO::PARAM_STR);
            $stmt->bindParam(':working_name_en', $data['working_name_en'], PDO::PARAM_STR);
            $stmt->bindParam(':is_include_vat', $data['is_include_vat'], PDO::PARAM_BOOL);
            $stmt->bindParam(':contract_value_before', $data['contract_value_before'], PDO::PARAM_STR);
            $stmt->bindParam(':contract_value', $data['contract_value'], PDO::PARAM_STR);
            $stmt->bindParam(':vat', $data['vat'], PDO::PARAM_STR);
            // $stmt->bindParam(':is_deposit', $data['is_deposit'], PDO::PARAM_BOOL);
            $stmt->bindParam(':deposit_percent', $data['deposit_percent'], PDO::PARAM_STR);
            $stmt->bindParam(':deposit_value', $data['deposit_value'], PDO::PARAM_STR);
            $stmt->bindParam(':retention_percent', $data['retention_percent'], PDO::PARAM_STR);
            $stmt->bindParam(':retention_value', $data['retention_value'], PDO::PARAM_STR);
            $stmt->bindParam(':working_date_from', $data['working_date_from'], PDO::PARAM_STR);
            $stmt->bindParam(':working_date_to', $data['working_date_to'], PDO::PARAM_STR);
            $stmt->bindParam(':working_day', $data['working_day'], PDO::PARAM_INT);
            $stmt->bindParam(':number_of_period', $data['number_of_period'], PDO::PARAM_INT);
            $stmt->bindParam(':create_by', $_SESSION['user_code'], PDO::PARAM_STR);
            $stmt->bindParam(':workflow_id', $workflowId, PDO::PARAM_INT);
            $stmt->execute();
            $stmt->closeCursor();
            // กำหนดค่าให้ตัวแปร $poId จาก ID ของ PO ที่เพิ่งสร้างใหม่ด้วย lastInsertId()
            $poId = $this->db->lastInsertId();
        } else {
            // --- UPDATE MODE ---
            $sql = "UPDATE `po_main`
                    SET `project_name`= :project_name
                    , `supplier_id`= :supplier_id
                    , `location_id`= :location_id
                    , `working_name_th`= :working_name_th
                    , `working_name_en`= :working_name_en
                    , `is_include_vat`= :is_include_vat
                    , `contract_value_before`= :contract_value_before
                    , `contract_value`= :contract_value
                    , `vat`= :vat
                    , `deposit_percent`= :deposit_percent
                    , `deposit_value`= :deposit_value
                    , `retention_percent`= :retention_percent
                    , `retention_value`= :retention_value
                    , `working_date_from`= :working_date_from
                    , `working_date_to`= :working_date_to
                    , `working_day`= :working_day
                    , `number_of_period` = :number_of_period
                    WHERE `po_id` = :po_id";

            $stmt = $this->db->prepare($sql);
            // $stmt->bindParam(':po_number', $data['po_number'], PDO::PARAM_STR);
            $stmt->bindParam(':project_name', $data['project_name'], PDO::PARAM_STR);
            $stmt->bindParam(':supplier_id', $data['supplier_id'],  PDO::PARAM_INT);
            $stmt->bindParam(':location_id', $data['location_id'], PDO::PARAM_INT);
            $stmt->bindParam(':working_name_th', $data['working_name_th'], PDO::PARAM_STR);
            $stmt->bindParam(':working_name_en', $data['working_name_en'], PDO::PARAM_STR);
            $stmt->bindParam(':is_include_vat', $data['is_include_vat'], PDO::PARAM_BOOL);
            $stmt->bindParam(':contract_value_before', $data['contract_value_before'], PDO::PARAM_STR);
            $stmt->bindParam(':contract_value', $data['contract_value'], PDO::PARAM_STR);
            $stmt->bindParam(':vat', $data['vat'], PDO::PARAM_STR);
            // $stmt->bindParam(':is_deposit', $data['is_deposit'], PDO::PARAM_BOOL);
            $stmt->bindParam(':deposit_percent', $data['deposit_percent'], PDO::PARAM_STR);
            $stmt->bindParam(':deposit_value', $data['deposit_value'], PDO::PARAM_STR);
            $stmt->bindParam(':retention_percent', $data['retention_percent'], PDO::PARAM_STR);
            $stmt->bindParam(':retention_value', $data['retention_value'], PDO::PARAM_STR);
            $stmt->bindParam(':working_date_from', $data['working_date_from'], PDO::PARAM_STR);
            $stmt->bindParam(':working_date_to', $data['working_date_to'], PDO::PARAM_STR);
            $stmt->bindParam(':working_day', $data['working_day'], PDO::PARAM_INT);
            $stmt->bindParam(':number_of_period', $data['number_of_period'], PDO::PARAM_INT);
            // $stmt->bindParam(':create_by', $_SESSION['user_code'], PDO::PARAM_STR);
            // $stmt->bindParam(':workflow_id', $workflowId, PDO::PARAM_INT);
            $stmt->bindParam(':po_id', $poId, PDO::PARAM_INT);
            // $stmt->execute();
            $stmt->execute();

            $stmt->closeCursor();
        }
        return (int)$poId;
    }

        public function save_old(array $headerData, array $periodsData): int
    {
        // --- WORKFLOW ---
        // กำหนดค่า default สำหรับ workflow step ของ inspection และ ipc (อาจจะมีหน้าจอ config) โดยที่
        // 1. ทำการสร้าง inspection_approvals เมื่อมีการ save po เรียบร้อยแล้ว
        // 2. ทำการสร้าง ipc_period_approvals เมื่อมีการ approve ใน step สุดท้ายของ inspection ในแต่ละ period  
        // workflow_id = 1 สร้าง inspection_approvals
        // workflow_id = 2 สร้าง ipc_period_approvals
        $workflowId = 1; //ในที่นี้กำหนด workflow_id = 1
        
        $this->db->beginTransaction();
        try {
            // กำหนดค่าให้ตัวแปร $poId ที่ส่งมา
            $poId = $headerData['po_id'] ?? 0;

            // 1. ตรวจสอบและจัดการข้อมูล Header (INSERT หรือ UPDATE)
            if (empty($poId)) { //ถ้าไม่มีค่าหรือมีค่าเป็น 0
                // --- CREATE MODE ---
                // ถ้าจะสร้าง id มี prefix ด้วยตนเอง สมมติให้ prefix เป็น PO เช่น $poId = uniqid('PO', true);
                // INSERT INTO po_main"
                $sql = "INSERT INTO `po_main`(`po_number`, `project_name`, `supplier_id`, `location_id`, `working_name_th`, `working_name_en`
                    , `is_include_vat`, `contract_value`, `contract_value_before`, `vat`, `is_deposit`, `deposit_percent`, `deposit_value`
                    , `working_date_from`, `working_date_to`, `working_day`, `create_by`, `number_of_period`, `workflow_id`) 
                    VALUES(:po_number, :project_name, :supplier_id, :location_id, :working_name_th, :working_name_en
                    , :is_include_vat, :contract_value, :contract_value_before, :vat, :is_deposit, :deposit_percent, :deposit_value
                    , :working_date_from, :working_date_to, :working_day, :create_by, :number_of_period, :workflow_id)";

                $stmtCreatePoMain = $this->db->prepare($sql);
                $stmtCreatePoMain->bindParam(':po_number', $headerData['po_number'], PDO::PARAM_STR);
                $stmtCreatePoMain->bindParam(':project_name', $headerData['project_name'], PDO::PARAM_STR);
                $stmtCreatePoMain->bindParam(':supplier_id', $headerData['supplier_id'],  PDO::PARAM_INT);
                $stmtCreatePoMain->bindParam(':location_id', $headerData['location_id'], PDO::PARAM_INT);
                $stmtCreatePoMain->bindParam(':working_name_th', $headerData['working_name_th'], PDO::PARAM_STR);
                $stmtCreatePoMain->bindParam(':working_name_en', $headerData['working_name_en'], PDO::PARAM_STR);
                $stmtCreatePoMain->bindParam(':is_include_vat', $headerData['is_include_vat'], PDO::PARAM_BOOL);
                $stmtCreatePoMain->bindParam(':contract_value_before', $headerData['contract_value_before'], PDO::PARAM_STR);
                $stmtCreatePoMain->bindParam(':contract_value', $headerData['contract_value'], PDO::PARAM_STR);
                $stmtCreatePoMain->bindParam(':vat', $headerData['vat'], PDO::PARAM_STR);
                $stmtCreatePoMain->bindParam(':is_deposit', $headerData['is_deposit'], PDO::PARAM_BOOL);
                $stmtCreatePoMain->bindParam(':deposit_percent', $headerData['deposit_percent'], PDO::PARAM_STR);
                $stmtCreatePoMain->bindParam(':deposit_value', $deposit_value, PDO::PARAM_STR);
                $stmtCreatePoMain->bindParam(':working_date_from', $headerData['working_date_from'], PDO::PARAM_STR);
                $stmtCreatePoMain->bindParam(':working_date_to', $headerData['working_date_to'], PDO::PARAM_STR);
                $stmtCreatePoMain->bindParam(':working_day', $headerData['working_day'], PDO::PARAM_INT);
                $stmtCreatePoMain->bindParam(':number_of_period', $headerData['number_of_period'], PDO::PARAM_INT);
                $stmtCreatePoMain->bindParam(':create_by', $_SESSION['user_code'], PDO::PARAM_STR);
                $stmtCreatePoMain->bindParam(':workflow_id', $workflowId, PDO::PARAM_INT);
                $stmtCreatePoMain->execute();
                $stmtCreatePoMain->closeCursor();
                // กำหนดค่าให้ตัวแปร $poId จาก ID ของ PO ที่เพิ่งสร้างใหม่ด้วย lastInsertId()
                $poId = $this->db->lastInsertId();
            } else {
                // --- UPDATE MODE ---
                $sql = "UPDATE `po_main`
                        SET `project_name`= :project_name
                        , `supplier_id`= :supplier_id
                        , `location_id`= :location_id
                        , `working_name_th`= :working_name_th
                        , `working_name_en`= :working_name_en
                        , `is_include_vat`= :is_include_vat
                        , `contract_value_before`= :contract_value_before
                        , `contract_value`= :contract_value
                        , `vat`= :vat
                        , `is_deposit`= :is_deposit
                        , `deposit_percent`= :deposit_percent
                        , `deposit_value`= :deposit_value
                        , `working_date_from`= :working_date_from
                        , `working_date_to`= :working_date_to
                        , `working_day`= :working_day
                        , `number_of_period` = :number_of_period
                        WHERE `po_id` = :po_id";

                $stmtUpdatePoMain = $this->db->prepare($sql);
                // $stmtUpdatePoMain->bindParam(':po_number', $headerData['po_number'], PDO::PARAM_STR);
                $stmtUpdatePoMain->bindParam(':project_name', $headerData['project_name'], PDO::PARAM_STR);
                $stmtUpdatePoMain->bindParam(':supplier_id', $headerData['supplier_id'],  PDO::PARAM_INT);
                $stmtUpdatePoMain->bindParam(':location_id', $headerData['location_id'], PDO::PARAM_INT);
                $stmtUpdatePoMain->bindParam(':working_name_th', $headerData['working_name_th'], PDO::PARAM_STR);
                $stmtUpdatePoMain->bindParam(':working_name_en', $headerData['working_name_en'], PDO::PARAM_STR);
                $stmtUpdatePoMain->bindParam(':is_include_vat', $headerData['is_include_vat'], PDO::PARAM_BOOL);
                $stmtUpdatePoMain->bindParam(':contract_value_before', $headerData['contract_value_before'], PDO::PARAM_STR);
                $stmtUpdatePoMain->bindParam(':contract_value', $headerData['contract_value'], PDO::PARAM_STR);
                $stmtUpdatePoMain->bindParam(':vat', $headerData['vat'], PDO::PARAM_STR);
                $stmtUpdatePoMain->bindParam(':is_deposit', $headerData['is_deposit'], PDO::PARAM_BOOL);
                $stmtUpdatePoMain->bindParam(':deposit_percent', $headerData['deposit_percent'], PDO::PARAM_STR);
                $stmtUpdatePoMain->bindParam(':deposit_value', $deposit_value, PDO::PARAM_STR);
                $stmtUpdatePoMain->bindParam(':working_date_from', $headerData['working_date_from'], PDO::PARAM_STR);
                $stmtUpdatePoMain->bindParam(':working_date_to', $headerData['working_date_to'], PDO::PARAM_STR);
                $stmtUpdatePoMain->bindParam(':working_day', $headerData['working_day'], PDO::PARAM_INT);
                $stmtUpdatePoMain->bindParam(':number_of_period', $headerData['number_of_period'], PDO::PARAM_INT);
                // $stmtUpdatePoMain->bindParam(':create_by', $_SESSION['user_code'], PDO::PARAM_STR);
                // $stmtUpdatePoMain->bindParam(':workflow_id', $workflowId, PDO::PARAM_INT);
                $stmtUpdatePoMain->bindParam(':po_id', $poId, PDO::PARAM_INT);
                // $stmtUpdatePoMain->execute();
                $stmtUpdatePoMain->execute();
                
                $stmtUpdatePoMain->closeCursor();
            }

            // ถ้า $poId ยังคงเป็น 0 หรือว่าง แสดงว่าเกิดข้อผิดพลาด
            if (empty($poId)) {
                throw new Exception("Could not create or find a valid PO ID.");
            }

            // 2. จัดการข้อมูล Periods ตามลำดับ(D-U-C Logic)
            $deleteItems = array_filter($periodsData, fn($item) => ($item['period_crud'] ?? 'none') === 'delete');
            $updateItems = array_filter($periodsData, fn($item) => ($item['period_crud'] ?? 'none') === 'update');
            $createItems = array_filter($periodsData, fn($item) => ($item['period_crud'] ?? 'none') === 'create');

            $_SESSION['delete']=$deleteItems;
            $_SESSION['update']=$updateItems;
            $_SESSION['create']=$createItems;

            $isPaid=0;
            $isRetention=0;

            // 3. ทำงานตามลำดับ D-U-C
            // 3.1 ************************* ตรวจสอบ deleteItems ****************************
            if (!empty($deleteItems)) {
                $stmtDelete = $this->db->prepare("DELETE FROM po_periods WHERE period_id = :period_id");
                foreach ($deleteItems as $item) {
                    if (!empty($item['period_id'])) {
                        $stmtDelete->execute([$item['period_id']]);
                        $stmtDelete->closeCursor();
                    }

                    // ดึงข้อมูลไฟล์ที่จะลบ
                    $sql = "SELECT file_path
                            FROM `inspection_files` 
                            INNER JOIN `inspection`
                                ON `inspection_files`.`inspection_id` = `inspection`.`inspection_id`
                            WHERE `period_id` = :period_id";

                    $stmt = $this->db->prepare($sql);
                    $stmt->bindParam(':period_id', $item['period_id'], PDO::PARAM_INT);
                    $stmt->execute();
                    $rs = $stmt->fetchAll();

                    // ลบไฟล์ออกจาก server
                    foreach ($rs as $row) {
                        $filePath = $row['file_path'];
                        if (file_exists($filePath)) {
                            unlink($filePath); // ลบไฟล์
                        }
                    }
                }
            }

            // 3.2 ************************* ตรวจสอบ updateItems ****************************
            // ถ้ารายการผ่านขั้นตอนแรกใน inspection_approvals (เปลี่ยน approval_status_id จาก 1-pending เป็น 2-approved) แล้วจะต้องห้ามแก้ไขหรือลบ period นี้
            // แต่ถ้า approval_status_id เปลี่ยนจาก 1-pending เป็น 0-reject จะสามารถแก้ไขหรือลบได้
            // ในขั้นตอนเริ่มต้นของ approval_type ที่เป็น submit จะไม่สามารถ reject เอกสารของตัวเองได้่  ทำได้เพียงเปลี่ยนจาก 1-pending เป็น 2-approved 
            // เพื่อเปลี่ยน approval_type เป็นค่าอื่นที่ไม่ใช่ submit เพื่อส่งให้ผู้ดำเนินการในลำดับถัดไป เช่น จาก 1-submit เป็น verify, confirm หรือ approve ตามแต่ที่กำหนดใน inspection_approvals
            // และในการลบจะยังคงลบจากรายการสุดท้ายก่อนเสมอ
            if (!empty($updateItems)) {
                // UPDATE po_periods
                $sql = "UPDATE `po_periods`
                            SET `workload_planned_percent` = :workload_planned_percent
                            , `interim_payment` = :interim_payment
                            , `interim_payment_percent` = :interim_payment_percent
                            , `remark` = :remark
                            WHERE `po_id` = :po_id
                                AND `period_id` = :period_id";
                $stmtUpdatePoPeriod = $this->db->prepare($sql);

                // UPDATE inspection
                $sql = "UPDATE `inspection`
                        SET `workload_planned_percent` = :workload_planned_percent
                        , `interim_payment` = :interim_payment
                        , `interim_payment_percent` = :interim_payment_percent
                        , `is_paid` = :is_paid
                        , `is_retention` = :is_retention
                        WHERE `po_id` = :po_id
                            AND `period_id` = :period_id";
                $stmtUpdateInspectionPeriod = $this->db->prepare($sql);

                        // $_SESSION['updateItems'] = $updateItems;
                foreach ($updateItems as $item) {
                    if (!empty($item['period_id'])) {
                        $stmtUpdatePoPeriod->bindParam(':po_id', $poId, PDO::PARAM_INT);
                        $stmtUpdatePoPeriod->bindParam(':period_id', $item['period_id'], PDO::PARAM_INT);
                        $stmtUpdatePoPeriod->bindParam(':workload_planned_percent', $item['workload_planned_percent'],  PDO::PARAM_STR);
                        $stmtUpdatePoPeriod->bindParam(':interim_payment', $item['interim_payment'],  PDO::PARAM_STR);
                        $stmtUpdatePoPeriod->bindParam(':interim_payment_percent', $item['interim_payment_percent'], PDO::PARAM_STR);
                        $stmtUpdatePoPeriod->bindParam(':remark', $item['remark'], PDO::PARAM_STR);
                        $stmtUpdatePoPeriod->execute();
                        $stmtUpdatePoPeriod->closeCursor();

                        $stmtUpdateInspectionPeriod->bindParam(':po_id', $poId, PDO::PARAM_INT);
                        $stmtUpdateInspectionPeriod->bindParam(':period_id', $item['period_id'], PDO::PARAM_INT);
                        $stmtUpdateInspectionPeriod->bindParam(':workload_planned_percent', $item['workload_planned_percent'],  PDO::PARAM_STR);
                        $stmtUpdateInspectionPeriod->bindParam(':interim_payment', $item['interim_payment'],  PDO::PARAM_STR);
                        $stmtUpdateInspectionPeriod->bindParam(':interim_payment_percent', $item['interim_payment_percent'], PDO::PARAM_STR);
                        $stmtUpdateInspectionPeriod->bindParam(':is_paid', $is_paid, PDO::PARAM_BOOL);
                        $stmtUpdateInspectionPeriod->bindParam(':is_retention', $is_retention, PDO::PARAM_BOOL);
                        $stmtUpdateInspectionPeriod->execute();
                        $stmtUpdateInspectionPeriod->closeCursor();
                    }
                }
            }

            // 3.3 ************************* ตรวจสอบ createItems ****************************
            if (!empty($createItems)) {
                // ดึงข้อมูล workflow step เพื่อนำ Loop สร้าง inspection_approvals
                $sql = "SELECT `workflow_step_id`, `workflow_id`, `approval_level`, `approver_id`, `approval_type_id`, `approval_type_text`
                        FROM `workflow_steps`
                        WHERE `workflow_id` = :workflow_id
                        ORDER BY approval_level asc";

                $stmtWorkflowSteps = $this->db->prepare($sql);
                $stmtWorkflowSteps->bindParam(':workflow_id', $workflowId, PDO::PARAM_INT);
                $stmtWorkflowSteps->execute();
                $rsWorkflowSteps = $stmtWorkflowSteps->fetchAll();
                
                // INSERT INTO po_periods
                $sql = "INSERT INTO `po_periods`(`po_id`, `period_number`, `workload_planned_percent`, `interim_payment`, `interim_payment_percent`, `remark`) 
                        VALUES (:po_id, :period_number, :workload_planned_percent, :interim_payment, :interim_payment_percent, :remark)";
                $stmtCreatePoPeriods = $this->db->prepare($sql);

                // INSERT INTO inspection
                $sql = "INSERT INTO `inspection`(`po_id`, `period_number`, `period_id`, `workload_planned_percent`, `interim_payment`, `interim_payment_percent`, `is_paid`, `is_retention`, `workflow_id`) 
                        VALUES (:po_id, :period_number, :period_id, :workload_planned_percent, :interim_payment, :interim_payment_percent, :is_paid, :is_retention, :workflow_id)";
                $stmtCreateInspectionPeriods = $this->db->prepare($sql);

                // INSERT inspection_details
                $sql = "INSERT INTO `inspection_details`(`inspection_id`) 
                        VALUES (:inspection_id)";
                $stmtCreateInspectionPeriodDetails = $this->db->prepare($sql);

                // INSERT inspection_approvals
                $sql = "INSERT INTO `inspection_approvals`(`inspection_id`, `period_id`, `po_id`, `period_number`, `approval_level`, `approver_id`, `approval_type_id`, `approval_type_text`, `approval_status_id`) 
                        VALUES (:inspection_id, :period_id, :po_id, :period_number, :approval_level, :approver_id, :approval_type_id, :approval_type_text, :approval_status_id)";
                $stmtCreateInspectApprovals = $this->db->prepare($sql);

                foreach ($createItems as $item) {
                    // $_SESSION['=== interim_payment ===']= $item['interim_payment'];
                    // $_SESSION['=== interim_payment_percent ===']= $item['interim_payment_percent'];
                    // $_SESSION['=== workload_planned_percents ===']= $item['workload_planned_percent'];
                    $stmtCreatePoPeriods->bindParam(':po_id', $poId, PDO::PARAM_INT);
                    $stmtCreatePoPeriods->bindParam(':period_number', $item['period_number'], PDO::PARAM_INT);
                    $stmtCreatePoPeriods->bindParam(':workload_planned_percent', $item['workload_planned_percent'],  PDO::PARAM_STR);
                    $stmtCreatePoPeriods->bindParam(':interim_payment', $item['interim_payment'],  PDO::PARAM_STR);
                    $stmtCreatePoPeriods->bindParam(':interim_payment_percent', $item['interim_payment_percent'], PDO::PARAM_STR);
                    $stmtCreatePoPeriods->bindParam(':remark', $item['remark'], PDO::PARAM_STR);
                    $stmtCreatePoPeriods->execute();
                    $stmtCreatePoPeriods->closeCursor();

                    $periodId = $this->db->lastInsertId();

                    $stmtCreateInspectionPeriods->bindParam(':period_id', $periodId, PDO::PARAM_INT);
                    $stmtCreateInspectionPeriods->bindParam(':po_id', $poId, PDO::PARAM_INT);
                    $stmtCreateInspectionPeriods->bindParam(':period_number', $item['period_number'], PDO::PARAM_INT);
                    $stmtCreateInspectionPeriods->bindParam(':workload_planned_percent', $item['workload_planned_percent'],  PDO::PARAM_STR);
                    $stmtCreateInspectionPeriods->bindParam(':interim_payment', $item['interim_payment'],  PDO::PARAM_STR);
                    $stmtCreateInspectionPeriods->bindParam(':interim_payment_percent', $item['interim_payment_percent'], PDO::PARAM_STR);
                    $stmtCreateInspectionPeriods->bindParam(':is_paid', $isPaid, PDO::PARAM_BOOL);
                    $stmtCreateInspectionPeriods->bindParam(':is_retention', $isRetention, PDO::PARAM_BOOL);
                    $stmtCreateInspectionPeriods->bindParam(':workflow_id', $workflowId, PDO::PARAM_INT);
                    $stmtCreateInspectionPeriods->execute();
                    $stmtCreateInspectionPeriods->closeCursor();

                    $inspectionId = $this->db->lastInsertId();

                    $stmtCreateInspectionPeriodDetails->bindParam(':inspection_id', $inspectionId, PDO::PARAM_INT);
                    $stmtCreateInspectionPeriodDetails->execute();
                    $stmtCreateInspectionPeriodDetails->closeCursor();

                    $approvalStatusId = 1;
                    foreach ($rsWorkflowSteps as $row) {
                        $stmtCreateInspectApprovals->bindParam(':inspection_id', $inspectionId, PDO::PARAM_INT);
                        $stmtCreateInspectApprovals->bindParam(':period_id', $periodId, PDO::PARAM_INT);
                        $stmtCreateInspectApprovals->bindParam(':po_id', $poId, PDO::PARAM_INT);
                        $stmtCreateInspectApprovals->bindParam(':period_number', $item['period_number'], PDO::PARAM_INT);
                        $stmtCreateInspectApprovals->bindParam(':approval_level', $approvalLevel,  PDO::PARAM_INT);
                        $stmtCreateInspectApprovals->bindParam(':approver_id', $row['approver_id'], PDO::PARAM_INT);
                        $stmtCreateInspectApprovals->bindParam(':approval_type_id', $row['approval_type_id'], PDO::PARAM_INT);
                        $stmtCreateInspectApprovals->bindParam(':approval_type_text', $row['approval_type_text'], PDO::PARAM_STR);
                        $stmtCreateInspectApprovals->bindParam(':approval_status_id', $approvalStatusId, PDO::PARAM_INT);
                        $stmtCreateInspectApprovals->execute();
                    }
                    $stmtCreateInspectApprovals->closeCursor();
                }
            }

            $this->db->commit();
            // คืนค่า PO ID ที่บันทึกสำเร็จกลับไป
            return (int)$poId;
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    public function deletePeriod(int $periodId): bool
    {
        $sql = "DELETE FROM `po_periods` 
                WHERE period_id = :period_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':period_id', $periodId, PDO::PARAM_INT);

        $affected = $stmt->execute();
        $stmt->closeCursor();
        return $affected;
    }

    public function savePeriod(array $periodData):int
    {
        $periodId = $periodData['period_id'];
        if(empty($periodId)){
            $_SESSION['periodData po_class Insert QQQQQQQQQQQQQQQQQQQ'] = $periodData;
            $sql = "INSERT INTO `po_periods`(`po_id`, `period_number`, `workload_planned_percent`, `interim_payment`, `interim_payment_percent`, `remark`) 
                    VALUES (:po_id, :period_number, :workload_planned_percent, :interim_payment, :interim_payment_percent, :remark)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':po_id', $periodData['po_id'], PDO::PARAM_INT);
            $stmt->bindParam(':period_number', $periodData['period_number'], PDO::PARAM_INT);
            $stmt->bindParam(':workload_planned_percent', $periodData['workload_planned_percent'],  PDO::PARAM_STR);
            $stmt->bindParam(':interim_payment', $periodData['interim_payment'],  PDO::PARAM_STR);
            $stmt->bindParam(':interim_payment_percent', $periodData['interim_payment_percent'], PDO::PARAM_STR);
            $stmt->bindParam(':remark', $periodData['remark'], PDO::PARAM_STR);
            $stmt->execute();
            $stmt->closeCursor();
            $periodId = $this->db->lastInsertId();
            return $periodId;
        }else{
            $_SESSION['periodData po_class Update QQQQQQQQQQQQQQQQQQQ'] = $periodData;
            $sql = "UPDATE `po_periods`
                    SET `workload_planned_percent` = :workload_planned_percent
                    , `interim_payment` = :interim_payment
                    , `interim_payment_percent` = :interim_payment_percent
                    , `remark` = :remark
                    WHERE `po_id` = :po_id
                        AND `period_id` = :period_id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':po_id', $periodData['po_id'], PDO::PARAM_INT);
            $stmt->bindParam(':period_id', $periodId, PDO::PARAM_INT);
            $stmt->bindParam(':workload_planned_percent', $periodData['workload_planned_percent'],  PDO::PARAM_STR);
            $stmt->bindParam(':interim_payment', $periodData['interim_payment'],  PDO::PARAM_STR);
            $stmt->bindParam(':interim_payment_percent', $periodData['interim_payment_percent'], PDO::PARAM_STR);
            $stmt->bindParam(':remark', $periodData['remark'], PDO::PARAM_STR);
            $stmt->execute();
            $stmt->closeCursor();
            return $periodId;
        }
    }


}


// $stmt = $conn->prepare('select ...where :');
// $stmt->bindParam(':supplier_id', $getData['supplier_id'], PDO::PARAM_STR);
// $stmt->bindParam(':building_name', $building_name, PDO::PARAM_STR);
// $stmt->bindParam(':is_active', $is_active, PDO::PARAM_BOOL);
// $stmt->execute();
// $rs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// function calculateVATExcludingVAT($amount, floatval($getData['vat'] ?? 0)Rate)
// {
//     floatval($getData['vat'] ?? 0)Amount = $amount * (floatval($getData['vat'] ?? 0)Rate / 100);
//     return floatval($getData['vat'] ?? 0)Amount;
// }

// function calculateTotalIncludingVAT($amount, floatval($getData['vat'] ?? 0)Rate)
// {
//     $totalAmount = $amount * (1 + (floatval($getData['vat'] ?? 0)Rate / 100));
//     return $totalAmount;
// }

// $amount_exclude_vat = 1000;
// floatval($getData['vat'] ?? 0)_rate = 7;

// floatval($getData['vat'] ?? 0) = calculateVATExcludingVAT($amount_exclude_vat, floatval($getData['vat'] ?? 0)_rate);
// $total_include_vat = calculateTotalIncludingVAT($amount_exclude_vat, floatval($getData['vat'] ?? 0)_rate);