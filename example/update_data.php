<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. รับข้อมูลจาก Form
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $field1 = isset($_POST['field1']) ? $_POST['field1'] : '';
    $field2 = isset($_POST['field2']) ? $_POST['field2'] : '';

    // 2. Validate ข้อมูล (ตรวจสอบความถูกต้อง, ข้อมูลครบถ้วน)
    $errors = [];
    if (empty($field1)) {
        $errors[] = "Field 1 ห้ามว่าง";
    }
    // ... เพิ่ม validation สำหรับ field อื่นๆ ...

    if (empty($errors)) {
        // 3. ** ส่วนของการ Update ข้อมูลในฐานข้อมูล **
        // (ตัวอย่างนี้สมมติว่าคุณมีฟังก์ชัน updateData($id, $field1, $field2) ที่ update ข้อมูลใน DB)
        function updateData($id, $field1, $field2) {
            // ** จำลองการ Update ฐานข้อมูล (แทนการ update DB จริง) **
            // ในระบบจริง คุณจะต้องเขียนโค้ดเชื่อมต่อฐานข้อมูล
            // และ execute คำสั่ง SQL UPDATE ที่นี่
            echo "จำลองการ Update ข้อมูล ID: " . htmlspecialchars($id) . " สำเร็จ<br>";
            echo "Field 1 ใหม่: " . htmlspecialchars($field1) . "<br>";
            echo "Field 2 ใหม่: " . htmlspecialchars($field2) . "<br>";
            return true; // สมมติว่า update สำเร็จ
        }

        $updateResult = updateData($id, $field1, $field2);

        if ($updateResult) {
            echo "แก้ไขข้อมูลสำเร็จ!";
            echo '<br><a href="index.php">กลับไปหน้าหลัก</a>';
        } else {
            echo "เกิดข้อผิดพลาดในการแก้ไขข้อมูล";
            echo '<br><a href="edit_data.php?id=' . htmlspecialchars($id) . '">ลองแก้ไขใหม่อีกครั้ง</a>';
        }

    } else {
        // 4. กรณีมี Error Validation
        echo "<h3>พบข้อผิดพลาด:</h3>";
        echo "<ul>";
        foreach ($errors as $error) {
            echo "<li>" . htmlspecialchars($error) . "</li>";
        }
        echo "</ul>";
        echo '<a href="edit_data.php?id=' . htmlspecialchars($id) . '">กลับไปแก้ไขข้อมูล</a>';
    }

} else {
    // กรณีที่ไม่ได้เรียกผ่าน POST (เช่น เข้ามาที่ไฟล์นี้โดยตรง)
    echo "ไม่สามารถเข้าถึงไฟล์นี้โดยตรง";
}
?>
