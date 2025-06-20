// app_periods.js

document.addEventListener('DOMContentLoaded', () => {

    const saveButton = document.getElementById('save-periods-btn');

    saveButton.addEventListener('click', async () => {

        // --- 1. รวบรวมข้อมูลจากทุกแถวในตาราง ---
        const tbody = document.getElementById('periods-tbody');
        const rows = tbody.querySelectorAll('tr');
        const periodsData = []; // เตรียม array ว่างสำหรับเก็บข้อมูล

        rows.forEach(row => {
            // สร้าง object สำหรับเก็บข้อมูลของแถวนี้
            const periodRecord = {
                // ดึงค่าจาก data attribute ของ <tr>
                period_id: row.dataset.periodId,
                // ใช้ row.querySelector เพื่อหา input ที่อยู่ในแถวนี้เท่านั้น
                period: row.querySelector('input[name="period"]').value,
                work_percent: row.querySelector('input[name="work_percent"]').value,
                interim_payments: row.querySelector('input[name="interim_payments"]').value,
                interim_payment_percents: row.querySelector('input[name="interim_payment_percents"]').value,
                remarks: row.querySelector('input[name="remarks"]').value,
                action: row.querySelector('input[name="action"]').value
            };
            // เพิ่ม object ของแถวนี้เข้าไปใน array หลัก
            periodsData.push(periodRecord);
        });

        // ลองแสดงผลข้อมูลที่รวบรวมได้ใน Console เพื่อตรวจสอบ
        console.log('Data to be sent:', periodsData);

        // --- 2. ส่งข้อมูลที่รวบรวมได้ไปที่ Server ผ่าน AJAX ---
        if (periodsData.length === 0) {
            alert('ไม่พบข้อมูลที่จะบันทึก');
            return;
        }

        try {
            const response = await fetch('api_period_handler.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                // ส่ง Array ทั้งก้อนไปใน Body โดยแปลงเป็น JSON string
                body: JSON.stringify(periodsData) 
            });

            const result = await response.json();
            
            alert(result.message); // แสดงข้อความผลลัพธ์จาก Server

            if (result.status === 'success') {
                // อาจจะโหลดหน้าใหม่ หรืออัปเดต UI ตามความเหมาะสม
                window.location.reload(); 
            }

        } catch (error) {
            console.error('Error:', error);
            alert('เกิดข้อผิดพลาดในการเชื่อมต่อกับเซิร์ฟเวอร์');
        }
    });
});