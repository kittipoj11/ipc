<?php
@session_start();

// ไม่ต้อง require_once ที่นี่ แต่จะไป require ในไฟล์ที่ใช้งานจริง
// require_once 'connection_class.php';

class Role
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

    public function fetchAll()
    {
        $sql = <<<EOD
                    SELECT `role_id`, `role_name` 
                    FROM `roles` 
                EOD;

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $rs = $stmt->fetchAll();
        return $rs;
    }

    public function fetchById($getRoleId):?array
    {
        $sql = <<<EOD
                    SELECT `role_id`, `role_name` 
                    FROM `roles` 
                    WHERE role_id = :role_id
                EOD;

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':role_id', $role_id, PDO::PARAM_INT);
        $stmt->execute();
        $rs = $stmt->fetch();
        
        if (!$rs) {
            return null; // ไม่พบข้อมูล
        }
        return $rs;
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
        if ($user_data && password_verify($getPassword, $user_data['password'])) {
            // ❗️ สำคัญ: คืนค่าเป็น array ข้อมูลผู้ใช้ทั้งหมด
            return $user_data;
        } else {
            // ถ้าไม่เจอผู้ใช้ หรือรหัสผ่านไม่ถูก ให้คืนค่า false
            return false;
        }
    }
    
    public function create($getData)
    {
        $username = $getData['username'];
        $fullname = $getData['fullname'];
        $password = $getData['password'];
        $role_id = $getData['role_id'];
        $department_id = $getData['department_id'];
        $phone = $getData['phone'];
        $email = $getData['email'];
        // $is_active = isset($getData['is_active']) ? 1 : 0;
        $sql = "insert into users(username, password, fullname, role_id, department_id, phone, email) 
                values(:username, :password, :fullname, :role_id, :department_id, :phone, :email)";
        $stmt = $this->db->prepare($sql);
        // $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':fullname', $fullname, PDO::PARAM_STR);
        $stmt->bindParam(':password', $password, PDO::PARAM_STR);
        $stmt->bindParam(':role_id', $role_id, PDO::PARAM_STR);
        $stmt->bindParam(':department_id', $department_id, PDO::PARAM_STR);
        $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        // $stmt->bindParam(':is_active', $is_active, PDO::PARAM_BOOL);
        // $affected = $stmt->execute();

        try {
            if ($stmt->execute()) {
                $_SESSION['message'] =  'data has been created successfully.';
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $_SESSION['message'] =  'This item could not be added.Because the data has duplicate values!!!';
            } else {
                $_SESSION['message'] =  'Something is wrong.Can not add data.';
            }
        }
    }
    public function update(int $getId, array $getData)
    {
        $username = $getData['username'];
        $fullname = $getData['fullname'];
        $password = $getData['password'];
        $role_id = $getData['role_id'];
        $department_id = $getData['department_id'];
        $phone = $getData['phone'];
        $email = $getData['email'];
        // $is_active = isset($getData['is_active']) ? 1 : 0;
        $sql = "update users 
                set fullname = :fullname
                , password = :password
                , role_id = :role_id
                , department_id = :department_id
                , phone = :phone
                , email = :email
                , update_datetime = CURRENT_TIMESTAMP()
                where username = :username";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':fullname', $fullname, PDO::PARAM_STR);
        $stmt->bindParam(':password', $password, PDO::PARAM_STR);
        $stmt->bindParam(':role_id', $role_id, PDO::PARAM_STR);
        $stmt->bindParam(':department_id', $department_id, PDO::PARAM_STR);
        $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        // $stmt->bindParam(':is_active', $is_active, PDO::PARAM_BOOL);

        try {
            if ($stmt->execute()) {
                $_SESSION['message'] =  'data has been update successfully.';
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $_SESSION['message'] =  'This item could not be added.Because the data has duplicate values!!!';
            } else {
                $_SESSION['message'] =  'Something is wrong.Can not add data.';
            }
        }
    }
    public function delete(int $getId)
    {
        $username = $getData['delete_id'];
        // $is_active = isset($getData['is_active']) ? 1 : 0;
        $sql = "update users 
                set is_deleted = 1
                where username = :username";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);

        try {
            if ($stmt->execute()) {
                $_SESSION['message'] =  'data has been delete successfully.';
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $_SESSION['message'] =  'This item could not be added.Because the data has duplicate values!!!';
            } else {
                $_SESSION['message'] =  'Something is wrong.Can not add data.';
            }
        }
    }
}
