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

    //ต้องแก้ไข
        public function delete(int $poId): bool
    {
        try {
            $sql = "SELECT file_path
                    FROM `inspection_files` 
                    INNER JOIN `inspection_periods`
                        ON `inspection_files`.`inspection_id` = `inspection_periods`.`inspection_id`
                    INNER JOIN `po_periods`
                        ON `po_periods`.`period_id` = `inspection_periods`.`period_id`
                    WHERE `po_periods`.`po_id` = :po_id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':po_id', $poId, PDO::PARAM_INT);
            $stmt->execute();
            $rs = $stmt->fetchAll();

            // ลบไฟล์ออกจาก server
            foreach ($rs as $row) {
                $filePath = $row['file_path'];
                if (file_exists($filePath)) {
                    unlink($filePath); // ลบไฟล์
                }
            }

            $sql = "DELETE FROM `po_main` 
                    WHERE po_id = :po_id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':po_id', $poId, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function savePeriod(array $periodData):int
    {
        $periodId = $periodData['period_id'];
        if(empty($periodId)){
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
        // return $rs ?: null; //ถ้าไม่มีการดึงข้อมูลจากตารางย่อยสามารถใช้การ return แบบนี้ได้

        // ดึงข้อมูลจากตารางย่อย
        $rs['periods'] = $this->getAllPeriodByPoId($poId);

        return $rs;
    }
    public function getPoMainByPoId($poId): ?array
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

        return $rs;
    }

    public function getAllPeriodByPoId($poId): array
    {
        $sql = "SELECT `period_id`, `po_id`, `period_number`, `workload_planned_percent`, `interim_payment`, `interim_payment_percent`, period_status, `remark`
                FROM `po_periods`
                WHERE `po_id` = :po_id
                ORDER BY `po_id`, `period_number`";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':po_id', $poId, PDO::PARAM_INT);
        $stmt->execute();

        $rs = $stmt->fetchAll();
        return $rs;
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