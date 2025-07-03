<?php
// class/OrderRepository.php
class OrderRepository
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
    
    // ★★★ เพิ่มเมธอดสำหรับดึง Items ★★★
    public function getOrderItems(int $poId): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM order_items WHERE po_id = ? ORDER BY item_id ASC");
        $stmt->execute([$poId]);
        return $stmt->fetchAll();
    }
    
    // เปลี่ยนชื่อเมธอดเพื่อความชัดเจน
    public function getOrderPeriods(int $poId): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM order_periods WHERE po_id = ? ORDER BY period_no ASC");
        $stmt->execute([$poId]);
        return $stmt->fetchAll();
    }

    public function save(array $headerData, array $itemsData, array $periodsData): int
    {
        $this->pdo->beginTransaction();
        try {
            $poId = $headerData['po_id'] ?? 0;

            // 1. จัดการข้อมูล Header (INSERT หรือ UPDATE)
            if (empty($poId)) {
                // CREATE MODE
                $sqlHeader = "INSERT INTO purchase_orders (po_number, project_name, contract_value, working_date_from, working_date_to) VALUES (?, ?, ?, ?, ?)";
                $stmtHeader = $this->pdo->prepare($sqlHeader);
                $stmtHeader->execute([$headerData['po_number'], $headerData['project_name'], $headerData['contract_value'], $headerData['working_date_from'], $headerData['working_date_to']]);
                $poId = $this->pdo->lastInsertId();
            } else {
                // UPDATE MODE
                $sqlHeader = "UPDATE purchase_orders SET po_number = ?, project_name = ?, contract_value = ?, working_date_from = ?, working_date_to = ? WHERE po_id = ?";
                $stmtHeader = $this->pdo->prepare($sqlHeader);
                $stmtHeader->execute([$headerData['po_number'], $headerData['project_name'], $headerData['contract_value'], $headerData['working_date_from'], $headerData['working_date_to'], $poId]);
            }
            
            if (empty($poId)) {
                throw new Exception("Could not create or find a valid PO ID.");
            }

            // ★★★ 2. จัดการข้อมูล Items (D-U-C Logic) ★★★
            $this->processDetails($poId, $itemsData, 'order_items', 'item_id', [
                'insert_sql' => "INSERT INTO order_items (po_id, product_name, quantity, unit_price) VALUES (?, ?, ?, ?)",
                'insert_params' => ['product_name', 'quantity', 'unit_price'],
                'update_sql' => "UPDATE order_items SET product_name = ?, quantity = ?, unit_price = ? WHERE item_id = ?",
                'update_params' => ['product_name', 'quantity', 'unit_price']
            ]);

            // 3. จัดการข้อมูล Periods (D-U-C Logic)
            $this->processDetails($poId, $periodsData, 'order_periods', 'period_id', [
                'insert_sql' => "INSERT INTO order_periods (po_id, period_no, work_percent, interim_payments, remarks) VALUES (?, ?, ?, ?, ?)",
                'insert_params' => ['period_no', 'work_percent', 'interim_payments', 'remarks'],
                'update_sql' => "UPDATE order_periods SET period_no = ?, work_percent = ?, interim_payments = ?, remarks = ? WHERE period_id = ?",
                'update_params' => ['period_no', 'work_percent', 'interim_payments', 'remarks']
            ]);

            $this->pdo->commit();
            return (int)$poId;

        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Helper method สำหรับจัดการ Details (Items หรือ Periods)
     */
    private function processDetails(int $poId, array $detailsData, string $tableName, string $primaryKey, array $config)
    {
        $deleteItems = array_filter($detailsData, fn($item) => ($item['crud_status'] ?? 'none') === 'delete');
        $updateItems = array_filter($detailsData, fn($item) => ($item['crud_status'] ?? 'none') === 'update');
        $createItems = array_filter($detailsData, fn($item) => ($item['crud_status'] ?? 'none') === 'create');
        
        if (!empty($deleteItems)) {
            $stmtDelete = $this->pdo->prepare("DELETE FROM $tableName WHERE $primaryKey = ?");
            foreach ($deleteItems as $item) {
                if (!empty($item[$primaryKey])) $stmtDelete->execute([$item[$primaryKey]]);
            }
        }
        
        if (!empty($updateItems)) {
            $stmtUpdate = $this->pdo->prepare($config['update_sql']);
            foreach ($updateItems as $item) {
                if (!empty($item[$primaryKey])) {
                    $params = array_map(fn($key) => $item[$key], $config['update_params']);
                    $params[] = $item[$primaryKey]; // Add primary key for WHERE clause
                    $stmtUpdate->execute($params);
                }
            }
        }

        if (!empty($createItems)) {
            $stmtCreate = $this->pdo->prepare($config['insert_sql']);
            foreach ($createItems as $item) {
                $params = array_map(fn($key) => $item[$key], $config['insert_params']);
                array_unshift($params, $poId); // Add po_id as the first parameter
                $stmtCreate->execute($params);
            }
        }
    }
}