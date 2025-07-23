<?php
@session_start();
// require_once 'config.php';
require_once 'connection_class.php';
require_once 'inspection_class.php';
require_once 'ipc_class.php';

class InspectionService
{
    private $db;
    private $inspection;
    private $ipc;

    public function __construct(PDO $pdoConnection, Inspection $inspection, Ipc $ipc)
    {
        $this->db = $pdoConnection;
        $this->inspection = $inspection;
        $this->ipc = $ipc;
    }

    public function approveInspection(array $approvalData,array $ipcData): bool
    {
        try {
            $this->db->beginTransaction();

            $this->inspection->updateApprovalLevel($approvalData);
            $this->ipc->save($ipcData);

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