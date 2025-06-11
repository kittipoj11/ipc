<?php
// เริ่มต้นการใช้งาน session
session_start();

// --- 1. การกำหนดค่าให้กับ Session (โครงสร้างใหม่) ---
// ตอนนี้ทั้งเมนูหลักและเมนูย่อยจะมีโครงสร้างเป็น Array ที่เก็บคุณสมบัติต่างๆ
$_SESSION['user_menu'] = [
    'dashboard' => [
        'visible' => true,
        'title' => 'แดชบอร์ด',
        'url' => '/dashboard.php',
        'icon' => 'fa-solid fa-house', // ใช้ class จาก Font Awesome เป็นตัวอย่าง
        'sub_menus' => [] // ไม่มีเมนูย่อย
    ],
    'products' => [
        'visible' => true,
        'title' => 'จัดการสินค้า',
        'url' => '#', // เมนูหลักที่มีเมนูย่อยมักจะใส่ #
        'icon' => 'fa-solid fa-box-archive',
        'sub_menus' => [
            'product_list' => [
                'visible' => true,
                'title' => 'รายการสินค้าทั้งหมด',
                'url' => '/products/list.php',
                'icon' => 'fa-solid fa-list'
            ],
            'product_add' => [
                'visible' => true,
                'title' => 'เพิ่มสินค้าใหม่',
                'url' => '/products/add.php',
                'icon' => 'fa-solid fa-plus'
            ],
            'categories' => [
                'visible' => false, // ซ่อนเมนูนี้ไว้
                'title' => 'หมวดหมู่สินค้า',
                'url' => '/products/categories.php',
                'icon' => 'fa-solid fa-tags'
            ]
        ]
    ],
    'reports' => [
        'visible' => true,
        'title' => 'รายงาน',
        'url' => '#',
        'icon' => 'fa-solid fa-chart-line',
        'sub_menus' => [
            'sales_report' => [
                'visible' => true,
                'title' => 'รายงานยอดขาย',
                'url' => '/reports/sales.php',
                'icon' => 'fa-solid fa-dollar-sign'
            ],
            'stock_report' => [
                'visible' => true,
                'title' => 'รายงานสต็อก',
                'url' => '/reports/stock.php',
                'icon' => 'fa-solid fa-warehouse'
            ]
        ]
    ],
    'settings' => [
        'visible' => false, // ซ่อนเมนูตั้งค่าทั้งเมนู
        'title' => 'ตั้งค่าระบบ',
        'url' => '/settings.php',
        'icon' => 'fa-solid fa-gears',
        'sub_menus' => []
    ]
];

?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ตัวอย่างเมนูจาก Session</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        body { font-family: sans-serif; }
        .side-menu { list-style: none; padding: 0; width: 250px; }
        .side-menu li a {
            display: block;
            padding: 10px 15px;
            text-decoration: none;
            color: #333;
        }
        .side-menu li a:hover { background-color: #f0f0f0; }
        .side-menu .icon { margin-right: 10px; }
        .side-menu ul { list-style: none; padding-left: 30px; }
    </style>
</head>
<body>

    <h1>เมนูของระบบ</h1>
    <nav>
        <ul class="side-menu">
            <?php foreach ($_SESSION['user_menu'] as $menu_key => $menu_data): ?>
                <?php if ($menu_data['visible']): // ตรวจสอบว่าเมนูหลักแสดงได้หรือไม่ ?>
                    <li>
                        <a href="<?php echo htmlspecialchars($menu_data['url']); ?>">
                            <i class="icon <?php echo htmlspecialchars($menu_data['icon']); ?>"></i>
                            <?php echo htmlspecialchars($menu_data['title']); ?>
                        </a>

                        <?php // ตรวจสอบว่ามีเมนูย่อยและไม่เป็น Array ว่าง ?>
                        <?php if (!empty($menu_data['sub_menus'])): ?>
                            <ul>
                                <?php foreach ($menu_data['sub_menus'] as $submenu_key => $submenu_data): ?>
                                    <?php if ($submenu_data['visible']): // ตรวจสอบว่าเมนูย่อยแสดงได้หรือไม่ ?>
                                        <li>
                                            <a href="<?php echo htmlspecialchars($submenu_data['url']); ?>">
                                                <i class="icon <?php echo htmlspecialchars($submenu_data['icon']); ?>"></i>
                                                <?php echo htmlspecialchars($submenu_data['title']); ?>
                                            </a>
                                        </li>
                                    <?php endif; // จบ if ของเมนูย่อย ?>
                                <?php endforeach; // จบ loop ของเมนูย่อย ?>
                            </ul>
                        <?php endif; // จบ if ตรวจสอบเมนูย่อย ?>
                    </li>
                <?php endif; // จบ if ของเมนูหลัก ?>
            <?php endforeach; // จบ loop ของเมนูหลัก ?>
        </ul>
    </nav>

</body>
</html>