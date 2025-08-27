วิธีการทำงาน
1. เปิดเว็บครั้งแรก → JS จะเรียก ipc_handler_api.php (page=1)
2. API จะ query DB → คืน JSON ที่มี ข้อมูล + totalPages
3. JS จะเอา JSON มา render ใน <div id="content">
    - ถ้า file_type = image → แสดง <img>
    - ถ้า file_type = pdf → แสดง <iframe> ฝัง PDF
4. Pagination จะสร้างใหม่ทุกครั้งตามจำนวนหน้า
