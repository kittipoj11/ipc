<?php
// class/OrderPeriodRepository.php

class OrderPeriodRepository
{
    private $pdo;

    public function __construct(PDO $pdoConnection)
    {
        $this->pdo = $pdoConnection;
    }
    
    public function getHeader(int $poId): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM purchase_orders WHERE po_id = ?");
        $stmt->execute([$poId]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function getDetails(int $poId): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM order_periods WHERE po_id = ? ORDER BY period_no ASC");
        $stmt->execute([$poId]);
        return $stmt->fetchAll();
    }
    
    public function save(array $headerData, array $detailsData): int
    {
        $this->pdo->beginTransaction();
        try {
            $poId = $headerData['po_id'] ?? 0;

            // 1. ตรวจสอบและจัดการข้อมูล Header (INSERT หรือ UPDATE)
            if (empty($poId)) {
                // --- CREATE MODE ---
                $sqlHeader = "INSERT INTO purchase_orders (po_number, project_name, contract_value, working_date_from, working_date_to) 
                              VALUES (?, ?, ?, ?, ?)";
                $stmtHeader = $this->pdo->prepare($sqlHeader);
                $stmtHeader->execute([
                    $headerData['po_number'], $headerData['project_name'], $headerData['contract_value'],
                    $headerData['working_date_from'], $headerData['working_date_to']
                ]);
                // ดึง ID ของ PO ที่เพิ่งสร้างใหม่
                $poId = $this->pdo->lastInsertId();
            } else {
                // --- UPDATE MODE ---
                $sqlHeader = "UPDATE purchase_orders SET 
                                po_number = ?, project_name = ?, contract_value = ?, 
                                working_date_from = ?, working_date_to = ? 
                              WHERE po_id = ?";
                $stmtHeader = $this->pdo->prepare($sqlHeader);
                $stmtHeader->execute([
                    $headerData['po_number'], $headerData['project_name'], $headerData['contract_value'],
                    $headerData['working_date_from'], $headerData['working_date_to'], $poId
                ]);
            }

            // ถ้า $poId ยังคงเป็น 0 หรือว่าง แสดงว่าเกิดข้อผิดพลาด
            if (empty($poId)) {
                throw new Exception("Could not create or find a valid PO ID.");
            }
            
            // 2. จัดการข้อมูล Details (D-U-C Logic เหมือนเดิม)
            $deleteItems = array_filter($detailsData, fn($item) => ($item['crud_status'] ?? 'none') === 'delete');
            $updateItems = array_filter($detailsData, fn($item) => ($item['crud_status'] ?? 'none') === 'update');
            $createItems = array_filter($detailsData, fn($item) => ($item['crud_status'] ?? 'none') === 'create');
            
            // 3. ทำงานตามลำดับ D-U-C
            if (!empty($deleteItems)) {
                $stmtDelete = $this->pdo->prepare("DELETE FROM order_periods WHERE period_id = ?");
                foreach ($deleteItems as $item) {
                    if (!empty($item['period_id'])) $stmtDelete->execute([$item['period_id']]);
                }
            }
            
            if (!empty($updateItems)) {
                $stmtUpdate = $this->pdo->prepare(
                    "UPDATE order_periods SET period_no = ?, work_percent = ?, interim_payments = ?, remarks = ? WHERE period_id = ?"
                );
                foreach ($updateItems as $item) {
                    if (!empty($item['period_id'])) {
                        $stmtUpdate->execute([$item['period_no'], $item['work_percent'], $item['interim_payments'], $item['remarks'], $item['period_id']]);
                    }
                }
            }

            if (!empty($createItems)) {
                $stmtCreate = $this->pdo->prepare(
                    "INSERT INTO order_periods (po_id, period_no, work_percent, interim_payments, remarks) VALUES (?, ?, ?, ?, ?)"
                );
                foreach ($createItems as $item) {
                    $stmtCreate->execute([$poId, $item['period_no'], $item['work_percent'], $item['interim_payments'], $item['remarks']]);
                }
            }

            $this->pdo->commit();
            // คืนค่า PO ID ที่บันทึกสำเร็จกลับไป
            return (int)$poId;

        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }
}