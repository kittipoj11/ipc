<?php
// ไฟล์นี้จะถูกเรียกด้วย AJAX เท่านั้น

// ข้อมูลสมมติ (ต้องเป็นข้อมูลเดียวกันกับใน index.php)
$data = [
    "รายการที่ 1",
    "รายการที่ 2",
    "รายการที่ 3",
    "รายการที่ 4",
    "รายการที่ 5",
    "รายการที่ 6",
    "รายการที่ 7",
    "รายการที่ 8",
    "รายการที่ 9",
    "รายการที่ 10",
    "รายการที่ 11",
    "รายการที่ 12",
    "รายการที่ 13",
    "รายการที่ 14",
    "รายการที่ 15"
];
$items_per_page = 5;
$total_items = count($data);
$total_pages = ceil($total_items / $items_per_page);

$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($current_page < 1) {
    $current_page = 1;
}
if ($current_page > $total_pages) {
    $current_page = $total_pages;
}

$start_index = ($current_page - 1) * $items_per_page;
$end_index = $start_index + $items_per_page;

// แสดงผลเฉพาะเนื้อหา
for ($i = $start_index; $i < $end_index; $i++) {
    if (isset($data[$i])) {
        echo "<div class='content'>" . $data[$i] . "</div>";
    }
}
