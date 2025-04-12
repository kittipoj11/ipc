<?php
@session_start();

// destroy the session
// session_destroy();

require_once  'connection_class.php';

class Menu extends Connection
{
    public function getRecordAll()
    {
        $sql = <<<EOD
                select * 
                from users u
                left join departments d
                    on u.department_id = d.department_id
                left join roles r
                    on u.role_id = r.role_id"
                EOD;

        $stmt = $this->myConnect->prepare($sql);
        $stmt->execute();
        $rs = $stmt->fetchAll();
        return $rs;
    }

    
    public function getMenuByUsername($getUsername)
    {
        $sql = <<<EOD
                    SELECT U.user_id, U.username, P.permission_id, P.permission_name, P.menu_name
                    , P.content_filename, P.function_name
                    , CASE WHEN RP.permission_id IS NOT NULL THEN 'yes' ELSE 'no' END AS role_status
                    FROM users U
                    CROSS JOIN permissions P
                    LEFT JOIN role_permissions RP
                        ON RP.role_id = U.role_id AND RP.permission_id = P.permission_id
                    WHERE U.username = :username
                    ORDER BY U.user_id, P.permission_id
                EOD;

        $stmt = $this->myConnect->prepare($sql);
        $stmt->bindParam(':username', $getUsername, PDO::PARAM_STR);
        $stmt->execute();
        $rs = $stmt->fetchAll();
        return $rs;
    }

    public function insertData($getData)
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
        $stmt = $this->myConnect->prepare($sql);
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
    public function updateData($getData)
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
        $stmt = $this->myConnect->prepare($sql);
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
    public function deleteData($getData)
    {
        $username = $getData['delete_id'];
        // $is_active = isset($getData['is_active']) ? 1 : 0;
        $sql = "update users 
                set is_deleted = 1
                where username = :username";
        $stmt = $this->myConnect->prepare($sql);
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