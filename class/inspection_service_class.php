<?php
@session_start();
// require_once 'config.php';
require_once 'connection_class.php';
require_once 'po_class.php';
require_once 'inspection_class.php';
require_once 'ipc_class.php';
require_once 'workflows_class.php';

class InspectionService
{
    private $db;
    private $po;
    private $inspection;
    private $ipc;
    private $workflow;

    public function __construct(PDO $pdoConnection,Po $po, Inspection $inspection, Ipc $ipc, Workflows $workflow)
    {
        $this->db = $pdoConnection;
        $this->po = $po;
        $this->inspection = $inspection;
        $this->ipc = $ipc;
        $this->workflow = $workflow;
    }

    public function saveInspection(array $periodData, array $detailsData): int
    {
        try {
            $this->db->beginTransaction();
            // 1.ดึง user_id จาก SESSION
            $userId = $_SESSION['user_id'];
            // 2.หา current_approval_level, workflow_id จาก inspection
            $rsInspection = $this->inspection->getByInspectionId($periodData['inspection_id']);
            // $_SESSION['getByInspectionId AAAAAAAAAAAAA'] = $rsInspection;

            // 3.หา workflow_step
            $inspectionId = $periodData['inspection_id'];
            $currentLevel = $rsInspection['period']['current_approval_level'];
            $nextLevel = $currentLevel + 1;
            $workflowId = $rsInspection['period']['workflow_id'];

            $rsWorkflow = $this->workflow->getStep($workflowId, $nextLevel);
            $nextApproverId = $rsWorkflow['approver_id'];
            // $_SESSION['getStep BBBBBBBBBBBBBB'] = $rsWorkflow;
            // 4.save inspection
            $this->inspection->save($periodData, $detailsData);
            // $_SESSION['save CCCCCCCCCCCCCCCCCCC'] = "Save success";

            // 5.update inspection status
            $this->inspection->updateStatus($inspectionId, 'pending-submit', $nextApproverId, $nextLevel);
            // $_SESSION['updateStatus DDDDDDDDDDDDDDDDDd'] = "updateStatus success";
            // 6.log history 
            $this->inspection->logHistory($inspectionId, $userId, 'Inspection Created');
            // $_SESSION['logHistory EEEEEEEEEEEEEE'] = "logHistory success";
            $this->db->commit();
            // $this->db->rollBack();
            return $inspectionId;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            // สามารถบันทึก error หรือโยน exception ต่อไปได้
            // error_log($e->getMessage());
            return 0;
        }
    }

    public function updateInspection(array $periodData, array $detailsData): int
    {
        try {
            $this->db->beginTransaction();
            $inspectionId = $periodData['inspection_id'];

            // 1.ดึง user_id จาก SESSION
            $userId = $_SESSION['user_id'];

            // 2.หา current_approval_level, workflow_id จาก inspection
            $rsInspection = $this->inspection->getByInspectionId($inspectionId);
            // $_SESSION['getByInspectionId AAAAAAAAAAAAA'] = $rsInspection;

            // 3.save inspection
            $this->inspection->save($periodData, $detailsData);
            // $_SESSION['save CCCCCCCCCCCCCCCCCCC'] = "Update success";

            // 4.log history 
            $this->inspection->logHistory($inspectionId, $userId, 'Inspection Updated');
            // $_SESSION['logHistory EEEEEEEEEEEEEE'] = "logHistory success";
            $this->db->commit();
            return $inspectionId;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            // สามารถบันทึก error หรือโยน exception ต่อไปได้
            // error_log($e->getMessage());
            return 0;
        }
    }

    public function approveInspection($inspectionId): int
    {
        try {
            $this->db->beginTransaction();
            // 1.ดึง user_id จาก SESSION
            $userId = $_SESSION['user_id'];

            // 2.หา current_approval_level, workflow_id จาก inspection
            $rsInspection = $this->inspection->getByInspectionId($inspectionId);
            // $_SESSION['getByInspectionId AAAAAAAAAAAAA']='success';
            // return $inspectionId;
            
            // 3.หา workflow_step
            $currentLevel = $rsInspection['period']['current_approval_level'];
            $nextLevel = $currentLevel + 1;
            $workflowId = $rsInspection['period']['workflow_id'];
            
            $rsWorkflow = $this->workflow->getStep($workflowId, $nextLevel);
            // $_SESSION['getStep BBBBBBBBBBBBBBB']='success';
            
            // 4.update po status ???
            $poId = $rsInspection['period']['po_id'];
            $this->po->updateStatus($poId, 2);
            
            // 4.update inspection status
            // ตรวจสอบ $rsWorkflow ว่ามีข้อมูลหรือไม่
            if ($rsWorkflow) {
                $nextApproverId = $rsWorkflow['approver_id'];
                $this->inspection->updateStatus($inspectionId, 'pending-approve', $nextApproverId, $nextLevel);
                // $_SESSION['updateStatus CCCCCCCCCCCCCCCCC']='success';
                
                // 5.log history 
                $this->inspection->logHistory($inspectionId, $userId, "Approved at Step {$currentLevel}");
                // $_SESSION['logHistory DDDDDDDDDDDDDDDDDD']='success';
                
            } else {
                //inspection_status สถานะปัจจุบัน (Completed)
                //current_approver_id บอกว่าไม่มีใครต้องทำอะไรต่อ (Null) 
                //current_level บอกประวัติว่าไปถึงขั้นตอนไหน (ขั้นตอนสุดท้าย)
                $this->inspection->updateStatus($inspectionId, 'completed', NULL, $currentLevel);
                // $_SESSION['updateStatus EEEEEEEEEEEEEEEEEEe']='success';
                
                // 5.log history 
                $this->inspection->logHistory($inspectionId, $userId, "Final Approved at Step {$currentLevel}. Status: Completed");
                // $_SESSION['logHistory FFFFFFFFFFFFFFFF']='success';

                // ถ้าเป็น inspection(มี workflow_id = 1) จะทำการสร้างเอกสาร ipc(workflow_id=2)
                if ($rsInspection['period']['workflow_id'] === 1) {
                    $less_retension_exclude_vat = 0;
                    $sum_of_less_retension_exclude_vat = 0;
                    $ipcData = [
                        "po_id" => $rsInspection['period']["po_id"],
                        "period_id" => $rsInspection['period']["period_id"],
                        "inspection_id" => $rsInspection['period']["inspection_id"],
                        "ipc_id" => 0,
                        "period_number" => $rsInspection['period']["period_number"],
                        "project_name" => $rsInspection['header']["project_name"],
                        "contractor" => $rsInspection['header']["supplier_name"],
                        "contract_value" => $rsInspection['header']["contract_value"],
                        "total_value_of_interim_payment" => $rsInspection['period']["interim_payment_less_previous"] + $rsInspection['period']["interim_payment"], //(3)total_value_of_interim_payment
                        "less_previous_interim_payment" => $rsInspection['period']["interim_payment_less_previous"], //(1)less_previous_interim_payment
                        "net_value_of_current_claim" => $rsInspection['period']["interim_payment"], //(2)net_value_of_current_claim
                        "less_retension_exclude_vat" => $less_retension_exclude_vat, //(5)less_retension_exclude_vat
                        "net_amount_due_for_payment" => $rsInspection['period']["interim_payment"] - $less_retension_exclude_vat, //(6)net_amount_due_for_payment
                        "total_value_of_retention" => $sum_of_less_retension_exclude_vat, //(7)total_value_of_retention
                        "total_value_of_certification_made" => $rsInspection['period']["interim_payment_accumulated"] - $sum_of_less_retension_exclude_vat, //(8)total_value_of_certification_made
                        "resulting_balance_of_contract_sum_outstanding" => $rsInspection['period']["interim_payment_remain"] - $sum_of_less_retension_exclude_vat, //(9)resulting_balance_of_contract_sum_outstanding
                        "remark" => '',
                        "workflow_id" => 2,
                        "interim_payment_less_previous" => $rsInspection['period']["interim_payment_less_previous"], //(1)ยอดเบิกเงินงวดสะสมไม่รวมปัจจุบัน
                        "interim_payment" => $rsInspection['period']["interim_payment"], //(2)ยอดเบิกเงินงวดปัจจุบัน
                        "interim_payment_accumulated" => $rsInspection['period']["interim_payment_accumulated"], //(3)ยอดเบิกเงินงวดสะสมถึงปัจจุบัน
                        "interim_payment_remain" => $rsInspection['period']["interim_payment_remain"], //(4)ยอดเงินงวดคงเหลือ
                    ];

                    $ipcId = $this->ipc->create($ipcData);
                    // $_SESSION['create GGGGGGGGGGGGGGG']='success';
                    
                    // 2.หา current_approval_level, workflow_id จาก ipc
                    // $_SESSION['ipcId HHHHHHHHHHHHHHHH']=$ipcId;
                    $rsIpc = $this->ipc->getIpcByIpcId($ipcId);
                    // $_SESSION['getByIpcId HHHHHHHHHHHHHHHH']='success';
                    
                    // 3.หา workflow_step
                    $nextLevel = 1;
                    $workflowId = $rsIpc['ipc']['workflow_id'];
                    
                    $rsWorkflow = $this->workflow->getStep($workflowId, $nextLevel);
                    $nextApproverId = $rsWorkflow['approver_id'];
                    // $_SESSION['getStep IIIIIIIIIIIIIIIII']='success';
                    
                    // 5.update ipc status
                    $this->ipc->updateStatus($ipcId, 'pending-submit', $nextApproverId, $nextLevel);
                    // $_SESSION['updateStatus JJJJJJJJJJJJJJJJJJJJ']='success';
                    
                    // 6.log history 
                    $this->ipc->logHistory($ipcId, $userId, 'IPC Created');
                    // $_SESSION['logHistory KKKKKKKKKKKKKKKK']='success';
                }
            }
            $this->db->commit();
            return $inspectionId;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            // สามารถบันทึก error หรือโยน exception ต่อไปได้
            // $_SESSION['rollback GGGGGGGGGGGGG'] = $e->getMessage();
            error_log($e->getMessage());
            return 0;
        }
    }

    public function rejectInspection($inspectionId, $comments): int
    {
        try {
            $this->db->beginTransaction();
            // 1.ดึง user_id จาก SESSION
            $userId = $_SESSION['user_id'];
            // 2.หา current_approval_level, workflow_id จาก inspection
            $rsInspection = $this->inspection->getByInspectionId($inspectionId);

            // 3.หา workflow_step
            $currentLevel = $rsInspection['period']['current_approval_level'];
            $nextLevel = $currentLevel - 1;
            $workflowId = $rsInspection['period']['workflow_id'];

            $rsWorkflow = $this->workflow->getStep($workflowId, $nextLevel);

            // 4.update inspection status
            $nextApproverId = $rsWorkflow['approver_id'];
            $this->inspection->updateStatus($inspectionId, 'pending-submit', $nextApproverId, $nextLevel);

            // 5.log history 
            $this->inspection->logHistory($inspectionId, $userId, "Inspection rejected at Step {$currentLevel}", $comments);

            $this->db->commit();
            return $inspectionId;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            // สามารถบันทึก error หรือโยน exception ต่อไปได้
            // error_log($e->getMessage());
            return 0;
        }
    }

    public function approveIpc($ipcId): int
    {
        try {
            $this->db->beginTransaction();
            // 1.ดึง user_id จาก SESSION
            $userId = $_SESSION['user_id'];
            // 2.หา current_approval_level, workflow_id จาก inspection
            $rsIpc = $this->ipc->getIpcByIpcId($ipcId);
            // $_SESSION['rsIpc XXXXXXXXXXXXX']=$rsIpc;
            // return $ipcId;

            // 3.หา workflow_step
            $currentLevel = $rsIpc['ipc']['current_approval_level'];
            $nextLevel = $currentLevel + 1;
            $workflowId = $rsIpc['ipc']['workflow_id'];

            $rsWorkflow = $this->workflow->getStep($workflowId, $nextLevel);

            // 4.update ipc status
            // ตรวจสอบ $rsWorkflow ว่ามีข้อมูลหรือไม่
            if ($rsWorkflow) {
                $nextApproverId = $rsWorkflow['approver_id'];
                $this->ipc->updateStatus($ipcId, 'pending-approve', $nextApproverId, $nextLevel);

                // 5.log history 
                $this->ipc->logHistory($ipcId, $userId, "Approved at Step {$currentLevel}");
            } else {
                //inspection_status สถานะปัจจุบัน (Completed)
                //current_approver_id บอกว่าไม่มีใครต้องทำอะไรต่อ (Null) 
                //current_level บอกประวัติว่าไปถึงขั้นตอนไหน (ขั้นตอนสุดท้าย)
                $this->ipc->updateStatus($ipcId, 'completed', NULL, $currentLevel);

                // 5.log history 
                $this->ipc->logHistory($ipcId, $userId, "Final Approved at Step {$currentLevel}. Status: Completed");
            }
            $this->db->commit();
            return $ipcId;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            // สามารถบันทึก error หรือโยน exception ต่อไปได้
            // $_SESSION['rollback GGGGGGGGGGGGG'] = $e->getMessage();
            error_log($e->getMessage());
            return 0;
        }
    }

    public function rejectIpc($ipcId, $comments): int
    {
        try {
            $this->db->beginTransaction();
            // 1.ดึง user_id จาก SESSION
            $userId = $_SESSION['user_id'];
            // 2.หา current_approval_level, workflow_id จาก ipc
            $rsIpc = $this->ipc->getIpcByIpcId($ipcId);

            // 3.หา workflow_step
            $currentLevel = $rsIpc['ipc']['current_approval_level'];
            $nextLevel = $currentLevel - 1;
            $workflowId = $rsIpc['ipc']['workflow_id'];

            $rsWorkflow = $this->workflow->getStep($workflowId, $nextLevel);

            // 4.update ipc status
            $nextApproverId = $rsWorkflow['approver_id'];
            $this->ipc->updateStatus($ipcId, 'pending-submit', $nextApproverId, $nextLevel);

            // 5.log history 
            $this->ipc->logHistory($ipcId, $userId, "IPC rejected at Step {$currentLevel}", $comments);

            $this->db->commit();
            return $ipcId;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            // สามารถบันทึก error หรือโยน exception ต่อไปได้
            // error_log($e->getMessage());
            return 0;
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