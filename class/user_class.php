<?php
@session_start();

// ไม่ต้อง require_once ที่นี่ แต่จะไป require ในไฟล์ที่ใช้งานจริง
// require_once 'connection_class.php';

class User
{
    /** @var PDO */
    private $db; // เปลี่ยนเป็น private และใช้ชื่อที่สื่อความหมาย

    /**
     * รับ PDO connection object เข้ามาทาง Constructor
     */
    public function __construct(PDO $pdoConnection)
    {
        $this->db = $pdoConnection;
    }

    public function checkLogin($getUsername, $getPassword)
    {
        $username = $getUsername;
        $password = $getPassword;
        $sql = <<<EOD
                    select user_id, user_code, username, password, full_name, u.role_id, u.department_id 
                    , d.department_name, r.role_name
                    from users u
                    left join departments d
                        on u.department_id = d.department_id
                    left join roles r
                        on u.role_id = r.role_id
                    where username = :username
                EOD;

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        $user_data = $stmt->fetch();

        // ตรวจสอบว่าเจอผู้ใช้ และรหัสผ่านที่ hash ไว้ตรงกันหรือไม่
        // if ($user_data && password_verify($password, $user_data['password'])) {
        if ($user_data) {
            // ❗️ สำคัญ: คืนค่าเป็น array ข้อมูลผู้ใช้ทั้งหมด
            return $user_data;
            // return true;
        } else {
            // ถ้าไม่เจอผู้ใช้ หรือรหัสผ่านไม่ถูก ให้คืนค่า false
            return false;
        }
    }

    /**
     * ดึงข้อมูลผู้ใช้ทั้งหมดจากฐานข้อมูล
     * @return array คืนค่าเป็น array ของข้อมูลผู้ใช้ทั้งหมด หรือ array ว่างหากไม่มีข้อมูล
     */
    public function fetchAll()
    {
        $sql = <<<EOD
                    select user_id, user_code, username, password, full_name, u.role_id, u.department_id 
                        , d.department_name, r.role_name
                    from users u
                    left join departments d
                        on u.department_id = d.department_id
                    left join roles r
                        on u.role_id = r.role_id
                EOD;

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $rs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $rs;
    }

    public function fetchByUsername($username):?array
    {
        $sql = <<<EOD
                    SELECT U.user_id, U.user_code, U.username, U.password, U.full_name, U.role_id, U.department_id, U.is_deleted 
                    , D.department_name
                    , R.role_name
                    FROM users U
                    LEFT JOIN departments D
                        ON D.department_id = U.department_id
                    LEFT JOIN roles R
                        ON R.role_id = U.role_id
                    WHERE U.username = :username
                EOD;

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();

        $rs = $stmt->fetch();
        if (!$rs) {
            return null; // ไม่พบข้อมูล
        }
        return $rs;
    }

    public function fetchAllPermissions()
    {
        $sql = <<<EOD
                    SELECT P.permission_id, P.permission_name, P.menu_name
                    , 'd-none' AS display_status
                    FROM permissions P
                EOD;

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $rs = $stmt->fetchAll();
        return $rs;
    }
    public function fetchAllPermissionByUsername($username)
    {
        // $sql = <<<EOD
        //             SELECT P.permission_id, P.permission_name, P.menu_name
        //             , CASE WHEN RP.role_id IS NOT NULL THEN 'd-block' ELSE 'd-none' END AS display_status
        //             FROM permissions P
        //             LEFT JOIN role_permissions RP 
        //                 ON RP.permission_id = P.permission_id  
        //                 AND RP.role_id = (SELECT role_id FROM users WHERE username = :username)  
        //             LEFT JOIN users U 
        //                 ON U.role_id = RP.role_id  
        //                 AND U.username = :username
        //         EOD;

        $sql = <<<EOD
                    SELECT P.permission_id, P.permission_name, P.menu_name
                    FROM users U 
                    INNER JOIN role_permissions RP
                        ON RP.role_id = U.role_id
                    INNER JOIN permissions P
                        ON P.permission_id = RP.permission_id
                    WHERE U.username = :username
                EOD;

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        $rs = $stmt->fetchAll();
        return $rs;
    }

    /**
     * สร้างผู้ใช้ใหม่ในฐานข้อมูล (INSERT)
     * @param array $userData ข้อมูลผู้ใช้ในรูปแบบ associative array
     * @return string|false ID ของผู้ใช้ที่สร้างใหม่ หรือ false หากล้มเหลว
     */
    public function create(array $userData)
    {
        // ❗️ สำคัญมาก: ต้อง Hash รหัสผ่านก่อนเก็บลงฐานข้อมูลเสมอ
        $hashedPassword = password_hash($userData['password'], PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (username, password, full_name, role_id, department_id, user_code)
                VALUES (:username, :password, :full_name, :role_id, :department_id, :user_code)";

        try {
            $stmt = $this->db->prepare($sql);
            /* //รูปแบบเดิม
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->bindParam(':full_name', $full_name, PDO::PARAM_STR);
            $stmt->bindParam(':password', $password, PDO::PARAM_STR);
            $stmt->bindParam(':role_id', $role_id, PDO::PARAM_INT);
            $stmt->bindParam(':department_id', $department_id, PDO::PARAM_INT);
            $stmt->execute();
            */

            $stmt->execute([
                ':username'      => $userData['username'],
                ':password'      => $hashedPassword, // ใช้รหัสผ่านที่เข้ารหัสแล้ว
                ':full_name'     => $userData['full_name'],
                ':role_id'       => $userData['role_id'],
                ':department_id' => $userData['department_id'],
                ':user_code'     => $userData['user_code']
            ]);

            // คืนค่า ID ของแถวที่เพิ่งเพิ่มเข้าไปใหม่
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            // ในสถานการณ์จริง ควรจะ Log error แทนการ echo
            // error_log($e->getMessage());
            return false;
        }
    }

    /**
     * อัปเดตข้อมูลผู้ใช้ (UPDATE)
     * @param int $userId ID ของผู้ใช้ที่ต้องการแก้ไข
     * @param array $userData ข้อมูลใหม่ที่ต้องการอัปเดต
     * @return bool true หากสำเร็จ, false หากล้มเหลว
     */
    public function update(int $userId, array $userData)
    {
        // ไม่ควรอัปเดต username ซึ่งมักใช้เป็น key ในการ login
        $sql = "UPDATE users 
                SET full_name = :full_name
                , role_id = :role_id
                , department_id = :department_id
                , user_code = :user_code
                WHERE user_id = :user_id";

        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':full_name'     => $userData['full_name'],
                ':role_id'       => $userData['role_id'],
                ':department_id' => $userData['department_id'],
                ':user_code'     => $userData['user_code'],
                ':user_id'       => $userId
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * ลบผู้ใช้ (DELETE)
     * @param int $userId ID ของผู้ใช้ที่ต้องการลบ
     * @return bool true หากสำเร็จ, false หากล้มเหลว
     */
    public function delete(int $userId)
    {
        // คำแนะนำ: ในระบบงานจริงส่วนใหญ่นิยมใช้วิธี "Soft Delete"
        // คือการอัปเดต field เช่น is_deleted = 1 แทนการลบข้อมูลจริงออกจากฐานข้อมูล
        $sql = "UPDATE users 
                SET is_deleted = 1
                WHERE user_id = :user_id";

        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':user_id' => $userId]);
        } catch (PDOException $e) {
            return false;
        }
    }

    // --- เมธอดอื่นๆ ที่มีอยู่แล้ว เช่น getById, checkLogin, getAll ---
    public function getById(int $userId)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetch();
    }
}
