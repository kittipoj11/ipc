ภาพรวมของโปรเจกต์
- สถาปัตยกรรม (Architecture): ใช้ PHP เชิงวัตถุ (OOP) ในรูปแบบ Repository Pattern เพื่อแยก Logic การจัดการข้อมูลออกจากส่วนควบคุม (Controller)
- Backend: API (api_order_handler.php) ทำหน้าที่เป็น Controller รับส่งข้อมูลแบบ JSON และเรียกใช้ OrderRepository เพื่อจัดการข้อมูลในฐานข้อมูล
- Frontend: หน้าเว็บ (order_form.html) ที่ใช้ jQuery และ AJAX (app_order_logic.js) ในการจัดการหน้าจอและสื่อสารกับ Backend ทำให้ผู้ใช้ได้รับประสบการณ์ที่ดีโดยไม่ต้องโหลดหน้าเว็บใหม่
- หัวใจหลัก: ฟังก์ชัน save() ที่สามารถจัดการการสร้าง (Create) และแก้ไข (Update) ข้อมูลหลัก (Header) พร้อมกับรายการย่อย (Items, Periods) ที่มีสถานะ create, update, delete ได้ภายในการทำงาน (Transaction) เดียว

โครงสร้างไฟล์
.
├── class/
│   ├── connection_class.php
│   └── OrderRepository.php
├── api_order_handler.php
├── order_form.html
├── app_order_logic.js
└── database_setup.sql

