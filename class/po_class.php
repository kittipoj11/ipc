<?php
// require_once 'config.php';
require_once 'connection_class.php';

class Po extends Connection
{
    public function getAllRecord()
    {
        $sql = <<<EOD
                SELECT `po_id`, `po_no`, `project_name`, `po_main`.`supplier_id`, `po_main`.`location_id`, `working_name_th`
                , `working_name_en`, `is_include_vat`, `contract_value`, `contract_value_before`, `vat`, `is_deposit`
                , `deposit_percent`, `deposit_value`, `create_by`, `create_date`, `number_of_period`
                , `suppliers`.`supplier_name`
                , `locations`.`location_name`
                FROM `po_main`
                INNER JOIN `suppliers`
                    ON `suppliers`.`supplier_id` = `po_main`.`supplier_id`
                INNER JOIN `locations`
                    ON `locations`.`location_id` = `po_main`.`location_id`
                EOD;
        $stmt = $this->myConnect->prepare($sql);
        $stmt->execute();
        $rs = $stmt->fetchAll();
        return $rs;
    }

    public function getRecordById($id)
    {
        $sql = <<<EOD
                SELECT `po_id`, `po_no`, `project_name`, `po_main`.`supplier_id`, `po_main`.`location_id`, `working_name_th`
                , `working_name_en`, `is_include_vat`, `contract_value`, `contract_value_before`, `vat`, `is_deposit`
                , `deposit_percent`, `deposit_value`, `create_by`, `create_date`, `number_of_period`
                , `suppliers`.`supplier_name`
                , `locations`.`location_name`
                FROM `po_main`
                INNER JOIN `suppliers`
                    ON `suppliers`.`supplier_id` = `po_main`.`supplier_id`
                INNER JOIN `locations`
                    ON `locations`.`location_id` = `po_main`.`location_id`
                EOD;

        $stmt = $this->myConnect->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $rs = $stmt->fetch();
        return $rs;
    }

    public function insertData($getData)
    {
        try {
            // สร้าง id มี prefix ในที่นี่ให้ prefix เป็น PO 
            // $po_id = uniqid('PO', true);
            // ดึงข้อมูล id ที่เป็น Auto Increment หลังจาก Insert ข้อมูล Header แล้ว
            // $po_id = $pdo->lastInsertId();

            // parameters ในส่วน po_main
            $po_no = $getData['po_no'];
            $project_name = $getData['project_name'];
            $supplier_id = $getData['supplier_id'];
            $location_id = $getData['location_id'];
            $working_name_th = $getData['working_name_th'];
            $working_name_en = $getData['working_name_en'];
            $contract_value_before = $getData['contract_value_before'];
            $contract_value = $getData['contract_value'];
            $vat = $getData['vat'];
            $is_deposit = $getData['is_deposit'];
            $deposit_percent = $getData['deposit_percent'];
            $working_date_from = $getData['working_date_from'];
            $working_date_to = $getData['working_date_to'];
            $working_day = $getData['working_day'];
            // $is_active = isset($getData['is_active']) ? 1 : 0;

            // parameters ในส่วน po_period
            $number_of_period=count($getData['period']);

            $sql = <<<EOD
                        INSERT INTO `po_main`(`po_id`, `po_no`, `project_name`, `supplier_id`, `location_id`, `working_name_th`, `working_name_en`
                        , `is_include_vat`, `contract_value`, `contract_value_before`, `vat`, `is_deposit`, `deposit_percent`, `deposit_value`
                        , `working_date_from`, `working_date_to`, `working_day`, `remain_value_interim_payment`, `total_retention_value`
                        , `create_by`, `create_date`, `number_of_period`) 
                        VALUES(:po_id, :po_no, :project_name, :supplier_id, :location_id, :working_name_th, :working_name_en
                        , :is_include_vat, :contract_value, :contract_value_before, :vat, :is_deposit, :deposit_percent, :deposit_value
                        , :working_date_from, :working_date_to, :working_day, :remain_value_interim_payment, :total_retention_value
                        , :create_by, :create_date, :number_of_period)
                    EOD;
            $stmt = $this->myConnect->prepare($sql);
            // $stmt->bindParam(':id', $headerId, PDO::PARAM_STR);
            $stmt->bindParam(':po_no', $po_no, PDO::PARAM_STR);
            $stmt->bindParam(':project_name', $project_name, PDO::PARAM_STR);
            $stmt->bindParam(':supplier_id', $supplier_id,  PDO::PARAM_INT);
            $stmt->bindParam(':location_id', $location_id, PDO::PARAM_INT);
            $stmt->bindParam(':working_name_th', $working_name_th, PDO::PARAM_STR);
            $stmt->bindParam(':working_name_en', $working_name_en, PDO::PARAM_STR);
            $stmt->bindParam(':contract_value_before', $contract_value_before, PDO::PARAM_STR);
            $stmt->bindParam(':contract_value', $contract_value, PDO::PARAM_STR);
            $stmt->bindParam(':vat', $vat, PDO::PARAM_STR);
            $stmt->bindParam(':is_deposit', $is_deposit, PDO::PARAM_BOOL);
            $stmt->bindParam(':deposit_percent', $deposit_percent, PDO::PARAM_STR);
            $stmt->bindParam(':working_date_from', $working_date_from->format('Y-m-d'), PDO::PARAM_STR);
            $stmt->bindParam(':working_date_to', $working_date_to->format('Y-m-d'), PDO::PARAM_STR);
            $stmt->bindParam(':working_day', $working_day, PDO::PARAM_INT);
            $stmt->bindParam(':number_of_period', $number_of_period, PDO::PARAM_INT);

            // $stmt->bindParam(':is_active', $is_active, PDO::PARAM_BOOL);
            // $affected = $stmt->execute();
            // $_SESSION['getData'] = $getData;
            // $_SESSION['sql'] = $stmt->debugDumpParams();
            // $stmt->debugDumpParams();
            exit;
            if ($stmt->execute()) {
                $po_id = $this->myConnect->lastInsertId();
                $sqlDetail = 'insert into tbl_open_area_schedule_detail(id, po_id, po_no, supplier_id, project_name, working_date_from, working_date_to, working_name_en, contract_value_before, location_id, car_type_json) 
                        values(:id, :po_id, :po_no, :supplier_id, :project_name, :working_date_from, :working_date_to, :working_name_en, :contract_value_before, :location_id, :car_type_arr)';
                $stmtDetail = $this->myConnect->prepare($sqlDetail);

                $sqlSubs = 'insert into tbl_open_area_schedule_sub_details(id, po_id, po_no, supplier_id, project_name, working_date_from, working_date_to, working_name_en, contract_value_before, open_date, open_time_json, location_id, car_type_json) 
                            values(:id, :po_id, :po_no, :supplier_id, :project_name, :working_date_from, :working_date_to, :working_name_en, :contract_value_before, :open_date, :open_time_json, :location_id, :car_type_arr)';
                $stmtSubs = $this->myConnect->prepare($sqlSubs);

                // กำหนด parameter
                foreach ($getData['date_start'] as $key_data => $value_data) :
                    $working_date_from = $getData['date_start'][$key_data];
                    $working_date_to = $getData['date_end'][$key_data];

                    $working_name_en = $getData['time_start'][$key_data];
                    $contract_value_before = $getData['time_end'][$key_data];
                    $location_id = $getData['location_id'][$key_data];

                    $rowid = $getData['id'][$key_data];
                    $checkbox = implode(',', $getData['chkCarType' . $rowid]);
                    $car_type_arr = '[' . $checkbox . ']';

                    $stmtDetail->bindParam(':id', $id, PDO::PARAM_STR);
                    $stmtDetail->bindParam(':po_id', $po_id, PDO::PARAM_INT);
                    $stmtDetail->bindParam(':po_no', $po_no, PDO::PARAM_STR);
                    $stmtDetail->bindParam(':supplier_id', $supplier_id, PDO::PARAM_STR);
                    $stmtDetail->bindParam(':project_name', $project_name, PDO::PARAM_STR);
                    $stmtDetail->bindParam(':working_date_from', $working_date_from, PDO::PARAM_STR);
                    $stmtDetail->bindParam(':working_date_to', $working_date_to, PDO::PARAM_STR);
                    $stmtDetail->bindParam(':working_name_en', $working_name_en, PDO::PARAM_STR);
                    $stmtDetail->bindParam(':contract_value_before', $contract_value_before, PDO::PARAM_STR);
                    $stmtDetail->bindParam(':location_id', $location_id, PDO::PARAM_INT);
                    $stmtDetail->bindParam(':car_type_arr', $car_type_arr);
                    if ($stmtDetail->execute()) {
                        //Sub detail
                        $start = new DateTime($working_date_from);
                        $end = new DateTime($working_date_to);
                        $end->modify('+1 day');
                        $interval = new DateInterval('P1D');
                        //หรือ $interval = DateInterval::createFromDateString('1 day');
                        $period = new DatePeriod($start, $interval, $end);

                        $start_time = new DateTime($working_name_en);
                        $end_time = new DateTime($contract_value_before);
                        $end_time->modify('+10 minute');
                        $interval_time = new DateInterval('PT10M');
                        $period_time = new DatePeriod($start_time, $interval_time, $end_time);

                        $stmtSubs->bindParam(':id', $id, PDO::PARAM_STR);
                        $stmtSubs->bindParam(':po_id', $po_id, PDO::PARAM_INT);
                        $stmtSubs->bindParam(':po_no', $po_no, PDO::PARAM_STR);
                        $stmtSubs->bindParam(':supplier_id', $supplier_id, PDO::PARAM_STR);
                        $stmtSubs->bindParam(':project_name', $project_name, PDO::PARAM_STR);
                        $stmtSubs->bindParam(':working_date_from', $working_date_from, PDO::PARAM_STR);
                        $stmtSubs->bindParam(':working_date_to', $working_date_to, PDO::PARAM_STR);
                        $stmtSubs->bindParam(':working_name_en', $working_name_en, PDO::PARAM_STR);
                        $stmtSubs->bindParam(':contract_value_before', $contract_value_before, PDO::PARAM_STR);
                        $stmtSubs->bindParam(':location_id', $location_id, PDO::PARAM_INT);
                        $stmtSubs->bindParam(':car_type_arr', $car_type_arr);

                        foreach ($period as $date) :
                            $open_date = $date->format("Y-m-d");
                            $stmtSubs->bindParam(':open_date', $open_date, PDO::PARAM_STR);

                            // แก้ไขตรงนี้
                            $open_time_json = '{';
                            foreach ($period_time as $time) :
                                $open_time_json .= '"' . $time->format("H:i") . '":' . $location_id . ',';

                            endforeach;
                            $open_time_json = substr($open_time_json, 0, -1);
                            $open_time_json .= '}';

                            // $_SESSION['open_time_json'] = $open_time_json;
                            $stmtSubs->bindParam(':open_time_json', $open_time_json, PDO::PARAM_STR);
                            $stmtSubs->execute();
                        endforeach;
                    }
                endforeach;

                $_SESSION['message'] =  'data has been created successfully.';
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $_SESSION['message'] =  'This item could not be added.Because the data has duplicate values!!!';
            } else {
                $_SESSION['message'] =  'Something is wrong.Can not add data.';
            }
        }finally{
            $stmt->closeCursor();
            // $stmtDetail->closeCursor();
            // $stmtSubs->closeCursor();
            unset($stmt);
            unset($stmtDetail);
            unset($stmtSubs);
        }
    }

    public function updateData($getData)
    {
        $po_id = $getData['po_id'];
        $po_name = $getData['po_name'];
        $sql = "update po
                set po_name = :po_name
                where po_id = :po_id";
        // , update_datetime = CURRENT_TIMESTAMP()
        $stmt = $this->myConnect->prepare($sql);
        $stmt->bindParam(':po_id', $po_id, PDO::PARAM_INT);
        $stmt->bindParam(':po_name', $po_name, PDO::PARAM_STR);

        try {
            if ($stmt->execute()) {
                echo 'data has been update successfully.';
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                echo 'This item could not be added.Because the data has duplicate values!!!';
            } else {
                echo 'Something is wrong.Can not add data.';
            }
        }
    }
    public function deleteData($getData)
    {
        $po_id = $getData['delete_id'];
        // $is_active = isset($getData['is_active']) ? 1 : 0;
        $sql = "update po
                set is_deleted = 1
                where po_id = :po_id";
        $stmt = $this->myConnect->prepare($sql);
        $stmt->bindParam(':po_id', $po_id, PDO::PARAM_INT);

        try {
            if ($stmt->execute()) {
                echo 'data has been delete successfully.';
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                echo 'This item could not be added.Because the data has duplicate values!!!';
            } else {
                echo 'Something is wrong.Can not add data.';
            }
        }
    }

    public function getHtmlData()
    {
        $sql = "select po_id, po_name, is_deleted 
                from po
                where is_deleted = false";

        $stmt = $this->myConnect->prepare($sql);
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