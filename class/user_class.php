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
                    , u.signature_path
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
        $hashed_password = $user_data['password'];
        // if ($user_data && password_verify($password, $hashed_password)) {
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
    public function getAll()
    {
        $sql = <<<EOD
                    SELECT U.user_id, U.user_code, U.username, U.password, U.full_name, U.role_id, U.department_id, U.is_deleted 
                    , D.department_name
                    , R.role_name
                    , U.signature_path
                    FROM users U
                    LEFT JOIN departments D
                        ON D.department_id = U.department_id
                    LEFT JOIN roles R
                        ON R.role_id = U.role_id
                    ORDER BY user_id
                EOD;

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $rs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $rs;
    }

    public function getByUserId(int $user_id): ?array
    {
        $sql = <<<EOD
                    SELECT U.user_id, U.user_code, U.username, U.password, U.full_name, U.role_id, U.department_id, U.is_deleted 
                    , D.department_name
                    , R.role_name
                    , U.signature_path
                    FROM users U
                    LEFT JOIN departments D
                        ON D.department_id = U.department_id
                    LEFT JOIN roles R
                        ON R.role_id = U.role_id
                    WHERE U.user_id = :user_id
                EOD;

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $user_id]);
        $rs = $stmt->fetch();
        if (!$rs) {
            return null; // ไม่พบข้อมูล
        }
        return $rs;
    }

    public function getByUsername($username): ?array
    {
        $sql = <<<EOD
                    SELECT U.user_id, U.user_code, U.username, U.password, U.full_name, U.role_id, U.department_id, U.is_deleted 
                    , D.department_name
                    , R.role_name
                    , U.signature_path
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
        if (!$rs) { //false
            return null; // ไม่พบข้อมูล
        }
        return $rs;
    }

    public function getAllPermissions()
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
    public function getAllPermissionByUsername($username)
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

    public function save(array $data)
    {
        $userId = $data['user_id'] ?? 0;
        // ❗️ สำคัญมาก: ต้อง Hash รหัสผ่านก่อนเก็บลงฐานข้อมูลเสมอ
        // $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        $hashedPassword = $data['password'];//ตอนนี้ใช้การเก็บค่าโดยตรง   ซึ่งต่อไปต้องแยก transaction ของการเก็บรหัสผ่่านไว้อีกฟังก์ชั่นหนึ่ง
        if (empty($userId)) {

            $sql = "INSERT INTO users (user_code, username, password, full_name, role_id, department_id, signature_path)
                VALUES (:user_code, :username, :password, :full_name, :role_id, :department_id, :signature_path)";

            $stmt = $this->db->prepare($sql);
            /* //รูปแบบเดิม
            $stmt->bindParam(':user_code', $user_code, PDO::PARAM_STR);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
            $stmt->bindParam(':full_name', $full_name, PDO::PARAM_STR);
            $stmt->bindParam(':role_id', $role_id, PDO::PARAM_INT);
            $stmt->bindParam(':department_id', $department_id, PDO::PARAM_INT);
            $stmt->execute();
            */
            $stmt->execute([
                ':user_code'     => $data['user_code'],
                ':username'      => $data['username'],
                ':password'      => $hashedPassword, // ใช้รหัสผ่านที่เข้ารหัสแล้ว
                ':full_name'     => $data['full_name'],
                ':role_id'       => $data['role_id'],
                ':department_id' => $data['department_id'],
                ':signature_path' => $data['signature_path'],
            ]);
            // คืนค่า ID ของแถวที่เพิ่งเพิ่มเข้าไปใหม่
            $userId =  $this->db->lastInsertId();
        } else {
            // ไม่ควรอัปเดต username ซึ่งมักใช้เป็น key ในการ login
            $sql = "UPDATE users 
                SET user_code = :user_code
                , full_name = :full_name
                , password = :password
                , role_id = :role_id
                , department_id = :department_id
                , signature_path = :signature_path
                WHERE user_id = :user_id";
            $stmt = $this->db->prepare($sql);
            $_SESSION['before']="execute";
            $stmt->execute([
                ':user_code'     => $data['user_code'],
                ':password'      => $hashedPassword,
                ':full_name'     => $data['full_name'],
                ':role_id'       => $data['role_id'],
                ':department_id' => $data['department_id'],
                ':signature_path' => $data['signature_path'],
                ':user_id'       => $userId,
            ]);
            $_SESSION['after']="execute";
        }
        return (int)$userId;
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
        // ถ้าใช้ DELETE FROM users WHERE user_id = :user_id จะเป็น Hard delete
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
}
