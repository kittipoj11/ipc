<?php
header('Content-Type: application/json');
session_start();

// Autoload all classes
spl_autoload_register(function ($class_name) {
    include 'classes/' . $class_name . '.php';
});

// Mock user login. In a real app, this would come from a session.
// Change this ID to test different users: 1=admin, 2=approver1, etc.
$_SESSION['user_id'] = 1; 


$action = $_POST['action'] ?? '';
$user_id = $_SESSION['user_id'] ?? 0;
$doc_id = $_POST['document_id'] ?? 0;
$comments = $_POST['comments'] ?? '';

if ($user_id === 0 || $doc_id === 0) {
    echo json_encode(['status' => 'error', 'message' => 'User or Document ID is missing.']);
    exit();
}

$approvalService = new ApprovalService();

try {
    $document = Document::find($doc_id);
    if (!$document) throw new Exception("Document not found.");

    $currentUser = User::find($user_id);
    if (!$currentUser) throw new Exception("User not found.");

    switch ($action) {
        case 'submit':
            $approvalService->submitDocument($document, $currentUser);
            echo json_encode(['status' => 'success', 'message' => "Document #{$doc_id} submitted."]);
            break;
        case 'approve':
            $approvalService->approveDocument($document, $currentUser, $comments);
            echo json_encode(['status' => 'success', 'message' => "Document #{$doc_id} approved."]);
            break;
        case 'reject':
            $approvalService->rejectDocument($document, $currentUser, $comments);
            echo json_encode(['status' => 'success', 'message' => "Document #{$doc_id} rejected."]);
            break;
        default:
            throw new Exception("Invalid action.");
    }
} catch (Exception $e) {
    http_response_code(400); // Bad Request
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}