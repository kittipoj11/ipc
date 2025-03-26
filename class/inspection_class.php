<?php
// require_once 'config.php';
require_once 'connection_class.php';

class Inspection extends Connection
{
    public function getInspectionPeriodAllByPoId($getPoId)
    {
        $sql = <<<EOD
                    SELECT inspection_periods.inspection_id, inspection_periods.period_id, inspection_periods.po_id, inspection_periods.period_number
                    , inspection_periods.workload_planned_percent, inspection_periods.workload_actual_completed_percent, inspection_periods.workload_remaining_percent
                    , inspection_periods.interim_payment, inspection_periods.interim_payment_percent
                    , inspection_periods.interim_payment_less_previous, inspection_periods.interim_payment_less_previous_percent
                    , inspection_periods.interim_payment_accumulated, inspection_periods.interim_payment_accumulated_percent
                    , inspection_periods.interim_payment_remain, inspection_periods.interim_payment_remain_percent
                    , inspection_periods.retention_value, inspection_periods.plan_status, inspection_periods.is_paid, inspection_periods.is_retention
                    , inspection_periods.remark, inspection_periods.current_status, inspection_periods.current_approval_level 
                    FROM `inspection_periods`
                    WHERE `po_id` = :po_id
                    ORDER BY `period_number`
                EOD;
        $stmt = $this->myConnect->prepare($sql);
        $stmt->bindParam(':po_id', $getPoId, PDO::PARAM_INT);
        $stmt->execute();
        $rs = $stmt->fetchAll();
        return $rs;
    }

    public function getInspectionPeriodByPeriodId($getPoId, $getPeriodId)
    {
        $sql = <<<EOD
                    SELECT P1.inspection_id, P1.period_id, P1.po_id, P1.period_number
                    , P1.workload_planned_percent, P1.workload_actual_completed_percent, P1.workload_remaining_percent
                    , P1.interim_payment, P1.interim_payment_percent
                    , P1.interim_payment_less_previous, P1.interim_payment_less_previous_percent
                    , P1.interim_payment_accumulated, P1.interim_payment_accumulated_percent
                    , P1.interim_payment_remain, P1.interim_payment_remain_percent
                    , P1.retention_value, P1.plan_status, P1.is_paid, P1.is_retention
                    , P1.remark, P1.current_status, P1.current_approval_level 
                    , po_main.supplier_id, po_main.location_id , po_main.po_number, po_main.project_name
                    , po_main.working_name_th, po_main.working_name_en
                    , po_main.is_include_vat, po_main.contract_value, po_main.contract_value_before, po_main.vat, is_deposit, deposit_percent, deposit_value
                    , working_date_from, working_date_to, working_day
                    , suppliers.supplier_name, locations.location_name
                    , inspection_approvals.approval_level , approval_status.action_type_id,  action_type.action_type_name
                    , COALESCE(P2.interim_payment_accumulated, 0) AS previous_interim_payment_accumulated
                    FROM inspection_periods P1
                    INNER JOIN po_main
                        ON P1.po_id = po_main.po_id
                    INNER JOIN suppliers
                        ON suppliers.supplier_id = po_main.supplier_id
                    INNER JOIN locations
                        ON locations.location_id = po_main.location_id   
                    LEFT JOIN inspection_approvals
                        ON inspection_approvals.approval_level = P1.current_approval_level
                        AND inspection_approvals.inspection_id = P1.inspection_id
                    LEFT JOIN approval_status
                        ON approval_status.approval_status_id = inspection_approvals.approval_status_id
                    LEFT JOIN action_type
                        ON action_type.action_type_id = approval_status.action_type_id
                    LEFT JOIN inspection_periods P2 
                        ON P2.po_id = P1.po_id AND P2.period_number = P1.period_number - 1
                    WHERE P1.po_id = :po_id
                        AND P1.period_id = :period_id
                    ORDER BY P1.po_id, period_number
                EOD;
        $stmt = $this->myConnect->prepare($sql);
        $stmt->bindParam(':po_id', $getPoId, PDO::PARAM_INT);
        $stmt->bindParam(':period_id', $getPeriodId, PDO::PARAM_INT);
        $stmt->execute();
        $rs = $stmt->fetch();
        return $rs;
    }

    public function getInspectionPeriodDetailByPeriodId($getPoId, $getPeriodId)
    {
        $sql = <<<EOD
                    SELECT `rec_id`, `inspection_period_details`.`inspection_id`, `order_no`, `details`, `inspection_period_details`.`remark`
                    FROM `inspection_period_details`
                    INNER JOIN `inspection_periods`
                        ON `inspection_periods`.`inspection_id` = `inspection_period_details`.`inspection_id`
                    WHERE `po_id` = :po_id
                        AND `inspection_periods`.`period_id` = :period_id
                    ORDER BY `order_no`
                EOD;
        $stmt = $this->myConnect->prepare($sql);
        $stmt->bindParam(':po_id', $getPoId, PDO::PARAM_INT);
        $stmt->bindParam(':period_id', $getPeriodId, PDO::PARAM_INT);
        $stmt->execute();
        $rs = $stmt->fetchAll();
        return $rs;
    }

    public function getInspectionFilesByInspectionId($getPoId, $getPeriodId, $getInspectionId)
    {
        $sql = <<<EOD
                    SELECT `file_id`, `inspection_files`.`inspection_id`, `file_name`, `file_path`, `file_type`, `uploaded_at` 
                    FROM `inspection_files` 
                    INNER JOIN `inspection_periods`
                        ON `inspection_periods`.`inspection_id` = `inspection_files`.`inspection_id`
                    WHERE `inspection_periods`.`inspection_id` = :inspection_id
                        AND `inspection_periods`.`period_id` = :period_id
                        AND `inspection_periods`.`po_id` = :po_id
                    EOD;
        $stmt = $this->myConnect->prepare($sql);
        $stmt->bindParam(':po_id', $getPoId, PDO::PARAM_INT);
        $stmt->bindParam(':period_id', $getPeriodId, PDO::PARAM_INT);
        $stmt->bindParam(':inspection_id', $getInspectionId, PDO::PARAM_INT);
        $stmt->execute();
        $rs = $stmt->fetchAll();
        return $rs;
    }

    
    public function updateInspectionPeriod($getData)
    {
        @session_start();

        try {
            // $_SESSION['getData'] =  $getData;
            // exit;
            // $this->myConnect->beginTransaction();

            // parameters ในส่วน main
            $po_id = $getData['po_id'];
            $period_id = $getData['period_id'];
            $inspection_id = $getData['inspection_id'];
            $po_number = $getData['po_number'];

            $workload_planned_percent=floatval($getData['workload_planned_percent'] ?? 0);
            $workload_actual_completed_percent=floatval($getData['workload_actual_completed_percent'] ?? 0);
            $workload_remaining_percent=floatval($getData['workload_remaining_percent'] ?? 0);
            $interim_payment=floatval($getData['interim_payment'] ?? 0);
            $interim_payment_less_previous=floatval($getData['interim_payment_less_previous'] ?? 0);
            $interim_payment_accumulated=floatval($getData['interim_payment_accumulated'] ?? 0);
            $interim_payment_remain=floatval($getData['interim_payment_remain'] ?? 0);
            $retention_value=floatval($getData['retention_value'] ?? 0);
            
            $interim_payment_percent=floatval($getData['interim_payment_percent'] ?? 0);
            $interim_payment_less_previous_percent=floatval($getData['interim_payment_less_previous_percent'] ?? 0);
            $interim_payment_accumulated_percent=floatval($getData['interim_payment_accumulated_percent'] ?? 0);
            $interim_payment_remain_percent=floatval($getData['interim_payment_remain_percent'] ?? 0);
            
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

            // $_SESSION['insert'] = $insert_indexs;
            // $_SESSION['update'] = $update_indexs;
            // $_SESSION['delete'] = $delete_indexs;

            //UPDATE po_main
            $sql = <<<EOD
                        UPDATE `inspection_periods`
                            SET `workload_actual_completed_percent` = :workload_actual_completed_percent
                            , `workload_remaining_percent` = :workload_remaining_percent
                            , `workload_planned_percent` =:workload_planned_percent
                            , `interim_payment` =:interim_payment
                            , `interim_payment_percent` =:interim_payment_percent
                            , `interim_payment_less_previous` =:interim_payment_less_previous
                            , `interim_payment_less_previous_percent` =:interim_payment_less_previous_percent
                            , `interim_payment_accumulated` =:interim_payment_accumulated
                            , `interim_payment_accumulated_percent` =:interim_payment_accumulated_percent
                            , `interim_payment_remain` =:interim_payment_remain
                            , `interim_payment_remain_percent` =:interim_payment_remain_percent
                            , `retention_value` =:retention_value
                            WHERE `po_id` = :po_id
                                AND `period_id` = :period_id
                                AND `inspection_id` = :inspection_id
                    EOD;
            $stmtInspectPeriods = $this->myConnect->prepare($sql);
            // $stmtInspectPeriods->bindParam(':po_number', $po_number, PDO::PARAM_STR);
            $stmtInspectPeriods->bindParam(':po_id', $po_id, PDO::PARAM_INT);
            $stmtInspectPeriods->bindParam(':period_id', $period_id, PDO::PARAM_INT);
            $stmtInspectPeriods->bindParam(':inspection_id', $inspection_id, PDO::PARAM_INT);
            $stmtInspectPeriods->bindParam(':workload_actual_completed_percent', $workload_actual_completed_percent, PDO::PARAM_STR);
            $stmtInspectPeriods->bindParam(':workload_remaining_percent', $workload_remaining_percent, PDO::PARAM_STR);
            $stmtInspectPeriods->bindParam(':workload_planned_percent', $workload_planned_percent, PDO::PARAM_STR);
            $stmtInspectPeriods->bindParam(':interim_payment', $interim_payment, PDO::PARAM_STR);
            $stmtInspectPeriods->bindParam(':interim_payment_percent', $interim_payment_percent, PDO::PARAM_STR);
            $stmtInspectPeriods->bindParam(':interim_payment_less_previous', $interim_payment_less_previous, PDO::PARAM_STR);
            $stmtInspectPeriods->bindParam(':interim_payment_less_previous_percent', $interim_payment_less_previous_percent, PDO::PARAM_STR);
            $stmtInspectPeriods->bindParam(':interim_payment_accumulated', $interim_payment_accumulated, PDO::PARAM_STR);
            $stmtInspectPeriods->bindParam(':interim_payment_accumulated_percent', $interim_payment_accumulated_percent, PDO::PARAM_STR);
            $stmtInspectPeriods->bindParam(':interim_payment_remain', $interim_payment_remain, PDO::PARAM_STR);
            $stmtInspectPeriods->bindParam(':interim_payment_remain_percent', $interim_payment_remain_percent, PDO::PARAM_STR);
            $stmtInspectPeriods->bindParam(':retention_value', $retention_value, PDO::PARAM_STR);

            $_SESSION['period_id'] = $period_id;
            $_SESSION['inspection_id'] = $inspection_id;
            $_SESSION['interim_payment'] = $interim_payment;
            $_SESSION['workload_actual_completed_percent'] = $workload_actual_completed_percent;
            $_SESSION['workload_remaining_percent'] = $workload_remaining_percent;
            // $_SESSION['stmtInspectPeriods->execute1'] = $stmtInspectPeriods->queryString;
            if ($stmtInspectPeriods->execute()) {
                // $_SESSION['stmtInspectPeriods->execute2'] = $stmtInspectPeriods->queryString;
                $stmtInspectPeriods->closeCursor();
                
                // INSERT inspection_period_details
                $sql = <<<EOD
                            INSERT INTO `inspection_period_details`(`inspection_id`, `order_no`, `details`, `remark`) 
                            VALUES (:inspection_id, :order_no, :details, :remark)
                        EOD;
                $stmtInspectPeriodDetails = $this->myConnect->prepare($sql);
                
                foreach ($insert_indexs as $i) { //ถ้าต้องการใช้ key ด้วย foreach($insert_indexs as $key=> $value){
                    $stmtInspectPeriodDetails->bindParam(':inspection_id', $inspection_id, PDO::PARAM_INT);
                    $stmtInspectPeriodDetails->bindParam(':order_no', $order_nos[$i], PDO::PARAM_INT);
                    $stmtInspectPeriodDetails->bindParam(':details', $details[$i],  PDO::PARAM_STR);
                    $stmtInspectPeriodDetails->bindParam(':remark', $remarks[$i], PDO::PARAM_STR);
                    
                    $stmtInspectPeriodDetails->execute();
                    $stmtInspectPeriodDetails->closeCursor();
                }

                // UPDATE inspection_period_details
                $sql = <<<EOD
                            UPDATE `inspection_period_details`
                            SET `details` = :details
                            , `remark` = :remark
                            WHERE `inspection_id` = :inspection_id
                                AND `rec_id` = :rec_id
                        EOD;
                $stmtInspectPeriodDetails = $this->myConnect->prepare($sql);

                foreach ($update_indexs as $i) {
                    $stmtInspectPeriodDetails->bindParam(':inspection_id', $inspection_id, PDO::PARAM_INT);
                    $stmtInspectPeriodDetails->bindParam(':rec_id', $rec_ids[$i], PDO::PARAM_INT);
                    $stmtInspectPeriodDetails->bindParam(':details', $details[$i],  PDO::PARAM_STR);
                    $stmtInspectPeriodDetails->bindParam(':remark', $remarks[$i], PDO::PARAM_STR);

                    $stmtInspectPeriodDetails->execute();
                    $stmtInspectPeriodDetails->closeCursor();
                }

                // DELETE inspection_period_details
                $sql = <<<EOD
                            DELETE FROM `inspection_period_details`
                            WHERE `inspection_id` = :inspection_id
                                AND `rec_id` = :rec_id
                        EOD;
                $stmtInspectPeriodDetails = $this->myConnect->prepare($sql);

                foreach ($delete_indexs as $i) {
                    $stmtInspectPeriodDetails->bindParam(':inspection_id', $inspection_id, PDO::PARAM_INT);
                    $stmtInspectPeriodDetails->bindParam(':rec_id', $rec_ids[$i], PDO::PARAM_INT);

                    $stmtInspectPeriodDetails->execute();
                    $stmtInspectPeriodDetails->closeCursor();
                }

                $_SESSION['message'] =  'data has been created successfully.';
            }
        } catch (PDOException $e) {
            $_SESSION['message'] =  $e->getCode() + ' : ' + $e->getMessage();
        }
    }

    public function insertInspectionFiles($getData)
    {
        @session_start();

        // $_SESSION['getData in po_class'] = $getData;
        // return;
        try {
            // เริ่ม transaction
            $this->myConnect->beginTransaction();

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
                        $stmt = $this->myConnect->prepare($sql);
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
            $this->myConnect->commit();
            // $_SESSION['commit'] = 'Completed';
            echo json_encode(['status' => 'success', 'message' => 'Record and files uploaded successfully.']);
            // echo json_encode(["a" => "A", "b" => "B"]);
        } catch (PDOException $e) {
            // $_SESSION['rollBack'] = 'Completed';
            // rollback transaction
            $this->myConnect->rollBack();
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
            $this->myConnect->beginTransaction();

            // ดึงข้อมูลไฟล์ที่จะลบ
            $sql = <<<EOD
                        SELECT file_path 
                        FROM `inspection_files`
                        WHERE file_id = :file_id
                    EOD;
            $stmt = $this->myConnect->prepare($sql);
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
            $stmt = $this->myConnect->prepare($sql);
            $stmt->bindParam(':file_id', $file_id, PDO::PARAM_INT);
            $stmt->execute();

            // commit transaction
            $this->myConnect->commit();
            echo json_encode(['status' => 'success', 'message' => 'Record and associated files deleted successfully.']);
        } catch (Exception $e) {
            // rollback transaction
            $this->myConnect->rollBack();
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
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
// $stmt->bindParam(':building_id', $building_id, PDO::PARAM_STR);
// $stmt->bindParam(':building_name', $building_name, PDO::PARAM_STR);
// $stmt->bindParam(':is_active', $is_active, PDO::PARAM_BOOL);
// $stmt->execute();
// $rs = $stmt->fetchAll(PDO::FETCH_ASSOC);