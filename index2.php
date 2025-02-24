<?php
// ** ส่วนของการดึงข้อมูลรายการจากฐานข้อมูลมาแสดง **
// (ตัวอย่างนี้สมมติว่าคุณมีฟังก์ชัน getListData() ที่ดึงข้อมูลจาก DB)
function getListData() {
    // ** จำลองข้อมูล (แทนการดึงจากฐานข้อมูลจริง) **
    return [
        ['id' => 1, 'field1' => 'ข้อมูล 1', 'field2' => 'รายละเอียด 1'],
        ['id' => 2, 'field1' => 'ข้อมูล 2', 'field2' => 'รายละเอียด 2'],
        ['id' => 3, 'field1' => 'ข้อมูล 3', 'field2' => 'รายละเอียด 3'],
    ];
}

$dataList = getListData();
?>

<!DOCTYPE html>
<html>
<head>
    <title>หน้าหลัก - รายการข้อมูล (AJAX)</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="manage_data.js"></script> <!- Include ไฟล์ manage_data.js -->
</head>
<body>
    <h2>รายการข้อมูล (AJAX)</h2>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Field 1</th>
                <th>Field 2</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($dataList as $data): ?>
            <tr>
                <td><?php echo htmlspecialchars($data['id']); ?></td>
                <td><?php echo htmlspecialchars($data['field1']); ?></td>
                <td><?php echo htmlspecialchars($data['field2']); ?></td>
                <td>
                    <a href="#" class="editLink" data-id="<?php echo htmlspecialchars($data['id']); ?>">แก้ไข (AJAX)</a> <!- เปลี่ยนลิงก์เป็น # และเพิ่ม data-id -->
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div id="editFormContainer">
        <!- div สำหรับแสดงฟอร์มแก้ไขข้อมูลที่จะโหลดด้วย AJAX -->
    </div>

</body>
</html>