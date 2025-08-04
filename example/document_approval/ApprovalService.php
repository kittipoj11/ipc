<?php
class ApprovalService {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function submitDocument(Document $document, User $submitter) {
        if ($document->status !== 'draft' || $document->created_by !== $submitter->id) {
            throw new Exception("Only the creator can submit a draft document.");
        }
        $workflow = Workflow::find($document->workflow_id);
        $firstStep = $workflow->getStep(1);

        if (!$firstStep) {
            throw new Exception("Workflow is not configured correctly.");
        }
        
        $document->status = 'pending_approval';
        $document->current_step = 1;
        $document->current_approver_id = $firstStep['approver_user_id'];
        $document->save();
        
        $history = new ApprovalHistory($document->id, $submitter->id, 'submitted');
        $history->save();
    }

    public function approveDocument(Document $document, User $approver, $comments) {
        if ($document->status !== 'pending_approval' || $document->current_approver_id !== $approver->id) {
            throw new Exception("You do not have permission to approve this document.");
        }

        $history = new ApprovalHistory($document->id, $approver->id, 'approved', $comments);
        $history->save();

        $workflow = Workflow::find($document->workflow_id);
        $nextStep = $workflow->getStep($document->current_step + 1);

        if ($nextStep) {
            $document->current_step++;
            $document->current_approver_id = $nextStep['approver_user_id'];
        } else { // Final approval
            $document->status = 'completed';
            $document->current_approver_id = null;

            if ($workflow->next_workflow_id) {
                $this->createNextDocument($workflow->next_workflow_id, $approver->id, $document->data);
            }
        }
        $document->save();
    }

    public function rejectDocument(Document $document, User $approver, $comments) {
        if ($document->status !== 'pending_approval' || $document->current_approver_id !== $approver->id) {
            throw new Exception("You do not have permission to reject this document.");
        }
        if (empty($comments)) {
            throw new Exception("Comments are required for rejection.");
        }

        $history = new ApprovalHistory($document->id, $approver->id, 'rejected', $comments);
        $history->save();

        $workflow = Workflow::find($document->workflow_id);
        $prevStepNumber = $document->current_step - 1;

        $document->status = 'rejected';
        if ($prevStepNumber < 1) { // Send back to creator
            $document->current_step = 0;
            $document->current_approver_id = $document->created_by;
        } else { // Send to previous approver
            $prevStep = $workflow->getStep($prevStepNumber);
            $document->current_step = $prevStepNumber;
            $document->current_approver_id = $prevStep['approver_user_id'];
        }
        $document->save();
    }

    private function createNextDocument($new_workflow_id, $creator_id, $source_data) {
        $workflow = Workflow::find($new_workflow_id);
        if (!$workflow) return;

        $firstStep = $workflow->getStep(1);
        if (!$firstStep) return;

        $sql = "INSERT INTO documents (workflow_id, data, status, current_step, current_approver_id, created_by) 
                VALUES (?, ?, 'pending_approval', 1, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$new_workflow_id, $source_data, $firstStep['approver_user_id'], $creator_id]);
        $newDocId = $this->pdo->lastInsertId();

        $history = new ApprovalHistory($newDocId, $creator_id, 'created_auto', 'Generated from workflow ' . $workflow->name);
        $history->save();
    }
}