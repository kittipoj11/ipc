<?php
@session_start();
// require_once 'config.php';
require_once 'connection_class.php';
require_once 'inspection_class.php';

class InspectionApprovalHistory
{
    private $db;
    private $inspection;

    public function __construct(PDO $pdoConnection, Inspection $inspection)
    {
        $this->db = $pdoConnection;
        $this->inspection = $inspection;
    }

    public function save(array $approvalData): bool
    {
        $sql= "INSERT INTO `inspection_approval_history`(`inspection_id`, `user_id`, `action`) 
        VALUES (:inspection_id, :user_id, :action)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam('inspection_id',$approvalData['inspection_id']);
        $stmt->bindParam('user_id',$_SESSION['user_id']);
        $stmt->bindParam('action',$approvalData['action']);
        $stmt->execute();
        $stmt->closeCursor();
        return true;
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