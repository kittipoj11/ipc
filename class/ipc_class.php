<?php
// require_once 'config.php';
require_once 'connection_class.php';
require_once 'po_class.php';
class Ipc
{
    private $db;
    public function __construct(PDO $pdoConnection)
    {
        $this->db = $pdoConnection;
    }

    // ดึงข้อมูลจาก po_main ที่มีข้อมูลใน ipc อย่างน้อย 1 รายการ  และจะ return ค่าออกไปเป็น array
    public function getPoMainAll(): array
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

    public function getPoMainByPoId($poId): ?array
    {
        // ดึงข้อมูลจากตารางหลัก - po_main
        $po=new Po($this->db);
        $rs=$po->getPoMainByPoId($poId);
        if (!$rs) {
            return null; // ไม่พบข้อมูล
        }

        return $rs;
    }

    public function getPoIpcAllByPoId($poId): ?array
    {
        // ดึงข้อมูลจากตารางหลัก - po_main
        $po=new Po($this->db);
        $rs=$po->getPoMainByPoId($poId);
        if (!$rs) {
            return null; // ไม่พบข้อมูล
        }

        // ดึงข้อมูลจากตารางรอง
        $rs['ipc'] = $this->getIpcAllByPoId($poId);

        return $rs;
    }

    public function getIpcAllByPoId($poId): array
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

    public function getIpcByIpcId(int $ipcId): ?array
    {
        // 1. ดึงข้อมูลของ ipc ที่ต้องการ
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
        $stmt->bindParam(':ipc_id', $ipcId, PDO::PARAM_INT);
        $stmt->execute();
        $rsIpc = $stmt->fetch(PDO::FETCH_ASSOC);

        // 2. ถ้าไม่พบข้อมูล Ipc ให้คืนค่า null ทันที
        if (!$rsIpc) {
            return null;
        }

        // 3. ดึงข้อมูลของ po_main ของ period_id ที่ต้องการ
        $rsPoMain = $this->getPoMainByPoId($rsIpc['po_id']);

        // 6. ดึงข้อมูล approver แยกเป็น row ออกมา
        $sql = "SELECT W.approver_id, W.order_in_block, W.approval_level
                , U.full_name
                , case when I.current_approval_level > W.approval_level then U.filename else '' end as signature
                , case when I.current_approval_level > W.approval_level then 'inline-block' else 'none' end as display
                from workflow_steps W
                inner join users U on W.approver_id = U.user_id
                inner join ipc I on I.workflow_id = W.workflow_id
                where I.workflow_id = :workflow_id
                and W.order_in_block > 0
                and I.ipc_id = :ipc_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':ipc_id', $ipcId, PDO::PARAM_INT);
        $stmt->bindParam(':workflow_id', $rsIpc['workflow_id'], PDO::PARAM_INT);
        $stmt->execute();
        $rsApprover = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 7. จัดโครงสร้างข้อมูลใหม่เพื่อความเข้าใจง่าย
        $result = [
            'pomain' => $rsPoMain,
            'ipc' => $rsIpc,
            'approver' => $rsApprover, // ข้อมูล approver ที่ได้จากขั้นตอนที่ 6
        ];

        return $result;
    }

public function getCurrentApprovalType($ipcId): ?array
    {
        $sql = "SELECT I.`ipc_status`, I.`current_approval_level`, I.`current_approver_id` 
                , W.approval_type_text
                FROM `ipc` I
                INNER JOIN workflow_steps W
                    ON I.workflow_id = W.workflow_id AND current_approval_level = approval_level
                WHERE I.`ipc_id` = :ipc_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':ipc_id', $ipcId, PDO::PARAM_INT);
        $stmt->execute();
        $rs = $stmt->fetch();
        if (!$rs) {
            return null;
        }
        return $rs;
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
            // $_SESSION['sql SSSSSSSSSSSSSSSSSS'] = $ipcId;
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
        $_SESSION['ipc execute']= $stmt->execute();
        // $stmt->execute();
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