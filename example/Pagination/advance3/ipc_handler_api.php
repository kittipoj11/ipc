<?php
header("Content-Type: application/json; charset=UTF-8");

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "file_demo";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die(json_encode(["error" => "DB connect failed"]));
}

$input = json_decode(file_get_contents("php://input"), true);
$page = isset($input["page"]) ? (int)$input["page"] : 1;
$perPage = 2;
$offset = ($page - 1) * $perPage;

$sql = "SELECT id, title, description, file_type, file_path 
        FROM files 
        LIMIT $offset, $perPage";
$result = $conn->query($sql);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

$total = $conn->query("SELECT COUNT(*) as cnt FROM files")->fetch_assoc()["cnt"];
$totalPages = ceil($total / $perPage);

echo json_encode([
    "currentPage" => $page,
    "totalPages" => $totalPages,
    "data" => $data
], JSON_UNESCAPED_UNICODE);
