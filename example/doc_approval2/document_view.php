<?php
require_once 'connection_class.php';
require_once 'document_class.php';

// จำลอง User Login และการเลือกเอกสาร
$current_user_id = (int)($_GET['user_id'] ?? 1);
$doc_id_to_view = (int)($_GET['doc_id'] ?? 0);

$doc = null;
$history = [];
if ($doc_id_to_view > 0) {
  try {
    $conn = new Connection();
    $docManager = new Document($conn->getDbConnection());
    $doc = $docManager->getDocumentById($doc_id_to_view);
    if ($doc) {
      $history = $docManager->getHistoryForDocument($doc_id_to_view);
    }
  } catch (Exception $e) {
    die("Could not connect to the database: " . $e->getMessage());
  }
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
  <meta charset="UTF-8">
  <title>Document Approval System</title>
  <style>
    body {
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
      padding: 20px;
      background-color: #f4f7f6;
      color: #333;
    }

    .container {
      max-width: 900px;
      margin: 0 auto;
      background-color: #fff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .doc-container,
    .history-container {
      margin-bottom: 20px;
    }

    .status {
      font-weight: bold;
      padding: 5px 10px;
      border-radius: 15px;
      color: white;
      font-size: 0.9em;
    }

    .status-Draft {
      background-color: #6c757d;
    }

    .status-Pending {
      background-color: #ffc107;
      color: #000;
    }

    .status-Rejected {
      background-color: #dc3545;
    }

    .status-Completed {
      background-color: #28a745;
    }

    .actions button {
      margin-top: 15px;
      padding: 10px 15px;
      cursor: pointer;
      border-radius: 5px;
      border: none;
      font-size: 1em;
    }

    #btnApprove {
      background-color: #28a745;
      color: white;
    }

    #btnReject {
      background-color: #dc3545;
      color: white;
    }

    #btnSave,
    #btnSubmit {
      background-color: #007bff;
      color: white;
    }

    #notification {
      padding: 12px;
      margin-bottom: 20px;
      border-radius: 4px;
      display: none;
    }

    #notification.success {
      background-color: #d4edda;
      border: 1px solid #c3e6cb;
      color: #155724;
    }

    #notification.error {
      background-color: #f8d7da;
      border: 1px solid #f5c6cb;
      color: #721c24;
    }

    .history-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
    }

    .history-table th,
    .history-table td {
      border: 1px solid #dee2e6;
      padding: 8px;
      text-align: left;
    }

    .history-table th {
      background-color: #e9ecef;
    }

    textarea,
    input[type=text] {
      width: 100%;
      padding: 8px;
      border: 1px solid #ccc;
      border-radius: 4px;
      box-sizing: border-box;
    }

    textarea#reject_comments {
      margin-top: 10px;
    }
  </style>
</head>

<body>
  <div class="container">
    <h1>Document Viewer</h1>
    <p>
      Viewing as User ID: <strong><?php echo $current_user_id; ?></strong>.
      <a href="#" id="btnCreateDocA">Create New Document A</a>
    </p>

    <div id="notification"></div>

    <?php if ($doc): ?>
      <div class="doc-container" data-doc-id="<?php echo $doc['doc_id']; ?>" data-user-id="<?php echo $current_user_id; ?>">
        <h2 id="doc_header"><?php echo htmlspecialchars($doc['title']); ?> (ID: <?php echo $doc['doc_id']; ?>)</h2>
        <p><strong>Status:</strong> <span class="status status-<?php echo htmlspecialchars($doc['status']); ?>"><?php echo htmlspecialchars($doc['status']); ?></span></p>
        <p><strong>Creator:</strong> <?php echo htmlspecialchars($doc['creator_name']); ?></p>
        <?php if ($doc['status'] == 'Pending'): ?>
          <p><strong>Waiting for:</strong> <?php echo htmlspecialchars($doc['approver_name'] ?? 'N/A'); ?> (Step: <?php echo $doc['current_step']; ?>)</p>
        <?php endif; ?>
        <hr>
        <div>
          <label for="doc_title">Title:</label>
          <input type="text" id="doc_title" value="<?php echo htmlspecialchars($doc['title']); ?>">
        </div>
        <div style="margin-top: 10px;">
          <label for="doc_content">Content:</label>
          <textarea id="doc_content" rows="8"><?php echo htmlspecialchars($doc['content']); ?></textarea>
        </div>
        <div class="actions">
          <?php
          $isCreator = ($current_user_id == $doc['creator_id']);
          $isApprover = ($current_user_id == $doc['current_approver_id']);
          if ($isCreator && in_array($doc['status'], ['Draft', 'Rejected'])) {
            echo '<button id="btnSave">Save Changes</button> <button id="btnSubmit">Submit</button>';
          }
          if ($isApprover && $doc['status'] == 'Pending') {
            echo '<button id="btnApprove">Approve</button> <button id="btnReject">Reject</button>';
            echo '<textarea id="reject_comments" placeholder="กรุณาระบุเหตุผลในการปฏิเสธ (จำเป็น)"></textarea>';
          }
          if ($doc['status'] == 'Completed') {
            echo '<p><i>This document is completed and cannot be edited.</i></p>';
          }
          ?>
        </div>
      </div>
      <div class="history-container">
        <h3>ประวัติการดำเนินการ</h3>
        <table class="history-table">
          <thead>
            <tr>
              <th>วัน-เวลา</th>
              <th>ผู้ดำเนินการ</th>
              <th>การกระทำ</th>
              <th>หมายเหตุ</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($history)): ?>
              <tr>
                <td colspan="4">ยังไม่มีประวัติการดำเนินการ</td>
              </tr>
            <?php else: ?>
              <?php foreach ($history as $log): ?>
                <tr>
                  <td><?php echo date('d-m-Y H:i', strtotime($log['action_timestamp'])); ?></td>
                  <td><?php echo htmlspecialchars($log['username']); ?></td>
                  <td><?php echo htmlspecialchars($log['action']); ?></td>
                  <td><?php echo nl2br(htmlspecialchars($log['comments'])); ?></td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    <?php elseif ($doc_id_to_view > 0): ?>
      <p>Document with ID <?php echo $doc_id_to_view; ?> not found.</p>
    <?php else: ?>
      <p>No document selected. Please create one or select via URL (e.g., `?doc_id=1&user_id=1`).</p>
    <?php endif; ?>
  </div>
  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
  <script src="app.js"></script>
</body>

</html>