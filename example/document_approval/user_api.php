<?php
header('Content-Type: application/json');
session_start();

// Autoload all classes
spl_autoload_register(function ($class_name) {
    include 'classes/' . $class_name . '.php';
});

// การสร้างผู้ใช้ใหม่ (INSERT)
// สร้าง Object User ใหม่ โดยยังไม่มี id
$newUser = new User('new_employee', 'New Employee Name');

// สั่งให้ Object บันทึกตัวเองลงฐานข้อมูล
if ($newUser->save()) {
    echo "User created successfully with ID: " . $newUser->id;
} else {
    echo "Failed to create user.";
}

// การค้นหาและแก้ไข (SELECT & UPDATE)
// ค้นหา user ที่มี id = 3
$user = User::find(3);

if ($user) {
    // แก้ไขข้อมูลใน Object
    $user->full_name = 'Approver Two - Updated';

    // สั่งบันทึกการเปลี่ยนแปลง (เมธอด save() จะรู้เองว่าต้อง UPDATE)
    if ($user->save()) {
        echo "User updated successfully.";
    } else {
        echo "Failed to update user.";
    }
}

// การลบ (DELETE)
// ค้นหา user ที่มี id = 4
$userToDelete = User::find(4);

if ($userToDelete) {
    // สั่งให้ Object ลบตัวเองออกจากฐานข้อมูล
    if ($userToDelete->delete()) {
        echo "User deleted successfully.";
    } else {
        echo "Failed to delete user.";
    }
}

/*
## ✨ สรุปข้อดีของแนวทางนี้
- ใช้งานง่ายและเป็นธรรมชาติ: โค้ดอ่านแล้วเข้าใจได้ทันที เช่น $user->save() หรือ $user->delete()
- โค้ดรวมศูนย์: ตรรกะทั้งหมดที่เกี่ยวข้องกับ User จะถูกเก็บไว้ในที่เดียวคือคลาส User
- ลดโค้ดซ้ำซ้อน: ไม่ว่าคุณจะสร้างหรือแก้ไขผู้ใช้จากหน้าไหนของเว็บไซต์ คุณก็จะเรียกใช้เมธอด .save() เดียวกันเสมอ
*/