<?php
// require_once 'config.php';
require_once 'connection_clasS.php';

class Ipc {
    private $db; 
    public function __construct(PDO $pdoConnection)
    {
        $this->db = $pdoConnection;
    }

    // ดึงข้อมูลจาก po_main ที่มีข้อมูลใน ipc_periods อย่างน้อย 1 รายการ  และจะ return ค่าออกไปเป็น array
    public function getAllPo(): array
    {
        $sql = "SELECT DISTINCT O.po_id, po_number, O.project_name, O.supplier_id, O.location_id
                , working_name_th, working_name_en, is_include_vat, O.contract_value, O.contract_value_before, vat
                , is_deposit, deposit_percent, deposit_value
                , working_date_from, working_date_to, working_day
                , number_of_period
                , S.supplier_name
                , L.location_name
                FROM po_main O
                INNER JOIN ipc_periods I
                    ON I.po_id = O.po_id
                INNER JOIN suppliers S
                    ON S.supplier_id = O.supplier_id
                INNER JOIN locations L
                    ON L.location_id = O.location_id
                ORDER BY O.po_id";
        $stmt = $this->db->prepare($sql);
        
        $stmt->execute();
        $rs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $rs;
    }

    public function getPoByPoId($poId): ?array
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
    
public function getAllPeriodByPoId($poId):array
{
    $sql = "SELECT ipc_id, inspection_id, period_id, po_id, period_number, create_date, project_name, agreement_date
            , contractor, contract_value, total_value_of_interim_payment, less_previous_interim_payment, net_value_of_current_claim
            , less_retension_exclude_vat, net_amount_due_for_payment, total_value_of_retention, total_value_of_certification_made
            , resulting_balance_of_contract_sum_outstanding, submit_by, approved1_by, approved2_by, remark, workflow_id 
            FROM ipc_periods
            WHERE po_id = :po_id
            ORDER BY period_number";
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':po_id', $poId, PDO::PARAM_INT);

    $stmt->execute();
    $rs = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        $rsPoMain = $this->getPoByPeriodId($periodId);

        // 4. ดึงข้อมูล PeriodDetails ทั้งหมดของ Period นี้ 
        $rsPeriodDetails = $this->getPeriodDetailsByPeriodId($periodId);

        // 5. ดึงข้อมูล Period Approvals ของ period_id ที่มี approval_level = current_approval_level
        $sql = "SELECT inspection_approval_id, inspection_id, period_id, po_id, period_number
                , approval_level, approver_id, approval_type_id, approval_type_text, approval_status_id, approval_date, approval_comment 
                FROM inspection_period_approvals 
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
            'periodApprovals' => $rsPeriodApprovals, // ข้อมูล period details ที่ได้จากขั้นตอนที่ 5
            'maxPeriodApproval' => $rsMaxPeriodApproval, // ข้อมูล period details ที่ได้จากขั้นตอนที่ 6
        ];

        return $result;
    }







}


// $stmt = $conn->prepare('select ...where :');
// $stmt->bindParam(':building_id', $building_id, PDO::PARAM_STR);
// $stmt->bindParam(':building_name', $building_name, PDO::PARAM_STR);
// $stmt->bindParam(':is_active', $is_active, PDO::PARAM_BOOL);
// $stmt->execute();
// $rs = $stmt->fetchAll(PDO::FETCH_ASSOC);