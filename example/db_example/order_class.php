<?php

class OrderRepository
{
    /** @var PDO */
    private $db;

    public function __construct(PDO $pdoConnection)
    {
        $this->db = $pdoConnection;
    }

    // ==========================================================
    // 1. การดึงข้อมูลทุกรายการ (Get All Orders)
    // ==========================================================
    public function getAll(): array
    {
        // ดึงเฉพาะข้อมูลหลักของออเดอร์เพื่อประสิทธิภาพ
        $sql = "SELECT order_id, customer_name, order_date, grand_total 
                FROM orders 
                ORDER BY order_date DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ==========================================================
    // 2. การดึงข้อมูลตาม ID ของ Order (Get Order By ID)
    // ==========================================================
    public function getById(int $orderId): ?array//?array คือการประกาศว่า "ฟังก์ชันนี้จะคืนค่าเป็น array หรือ null เท่านั้น
    {
        // 1. ดึงข้อมูล Order หลัก
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE order_id = :id");
        $stmt->execute([':id' => $orderId]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            return null; // ไม่พบออเดอร์
        }

        // 2. ดึงรายการสินค้า (Order Details)
        $stmt = $this->db->prepare("SELECT * FROM order_details WHERE order_id = :id");
        $stmt->execute([':id' => $orderId]);
        $order['details'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 3. ดึงข้อมูลงวดชำระ (Order Periods)
        $stmt = $this->db->prepare("SELECT * FROM order_periods WHERE order_id = :id");
        $stmt->execute([':id' => $orderId]);
        $order['periods'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $order;
    }

    // ==========================================================
    // 3. การเพิ่ม, แก้ไข, ลบข้อมูล (INSERT, UPDATE, DELETE)
    // ==========================================================

    /**
     * สร้างออเดอร์ใหม่พร้อมรายละเอียดและงวดชำระ
     * @param array $data ข้อมูลทั้งหมดของออเดอร์
     * @return int|false ID ของออเดอร์ที่สร้างใหม่ หรือ false หากล้มเหลว
     */
    public function create(array $data)
    {
        $this->db->beginTransaction();
        try {
            // 1. เพิ่มข้อมูลลงตาราง orders
            $sqlOrder = "INSERT INTO orders (customer_name, order_date, grand_total) VALUES (?, ?, ?)";
            $stmtOrder = $this->db->prepare($sqlOrder);
            $stmtOrder->execute([$data['customer_name'], $data['order_date'], $data['grand_total']]);
            $orderId = $this->db->lastInsertId();

            // 2. เพิ่มข้อมูลลงตาราง order_details (วนลูป)
            $sqlDetail = "INSERT INTO order_details (order_id, item_name, quantity, price) VALUES (?, ?, ?, ?)";
            $stmtDetail = $this->db->prepare($sqlDetail);
            foreach ($data['details'] as $item) {
                $stmtDetail->execute([$orderId, $item['item_name'], $item['quantity'], $item['price']]);
            }

            // 3. เพิ่มข้อมูลลงตาราง order_periods (วนลูป)
            $sqlPeriod = "INSERT INTO order_periods (order_id, period_amount, due_date, status) VALUES (?, ?, ?, ?)";
            $stmtPeriod = $this->db->prepare($sqlPeriod);
            foreach ($data['periods'] as $period) {
                $stmtPeriod->execute([$orderId, $period['period_amount'], $period['due_date'], 'pending']);
            }

            $this->db->commit();
            return $orderId;

        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Order Creation Failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * อัปเดตออเดอร์ (วิธีที่ง่ายคือ ลบของเก่าแล้วเพิ่มใหม่)
     * @param int $orderId ID ของออเดอร์
     * @param array $data ข้อมูลใหม่ทั้งหมด
     * @return bool
     */
    public function update(int $orderId, array $data): bool
    {
        $this->db->beginTransaction();
        try {
            // 1. อัปเดตตาราง orders
            $sqlOrder = "UPDATE orders SET customer_name = ?, order_date = ?, grand_total = ? WHERE order_id = ?";
            $stmtOrder = $this->db->prepare($sqlOrder);
            $stmtOrder->execute([$data['customer_name'], $data['order_date'], $data['grand_total'], $orderId]);

            // 2. ลบรายละเอียดและงวดชำระเก่าทิ้งทั้งหมด
            $this->db->prepare("DELETE FROM order_details WHERE order_id = ?")->execute([$orderId]);
            $this->db->prepare("DELETE FROM order_periods WHERE order_id = ?")->execute([$orderId]);

            // 3. เพิ่มรายละเอียดและงวดชำระใหม่เข้าไป (เหมือนตอน create)
            $sqlDetail = "INSERT INTO order_details (order_id, item_name, quantity, price) VALUES (?, ?, ?, ?)";
            $stmtDetail = $this->db->prepare($sqlDetail);
            foreach ($data['details'] as $item) {
                $stmtDetail->execute([$orderId, $item['item_name'], $item['quantity'], $item['price']]);
            }
            
            $sqlPeriod = "INSERT INTO order_periods (order_id, period_amount, due_date, status) VALUES (?, ?, ?, ?)";
            $stmtPeriod = $this->db->prepare($sqlPeriod);
            foreach ($data['periods'] as $period) {
                $stmtPeriod->execute([$orderId, $period['period_amount'], $period['due_date'], $period['status']]);
            }

            $this->db->commit();
            return true;

        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Order Update Failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * ลบออเดอร์และข้อมูลที่เกี่ยวข้องทั้งหมด
     * @param int $orderId
     * @return bool
     */
    public function delete(int $orderId): bool
    {
        // หมายเหตุ: หากตั้งค่า Foreign Key เป็น ON DELETE CASCADE ในฐานข้อมูล
        // เราจะสามารถลบแค่จากตาราง orders ได้เลย แต่การลบแบบ manual จะควบคุมได้ดีกว่า
        $this->db->beginTransaction();
        try {
            // ต้องลบจากตารางลูกก่อนเสมอ เพื่อไม่ให้ผิด Foreign Key Constraint
            $this->db->prepare("DELETE FROM order_details WHERE order_id = ?")->execute([$orderId]);
            $this->db->prepare("DELETE FROM order_periods WHERE order_id = ?")->execute([$orderId]);
            
            // จากนั้นจึงลบจากตารางแม่
            $this->db->prepare("DELETE FROM orders WHERE order_id = ?")->execute([$orderId]);

            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Order Deletion Failed: " . $e->getMessage());
            return false;
        }
    }
}