<?php
// class/OrderRepository.php
class OrderRepository
{
    private $pdo;

    public function __construct(PDO $pdoConnection)
    {
        $this->pdo = $pdoConnection;
    }

    /** ดึงข้อมูล Header */
    public function getHeader(int $poId): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM purchase_orders WHERE po_id = ?");
        $stmt->execute([$poId]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /** ดึงรายการสินค้า */
    public function getOrderItems(int $poId): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM order_items WHERE po_id = ? ORDER BY item_id ASC");
        $stmt->execute([$poId]);
        return $stmt->fetchAll();
    }

    /** ดึงงวดงาน */
    public function getOrderPeriods(int $poId): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM order_periods WHERE po_id = ? ORDER BY period_no ASC");
        $stmt->execute([$poId]);
        return $stmt->fetchAll();
    }

    /** บันทึกข้อมูล PO ทั้งหมด (จัดการทั้ง Create และ Update) */
    public function save(array $headerData, array $itemsData, array $periodsData): int
    {
        $this->pdo->beginTransaction();
        try {
            $poId = $headerData['po_id'] ?? 0;

            // 1. จัดการ Header (INSERT หรือ UPDATE)
            if (empty($poId)) {
                $sql = "INSERT INTO purchase_orders (po_number, project_name, contract_value, working_date_from, working_date_to) VALUES (?, ?, ?, ?, ?)";
                $params = [$headerData['po_number'], $headerData['project_name'], $headerData['contract_value'], $headerData['working_date_from'], $headerData['working_date_to']];
                $this->pdo->prepare($sql)->execute($params);
                $poId = $this->pdo->lastInsertId();
            } else {
                $sql = "UPDATE purchase_orders SET po_number = ?, project_name = ?, contract_value = ?, working_date_from = ?, working_date_to = ? WHERE po_id = ?";
                $params = [$headerData['po_number'], $headerData['project_name'], $headerData['contract_value'], $headerData['working_date_from'], $headerData['working_date_to'], $poId];
                $this->pdo->prepare($sql)->execute($params);
            }
            if (empty($poId)) throw new Exception("Could not create or find PO ID.");

            // 2. จัดการ Items และ Periods โดยใช้ Helper Method
            $this->processDetails($poId, $itemsData, 'order_items', 'item_id', [
                'insert' => ['sql' => "INSERT INTO order_items (po_id, product_name, quantity, unit_price) VALUES (?, ?, ?, ?)", 'params' => ['product_name', 'quantity', 'unit_price']],
                'update' => ['sql' => "UPDATE order_items SET product_name = ?, quantity = ?, unit_price = ? WHERE item_id = ?", 'params' => ['product_name', 'quantity', 'unit_price']]
            ]);
            $this->processDetails($poId, $periodsData, 'order_periods', 'period_id', [
                'insert' => ['sql' => "INSERT INTO order_periods (po_id, period_no, work_percent, interim_payments, remarks) VALUES (?, ?, ?, ?, ?)", 'params' => ['period_no', 'work_percent', 'interim_payments', 'remarks']],
                'update' => ['sql' => "UPDATE order_periods SET period_no = ?, work_percent = ?, interim_payments = ?, remarks = ? WHERE period_id = ?", 'params' => ['period_no', 'work_percent', 'interim_payments', 'remarks']]
            ]);

            $this->pdo->commit();
            return (int)$poId;
        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            throw $e;
        }
    }

    /** Helper method สำหรับจัดการรายการย่อย (Items/Periods) */
    private function processDetails(int $parentId, array $detailsData, string $table, string $pk, array $config)
    {
        $actions = ['delete' => [], 'update' => [], 'create' => []];
        foreach ($detailsData as $item) {
            $status = $item['crud_status'] ?? 'none';
            if (isset($actions[$status])) $actions[$status][] = $item;
        }

        if (!empty($actions['delete'])) {
            $stmt = $this->pdo->prepare("DELETE FROM $table WHERE $pk = ?");
            foreach ($actions['delete'] as $item) if (!empty($item[$pk])) $stmt->execute([$item[$pk]]);
        }
        if (!empty($actions['update'])) {
            $stmt = $this->pdo->prepare($config['update']['sql']);
            foreach ($actions['update'] as $item) {
                if (!empty($item[$pk])) {
                    $params = array_map(fn($key) => $item[$key], $config['update']['params']);
                    $params[] = $item[$pk];
                    $stmt->execute($params);
                }
            }
        }
        if (!empty($actions['create'])) {
            $stmt = $this->pdo->prepare($config['insert']['sql']);
            foreach ($actions['create'] as $item) {
                $params = array_map(fn($key) => $item[$key], $config['insert']['params']);
                array_unshift($params, $parentId);
                $stmt->execute($params);
            }
        }
    }
}
