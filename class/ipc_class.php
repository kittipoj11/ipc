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
                INNER JOIN inspection_periods I
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











}


// $stmt = $conn->prepare('select ...where :');
// $stmt->bindParam(':building_id', $building_id, PDO::PARAM_STR);
// $stmt->bindParam(':building_name', $building_name, PDO::PARAM_STR);
// $stmt->bindParam(':is_active', $is_active, PDO::PARAM_BOOL);
// $stmt->execute();
// $rs = $stmt->fetchAll(PDO::FETCH_ASSOC);