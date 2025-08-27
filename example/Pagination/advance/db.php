<?php
header("Content-Type: application/json; charset=UTF-8");

// การเชื่อมต่อฐานข้อมูล
$conn = new mysqli("localhost", "root", "", "test_db");
if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed"]));
}

// รับ page จาก request
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 4; // แสดง 4 รายการต่อหน้า
$offset = ($page - 1) * $limit;

// ดึงข้อมูล (เพิ่ม image)
$result = $conn->query("SELECT id, title, description, image FROM items LIMIT $limit OFFSET $offset");

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// หาจำนวนหน้าทั้งหมด
$countResult = $conn->query("SELECT COUNT(*) AS total FROM items");
$total = $countResult->fetch_assoc()["total"];
$totalPages = ceil($total / $limit);

echo json_encode([
    "data" => $data,
    "totalPages" => $totalPages,
    "currentPage" => $page
]);
