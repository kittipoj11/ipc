<?php
// require_once 'config.php';
require_once 'connection_class.php';

class Workflows {
    private $db; 
    public function __construct(PDO $pdoConnection)
    {
        $this->db = $pdoConnection;
    }

    public function create($data)
    {
        $workflow_name = $data['workflow_name'];

        $sql = "insert into workflows(workflow_name) 
                values(:workflow_name)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':workflow_name', $workflow_name, PDO::PARAM_STR);

        try {
            if ($stmt->execute()) {
                // echo  'Data has been created successfully.';
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                echo  'This item could not be added.Because the data has duplicate values!!!';
            } else {
                echo  'Something is wrong.Can not add data.';
            }
        }
    }
    public function update(array $data)
    {
        $workflow_id = $data['workflow_id'];
        $workflow_name = $data['workflow_name'];
        $sql = "update workflows 
                set workflow_name = :workflow_name
                where workflow_id = :workflow_id";
        // , update_datetime = CURRENT_TIMESTAMP()
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':workflow_id', $workflow_id, PDO::PARAM_INT);
        $stmt->bindParam(':workflow_name', $workflow_name, PDO::PARAM_STR);

        try {
            if ($stmt->execute()) {
                // echo 'Data has been update successfully.';
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                echo 'This item could not be added.Because the data has duplicate values!!!';
            } else {
                echo 'Something is wrong.Can not add data.';
            }
        }
    }
    public function delete(array $data)
    {
        $workflow_id = $data['workflow_id'];
        // $is_active = isset($data['is_active']) ? 1 : 0;
        $sql = "update workflows 
                set is_deleted = 1
                where workflow_id = :workflow_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':workflow_id', $workflow_id, PDO::PARAM_INT);

        try {
            if ($stmt->execute()) {
                echo 'Data has been delete successfully.';
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                echo 'This item could not be added.Because the data has duplicate values!!!';
            } else {
                echo 'Something is wrong.Can not add data.';
            }
        }
    }

    public function getAll(): array
    {
        $sql = <<<EOD
                select workflow_id, workflow_name, is_deleted 
                from workflows 
                where is_deleted = false
                EOD;
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        $rs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $rs;
    }

    public function getById($workflowId):?array
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

    

    
}


// $stmt = $conn->prepare('select ...where :');
// $stmt->bindParam(':building_id', $building_id, PDO::PARAM_STR);
// $stmt->bindParam(':building_name', $building_name, PDO::PARAM_STR);
// $stmt->bindParam(':is_active', $is_active, PDO::PARAM_BOOL);
// $stmt->execute();
// $rs = $stmt->fetchAll(PDO::FETCH_ASSOC);