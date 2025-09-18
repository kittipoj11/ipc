<?php
@session_start();
header('Content-Type: application/json');

$uploadDir = "uploads/signatures/";
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
    // $filename   = basename($_FILES['file']['name']);//เปลี่ยนชื่อไฟล์ตรงนี้
    $filename   = $_POST['file_new_name_text'];
    $tmp_name   = $_FILES['file']['tmp_name'];
    $uploadFile = $uploadDir . $filename;
$_SESSION['tmp_name']= $tmp_name;
$_SESSION['uploadFile']= $uploadFile;
    if (move_uploaded_file($tmp_name, $uploadFile)) {
        echo json_encode([
            "status" => "ok",
            "path"   => $uploadFile,
            "filename" => $filename
        ]);
    } else {
        echo json_encode(["status" => "fail", "message" => "move_uploaded_file failed"]);
    }
} else {
    echo json_encode(["status" => "fail", "message" => "no file uploaded"]);
}
