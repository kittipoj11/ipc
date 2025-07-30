<?php
@session_start();
// require_once 'config.php';
require_once 'connection_class.php';
require_once 'po_class.php';
require_once 'inspection_class.php';

class DocumentService
{
    private $db;
    private $po;
    private $inspection;

    public function __construct(PDO $pdoConnection, Po $po, Inspection $inspection)
    {
        $this->db = $pdoConnection;
        $this->po = $po;
        $this->inspection = $inspection;
    }

    public function managePoAndInspection(array $data): int
    {
        // --- WORKFLOW ---
        // กำหนดค่า default สำหรับ workflow step ของ inspection และ ipc (อาจจะมีหน้าจอ config) โดยที่
        // 1. ทำการสร้าง inspection_approvals เมื่อมีการ save po เรียบร้อยแล้ว
        // 2. ทำการสร้าง ipc_approvals เมื่อมีการ approve ใน step สุดท้ายของ inspection ในแต่ละ period  
        // workflow_id = 1 สร้าง inspection_approvals
        // workflow_id = 2 สร้าง ipc_approvals
        // $workflowId = 1; //ในที่นี้กำหนด workflow_id = 1
        try {
            $this->db->beginTransaction();

            $poId = $this->po->save($data['headerData']);
            // ถ้า $poId ยังคงเป็น 0 หรือว่าง แสดงว่าเกิดข้อผิดพลาด
            if (empty($poId)) {
                return (int)$poId;
            }

            // 2. จัดการข้อมูล Periods ตามลำดับ(D-U-C Logic)
            $deleteItems = array_filter($data['periodsData'], fn($item) => ($item['period_crud'] ?? 'none') === 'delete');
            $updateItems = array_filter($data['periodsData'], fn($item) => ($item['period_crud'] ?? 'none') === 'update');
            $createItems = array_filter($data['periodsData'], fn($item) => ($item['period_crud'] ?? 'none') === 'create');
            
            // $_SESSION['deleteItems DDDDDDDDDDDDDDDDDD'] = $deleteItems;
            // $_SESSION['updateItems UUUUUUUUUUUUUUUUUU'] = $updateItems;
            // $_SESSION['createItems CCCCCCCCCCCCCCCCCC'] = $createItems;
            // 3. ทำงานตามลำดับ D-U-C
            // 3.1 ************************* ตรวจสอบ deleteItems ****************************
            if (!empty($deleteItems)) {
                foreach ($deleteItems as $item) {
                    if (!empty($item['period_id'])) {
                        // หาชื่อพาธไฟล์ที่จะลบไฟล์ออกจาก server
                        $rs = $this->inspection->deleteFiles($item['period_id']);
                        // ทำการลบไฟล์ออกจาก server
                        foreach ($rs as $row) {
                            $filePath = $row['file_path'];
                            if (file_exists($filePath)) {
                                unlink($filePath); // ลบไฟล์
                            }
                        }

                        $affected = $this->po->deletePeriod($item['period_id']);
                        if (!$affected) {
                            throw new Exception('Can not delete po period.');;
                        };
                    }
                }
            }

            // 3.2 ************************* ตรวจสอบ updateItems ****************************
            // ถ้ารายการผ่านขั้นตอนแรกใน inspection_approvals (เปลี่ยน approval_status_id จาก 1-pending เป็น 2-approved) แล้วจะต้องห้ามแก้ไขหรือลบ period นี้
            // แต่ถ้า approval_status_id เปลี่ยนจาก 1-pending เป็น 0-reject จะสามารถแก้ไขหรือลบได้
            // ในขั้นตอนเริ่มต้นของ approval_type ที่เป็น submit จะไม่สามารถ reject เอกสารของตัวเองได้่  ทำได้เพียงเปลี่ยนจาก 1-pending เป็น 2-approved 
            // เพื่อเปลี่ยน approval_type เป็นค่าอื่นที่ไม่ใช่ submit เพื่อส่งให้ผู้ดำเนินการในลำดับถัดไป เช่น จาก 1-submit เป็น verify, confirm หรือ approve ตามแต่ที่กำหนดใน inspection_approvals
            // และในการลบจะยังคงลบจากรายการสุดท้ายก่อนเสมอ
            if (!empty($updateItems)) {
                foreach ($updateItems as &$item) {
                    // UPDATE po_periods
                    $periodId = $this->po->savePeriod($item);
                    // เป็นการ update ไม่ต้องตรวจสอบ period_id หลังจาก save
                    // UPDATE inspection
                    // ต้องตรวจสอบก่อนว่ามีรายการใน inspection ที่มี period_id ตรงกับ period_id ใน po_periods หรือไม่
                    // ถ้ามีจะกำหนด $item['inspection_id'] = ค่า inspection_id ที่ได้
                    // ถ้าไม่มีจะกำหนด $item['inspection_id'] = 0
                    $this->inspection->saveFromPoPeriod($item);
                }
            }

            // // // 3.3 ************************* ตรวจสอบ createItems ****************************
            if (!empty($createItems)) {
            //     // ดึงข้อมูล workflow step เพื่อนำ Loop สร้าง inspection_approvals
            //     $sql = "SELECT `workflow_step_id`, `workflow_id`, `approval_level`, `approver_id`, `approval_type_id`, `approval_type_text`
            //             FROM `workflow_steps`
            //             WHERE `workflow_id` = :workflow_id
            //             ORDER BY approval_level asc";

            //     $stmtWorkflowSteps = $this->db->prepare($sql);
            //     $stmtWorkflowSteps->bindParam(':workflow_id', $workflowId, PDO::PARAM_INT);
            //     $stmtWorkflowSteps->execute();
            //     $rsWorkflowSteps = $stmtWorkflowSteps->fetchAll();

            //     // INSERT inspection_approvals
            //     $sql = "INSERT INTO `inspection_approvals`(`inspection_id`, `period_id`, `po_id`, `period_number`, `approval_level`, `approver_id`, `approval_type_id`, `approval_type_text`, `approval_status_id`) 
            //             VALUES (:inspection_id, :period_id, :po_id, :period_number, :approval_level, :approver_id, :approval_type_id, :approval_type_text, :approval_status_id)";
            //     $stmtCreateInspectApprovals = $this->db->prepare($sql);

                foreach ($createItems as &$item) {
                    // CREATE po_periods
                    $periodId = $this->po->savePeriod($item);

                    $item['period_id']=$periodId;
                    $item['inspection_id']=0;

                    // CREATE inspection
                    $this->inspection->saveFromPoPeriod($item);


            //         $approvalStatusId = 1;
            //         foreach ($rsWorkflowSteps as $row) {
            //             $stmtCreateInspectApprovals->bindParam(':inspection_id', $inspectionId, PDO::PARAM_INT);
            //             $stmtCreateInspectApprovals->bindParam(':period_id', $periodId, PDO::PARAM_INT);
            //             $stmtCreateInspectApprovals->bindParam(':po_id', $poId, PDO::PARAM_INT);
            //             $stmtCreateInspectApprovals->bindParam(':period_number', $item['period_number'], PDO::PARAM_INT);
            //             $stmtCreateInspectApprovals->bindParam(':approval_level', $approvalLevel,  PDO::PARAM_INT);
            //             $stmtCreateInspectApprovals->bindParam(':approver_id', $row['approver_id'], PDO::PARAM_INT);
            //             $stmtCreateInspectApprovals->bindParam(':approval_type_id', $row['approval_type_id'], PDO::PARAM_INT);
            //             $stmtCreateInspectApprovals->bindParam(':approval_type_text', $row['approval_type_text'], PDO::PARAM_STR);
            //             $stmtCreateInspectApprovals->bindParam(':approval_status_id', $approvalStatusId, PDO::PARAM_INT);
            //             $stmtCreateInspectApprovals->execute();
            //         }
            //         $stmtCreateInspectApprovals->closeCursor();
                }
                // $_SESSION['createItems after UUUUUUUUUUUUUUUU'] = $createItems;
                // foreach ($createItems as $item) {
                //     // CREATE inspection
                //     $this->inspection->saveFromPoPeriod($item);
                // }
            }

            $this->db->commit();
            // คืนค่า PO ID ที่บันทึกสำเร็จกลับไป
            return (int)$poId;













            // return true;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            // สามารถบันทึก error หรือโยน exception ต่อไปได้
            // error_log($e->getMessage());
            return false;
        }
    }
}
/*Example
class RegistrationService {
    private $db;
    private $userRepo;
    private $logRepo;

    public function __construct(PDO $db, UserRepository $userRepo, LogRepository $logRepo) {
        $this->db = $db;
        $this->userRepo = $userRepo;
        $this->logRepo = $logRepo;
    }

    public function registerNewUser(string $name, string $email): bool {
        try {
            $this->db->beginTransaction();

            // ขั้นตอนที่ 1: สร้างผู้ใช้
            $userId = $this->userRepo->create($name, $email);

            // ขั้นตอนที่ 2: บันทึก Log
            $this->logRepo->create($userId, 'USER_REGISTERED');
            
            // ขั้นตอนอื่นๆ ที่อาจเพิ่มเข้ามาในอนาคต...

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            // สามารถบันทึก error หรือโยน exception ต่อไปได้
            // error_log($e->getMessage());
            return false;
        }
    }
}
*/
// $stmt = $conn->prepare('select ...where :');
// $stmt->bindParam(':building_id', $building_id, PDO::PARAM_STR);
// $stmt->bindParam(':building_name', $building_name, PDO::PARAM_STR);
// $stmt->bindParam(':is_active', $is_active, PDO::PARAM_BOOL);
// $stmt->execute();
// $rs = $stmt->fetchAll(PDO::FETCH_ASSOC);