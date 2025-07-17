<?php
@session_start();
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
    public function getPeriodByPeriodId(int $periodId): ?array
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
        $stmt->execute([$periodId]);
        $rsPeriods = $stmt->fetch(PDO::FETCH_ASSOC);

        // 2. ถ้าไม่พบข้อมูล Period ให้คืนค่า null ทันที
        if (!$rsPeriods) {
            return null;
        }

        // 3. ดึงข้อมูลของ po_main ของ period_id ที่ต้องการ
        $rsPoMain = $this->getHeaderByPeriodId($periodId);

        // 4. ดึงข้อมูล PeriodDetails ทั้งหมดของ Period นี้ 
        $rsPeriodDetails = $this->getPeriodDetailsByPeriodId($periodId);

        // 5. ดึงข้อมูล Period Approvals ของ period_id ที่มี approval_level = current_approval_level
        $sql = "SELECT `inspection_approval_id`, `inspection_id`, `period_id`, `po_id`, `period_number`
                , `approval_level`, `approver_id`, `approval_type_id`, `approval_type_text`, `approval_status_id`, `approval_date`, `approval_comment` 
                FROM `inspection_period_approvals` 
                WHERE period_id = :period_id
                AND approval_level = :approval_level";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':period_id', $periodId, PDO::PARAM_INT);
        $stmt->bindParam(':approval_level', $rsPeriods['current_approval_level'], PDO::PARAM_INT);
        $stmt->execute();
        $rsPeriodApprovals = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;

        // 6. ดึงข้อมูล max ของ approval_level ใน Period Approvals ของ period_id
        $sql = "SELECT `inspection_id`, `period_id`, `po_id`, max(`approval_level`) as max_approval_level
                FROM `inspection_period_approvals` 
                WHERE period_id = :period_id
                GROUP by `inspection_id`, `period_id`, `po_id`";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':period_id', $periodId, PDO::PARAM_INT);
        $stmt->execute();
        $rsMaxPeriodApproval = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;

        // 7. จัดโครงสร้างข้อมูลใหม่เพื่อความเข้าใจง่าย
        $result = [
            'header' => $rsPoMain,
            'period' => $rsPeriods,
            'periodDetails' => $rsPeriodDetails, // ข้อมูล period details ที่ได้จากขั้นตอนที่ 4
            'periodApprovals' => $rsPeriodApprovals, // ข้อมูล period details ที่ได้จากขั้นตอนที่ 4
            'maxPeriodApproval' => $rsMaxPeriodApproval, // ข้อมูล period details ที่ได้จากขั้นตอนที่ 4
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

    // ต้องแก้ไขฟังก์ชันนี้ใหม่
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

    // ต้องแก้ไขฟังก์ชันนี้ใหม่
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

    public function save(array $periodData, array $detailsData): int
    {
        $this->db->beginTransaction();
        try {
            // กำหนดค่าให้ตัวแปร $inspectionId ที่ส่งมา
            $inspectionId = $periodData['inspection_id'] ?? 0;

            // 1. ตรวจสอบและจัดการข้อมูล Header (INSERT หรือ UPDATE)
            if (empty($inspectionId)) { //ถ้าไม่มีค่าหรือมีค่าเป็น 0

            } else {
                // --- UPDATE MODE ---
                /*
                SELECT `inspection_id`, `period_id`, `po_id`, `period_number`
                , `workload_planned_percent`, `workload_actual_completed_percent`, `workload_remaining_percent`
                , `interim_payment`, `interim_payment_percent`, `interim_payment_less_previous`, `interim_payment_less_previous_percent`
                , `interim_payment_accumulated`, `interim_payment_accumulated_percent`, `interim_payment_remain`, `interim_payment_remain_percent`
                , `retention_value`, `plan_status_id`, `is_paid`, `is_retention`, `remark`, `inspection_status`, `current_approval_level`
                , `disbursement`, `workflow_id` FROM `inspection_periods`
                */
                $sql = "UPDATE `inspection_periods`
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
                            AND `inspection_id` = :inspection_id";

                $stmtInspectionPeriods = $this->db->prepare($sql);
                // $stmtInspectionPeriods->bindParam(':po_number', $po_number, PDO::PARAM_STR);
                $stmtInspectionPeriods->bindParam(':po_id', $periodData['po_id'], PDO::PARAM_INT);
                $stmtInspectionPeriods->bindParam(':period_id', $periodData['period_id'], PDO::PARAM_INT);
                $stmtInspectionPeriods->bindParam(':inspection_id', $periodData['inspection_id'], PDO::PARAM_INT);
                $stmtInspectionPeriods->bindParam(':workload_actual_completed_percent', $periodData['workload_actual_completed_percent'], PDO::PARAM_STR);
                $stmtInspectionPeriods->bindParam(':workload_remaining_percent', $periodData['workload_remaining_percent'], PDO::PARAM_STR);
                $stmtInspectionPeriods->bindParam(':workload_planned_percent', $periodData['workload_planned_percent'], PDO::PARAM_STR);
                $stmtInspectionPeriods->bindParam(':interim_payment', $periodData['interim_payment'], PDO::PARAM_STR);
                $stmtInspectionPeriods->bindParam(':interim_payment_percent', $periodData['interim_payment_percent'], PDO::PARAM_STR);
                $stmtInspectionPeriods->bindParam(':interim_payment_less_previous', $periodData['interim_payment_less_previous'], PDO::PARAM_STR);
                $stmtInspectionPeriods->bindParam(':interim_payment_less_previous_percent', $periodData['interim_payment_less_previous_percent'], PDO::PARAM_STR);
                $stmtInspectionPeriods->bindParam(':interim_payment_accumulated', $periodData['interim_payment_accumulated'], PDO::PARAM_STR);
                $stmtInspectionPeriods->bindParam(':interim_payment_accumulated_percent', $periodData['interim_payment_accumulated_percent'], PDO::PARAM_STR);
                $stmtInspectionPeriods->bindParam(':interim_payment_remain', $periodData['interim_payment_remain'], PDO::PARAM_STR);
                $stmtInspectionPeriods->bindParam(':interim_payment_remain_percent', $periodData['interim_payment_remain_percent'], PDO::PARAM_STR);
                $stmtInspectionPeriods->bindParam(':plan_status_id', $periodData['plan_status_id'], PDO::PARAM_INT);
                $stmtInspectionPeriods->bindParam(':disbursement', $periodData['disbursement'], PDO::PARAM_INT);
                $stmtInspectionPeriods->bindParam(':retention_value', $periodData['retention_value'], PDO::PARAM_STR);
                $stmtInspectionPeriods->bindParam(':remark', $periodData['remark'], PDO::PARAM_STR);

                // $stmtUpdatePoMain->execute();
                $stmtInspectionPeriods->execute();
                // $_SESSION['period data']= $periodData;
                $stmtInspectionPeriods->closeCursor();
            }

            // 2. จัดการข้อมูล detail ตามลำดับ(D-U-C Logic)
            $deleteItems = array_filter($detailsData, fn($item) => ($item['order_crud'] ?? 'none') === 'delete');
            $updateItems = array_filter($detailsData, fn($item) => ($item['order_crud'] ?? 'none') === 'update');
            $createItems = array_filter($detailsData, fn($item) => ($item['order_crud'] ?? 'none') === 'create');

            $_SESSION['delete'] = $deleteItems;
            $_SESSION['update'] = $updateItems;
            $_SESSION['create'] = $createItems;

            $isPaid = 0;
            $isRetention = 0;

            // 3. ทำงานตามลำดับ D-U-C
            // 3.1 ************************* ตรวจสอบ deleteItems ****************************
            if (!empty($deleteItems)) {
                $sql = "DELETE FROM inspection_period_details 
                        WHERE inspection_id = :inspection_id 
                            AND `rec_id` = :rec_id";
                $stmtDelete = $this->db->prepare($sql);
                foreach ($deleteItems as $item) {
                    if (!empty($item['rec_id'])) {
                        $stmtDelete->bindParam(':inspection_id', $item['inspection_id'], PDO::PARAM_INT);
                        $stmtDelete->bindParam(':rec_id', $item['rec_id'], PDO::PARAM_INT);
                        $stmtDelete->execute();
                        $_SESSION['delete detail[' . $item["rec_id"] . ']:'] = $item['detail'];
                        $stmtDelete->closeCursor();
                    }
                }
            }

            // 3.2 ************************* ตรวจสอบ updateItems ****************************
            if (!empty($updateItems)) {
                // UPDATE inspection_period_details
                $sql = "UPDATE `inspection_period_details`
                        SET `details` = :detail
                        , `remark` = :remark
                        WHERE `inspection_id` = :inspection_id
                            AND `rec_id` = :rec_id";
                $stmtUpdate = $this->db->prepare($sql);

                foreach ($updateItems as $item) {
                    if (!empty($item['rec_id'])) {
                        $stmtUpdate->bindParam(':inspection_id', $item['inspection_id'], PDO::PARAM_INT);
                        $stmtUpdate->bindParam(':rec_id', $item['rec_id'], PDO::PARAM_INT);
                        $stmtUpdate->bindParam(':detail', $item['detail'],  PDO::PARAM_STR);
                        $stmtUpdate->bindParam(':remark', $item['remark'], PDO::PARAM_STR);

                        $stmtUpdate->execute();
                        $_SESSION['update detail[' . $item["rec_id"] . ']:'] = $item['detail'];
                        $stmtUpdate->closeCursor();
                    }
                }
            }

            // 3.3 ************************* ตรวจสอบ createItems ****************************
            if (!empty($createItems)) {
                // INSERT inspection_period_details
                $sql = "INSERT INTO `inspection_period_details`(`inspection_id`, `order_no`, `details`, `remark`) 
                        VALUES (:inspection_id, :order_no, :detail, :remark)";
                $stmtCreate = $this->db->prepare($sql);

                foreach ($createItems as $item) {
                    $stmtCreate->bindParam(':inspection_id', $item['inspection_id'], PDO::PARAM_INT);
                    $stmtCreate->bindParam(':order_no', $item['order_no'], PDO::PARAM_INT);
                    $stmtCreate->bindParam(':detail', $item['detail'],  PDO::PARAM_STR);
                    $stmtCreate->bindParam(':remark', $item['remark'], PDO::PARAM_STR);

                    $stmtCreate->execute();
                    $_SESSION['insert detail[' . $this->db->lastInsertId() . ']:'] = $item['detail'];
                    $stmtCreate->closeCursor();

                    // $periodId = $this->db->lastInsertId();
                }
            }

            $this->db->commit();
            // คืนค่า Inspection ID ที่บันทึกสำเร็จกลับไป
            return (int)$inspectionId;
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    // เพิ่มเติมในส่วน ipc_periods
    public function updateCurrentApprovalLevel(array $approvalData,array $ipcData): int
    {
        $isApprove = $approvalData['is_approve'];
        $approvalLevel = $isApprove ? $approvalData['current_approval_level'] : $approvalData['new_approval_level'];

        $this->db->beginTransaction();
        try {
            // UPDATE inspection_periods
            $sql = "UPDATE `inspection_periods`
                    SET `current_approval_level` = :new_approval_level
                    , inspection_status = :inspection_status
                    WHERE `po_id` = :po_id
                        AND `period_id` = :period_id
                        AND `inspection_id` = :inspection_id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':po_id', $approvalData['po_id'], PDO::PARAM_INT);
            $stmt->bindParam(':period_id', $approvalData['period_id'], PDO::PARAM_INT);
            $stmt->bindParam(':inspection_id', $approvalData['inspection_id'], PDO::PARAM_INT);
            $stmt->bindParam(':new_approval_level', $approvalData['new_approval_level'], PDO::PARAM_INT);
            $stmt->bindParam(':inspection_status', $approvalData['inspection_status'], PDO::PARAM_INT);

            $stmt->execute();
            $stmt->closeCursor();

            // UPDATE inspection_period_approvals
            $sql = "UPDATE `inspection_period_approvals`
                    SET `approval_date` = IF(:isApprove, NOW(), NULL)
                    WHERE `inspection_id` = :inspection_id
                        AND `approval_level` = :approval_level";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':inspection_id', $approvalData['inspection_id'], PDO::PARAM_INT);
            $stmt->bindParam(':approval_level', $approvalLevel, PDO::PARAM_INT);
            $stmt->bindParam(':isApprove', $isApprove, PDO::PARAM_BOOL);

            $stmt->execute();
            $stmt->closeCursor();

            if ($approvalData['create_ipc']) {

                // INSERT ipc_periods
                $sql = "INSERT INTO ipc_periods(inspection_id, period_id, po_id, period_number, project_name, contractor, contract_value
                        , total_value_of_interim_payment, less_previous_interim_payment, net_value_of_current_claim, less_retension_exclude_vat
                        , net_amount_due_for_payment, total_value_of_retention, total_value_of_certification_made
                        , resulting_balance_of_contract_sum_outstanding, workflow_id)
                        VALUES(:inspection_id, :period_id, :po_id, :period_number, :project_name, :contractor, :contract_value
                        , :total_value_of_interim_payment, :less_previous_interim_payment, :net_value_of_current_claim, :less_retension_exclude_vat
                        , :net_amount_due_for_payment, :total_value_of_retention, :total_value_of_certification_made
                        , :resulting_balance_of_contract_sum_outstanding, :workflow_id)";

                $stmt->bindParam(':inspection_id', $ipcData['inspection_id'], PDO::PARAM_INT);
                $stmt->bindParam(':po_id', $ipcData['po_id'], PDO::PARAM_INT);
                $stmt->bindParam(':period_id', $ipcData['period_id'], PDO::PARAM_INT);
                $stmt->bindParam(':period_number', $ipcData['period_number'], PDO::PARAM_STR);
                $stmt->bindParam(':project_name', $ipcData['project_name'], PDO::PARAM_STR);
                $stmt->bindParam(':contractor', $ipcData['contractor'],  PDO::PARAM_INT);
                $stmt->bindParam(':contract_value', $ipcData['contract_value'], PDO::PARAM_STR);
                $stmt->bindParam(':total_value_of_interim_payment', $ipcData['total_value_of_interim_payment'], PDO::PARAM_STR);
                $stmt->bindParam(':less_previous_interim_payment', $ipcData['less_previous_interim_payment'], PDO::PARAM_STR);
                $stmt->bindParam(':net_value_of_current_claim', $ipcData['net_value_of_current_claim'], PDO::PARAM_STR);
                $stmt->bindParam(':less_retension_exclude_vat', $ipcData['less_retension_exclude_vat'], PDO::PARAM_STR);
                $stmt->bindParam(':net_amount_due_for_payment', $ipcData['net_amount_due_for_payment'], PDO::PARAM_STR);
                $stmt->bindParam(':total_value_of_retention', $ipcData['total_value_of_retention'], PDO::PARAM_STR);
                $stmt->bindParam(':total_value_of_certification_made', $ipcData['total_value_of_certification_made'], PDO::PARAM_STR);
                $stmt->bindParam(':resulting_balance_of_contract_sum_outstanding', $ipcData['resulting_balance_of_contract_sum_outstanding'], PDO::PARAM_STR);
                $stmt->bindParam(':workflow_id', 2, PDO::PARAM_INT);

                $stmt->execute();
                $stmt->closeCursor();

                // $ipcId = $this->db->lastInsertId();

                // INSERT ipc_period_approvals

            }
            $this->db->commit();
            return (int)$approvalData['inspection_id'];
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
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