<?php
require_once 'class/connection_class.php';
require_once 'class/OrderRepository.php';

$connection = new Connection();
$orderRepo = new OrderRepository($connection->getDbConnection());


// --- สร้างออเดอร์ใหม่ ---
$newOrderData = [
    'customer_name' => 'บริษัท ทดสอบ จำกัด',
    'order_date'    => date('Y-m-d H:i:s'),
    'grand_total'   => 15000.00,
    'details' => [
        ['item_name' => 'สินค้า A', 'quantity' => 2, 'price' => 5000.00],
        ['item_name' => 'สินค้า B', 'quantity' => 1, 'price' => 5000.00]
    ],
    'periods' => [
        ['period_amount' => 7500.00, 'due_date' => '2025-07-15'],
        ['period_amount' => 7500.00, 'due_date' => '2025-08-15']
    ]
];

$newId = $orderRepo->create($newOrderData);
if ($newId) {
    echo "สร้างออเดอร์สำเร็จ ID: $newId\n\n";
}

// --- ดึงข้อมูลออเดอร์ ID ที่เพิ่งสร้าง ---
if($newId) {
    $orderData = $orderRepo->getById($newId);
    echo "ข้อมูลออเดอร์ ID: $newId\n";
    print_r($orderData);
}

// --- ลบออเดอร์ ---
if($newId) {
    if ($orderRepo->delete($newId)) {
        echo "\nลบออเดอร์ ID: $newId สำเร็จ\n";
    }
}