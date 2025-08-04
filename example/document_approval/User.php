<?php
class User {
    // Properties to match the database table
    public ?int $id = null; // ใชเครื่องหมาย ? เพื่อบอกว่า id สามารถเป็น null ได้ (สำหรับ user ใหม่)
    public ?string $username = null;
    public ?string $full_name = null;

    //  Constructor สามารถใช้สร้าง Object สำหรับ user ใหม่ที่ยังไม่มีในฐานข้อมูล
    public function __construct($username = null, $full_name = null) {
        if ($username) {
            $this->username = $username;
        }
        if ($full_name) {
            $this->full_name = $full_name;
        }
    }

    // ค้นหา User ที่มีอยู่แล้วจาก ID
    public static function find($id): ?User {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        
        // fetchObject จะคืนค่า false หากไม่พบข้อมูล
        $user = $stmt->fetchObject('User');
        return $user ?: null;
    }

    /**
     * เมธอดอัจฉริยะสำหรับบันทึกข้อมูล
     * - ถ้า Object มี id: จะทำการ UPDATE
     * - ถ้า Object ไม่มี id: จะทำการ INSERT
     */
    public function save(): bool {
        $pdo = Database::getInstance()->getConnection();

        if (isset($this->id)) {
            // --- UPDATE ---
            $sql = "UPDATE users SET username = :username, full_name = :full_name WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                ':username' => $this->username,
                ':full_name' => $this->full_name,
                ':id' => $this->id
            ]);
        } else {
            // --- INSERT ---
            $sql = "INSERT INTO users (username, full_name) VALUES (:username, :full_name)";
            $stmt = $pdo->prepare($sql);
            $success = $stmt->execute([
                ':username' => $this->username,
                ':full_name' => $this->full_name
            ]);

            // ถ้า INSERT สำเร็จ, ให้ดึง ID ล่าสุดมาใส่ใน Object นี้ด้วย
            if ($success) {
                $this->id = (int)$pdo->lastInsertId();
            }
            return $success;
        }
    }

    /**
     * เมธอดสำหรับลบข้อมูล
     */
    public function delete(): bool {
        if (!isset($this->id)) {
            return false; // ไม่สามารถลบ user ที่ยังไม่มีในฐานข้อมูลได้
        }

        $pdo = Database::getInstance()->getConnection();
        $sql = "DELETE FROM users WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$this->id]);
    }
}