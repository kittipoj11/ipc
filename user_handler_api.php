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
        case 'check_login':
            try {
                // ตรวจสอบว่ามีการส่งข้อมูล username และ password มาหรือไม่
                if (isset($requestData['username']) && isset($requestData['password'])) {
                    $username = $requestData['username'] ?? '';
                    $password = $requestData['password'] ?? '';
                    // 1. สร้าง Connection
                    $connection = new Connection();
                    $pdo = $connection->getDbConnection(); // ดึง PDO object ออกมา

                    // 2. "ส่ง" PDO object เข้าไปใน User class
                    $user = new User($pdo);

                    // 3. ตรวจสอบการ login และรับผลลัพธ์ (จะเป็น array ข้อมูลผู้ใช้ หรือ false)
                    $loggedInUser = $user->checkLogin($username, $password);
                }
                // 4. กำหนด Content-Type เป็น application/json
                // header('Content-Type: application/json');///ประกาศอยู่ด้านบนแล้ว

                // ส่งผลลัพธ์กลับไปเป็น JSON
                if ($loggedInUser) {
                    // --- Login สำเร็จ ---
                    // 1. ป้องกัน Session Fixation Attack
                    session_regenerate_id(true);

                    // 2. เก็บข้อมูลที่จำเป็นจาก $loggedInUser ลงใน $_SESSION
                    $_SESSION['user_id'] = $loggedInUser['user_id'];
                    $_SESSION['user_code'] = $loggedInUser['user_code'];
                    $_SESSION['username'] = $loggedInUser['username'];
                    $_SESSION['full_name'] = $loggedInUser['full_name'];
                    $_SESSION['role_id'] = $loggedInUser['role_id'];
                    $_SESSION['role_name'] = $loggedInUser['role_name'];
                    $_SESSION['department_name'] = $loggedInUser['department_name'];
                    $_SESSION['logged_in'] = true; // สร้างตัวแปรเช็คสถานะ login

                    $_SESSION['user_menu'] = buildMenuForRole($pdo, $loggedInUser['role_id']);

                    // 3. ส่งผู้ใช้ไปยังหน้า dashboard
                    // header('Location: dashboard.php'); // สมมติว่า dashboard อยู่นอกโฟลเดอร์นี้
                    // header('Content-Type: application/json');
                    echo json_encode($loggedInUser);
                    // exit();
                } else {
                    // --- Login ล้มเหลว ---
                    // ส่งกลับไปหน้า login พร้อมกับแสดงข้อความผิดพลาด
                    // header('Location: ../login.php?error=1'); // ส่ง parameter ไปกับ URL
                    echo json_encode(false);
                    // exit();
                }
            } catch (PDOException $e) {
                // หากเกิดปัญหากับฐานข้อมูล (เช่น ต่อไม่ได้)
                // ใน production จริงๆ ควรจะ log error เก็บไว้ แทนที่จะแสดงผลให้ user เห็น
                error_log("Database Error: " . $e->getMessage());
                die("ระบบเกิดข้อผิดพลาดร้ายแรง กรุณาลองใหม่อีกครั้ง");
            }
            break;
        case 'select':
            $rs = $user->getAll();
            echo json_encode($rs);
            break;

        case 'save':
            try {
                $pdo->beginTransaction();
                $saveUserId = $user->save($requestData['headerData']);
                // ถ้า $saveUserId ยังคงเป็น 0 หรือว่าง แสดงว่าเกิดข้อผิดพลาด
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

function buildMenuForRole(PDO $pdo, int $role_id): array
{
    $sql = "SELECT m.`id`, m.`parent_id`, m.`title`, m.`url`, m.`icon`, m.`order_num`
            FROM menu_items m
            JOIN role_menu_permissions p ON m.id = p.menu_item_id
            WHERE p.role_id = :role_id
                AND is_deleted = 0
            ORDER BY m.parent_id, m.order_num";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['role_id' => $role_id]);
    $menuItems = $stmt->fetchAll();
    /*
        // จะได้ข้อมูลแบบนี้
        $menuItems = [
        0 => ['id' => 1, 'parent_id' => NULL, 'title' => 'แดชบอร์ด', ...],
        1 => ['id' => 2, 'parent_id' => NULL, 'title' => 'จัดการบทความ', ...],
        2 => ['id' => 5, 'parent_id' => NULL, 'title' => 'จัดการผู้ใช้', ...],
        3 => ['id' => 6, 'parent_id' => NULL, 'title' => 'ตั้งค่าระบบ', ...],
        4 => ['id' => 3, 'parent_id' => 2,    'title' => 'บทความทั้งหมด', ...],
        5 => ['id' => 4, 'parent_id' => 2,    'title' => 'เขียนบทความใหม่', ...]
        ];
    */

    // สร้าง Array โดยใช้ id ของเมนูเป็น Key เพื่อให้ง่ายต่อการค้นหา
    $itemsById = [];
    foreach ($menuItems as $item) {
        $itemsById[$item['id']] = $item;
        //เพิ่ม 'sub_menus' => [] เข้าไปในทุกๆ รายการ เพื่อเตรียมไว้รับเมนูย่อย
        $itemsById[$item['id']]['sub_menus'] = []; // เตรียม Array สำหรับเมนูย่อย
    }
    /*
        // จะได้ข้อมูลแบบนี้
        $itemsById = [
        1 => ['id' => 1, 'parent_id' => NULL, 'title' => 'แดชบอร์ด',       'sub_menus' => []],
        2 => ['id' => 2, 'parent_id' => NULL, 'title' => 'จัดการบทความ',   'sub_menus' => []],
        5 => ['id' => 5, 'parent_id' => NULL, 'title' => 'จัดการผู้ใช้',     'sub_menus' => []],
        6 => ['id' => 6, 'parent_id' => NULL, 'title' => 'ตั้งค่าระบบ',     'sub_menus' => []],
        3 => ['id' => 3, 'parent_id' => 2,    'title' => 'บทความทั้งหมด',  'sub_menus' => []],
        4 => ['id' => 4, 'parent_id' => 2,    'title' => 'เขียนบทความใหม่', 'sub_menus' => []]
        ];
    */

    // จัดโครงสร้างแบบลำดับชั้น (parent-child)
    $structuredMenu = [];
    //วน Loop ไปที่ข้อมูลใน $itemsById ทีละตัว แล้วตัดสินใจว่าจะเอามันไปไว้ที่ไหน
    foreach ($itemsById as $id => &$item) { // ใช้ & เพื่ออ้างอิงถึง Array ต้นฉบับ
        //เป็นการเช็คว่า "ฉันมีแม่หรือไม่?" (parent_id ไม่ใช่ค่าว่าง)
        if ($item['parent_id'] && isset($itemsById[$item['parent_id']])) {
            // ถ้ามีแม่(parent_id)
            $itemsById[$item['parent_id']]['sub_menus'][] = &$item;
        } else {
            // ถ้าไม่มีแม่(Null)
            $structuredMenu[] = &$item;
        }
    }

    /*
    การทำงานจริงทีละรอบ:

    รอบที่ 1: หยิบแฟ้ม 'แดชบอร์ด' (id: 1)
    - $item ในรอบนี้คือ: ['id' => 1, 'parent_id' => NULL, 'title' => 'แดชบอร์ด', 'sub_menus' => []]
    - ตรวจสอบเงื่อนไข if:
        $item['parent_id'] มีค่าเป็น NULL (ซึ่งเท่ากับ false ในทางตรรกะ)
        ดังนั้นเงื่อนไข if เป็นเท็จ
    - การทำงาน: โปรแกรมจะข้ามไปทำงานในส่วน else
        $structuredMenu[] = &$item;
        แปลว่า: "เอาแฟ้ม 'แดชบอร์ด' ทั้งยวง ไปวางในตู้เอกสารหลัก ($structuredMenu)"
    - ภาพหลังจบรอบที่ 1:
        $structuredMenu มีสมาชิก 1 ตัวคือ 'แดชบอร์ด'
        $structuredMenu ตอนนี้มี: [ 'แดชบอร์ด' ]

    รอบที่ 2: หยิบแฟ้ม 'จัดการบทความ' (id: 2)
    - $item ในรอบนี้คือ: ['id' => 2, 'parent_id' => NULL, 'title' => 'จัดการบทความ', 'sub_menus' => []]
    - ตรวจสอบเงื่อนไข if:
        $item['parent_id'] มีค่าเป็น NULL
        เงื่อนไข if เป็นเท็จ
    - การทำงาน: ทำงานในส่วน else
        $structuredMenu[] = &$item;
        แปลว่า: "เอาแฟ้ม 'จัดการบทความ' ทั้งยวง ไปวางในตู้เอกสารหลัก ($structuredMenu)"
    - ภาพหลังจบรอบที่ 2:
        $structuredMenu มีสมาชิก 2 ตัวคือ 'แดชบอร์ด' และ 'จัดการบทความ'
        $structuredMenu ตอนนี้มี: [ 'แดชบอร์ด', 'จัดการบทความ' ]

    ... (รอบที่ 3 และ 4 สำหรับ 'จัดการผู้ใช้' และ 'ตั้งค่าระบบ' จะทำงานเหมือนกัน) ...

    รอบที่ 5: (รอบสำคัญ!) หยิบแฟ้ม 'บทความทั้งหมด' (id: 3)
    - $item ในรอบนี้คือ: ['id' => 3, 'parent_id' => 2, 'title' => 'บทความทั้งหมด', 'sub_menus' => []]
    - ตรวจสอบเงื่อนไข if:
        $item['parent_id'] มีค่าเป็น 2 (ไม่ใช่ NULL ดังนั้นส่วนนี้เป็น true)
        isset($itemsById[$item['parent_id']]) คือ isset($itemsById[2]) -> "ในกองแฟ้มของเรา มีแฟ้มเบอร์ 2 อยู่จริงหรือไม่?" ... "มีจริง!" (มันคือแฟ้ม 'จัดการบทความ') ดังนั้นส่วนนี้เป็น true
        เมื่อ true && true เงื่อนไข if เป็นจริง
    - การทำงาน: โปรแกรมจะทำงานในส่วนของ if
        $itemsById[$item['parent_id']]['sub_menus'][] = &$item;
        แปลว่า: "ไปที่กองแฟ้ม ($itemsById) -> หยิบ แฟ้มแม่ ที่มี id ตรงกับ parent_id ของฉัน (คือแฟ้มเบอร์ 2) -> เปิดช่อง sub_menus ของแฟ้มแม่นั้น -> แล้วเอา ตัวฉันเอง ('บทความทั้งหมด') ไปใส่ไว้ข้างใน"
    - ภาพหลังจบรอบที่ 5:
        $structuredMenu ยังคงเหมือนเดิม (มี 4 แฟ้มหลัก)
        แต่! $itemsById มีการเปลี่ยนแปลง! ถ้าเราเปิดดูแฟ้มเบอร์ 2 จะเห็นว่า:
        $itemsById[2] = [
        'id' => 2, 
        'parent_id' => NULL, 
        'title' => 'จัดการบทความ', 
        'sub_menus' => [ // <-- ช่องนี้ไม่ว่างแล้ว!
                0 => ['id' => 3, 'parent_id' => 2, 'title' => 'บทความทั้งหมด', 'sub_menus' => []]
            ]
        ];

    รอบที่ 6: หยิบแฟ้ม 'เขียนบทความใหม่' (id: 4)
    - $item ในรอบนี้คือ: ['id' => 4, 'parent_id' => 2, 'title' => 'เขียนบทความใหม่', 'sub_menus' => []]
    - ตรวจสอบเงื่อนไข if:
        parent_id คือ 2 ดังนั้นเงื่อนไขเป็น จริง (เหมือนรอบที่ 5)
    - การทำงาน: ทำงานในส่วนของ if
        $itemsById[$item['parent_id']]['sub_menus'][] = &$item;
        แปลว่า: "ไปที่แฟ้มแม่เบอร์ 2 แล้วเอาตัวฉัน ('เขียนบทความใหม่') ไปใส่ในช่อง sub_menus ของมัน"
    - ภาพหลังจบรอบที่ 6 (รอบสุดท้าย):
        $itemsById แฟ้มเบอร์ 2 จะถูกอัปเดตอีกครั้ง:
        $itemsById[2] = [
        'id' => 2, 
        'parent_id' => NULL, 
        'title' => 'จัดการบทความ', 
        'sub_menus' => [ 
                0 => ['id' => 3, 'parent_id' => 2, 'title' => 'บทความทั้งหมด', 'sub_menus' => []],
                1 => ['id' => 4, 'parent_id' => 2, 'title' => 'เขียนบทความใหม่', 'sub_menus' => []]
            ]
        ];

    สรุปผลหลังจบ foreach ทั้งหมด
    ตอนนี้ $structuredMenu ที่เป็นตู้เอกสารหลักของเรามีหน้าตาแบบนี้:
    $structuredMenu = [
    0 => // 'แดชบอร์ด'
    1 => // 'จัดการบทความ' ที่ตอนนี้ข้างในมี sub_menus ที่ถูกเติมเต็มแล้ว
    2 => // 'จัดการผู้ใช้'
    3 => // 'ตั้งค่าระบบ'
    ]

    แล้วทำไม sub_menus ใน $structuredMenu ถึงมีข้อมูลล่ะ?
    เพราะ & (Pass by Reference) ครับ!
    ตอนที่เราทำ else { $structuredMenu[] = &$item; } เราไม่ได้ "คัดลอก" แฟ้ม 'จัดการบทความ' ไปวาง แต่เรา "สร้างทางเชื่อม" ไปยังแฟ้มตัวจริงในกอง $itemsById
    ดังนั้น เมื่อแฟ้ม 'จัดการบทความ' ตัวจริงใน $itemsById ถูกแก้ไข (โดยการที่ลูกๆ ของมันวิ่งเข้ามาหา) 
    แฟ้มที่อยู่ในตู้ $structuredMenu ซึ่งเป็นแค่ "ทางเชื่อม" ก็จะเห็นการเปลี่ยนแปลงนั้นไปด้วยโดยอัตโนมัติ
    นี่คือหลักการที่ทรงพลังของ Loop นี้ครับ มันใช้ Array เดียว ($itemsById) เป็นทั้ง "กองแฟ้มที่รอจัด" และ "พื้นที่ทำงาน" ไปในตัว เพื่อสร้างโครงสร้างต้นไม้ที่สมบูรณ์ขึ้นมา 
    แล้วผลลัพธ์สุดท้ายก็คือ $structuredMenu ที่ชี้ไปยังแฟ้มหลักที่ถูกจัดเรียงเรียบร้อยแล้วนั่นเองครับ
    สรุปก็คือ
    การใช้ & (Pass by Reference) ทำให้เมื่อเราแก้ไข $itemsById[2] ใน loop มันคือการแก้ไขข้อมูล "ตัวจริง" ใน Array
    $itemsById จริงๆ ไม่ใช่การทำสำเนาขึ้นมาแก้ไข ทำให้โครงสร้างมันเชื่อมกันได้อย่างสมบูรณ์
    */
    unset($item);

    return $structuredMenu;
    /* 
        ข้อมูลในตัวแปร $structuredMenu ที่ถูก return ออกไป
        [
        0 => [
            'id' => 1, 'parent_id' => NULL, 'title' => 'แดชบอร์ด', 
            'sub_menus' => [] // ว่างเพราะไม่มีเมนูย่อย
        ],
        1 => [
            'id' => 2, 'parent_id' => NULL, 'title' => 'จัดการบทความ',
            'sub_menus' => [ // มีเมนูย่อยแล้ว!
                0 => ['id' => 3, 'parent_id' => 2, 'title' => 'บทความทั้งหมด', 'sub_menus' => []],
                1 => ['id' => 4, 'parent_id' => 2, 'title' => 'เขียนบทความใหม่', 'sub_menus' => []]
            ]
        ],
        2 => [... 'จัดการผู้ใช้' ...],
        3 => [... 'ตั้งค่าระบบ' ...]
        ]

        ข้อมูลที่ได้นี้อยู่ในรูปแบบ Array ซ้อน Array ที่สมบูรณ์ 
        พร้อมให้หน้า dashboard.php นำไปวน loop สร้างเป็นเมนู HTML ได้ทันที
    */
}