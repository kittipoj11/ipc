<?php
// ** ส่วนของการดึงข้อมูลตาม ID จากฐานข้อมูลมาแสดงในฟอร์ม **
// (ตัวอย่างนี้สมมติว่าคุณมีฟังก์ชัน getDataById($id) ที่ดึงข้อมูลจาก DB ตาม ID)
function getDataById($id) {
    // ** จำลองข้อมูล (แทนการดึงจากฐานข้อมูลจริง) **
    $dummyData = [
        1 => ['id' => 1, 'field1' => 'ข้อมูล 1', 'field2' => 'รายละเอียด 1'],
        2 => ['id' => 2, 'field1' => 'ข้อมูล 2', 'field2' => 'รายละเอียด 2'],
        3 => ['id' => 3, 'field1' => 'ข้อมูล 3', 'field2' => 'รายละเอียด 3'],
    ];
    return isset($dummyData[$id]) ? $dummyData[$id] : null;
}

$dataId = isset($_GET['id']) ? intval($_GET['id']) : 0; // รับ ID จาก GET parameter
$dataToEdit = getDataById($dataId);

if (!$dataToEdit) {
    echo "<p>ไม่พบข้อมูลสำหรับ ID ที่ระบุ</p>"; // แสดงข้อความ error (ไม่ต้อง redirect)
    exit;
}
?>

<h2>แก้ไขข้อมูล ID: <?php echo htmlspecialchars($dataToEdit['id']); ?></h2>
<form id="editForm" action="update_data.php" method="post">
    <input type="hidden" name="id" value="<?php echo htmlspecialchars($dataToEdit['id']); ?>"> <!- Hidden input ส่ง ID -->

    <label for="field1">Field 1:</label><br>
    <input type="text" id="field1" name="field1" value="<?php echo htmlspecialchars($dataToEdit['field1']); ?>"><br><br>

    <label for="field2">Field 2:</label><br>
    <textarea id="field2" name="field2"><?php echo htmlspecialchars($dataToEdit['field2']); ?></textarea><br><br>

    <input type="submit" value="บันทึกการแก้ไข">
</form>