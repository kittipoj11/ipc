<?php
// require_once 'config.php';
require_once 'connection_class.php';
class Ipc
{
    private $db;
    public function __construct(PDO $pdoConnection)
    {
        $this->db = $pdoConnection;
    }

    // ดึงข้อมูลจาก po_main ที่มีข้อมูลใน ipc อย่างน้อย 1 รายการ  และจะ return ค่าออกไปเป็น array
    public function getAllPo(): array
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
        $po = new Po($this->db);
        $rs = $po->getHeaderByPoId($poId);
        if (!$rs) {
            return null; // ไม่พบข้อมูล
        }

        return $rs;
    }

    public function getByPoId($poId): ?array
    {
        // ดึงข้อมูลจากตารางหลัก - po_main
        $po = new Po($this->db);
        $rs = $po->getHeaderByPoId($poId);
        if (!$rs) {
            return null; // ไม่พบข้อมูล
        }

        // ดึงข้อมูลจากตารางรอง
        $rs['periods'] = $this->getAllPeriodByPoId($poId);

        // $rs2 = $this->po->getByPoId($poId);

        // $po = new Po($this->db);
        // $rs2=$po->getByPoId($poId);

        return $rs;
    }

    public function getPoByPeriodId($periodId): ?array
    {
        // ดึงข้อมูลจากตารางหลัก - po_main
        $sql = "SELECT O.supplier_id, O.location_id , O.po_number, O.project_name, O.working_name_th, O.working_name_en
                , O.is_include_vat, O.contract_value, O.contract_value_before, O.vat, O.is_deposit, O.deposit_percent, O.deposit_value
                , O.working_date_from, O.working_date_to, O.working_day
                , S.supplier_name, L.location_name
                FROM po_main O
                INNER JOIN ipc P
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
        $sql = "SELECT `ipc_id`, `inspection_id`, `period_id`, `po_id`, `period_number`, `workflow_id`, `project_name`, `agreement_date`, `contractor`, `contract_value`
        , `total_value_of_interim_payment`, `less_previous_interim_payment`, `net_value_of_current_claim`, `less_retension_exclude_vat`, `net_amount_due_for_payment`
        , `total_value_of_retention`, `total_value_of_certification_made`, `resulting_balance_of_contract_sum_outstanding`
        , `submit_by`, `approved1_by`, `approved2_by`, `remark`, `ipc_status`, `current_approval_level`, `current_approver_id`, `created_by`, `created_at`, `updated_at` 
            FROM ipc
            WHERE po_id = :po_id
            ORDER BY period_number";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':po_id', $poId, PDO::PARAM_INT);

        $stmt->execute();
        $rs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $rs;
    }

    public function getByIpcId(int $ipcId): ?array
    {
        // 1. ดึงข้อมูลของ inspection ที่ต้องการ
        $sql = "SELECT `ipc_id`, `inspection_id`, `period_id`, `po_id`, `period_number`, `workflow_id`, `project_name`
                , `agreement_date`, `contractor`, `contract_value`, `total_value_of_interim_payment`, `less_previous_interim_payment`
                , `net_value_of_current_claim`, `less_retension_exclude_vat`, `net_amount_due_for_payment`
                , `total_value_of_retention`, `total_value_of_certification_made`, `resulting_balance_of_contract_sum_outstanding`
                , `submit_by`, `approved1_by`, `approved2_by`, `remark`, `ipc_status`, `current_approval_level`, `current_approver_id`
                , `created_by`, `created_at`, `updated_at` 
                FROM ipc 
                WHERE ipc_id = :ipc_id
                ORDER BY po_id, period_number";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$ipcId]);
        $rsIpc = $stmt->fetch(PDO::FETCH_ASSOC);

        // 2. ถ้าไม่พบข้อมูล Inspection ให้คืนค่า null ทันที
        if (!$rsIpc) {
            return null;
        }

        // 3. ดึงข้อมูลของ po_main ของ period_id ที่ต้องการ
        $rsPoMain = $this->getHeaderByPoId($rsIpc['po_id']);

        // 7. จัดโครงสร้างข้อมูลใหม่เพื่อความเข้าใจง่าย
        $result = [
            'header' => $rsPoMain,
            'period' => $rsIpc,
            // 'periodApprovals' => $rsInspectionApprovals, // ข้อมูล period details ที่ได้จากขั้นตอนที่ 5
            // 'maxInspectionApproval' => $rsMaxInspectionApproval, // ข้อมูล period details ที่ได้จากขั้นตอนที่ 6
        ];

        return $result;
    }

    public function create(array $ipcData): int
    {
        
        // กำหนดค่าให้ตัวแปร $ipcId ที่ส่งมา
        // $ipcId = $ipcData['ipc_id'] ?? 0;
    
        // 1. ตรวจสอบและจัดการข้อมูล (INSERT หรือ UPDATE)
        // if (empty($ipcId)) { //ถ้าไม่มีค่าหรือมีค่าเป็น 0
            // --- INSERT MODE ---
            $sql = "INSERT INTO ipc(po_id, period_id, inspection_id, period_number, project_name, contractor, contract_value
                    , total_value_of_interim_payment, less_previous_interim_payment, net_value_of_current_claim, less_retension_exclude_vat
                    , net_amount_due_for_payment, total_value_of_retention, total_value_of_certification_made, resulting_balance_of_contract_sum_outstanding
                    , remark, workflow_id, created_by)
                    VALUES(:po_id, :period_id, :inspection_id, :period_number, :project_name, :contractor, :contract_value
                    , :total_value_of_interim_payment, :less_previous_interim_payment, :net_value_of_current_claim, :less_retension_exclude_vat
                    , :net_amount_due_for_payment, :total_value_of_retention, :total_value_of_certification_made, :resulting_balance_of_contract_sum_outstanding
                    , :remark, :workflow_id, :created_by)";
    
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':po_id', $ipcData['po_id'], PDO::PARAM_INT);
            $stmt->bindParam(':period_id', $ipcData['period_id'], PDO::PARAM_INT);
            $stmt->bindParam(':inspection_id', $ipcData['inspection_id'], PDO::PARAM_INT);
            $stmt->bindParam(':period_number', $ipcData['period_number'], PDO::PARAM_INT);
            $stmt->bindParam(':project_name', $ipcData['project_name'], PDO::PARAM_STR);
            $stmt->bindParam(':contractor', $ipcData['contractor'],  PDO::PARAM_STR);
            $stmt->bindParam(':contract_value', $ipcData['contract_value'], PDO::PARAM_STR);
            $stmt->bindParam(':total_value_of_interim_payment', $ipcData['total_value_of_interim_payment'], PDO::PARAM_STR);
            $stmt->bindParam(':less_previous_interim_payment', $ipcData['less_previous_interim_payment'], PDO::PARAM_STR);
            $stmt->bindParam(':net_value_of_current_claim', $ipcData['net_value_of_current_claim'], PDO::PARAM_STR);
            $stmt->bindParam(':less_retension_exclude_vat', $ipcData['less_retension_exclude_vat'], PDO::PARAM_STR);
            $stmt->bindParam(':net_amount_due_for_payment', $ipcData['net_amount_due_for_payment'], PDO::PARAM_STR);
            $stmt->bindParam(':total_value_of_retention', $ipcData['total_value_of_retention'], PDO::PARAM_STR);
            $stmt->bindParam(':total_value_of_certification_made', $ipcData['total_value_of_certification_made'], PDO::PARAM_STR);
            $stmt->bindParam(':resulting_balance_of_contract_sum_outstanding', $ipcData['resulting_balance_of_contract_sum_outstanding'], PDO::PARAM_STR);
            $stmt->bindParam(':remark', $ipcData['remark'], PDO::PARAM_STR);
            $stmt->bindParam(':workflow_id', $ipcData['workflow_id'], PDO::PARAM_INT);
            $stmt->bindParam(':created_by', $_SESSION['user_id'], PDO::PARAM_INT);
    
            $stmt->execute();
            $ipcId = $this->db->lastInsertId();
            $stmt->closeCursor();
            return (int)$ipcId;
        // } else {
        //     // --- UPDATE MODE ---
        //     return (int)$ipcId;
        // }
    }

    public function updateStatus($ipcId, $ipcStatus, $currentApproverId, $currentApprovalLevel)
    {
        $sql = "UPDATE `ipc`
                SET `ipc_status` = :ipc_status
                , `current_approver_id` = :current_approver_id
                , `current_approval_level` = :current_approval_level
                WHERE `ipc_id` = :ipc_id";

        $stmt = $this->db->prepare($sql);
        // $stmt->bindParam(':po_number', $po_number, PDO::PARAM_STR);
        $stmt->bindParam(':ipc_id', $ipcId, PDO::PARAM_INT);
        $stmt->bindParam(':current_approver_id', $currentApproverId, PDO::PARAM_INT);
        $stmt->bindParam(':current_approval_level', $currentApprovalLevel, PDO::PARAM_INT);
        $stmt->bindParam(':ipc_status', $ipcStatus, PDO::PARAM_STR);

        // $stmtUpdatePoMain->execute();
        $stmt->execute();
        // $_SESSION['period data']= $periodData;
        $stmt->closeCursor();
    }

    public function logHistory($ipcId, $userId, $action, $comments = '')
    {
        $sql = "INSERT INTO ipc_history (`ipc_id`, `user_id`, `action`, `comments`) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$ipcId, $userId, $action, $comments]);
        $stmt->closeCursor();
    }
}


// $stmt = $conn->prepare('select ...where :');
// $stmt->bindParam(':building_id', $building_id, PDO::PARAM_STR);
// $stmt->bindParam(':building_name', $building_name, PDO::PARAM_STR);
// $stmt->bindParam(':is_active', $is_active, PDO::PARAM_BOOL);
// $stmt->execute();
// $rs = $stmt->fetchAll(PDO::FETCH_ASSOC);