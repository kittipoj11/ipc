<?php
class Document {
    public $id;
    public $workflow_id;
    public $data;
    public $status;
    public $current_step;
    public $current_approver_id;
    public $created_by;

    public static function find($id) {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM documents WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetchObject('Document');
    }

    public function save() {
        $pdo = Database::getInstance()->getConnection();
        $sql = "UPDATE documents SET 
                    status = :status, 
                    current_step = :current_step, 
                    current_approver_id = :current_approver_id
                WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':status' => $this->status,
            ':current_step' => $this->current_step,
            ':current_approver_id' => $this->current_approver_id,
            ':id' => $this->id
        ]);
    }
}