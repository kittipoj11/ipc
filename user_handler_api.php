<?php
@session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

require_once 'config.php';
require_once 'class/connection_class.php';
require_once 'class/user_class.php';

$requestData = json_decode(file_get_contents('php://input'), true);

if (isset($requestData['action'])) {
    $connection = new Connection();
    $pdo = $connection->getDbConnection();

    $user = new User($pdo);

    switch ($requestData['action']) {
        case 'select':
            $rs = $user->getAll();
            echo json_encode($rs);
            break;

        case 'save':
            // $saveUserId = $user->save($requestData);

            try {
                $pdo->beginTransaction();

                $saveUserId = $user->save($requestData);
                // ถ้า $saveUserId ยังคงเป็น 0 หรือว่าง แสดงว่าเกิดข้อผิดพลาด
                if (empty($saveUserId)) {
                    return (int)$saveUserId;
                }

                $pdo->commit();

                $response = [
                    'status' => 'success',
                    'message' => 'บันทึกข้อมูล USER ID: ' . $saveUserId . ' เรียบร้อยแล้ว',
                    'data' => ['user_id' => $saveUserId]
                ];
                echo json_encode($response);
            } catch (Exception $e) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                // สามารถบันทึก error หรือโยน exception ต่อไปได้
                // error_log($e->getMessage());
                $response = [
                    'status' => 'fail',
                    'message' => 'บันทึกข้อมูลไม่สำเร็จ',
                    'data' => ['user_id' => $saveUserId]
                ];
                echo json_encode($response);
            }
            break;

        default:
    }
}
