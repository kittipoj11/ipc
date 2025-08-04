
<?php
class Workflow {
    public $id;
    public $name;
    public $next_workflow_id;

    public static function find($id) {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM workflows WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetchObject('Workflow');
    }

    public function getStep($step_number) {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM workflow_steps WHERE workflow_id = ? AND step_number = ?");
        $stmt->execute([$this->id, $step_number]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}