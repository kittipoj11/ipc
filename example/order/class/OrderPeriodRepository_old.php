<?php
// class/OrderPeriodRepository.php

class OrderPeriodRepository
{
    /** @var PDO */
    private $pdo;

    public function __construct(PDO $pdoConnection)
    {
        $this->pdo = $pdoConnection;
    }

    /**
     * ดึงข้อมูลงวดงานทั้งหมดของออเดอร์ที่กำหนด
     * @param int $orderId
     * @return array
     */
    public function getForOrder(int $orderId): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM order_periods WHERE order_id = ? ORDER BY period_no ASC");
        $stmt->execute([$orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * ประมวลผลการบันทึกข้อมูลงวดงานแบบ Batch (หลายรายการพร้อมกัน)
     * @param int $orderId
     * @param array $periodsData
     * @return bool
     * @throws Exception
     */
    public function processBatch(int $orderId, array $periodsData): bool
    {
        // 1. จัดกลุ่มข้อมูลตาม Action
        $deleteItems = [];
        $updateItems = [];
        $createItems = [];

        foreach ($periodsData as $item) {
            $action = $item['crud_status'] ?? 'none';
            switch ($action) {
                case 'delete': $deleteItems[] = $item; break;
                case 'update': $updateItems[] = $item; break;
                case 'create': $createItems[] = $item; break;
            }
        }

        // 2. เริ่ม Transaction และทำงานตามลำดับ D-U-C
        $this->pdo->beginTransaction();
        try {
            // 2.1 ทำการ DELETE ก่อน
            if (!empty($deleteItems)) {
                $stmtDelete = $this->pdo->prepare("DELETE FROM order_periods WHERE period_id = ? AND order_id = ?");
                foreach ($deleteItems as $item) {
                    if (!empty($item['period_id'])) {
                        $stmtDelete->execute([$item['period_id'], $orderId]);
                    }
                }
            }
            
            // 2.2 ทำการ UPDATE
            if (!empty($updateItems)) {
                $stmtUpdate = $this->pdo->prepare(
                    "UPDATE order_periods SET work_percent = ?, interim_payments = ?, remarks = ? WHERE period_id = ? AND order_id = ?"
                );
                foreach ($updateItems as $item) {
                    if (!empty($item['period_id'])) {
                        $stmtUpdate->execute([$item['work_percent'], $item['interim_payments'], $item['remarks'], $item['period_id'], $orderId]);
                    }
                }
            }

            // 2.3 ทำการ CREATE สุดท้าย
            if (!empty($createItems)) {
                $stmtCreate = $this->pdo->prepare(
                    "INSERT INTO order_periods (order_id, period_no, work_percent, interim_payments, remarks) VALUES (?, ?, ?, ?, ?)"
                );
                foreach ($createItems as $item) {
                    $stmtCreate->execute([$orderId, $item['period_no'], $item['work_percent'], $item['interim_payments'], $item['remarks']]);
                }
            }

            $this->pdo->commit();
            return true;

        } catch (Exception $e) {
            // หากเกิดข้อผิดพลาด ให้ยกเลิกทั้งหมดแล้วโยน Exception ออกไปให้ Controller จัดการ
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }
}