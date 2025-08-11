<?php
class Document
{
    private $db;

    public function __construct($db_connection)
    {
        $this->db = $db_connection;
    }

    private function logHistory($docId, $userId, $action, $comments = '')
    {
        $sql = "INSERT INTO document_history (doc_id, user_id, action, comments) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$docId, $userId, $action, $comments]);
    }

    public function getHistoryForDocument($docId)
    {
        $sql = "SELECT h.*, u.username
                FROM document_history h
                JOIN users u ON h.user_id = u.user_id
                WHERE h.doc_id = ?
                ORDER BY h.action_timestamp ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$docId]);
        return $stmt->fetchAll();
    }

    public function createDocumentA($creatorId, $title, $content)
    {
        $this->db->beginTransaction();
        try {
            $sql = "INSERT INTO documents (doc_type_id, creator_id, title, content, status, current_step) VALUES ('DOC_A', ?, ?, ?, 'Draft', 0)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$creatorId, $title, $content]);
            $lastId = $this->db->lastInsertId();
            $this->logHistory($lastId, $creatorId, 'Document Created');
            $this->db->commit();
            return $lastId;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function updateDocument($docId, $userId, $title, $content)
    {
        $this->db->beginTransaction();
        try {
            $sql = "UPDATE documents SET title = ?, content = ? WHERE doc_id = ? AND creator_id = ? AND status IN ('Draft', 'Rejected')";
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([$title, $content, $docId, $userId]);
            if ($result && $stmt->rowCount() > 0) {
                $this->logHistory($docId, $userId, 'Document Updated');
            }
            $this->db->commit();
            return $result;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function submitDocument($docId, $userId)
    {
        $this->db->beginTransaction();
        try {
            $doc = $this->getDocumentById($docId);
            $firstApprover = $this->getApproverByStep($doc['doc_type_id'], 1);
            if (!$firstApprover) {
                throw new Exception("Workflow for this document type is not configured.");
            }
            $sql = "UPDATE documents SET status = 'Pending', current_step = 1, current_approver_id = ? WHERE doc_id = ? AND creator_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$firstApprover['approver_user_id'], $docId, $userId]);
            $this->logHistory($docId, $userId, 'Submitted for Approval');
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
    
    public function approveDocument($docId, $actingUserId)
    {
        $this->db->beginTransaction();
        try {
            $doc = $this->getDocumentById($docId);
            if ($doc['current_approver_id'] != $actingUserId || $doc['status'] != 'Pending') {
                throw new Exception("User cannot approve this document or document not in pending state.");
            }

            $currentStep = $doc['current_step'];
            $nextStep = $currentStep + 1;
            $nextApprover = $this->getApproverByStep($doc['doc_type_id'], $nextStep);

            if ($nextApprover) {
                $sql = "UPDATE documents SET current_step = ?, current_approver_id = ? WHERE doc_id = ?";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$nextStep, $nextApprover['approver_user_id'], $docId]);
                $this->logHistory($docId, $actingUserId, "Approved at Step {$currentStep}");
            } else {
                $sql = "UPDATE documents SET status = 'Completed', current_approver_id = NULL WHERE doc_id = ?";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$docId]);
                $this->logHistory($docId, $actingUserId, "Final Approved. Status: Completed");

                if ($doc['doc_type_id'] === 'DOC_A') {
                    $this->createDocumentBFromA($docId, $actingUserId);
                }
            }
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function rejectDocument($docId, $actingUserId, $comments)
    {
        $this->db->beginTransaction();
        try {
            $doc = $this->getDocumentById($docId);
            if ($doc['current_approver_id'] != $actingUserId || $doc['status'] != 'Pending') {
                throw new Exception("User cannot reject this document or document not in pending state.");
            }
            
            $currentStep = $doc['current_step'];
            $logMessage = "Rejected at Step {$currentStep}";

            if ($currentStep > 1) {
                $prevStep = $currentStep - 1;
                $prevApprover = $this->getApproverByStep($doc['doc_type_id'], $prevStep);
                $sql = "UPDATE documents SET current_step = ?, current_approver_id = ? WHERE doc_id = ?";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$prevStep, $prevApprover['approver_user_id'], $docId]);
            } else {
                $sql = "UPDATE documents SET status = 'Rejected', current_step = 0, current_approver_id = NULL WHERE doc_id = ?";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$docId]);
            }
            $this->logHistory($docId, $actingUserId, $logMessage, $comments);
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
    
    private function createDocumentBFromA($sourceDocId, $actorUserId)
    {
        $firstApproverB = $this->getApproverByStep('DOC_B', 1);
        if (!$firstApproverB) {
            throw new Exception("Workflow for DOC_B is not defined.");
        }

        $sql = "INSERT INTO documents (doc_type_id, title, content, status, creator_id, source_doc_id, current_step, current_approver_id) 
                SELECT 'DOC_B', CONCAT('Doc B from A#', d.doc_id), d.content, 'Pending', d.creator_id, d.doc_id, 1, ?
                FROM documents d WHERE d.doc_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$firstApproverB['approver_user_id'], $sourceDocId]);
        $newDocB_Id = $this->db->lastInsertId();

        $this->logHistory($newDocB_Id, $actorUserId, 'Document B auto-created from A#'.$sourceDocId);
        return $newDocB_Id;
    }

    public function getDocumentById($docId)
    {
        $sql = "SELECT d.*, u_creator.username AS creator_name, u_approver.username AS approver_name
                FROM documents d
                LEFT JOIN users u_creator ON d.creator_id = u_creator.user_id
                LEFT JOIN users u_approver ON d.current_approver_id = u_approver.user_id
                WHERE d.doc_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$docId]);
        return $stmt->fetch();
    }

    private function getApproverByStep($docTypeId, $stepNumber)
    {
        $sql = "SELECT approver_user_id FROM workflow_steps WHERE doc_type_id = ? AND step_number = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$docTypeId, $stepNumber]);
        return $stmt->fetch();
    }
}