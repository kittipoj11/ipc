สรุปภาพรวมของ Workflow (รวมการบันทึกประวัติ)
1. Admin สร้างเอกสาร A: 
    Admin สร้างเอกสาร ระบบจะบันทึกข้อมูลลงตาราง documents พร้อม status = 'draft' 
    และทำการบันทึกประวัติ action = 'created' ลงใน approval_history

2. Admin กด Submit:
    ระบบอัปเดตสถานะเอกสารเป็น pending_approval และกำหนด current_approver_id เป็นผู้อนุมัติคนแรก
    บันทึกประวัติ: action = 'submitted' โดย user_id ของ Admin

3. ผู้อนุมัติคนที่ 1 Approve:
    บันทึกประวัติ: action = 'approved' โดย user_id ของผู้อนุมัติคนที่ 1
    ระบบอัปเดต current_approver_id เป็นผู้อนุมัติคนที่ 2

4. ผู้อนุมัติคนที่ 2 Reject:
    บันทึกประวัติ: action = 'rejected' พร้อม comments โดย user_id ของผู้อนุมัติคนที่ 2
    ระบบอัปเดตสถานะเป็น rejected และคืน current_approver_id กลับไปเป็นของผู้อนุมัติคนที่ 1

5. ผู้อนุมัติคนสุดท้าย Approve:
    บันทึกประวัติ: action = 'approved' โดย user_id ของผู้อนุมัติคนสุดท้าย
    ระบบอัปเดตสถานะเอกสาร A เป็น completed
    ระบบตรวจสอบพบ next_workflow_id และสร้างเอกสาร B ขึ้นมาใหม่
    บันทึกประวัติ: สำหรับเอกสาร B ใหม่ จะมี action = 'created_auto' เพื่อให้รู้ว่าเอกสารนี้ถูกสร้างโดยระบบ ไม่ใช่ผู้ใช้


    