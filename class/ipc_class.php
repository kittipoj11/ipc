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









}


// $stmt = $conn->prepare('select ...where :');
// $stmt->bindParam(':building_id', $building_id, PDO::PARAM_STR);
// $stmt->bindParam(':building_name', $building_name, PDO::PARAM_STR);
// $stmt->bindParam(':is_active', $is_active, PDO::PARAM_BOOL);
// $stmt->execute();
// $rs = $stmt->fetchAll(PDO::FETCH_ASSOC);