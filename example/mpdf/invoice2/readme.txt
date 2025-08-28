project/
│── index.php          // หน้าแสดงปุ่ม + preview
│── get_receipt.php    // ดึงข้อมูลใบเสร็จจาก MySQL
│── generate_pdf.php   // สร้าง PDF ด้วย mPDF
│── db.php             // เชื่อมต่อฐานข้อมูล MySQL

อธิบายการทำงาน
1. index.php → มีปุ่ม "พิมพ์ใบเสร็จ" → เมื่อกด จะเรียก AJAX ไปที่ get_receipt.php
2. get_receipt.php → ดึงข้อมูลจาก MySQL มาแสดงในรูปแบบใบเสร็จ (เหมือน preview ที่คุณให้ดู)
3. ใน preview มีปุ่ม "พิมพ์ PDF" → เมื่อกด จะเปิด generate_pdf.php?id=...
4. generate_pdf.php → ใช้ mPDF สร้าง PDF ออกมา (รองรับภาษาไทยด้วยฟอนต์ TH Sarabun New)
5. ผู้ใช้สามารถบันทึก/พิมพ์ PDF ได้ทันที


