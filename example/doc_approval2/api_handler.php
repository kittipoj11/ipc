<?php
header('Content-Type: application/json');
require_once 'connection_class.php';
require_once 'document_class.php';

// ในระบบจริงต้องใช้ session
// @session_start();
// $userId = $_SESSION['user_id'] ?? 0; 
$userId = (int)($_POST['user_id'] ?? 0);

$response = ['status' => 'error', 'message' => 'Invalid action or user not logged in.'];

if (isset($_POST['action']) && $userId > 0) {
    $conn = new Connection();
    $docManager = new Document($conn->getDbConnection());

    $action = $_POST['action'];
    $docId = (int)($_POST['doc_id'] ?? 0);

    try {
        switch ($action) {
            case 'create_doc_a':
                $newDocId = $docManager->createDocumentA($userId, 'New Document A', 'Initial content.');
                $response = ['status' => 'success', 'message' => 'Document A created successfully.', 'doc_id' => $newDocId];
                break;
            
            case 'update_doc':
                if ($docId > 0) {
                    $title = $_POST['title'] ?? '';
                    $content = $_POST['content'] ?? '';
                    if ($docManager->updateDocument($docId, $userId, $title, $content)) {
                        $response = ['status' => 'success', 'message' => 'Document updated.'];
                    } else {
                        $response['message'] = 'Failed to update. Check permissions or status.';
                    }
                }
                break;
            
            case 'submit_doc':
                if ($docId > 0 && $docManager->submitDocument($docId, $userId)) {
                    $response = ['status' => 'success', 'message' => 'Document submitted for approval.'];
                } else {
                    $response['message'] = 'Failed to submit document.';
                }
                break;

            case 'approve_doc':
                if ($docId > 0 && $docManager->approveDocument($docId, $userId)) {
                    $response = ['status' => 'success', 'message' => 'Document approved.'];
                } else {
                    $response['message'] = 'Failed to approve document.';
                }
                break;

            case 'reject_doc':
                $comments = trim($_POST['comments'] ?? '');
                if (empty($comments)) {
                     $response['message'] = 'Rejection reason is required.';
                     break;
                }
                if ($docId > 0 && $docManager->rejectDocument($docId, $userId, $comments)) {
                    $response = ['status' => 'success', 'message' => 'Document rejected.'];
                } else {
                    $response['message'] = 'Failed to reject document.';
                }
                break;
            
            default:
                 $response['message'] = 'Unknown action specified.';
                 break;
        }
    } catch (Exception $e) {
        $response['message'] = 'An error occurred: ' . $e->getMessage();
    }
}

echo json_encode($response);