<?php
// require_once 'config.php';
require_once 'connection_class.php';

class Inspection
{
    private $db;

    public function __construct(PDO $pdoConnection)
    {
        $this->db = $pdoConnection;
    }

    public function getHeaderAll(): array
    {
        $sql = "SELECT `po_id`, `po_number`, `project_name`, p.`supplier_id`, p.`location_id`
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
                    ORDER BY `po_id`";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        $rs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $rs;
    }

    public function getHeaderByPoId($poId): ?array
    {
        // ดึงข้อมูลจากตารางหลัก - po_main
        $sql = "SELECT `po_id`, `po_number`, `project_name`, p.`supplier_id`, p.`location_id`
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

    public function getHeaderByPeriodId($periodId): ?array
    {
        // ดึงข้อมูลจากตารางหลัก - po_main
        $sql = "SELECT O.supplier_id, O.location_id , O.po_number, O.project_name, O.working_name_th, O.working_name_en
                , O.is_include_vat, O.contract_value, O.contract_value_before, O.vat, O.is_deposit, O.deposit_percent, O.deposit_value
                , O.working_date_from, O.working_date_to, O.working_day
                , S.supplier_name, L.location_name
                FROM po_main O
                INNER JOIN inspection_periods P
                    ON P.po_id = O.po_id
                INNER JOIN suppliers S
                    ON S.supplier_id = O.supplier_id
                INNER JOIN locations L
                    ON L.location_id = O.location_id   
                WHERE P.period_id = :period_id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':period_id', $periodId, PDO::PARAM_INT);
        $stmt->execute();
        $rs = $stmt->fetch();
        if (!$rs) {
            return null; // ไม่พบข้อมูล
        }
        // return $rs ?: null;

        return $rs;
    }

    public function getAllPeriodByPoId($poId): array
    {
        $sql = "SELECT P.inspection_id, P.period_id, P.po_id, P.period_number
                , P.workload_planned_percent, P.workload_actual_completed_percent, P.workload_remaining_percent
                , P.interim_payment, P.interim_payment_percent
                , P.interim_payment_less_previous, P.interim_payment_less_previous_percent
                , P.interim_payment_accumulated, P.interim_payment_accumulated_percent
                , P.interim_payment_remain, P.interim_payment_remain_percent
                , P.retention_value, P.plan_status_id, P.is_paid, P.is_retention
                , P.remark, P.inspection_status, P.current_approval_level
                , P.disbursement, P.workflow_id
                FROM `inspection_periods` P
                WHERE `po_id` = :po_id
                ORDER BY `period_number`";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':po_id', $poId, PDO::PARAM_INT);
        $stmt->execute();
        $rs = $stmt->fetchAll();
        return $rs;
    }

    /**
     * ดึงข้อมูลทั้งหมดที่เกี่ยวข้องกับ Period ที่ระบุ
     * (ข้อมูล po_main หลัก, ข้อมูล inspection_periods, และข้อมูล inspection_period_details)
     *
     * @param int $period_id ID ของ Period ที่ต้องการ
     * @return array|null คืนค่าเป็น array ที่มีข้อมูลทั้งหมด หรือ null ถ้าไม่พบ
     */
    public function getPeriodByPeriodId(int $period_id): ?array
    {
        // 1. ดึงข้อมูลของ inspection_periods ที่ต้องการ
        $sql = "SELECT P.inspection_id, P.period_id, P.po_id, P.period_number
                , P.workload_planned_percent, P.workload_actual_completed_percent, P.workload_remaining_percent
                , P.interim_payment, P.interim_payment_percent
                , P.interim_payment_less_previous, P.interim_payment_less_previous_percent
                , P.interim_payment_accumulated, P.interim_payment_accumulated_percent
                , P.interim_payment_remain, P.interim_payment_remain_percent
                , P.retention_value, P.plan_status_id, P.is_paid, P.is_retention
                , P.remark, P.inspection_status, P.current_approval_level, P.disbursement, P.workflow_id
                , A.approver_id, A.approval_level 
                , COALESCE(P2.interim_payment_accumulated, 0) AS previous_interim_payment_accumulated
                FROM inspection_periods P
                INNER JOIN po_main O
                    ON P.po_id = O.po_id
                LEFT JOIN inspection_period_approvals A
                    ON A.approval_level = P.current_approval_level
                    AND A.inspection_id = P.inspection_id
                LEFT JOIN approval_status S
                    ON S.approval_status_id = A.approval_status_id
                LEFT JOIN inspection_periods P2 
                    ON P2.po_id = P.po_id AND P2.period_number = P.period_number - 1
                WHERE P.period_id = :period_id
                ORDER BY P.po_id, P.period_number";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$period_id]);
        $rsPeriods = $stmt->fetch(PDO::FETCH_ASSOC);

        // 2. ถ้าไม่พบข้อมูล Period ให้คืนค่า null ทันที
        if (!$rsPeriods) {
            return null;
        }

        // 3. ดึงข้อมูลของ po_main ของ period_id ที่ต้องการ
        $rsPoMain = $this->getHeaderByPeriodId($period_id);

        // 4. ดึงข้อมูล PeriodDetails ทั้งหมดของ Period นี้ 
        $rsPeriodDetails = $this->getPeriodDetailsByPeriodId($period_id);

        // 5. จัดโครงสร้างข้อมูลใหม่เพื่อความเข้าใจง่าย
        $result = [
            'header' => $rsPoMain,
            'period' => $rsPeriods,
            'PeriodDetails' => $rsPeriodDetails, // ข้อมูล period details ที่ได้จากขั้นตอนที่ 4
        ];

        return $result;
    }

    public function getPeriodDetailsByPeriodId($periodId): ?array
    {
        $sql = "SELECT I.`rec_id`, I.`inspection_id`, I.`order_no`, I.`details`, I.`remark` 
                FROM `inspection_period_details`I
                INNER JOIN inspection_periods P
                    ON P.`inspection_id` = I.`inspection_id`
                WHERE P.`period_id` = :period_id
                ORDER BY I.`order_no`";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':period_id', $periodId, PDO::PARAM_INT);
        $stmt->execute();
        $rs = $stmt->fetchAll();
        if (!$rs) {
            return null;
        }
        return $rs;
    }

    public function getInspectionPeriodAssignToMe($username): array
    {
        $sql = "SELECT P1.inspection_id, P1.period_id, P1.po_id, P1.period_number
                , P1.workload_planned_percent, P1.workload_actual_completed_percent, P1.workload_remaining_percent
                , P1.interim_payment, P1.interim_payment_percent
                , P1.interim_payment_less_previous, P1.interim_payment_less_previous_percent
                , P1.interim_payment_accumulated, P1.interim_payment_accumulated_percent
                , P1.interim_payment_remain, P1.interim_payment_remain_percent
                , P1.retention_value, P1.plan_status_id, P1.is_paid, P1.is_retention
                , P1.remark, P1.inspection_status, P1.current_approval_level, P1.disbursement, P1.workflow_id
                , po_main.supplier_id, po_main.location_id , po_main.po_number, po_main.project_name
                , po_main.working_name_th, po_main.working_name_en
                , po_main.is_include_vat, po_main.contract_value, po_main.contract_value_before, po_main.vat, is_deposit, deposit_percent, deposit_value
                , working_date_from, working_date_to, working_day
                , suppliers.supplier_name, locations.location_name
                , inspection_period_approvals.approver_id, inspection_period_approvals.approval_level 
                , U.username, U.full_name
                FROM inspection_periods P1
                INNER JOIN po_main
                    ON P1.po_id = po_main.po_id
                INNER JOIN suppliers
                    ON suppliers.supplier_id = po_main.supplier_id
                INNER JOIN locations
                    ON locations.location_id = po_main.location_id   
                INNER JOIN inspection_period_approvals
                    ON inspection_period_approvals.approval_level = P1.current_approval_level
                    AND inspection_period_approvals.inspection_id = P1.inspection_id
                INNER JOIN approval_status
                    ON approval_status.approval_status_id = inspection_period_approvals.approval_status_id
                INNER JOIN users U 
                    ON U.user_id = inspection_period_approvals.approver_id
                WHERE U.username = :username
                    AND P1.current_approval_level >1 
                ORDER BY P1.po_id, period_number";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        $rs = $stmt->fetchAll();
        return $rs;
    }

    public function getInspectionFilesByInspectionId($getPoId, $getPeriodId, $getInspectionId): array
    {
        $sql = "SELECT `file_id`, `inspection_files`.`inspection_id`, `file_name`, `file_path`, `file_type`, `uploaded_at` 
                FROM `inspection_files` 
                INNER JOIN `inspection_periods`
                    ON `inspection_periods`.`inspection_id` = `inspection_files`.`inspection_id`
                WHERE `inspection_periods`.`inspection_id` = :inspection_id
                    AND `inspection_periods`.`period_id` = :period_id
                    AND `inspection_periods`.`po_id` = :po_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':po_id', $getPoId, PDO::PARAM_INT);
        $stmt->bindParam(':period_id', $getPeriodId, PDO::PARAM_INT);
        $stmt->bindParam(':inspection_id', $getInspectionId, PDO::PARAM_INT);
        $stmt->execute();
        $rs = $stmt->fetchAll();
        return $rs;
    }

    public function save(array $headerData, array $periodsData): int
    {
        // --- WORKFLOW ---
        // กำหนดค่า default สำหรับ workflow step ของ inspection และ ipc (อาจจะมีหน้าจอ config) โดยที่
        // 1. ทำการสร้าง inspection_period_approvals เมื่อมีการ save po เรียบร้อยแล้ว
        // 2. ทำการสร้าง ipc_period_approvals เมื่อมีการ approve ใน step สุดท้ายของ inspection ในแต่ละ period  
        // workflow_id = 1 สร้าง inspection_period_approvals
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

            $_SESSION['delete'] = $deleteItems;
            $_SESSION['update'] = $updateItems;
            $_SESSION['create'] = $createItems;

            $isPaid = 0;
            $isRetention = 0;

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
                            INNER JOIN `inspection_periods`
                                ON `inspection_files`.`inspection_id` = `inspection_periods`.`inspection_id`
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
            // ถ้ารายการผ่านขั้นตอนแรกใน inspection_period_approvals (เปลี่ยน approval_status_id จาก 1-pending เป็น 2-approved) แล้วจะต้องห้ามแก้ไขหรือลบ period นี้
            // แต่ถ้า approval_status_id เปลี่ยนจาก 1-pending เป็น 0-reject จะสามารถแก้ไขหรือลบได้
            // ในขั้นตอนเริ่มต้นของ approval_type ที่เป็น submit จะไม่สามารถ reject เอกสารของตัวเองได้่  ทำได้เพียงเปลี่ยนจาก 1-pending เป็น 2-approved 
            // เพื่อเปลี่ยน approval_type เป็นค่าอื่นที่ไม่ใช่ submit เพื่อส่งให้ผู้ดำเนินการในลำดับถัดไป เช่น จาก 1-submit เป็น verify, confirm หรือ approve ตามแต่ที่กำหนดใน inspection_period_approvals
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

                // UPDATE inspection_periods
                $sql = "UPDATE `inspection_periods`
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
                // ดึงข้อมูล workflow step เพื่อนำ Loop สร้าง inspection_period_approvals
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

                // INSERT INTO inspection_periods
                $sql = "INSERT INTO `inspection_periods`(`po_id`, `period_number`, `period_id`, `workload_planned_percent`, `interim_payment`, `interim_payment_percent`, `is_paid`, `is_retention`, `workflow_id`) 
                        VALUES (:po_id, :period_number, :period_id, :workload_planned_percent, :interim_payment, :interim_payment_percent, :is_paid, :is_retention, :workflow_id)";
                $stmtCreateInspectionPeriods = $this->db->prepare($sql);

                // INSERT inspection_period_details
                $sql = "INSERT INTO `inspection_period_details`(`inspection_id`) 
                        VALUES (:inspection_id)";
                $stmtCreateInspectionPeriodDetails = $this->db->prepare($sql);

                // INSERT inspection_period_approvals
                $sql = "INSERT INTO `inspection_period_approvals`(`inspection_id`, `period_id`, `po_id`, `period_number`, `approval_level`, `approver_id`, `approval_type_id`, `approval_type_text`, `approval_status_id`) 
                        VALUES (:inspection_id, :period_id, :po_id, :period_number, :approval_level, :approver_id, :approval_type_id, :approval_type_text, :approval_status_id)";
                $stmtCreateInspectApprovals = $this->db->prepare($sql);

                foreach ($createItems as $item) {
                    $_SESSION['=== interim_payment ==='] = $item['interim_payment'];
                    $_SESSION['=== interim_payment_percent ==='] = $item['interim_payment_percent'];
                    $_SESSION['=== workload_planned_percents ==='] = $item['workload_planned_percent'];
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
    
    public function updateInspectionPeriod($getData)
    {
        @session_start();

        try {
            // $_SESSION['getData'] =  $getData;
            // exit;
            // $this->db->beginTransaction();

            // parameters ในส่วน main
            $po_id = $getData['po_id'];
            $period_id = $getData['period_id'];
            $inspection_id = $getData['inspection_id'];
            $po_number = $getData['po_number'];
            $plan_status_id = floatval($getData['plan_status_id'] ?? -1);
            $disbursement = floatval($getData['disbursement'] ?? -1);

            $workload_planned_percent = floatval($getData['workload_planned_percent'] ?? 0);
            $workload_actual_completed_percent = floatval($getData['workload_actual_completed_percent'] ?? 0);
            $workload_remaining_percent = floatval($getData['workload_remaining_percent'] ?? 0);
            $interim_payment = floatval($getData['interim_payment'] ?? 0);
            $interim_payment_less_previous = floatval($getData['interim_payment_less_previous'] ?? 0);
            $interim_payment_accumulated = floatval($getData['interim_payment_accumulated'] ?? 0);
            $interim_payment_remain = floatval($getData['interim_payment_remain'] ?? 0);
            $retention_value = floatval($getData['retention_value'] ?? 0);

            $interim_payment_percent = floatval($getData['interim_payment_percent'] ?? 0);
            $interim_payment_less_previous_percent = floatval($getData['interim_payment_less_previous_percent'] ?? 0);
            $interim_payment_accumulated_percent = floatval($getData['interim_payment_accumulated_percent'] ?? 0);
            $interim_payment_remain_percent = floatval($getData['interim_payment_remain_percent'] ?? 0);
            $remark = trim($getData['remark']);
            $_SESSION['remark'] = $remark;

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

            // $_SESSION['create'] = $insert_indexs;
            // $_SESSION['update'] = $update_indexs;
            // $_SESSION['delete'] = $delete_indexs;

            // UPDATE po_main
            $sql = <<<EOD
                        UPDATE `inspection_periods`
                            SET `workload_actual_completed_percent` = :workload_actual_completed_percent
                            , `workload_remaining_percent` = :workload_remaining_percent
                            , `workload_planned_percent` = :workload_planned_percent
                            , `interim_payment` = :interim_payment
                            , `interim_payment_percent` = :interim_payment_percent
                            , `interim_payment_less_previous` = :interim_payment_less_previous
                            , `interim_payment_less_previous_percent` = :interim_payment_less_previous_percent
                            , `interim_payment_accumulated` = :interim_payment_accumulated
                            , `interim_payment_accumulated_percent` = :interim_payment_accumulated_percent
                            , `interim_payment_remain` = :interim_payment_remain
                            , `interim_payment_remain_percent` = :interim_payment_remain_percent
                            , `retention_value` = :retention_value
                            , `plan_status_id` = :plan_status_id
                            , `disbursement` = :disbursement
                            , `remark` = :remark
                            WHERE `po_id` = :po_id
                                AND `period_id` = :period_id
                                AND `inspection_id` = :inspection_id
                    EOD;
            $stmtInspectionPeriods = $this->db->prepare($sql);
            // $stmtInspectionPeriods->bindParam(':po_number', $po_number, PDO::PARAM_STR);
            $stmtInspectionPeriods->bindParam(':po_id', $po_id, PDO::PARAM_INT);
            $stmtInspectionPeriods->bindParam(':period_id', $period_id, PDO::PARAM_INT);
            $stmtInspectionPeriods->bindParam(':inspection_id', $inspection_id, PDO::PARAM_INT);
            $stmtInspectionPeriods->bindParam(':workload_actual_completed_percent', $workload_actual_completed_percent, PDO::PARAM_STR);
            $stmtInspectionPeriods->bindParam(':workload_remaining_percent', $workload_remaining_percent, PDO::PARAM_STR);
            $stmtInspectionPeriods->bindParam(':workload_planned_percent', $workload_planned_percent, PDO::PARAM_STR);
            $stmtInspectionPeriods->bindParam(':interim_payment', $interim_payment, PDO::PARAM_STR);
            $stmtInspectionPeriods->bindParam(':interim_payment_percent', $interim_payment_percent, PDO::PARAM_STR);
            $stmtInspectionPeriods->bindParam(':interim_payment_less_previous', $interim_payment_less_previous, PDO::PARAM_STR);
            $stmtInspectionPeriods->bindParam(':interim_payment_less_previous_percent', $interim_payment_less_previous_percent, PDO::PARAM_STR);
            $stmtInspectionPeriods->bindParam(':interim_payment_accumulated', $interim_payment_accumulated, PDO::PARAM_STR);
            $stmtInspectionPeriods->bindParam(':interim_payment_accumulated_percent', $interim_payment_accumulated_percent, PDO::PARAM_STR);
            $stmtInspectionPeriods->bindParam(':interim_payment_remain', $interim_payment_remain, PDO::PARAM_STR);
            $stmtInspectionPeriods->bindParam(':interim_payment_remain_percent', $interim_payment_remain_percent, PDO::PARAM_STR);
            $stmtInspectionPeriods->bindParam(':plan_status_id', $plan_status_id, PDO::PARAM_INT);
            $stmtInspectionPeriods->bindParam(':disbursement', $disbursement, PDO::PARAM_INT);
            $stmtInspectionPeriods->bindParam(':retention_value', $retention_value, PDO::PARAM_STR);
            $stmtInspectionPeriods->bindParam(':remark', $remark, PDO::PARAM_STR);

            // $_SESSION['period_id'] = $period_id;
            // $_SESSION['inspection_id'] = $inspection_id;
            // $_SESSION['plan_status_id'] = $plan_status_id;
            // $_SESSION['disbursement'] = $disbursement;
            // $_SESSION['stmtInspectionPeriods->execute1'] = $stmtInspectionPeriods->queryString;
            if ($stmtInspectionPeriods->execute()) {
                // $_SESSION['remark'] = $remark;
                $stmtInspectionPeriods->closeCursor();
                // INSERT inspection_period_details
                $sql = <<<EOD
                            INSERT INTO `inspection_period_details`(`inspection_id`, `order_no`, `details`, `remark`) 
                            VALUES (:inspection_id, :order_no, :details, :remark)
                        EOD;
                $stmtInspectionPeriodDetails = $this->db->prepare($sql);

                foreach ($insert_indexs as $i) { //ถ้าต้องการใช้ key ด้วย foreach($insert_indexs as $key=> $value){
                    $stmtInspectionPeriodDetails->bindParam(':inspection_id', $inspection_id, PDO::PARAM_INT);
                    $stmtInspectionPeriodDetails->bindParam(':order_no', $order_nos[$i], PDO::PARAM_INT);
                    $stmtInspectionPeriodDetails->bindParam(':details', $details[$i],  PDO::PARAM_STR);
                    $stmtInspectionPeriodDetails->bindParam(':remark', $remarks[$i], PDO::PARAM_STR);

                    $stmtInspectionPeriodDetails->execute();
                    $stmtInspectionPeriodDetails->closeCursor();
                }

                // UPDATE inspection_period_details
                $sql = <<<EOD
                            UPDATE `inspection_period_details`
                            SET `details` = :details
                            , `remark` = :remark
                            WHERE `inspection_id` = :inspection_id
                                AND `rec_id` = :rec_id
                        EOD;
                $stmtInspectionPeriodDetails = $this->db->prepare($sql);

                foreach ($update_indexs as $i) {
                    $stmtInspectionPeriodDetails->bindParam(':inspection_id', $inspection_id, PDO::PARAM_INT);
                    $stmtInspectionPeriodDetails->bindParam(':rec_id', $rec_ids[$i], PDO::PARAM_INT);
                    $stmtInspectionPeriodDetails->bindParam(':details', $details[$i],  PDO::PARAM_STR);
                    $stmtInspectionPeriodDetails->bindParam(':remark', $remarks[$i], PDO::PARAM_STR);

                    $stmtInspectionPeriodDetails->execute();
                    $stmtInspectionPeriodDetails->closeCursor();
                }

                // DELETE inspection_period_details
                $sql = <<<EOD
                            DELETE FROM `inspection_period_details`
                            WHERE `inspection_id` = :inspection_id
                                AND `rec_id` = :rec_id
                        EOD;
                $stmtInspectionPeriodDetails = $this->db->prepare($sql);

                foreach ($delete_indexs as $i) {
                    $stmtInspectionPeriodDetails->bindParam(':inspection_id', $inspection_id, PDO::PARAM_INT);
                    $stmtInspectionPeriodDetails->bindParam(':rec_id', $rec_ids[$i], PDO::PARAM_INT);

                    $stmtInspectionPeriodDetails->execute();
                    $stmtInspectionPeriodDetails->closeCursor();
                }

                $_SESSION['Transaction'] =  'data has been updated successfully.';
            }
        } catch (PDOException $e) {
            $_SESSION['Transaction'] =  $e->getCode() + ' : ' + $e->getMessage();
        }
    }

    public function updateCurrentApprovalLevel($getData)
    {
        @session_start();

        try {
            $po_id = $getData['po_id'];
            $period_id = $getData['period_id'];
            $inspection_id = $getData['inspection_id'];
            $current_approval_level = $getData['current_approval_level'];
            $new_approval_level = $getData['new_approval_level'];

            // UPDATE inspection_periods
            $sql = <<<EOD
                        UPDATE `inspection_periods`
                        SET `current_approval_level` = :new_approval_level
                        WHERE `po_id` = :po_id
                            AND `period_id` = :period_id
                            AND `inspection_id` = :inspection_id
                    EOD;
            $stmtInspectionPeriods = $this->db->prepare($sql);
            $stmtInspectionPeriods->bindParam(':po_id', $po_id, PDO::PARAM_INT);
            $stmtInspectionPeriods->bindParam(':period_id', $period_id, PDO::PARAM_INT);
            $stmtInspectionPeriods->bindParam(':inspection_id', $inspection_id, PDO::PARAM_INT);
            $stmtInspectionPeriods->bindParam(':new_approval_level', $new_approval_level, PDO::PARAM_INT);

            if ($stmtInspectionPeriods->execute()) {
                // $_SESSION['remark'] = $remark;
                $stmtInspectionPeriods->closeCursor();

                // UPDATE inspection_period_approvals
                $sql = <<<EOD
                            UPDATE `inspection_period_approvals`
                            SET `approval_date` = NOW()
                            WHERE `inspection_id` = :inspection_id
                                AND `approval_level` = :approval_level
                        EOD;
                $stmtInspectionApproval = $this->db->prepare($sql);
                $stmtInspectionApproval->bindParam(':inspection_id', $inspection_id, PDO::PARAM_INT);
                $stmtInspectionApproval->bindParam(':approval_level', $current_approval_level, PDO::PARAM_INT);

                $stmtInspectionApproval->execute();
                $stmtInspectionApproval->closeCursor();

                $_SESSION['Transaction'] =  'data has been updated successfully.';
            }
        } catch (PDOException $e) {
            $_SESSION['Transaction'] =  $e->getCode() + ' : ' + $e->getMessage();
        }
    }

    public function insertInspectionFiles($getData)
    {
        @session_start();

        // $_SESSION['getData in po_class'] = $getData;
        // return;
        try {
            // เริ่ม transaction
            $this->db->beginTransaction();

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
                        $stmt = $this->db->prepare($sql);
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
            $this->db->commit();
            // $_SESSION['commit'] = 'Completed';
            echo json_encode(['status' => 'success', 'message' => 'Record and files uploaded successfully.']);
            // echo json_encode(["a" => "A", "b" => "B"]);
        } catch (PDOException $e) {
            // $_SESSION['rollBack'] = 'Completed';
            // rollback transaction
            $this->db->rollBack();
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
    public function deleteInspectionFiles($getFileId)
    {
        $file_id = $getFileId;
        // if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['file_id'])) {
        //     $file_id = $_POST['file_id'];

        // } else {
        //     echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
        // }
        try {
            // เริ่ม transaction
            $this->db->beginTransaction();

            // ดึงข้อมูลไฟล์ที่จะลบ
            $sql = <<<EOD
                        SELECT file_path 
                        FROM `inspection_files`
                        WHERE file_id = :file_id
                    EOD;
            $stmt = $this->db->prepare($sql);
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
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':file_id', $file_id, PDO::PARAM_INT);
            $stmt->execute();

            // commit transaction
            $this->db->commit();
            echo json_encode(['status' => 'success', 'message' => 'Record and associated files deleted successfully.']);
        } catch (Exception $e) {
            // rollback transaction
            $this->db->rollBack();
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}

// $stmt = $conn->prepare('select ...where :');
// $stmt->bindParam(':building_id', $building_id, PDO::PARAM_STR);
// $stmt->bindParam(':building_name', $building_name, PDO::PARAM_STR);
// $stmt->bindParam(':is_active', $is_active, PDO::PARAM_BOOL);
// $stmt->execute();
// $rs = $stmt->fetchAll(PDO::FETCH_ASSOC);