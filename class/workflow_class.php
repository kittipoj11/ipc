<?php
class Workflow {
    public ?int $id = null;
    public ?string $name = null;
    public ?int $next_workflow_id = null;

    /**
     * Property สำหรับเก็บลำดับขั้นอนุมัติทั้งหมดของ Workflow นี้
     */
    public array $steps = [];

    public function __construct($name = null, $next_workflow_id = null) {
        if ($name) $this->name = $name;
        if ($next_workflow_id) $this->next_workflow_id = $next_workflow_id;
    }

    /**
     * บันทึกข้อมูล Workflow (INSERT หรือ UPDATE)
     */
    public function save(): bool {
        $pdo = Database::getInstance()->getConnection();
        if (isset($this->id)) {
            // UPDATE
            $sql = "UPDATE workflows SET name = :name, next_workflow_id = :next_workflow_id WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                ':name' => $this->name,
                ':next_workflow_id' => $this->next_workflow_id,
                ':id' => $this->id
            ]);
        } else {
            // INSERT
            $sql = "INSERT INTO workflows (name, next_workflow_id) VALUES (:name, :next_workflow_id)";
            $stmt = $pdo->prepare($sql);
            $success = $stmt->execute([
                ':name' => $this->name,
                ':next_workflow_id' => $this->next_workflow_id
            ]);
            if ($success) {
                $this->id = (int)$pdo->lastInsertId();
            }
            return $success;
        }
    }

    /**
     * ลบ Workflow และ Steps ทั้งหมดที่เกี่ยวข้องอย่างปลอดภัยด้วย Transaction
     */
    public function delete(): bool {
        if (!isset($this->id)) return false;

        $pdo = Database::getInstance()->getConnection();
        try {
            // เริ่มต้น Transaction
            $pdo->beginTransaction();

            // 1. ลบ Steps ที่ผูกกับ Workflow นี้ทั้งหมดก่อน
            $stmt_steps = $pdo->prepare("DELETE FROM workflow_steps WHERE workflow_id = ?");
            $stmt_steps->execute([$this->id]);

            // 2. ลบ Workflow หลัก
            $stmt_workflow = $pdo->prepare("DELETE FROM workflows WHERE id = ?");
            $stmt_workflow->execute([$this->id]);

            // ยืนยันการเปลี่ยนแปลงทั้งหมด
            $pdo->commit();
            return true;
        } catch (Exception $e) {
            // หากเกิดข้อผิดพลาด ให้ย้อนกลับการเปลี่ยนแปลงทั้งหมด
            $pdo->rollBack();
            // สามารถ log error $e->getMessage() ไว้เพื่อตรวจสอบได้
            return false;
        }
    }
    
    /**
     * ค้นหา Workflow และโหลด Steps ทั้งหมดที่เกี่ยวข้องมาด้วย (Eager Loading)
     */
    public static function find(int $id): ?Workflow {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM workflows WHERE id = ?");
        $stmt->execute([$id]);
        
        $workflow = $stmt->fetchObject('Workflow');
        
        // ถ้าเจอ Workflow, ให้โหลด Steps มาเก็บไว้ในตัวทันที
        if ($workflow) {
            $workflow->loadSteps();
        }
        
        return $workflow ?: null;
    }

    /**
     * โหลด Steps จากฐานข้อมูลมาใส่ใน property $this->steps
     */
    public function loadSteps(): void {
        if (!$this->id) return; // ถ้าเป็น workflow ใหม่ที่ยังไม่ถูกสร้าง จะไม่มี steps

        $pdo = Database::getInstance()->getConnection();
        // ดึงข้อมูล step พร้อม join ตาราง users เพื่อเอาชื่อผู้อนุมัติมาด้วย
        $sql = "SELECT ws.step_number, ws.approver_user_id, u.full_name as approver_name
                FROM workflow_steps ws
                JOIN users u ON ws.approver_user_id = u.id
                WHERE ws.workflow_id = ?
                ORDER BY ws.step_number ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$this->id]);
        $this->steps = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * เพิ่มลำดับขั้นอนุมัติใหม่
     */
    public function addStep(int $step_number, int $approver_user_id): bool {
        if (!isset($this->id)) {
            throw new Exception("Cannot add step to a non-existent workflow. Please save the workflow first.");
        }
        $pdo = Database::getInstance()->getConnection();
        $sql = "INSERT INTO workflow_steps (workflow_id, step_number, approver_user_id) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $success = $stmt->execute([$this->id, $step_number, $approver_user_id]);
        
        // อัปเดตข้อมูล steps ใน object ให้ตรงกับฐานข้อมูลล่าสุด
        if ($success) {
            $this->loadSteps();
        }
        return $success;
    }

    /**
     * ลบลำดับขั้นอนุมัติ
     */
    public function removeStep(int $step_number): bool {
        if (!isset($this->id)) return false;

        $pdo = Database::getInstance()->getConnection();
        $sql = "DELETE FROM workflow_steps WHERE workflow_id = ? AND step_number = ?";
        $stmt = $pdo->prepare($sql);
        $success = $stmt->execute([$this->id, $step_number]);

        if ($success) {
            $this->loadSteps();
        }
        return $success;
    }
}