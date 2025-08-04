<?php
class ApprovalHistory {
    public $document_id;
    public $user_id;
    public $action;
    public $comments;

    public function __construct($doc_id, $user_id, $action, $comments = '') {
        $this->document_id = $doc_id;
        $this->user_id = $user_id;
        $this->action = $action;
        $this->comments = $comments;
    }

    public function save() {
        $pdo = Database::getInstance()->getConnection();
        $sql = "INSERT INTO approval_history (document_id, user_id, action, comments) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$this->document_id, $this->user_id, $this->action, $this->comments]);
    }
}