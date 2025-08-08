<?php
// require_once 'config.php';
require_once 'connection_class.php';

class Workflows
{
    private $db;
    public function __construct(PDO $pdoConnection)
    {
        $this->db = $pdoConnection;
    }

    public function save(array $data): int
    {
        $workflowId = $data['workflow_id'] ?? 0;
        if (empty($workflowId)) {
            $sql = "INSERT INTO `workflow_name`
                VALUES(:workflow_name)";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':workflow_name', $data['workflow_name'], PDO::PARAM_STR);
            $stmt->execute();
            $stmt->closeCursor();
            $workflowId = $this->db->lastInsertId();
        } else {
            $sql = "UPDATE workflows 
                SET workflow_name = :workflow_name
                WHERE workflow_id = :workflow_id";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':workflow_id', $workflowId, PDO::PARAM_INT);
            $stmt->bindParam(':workflow_name', $data['workflow_name'], PDO::PARAM_STR);
            $stmt->execute();
            $stmt->closeCursor();
        }

        return (int)$workflowId;
    }

    public function delete(int $workflowId): int
    {
        // $is_active = isset($data['is_active']) ? 1 : 0;
        $sql = "UPDATE workflows 
                SET is_deleted = 1
                WHERE workflow_id = :workflow_id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':workflow_id', $workflowId, PDO::PARAM_INT);
        
        return (int)$workflowId;
    }

    public function getAll(): array
    {
        $sql = "SELECT workflow_id, workflow_name, is_deleted 
                FROM workflows 
                WHERE is_deleted = false";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        $rs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $rs;
    }

    public function getById($workflowId): ?array
    {
        $sql = "SELECT workflow_id, workflow_name, is_deleted 
                FROM workflows
                WHERE is_deleted = false
                    AND workflow_id = :workflow_id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':workflow_id', $workflowId, PDO::PARAM_INT);
        $stmt->execute();

        $rs = $stmt->fetch();
        if (!$rs) {
            return null; // ไม่พบข้อมูล
        }

        // ดึงข้อมูลจากตารางรอง
        $rs['steps'] = $this->getAllStepsById($workflowId);
        return $rs;
    }

    public function getAllStepsById($workflowId): array
    {
        $sql = "SELECT `workflow_step_id`, `workflow_id`, `approval_level`, `approver_id`, `approval_type_id`, `approval_type_text` 
                FROM `workflow_steps` 
                WHERE `workflow_id` = :workflow_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':workflow_id', $workflowId, PDO::PARAM_INT);
        $stmt->execute();

        $rs = $stmt->fetchAll();
        return $rs;
    }

    // ฟังก์ชั่นนี้ต้องใช้หรือไม่
    public function getStep($workflowId, $approvalLevel)
    {
        $sql = "SELECT `workflow_step_id`, `workflow_id`, `approval_level`, `approver_id`, `approval_type_id`, `approval_type_text` 
                FROM workflow_steps 
                WHERE workflow_id = :workflow_id 
                    AND approval_level = :approval_level";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':workflow_id', $workflowId, PDO::PARAM_INT);
        $stmt->bindParam(':approval_level', $approvalLevel, PDO::PARAM_INT);
        $stmt->execute();
        $rs =  $stmt->fetch(PDO::FETCH_ASSOC);
        return $rs;
    }
}


// $stmt = $conn->prepare('select ...where :');
// $stmt->bindParam(':building_id', $building_id, PDO::PARAM_STR);
// $stmt->bindParam(':building_name', $building_name, PDO::PARAM_STR);
// $stmt->bindParam(':is_active', $is_active, PDO::PARAM_BOOL);
// $stmt->execute();
// $rs = $stmt->fetchAll(PDO::FETCH_ASSOC);