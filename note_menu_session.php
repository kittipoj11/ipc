<?php
// เริ่มต้นการใช้งาน session
session_start();

// --- 1. การกำหนดค่าให้กับ Session ---
// สร้าง Array หลักสำหรับเก็บสิทธิ์ของเมนูทั้งหมด
$_SESSION['menu_permissions'] = [
  'A' => [
    'visible' => true,
    'url' => '/page-a.php',
    'icon' => 'fa-home',
    'sub_menus' => [
      'A1' => [
        'visible' => true,
        'url' => '/page-a1.php',
        'icon' => 'page-a1'
      ],
      'A2' => [
        'visible' => true,
        'url' => '/page-a2.php',
        'icon' => 'page-a2'
      ],
      'A3' => [
        'visible' => false,
        'url' => '/page-a3.php',
        'icon' => 'page-a3'
      ],
      'A4' => [
        'visible' => true,
        'url' => '/page-a4.php',
        'icon' => 'page-a4'
      ],
    ]
  ],
  'B' => [
    'visible' => true,
    'sub_menus' => [
      'B1' => [
        'visible' => true,
        'url' => '/page-b1.php',
        'icon' => 'page-b1'
      ],
      'B2' => [
        'visible' => true,
        'url' => '/page-b2.php',
        'icon' => 'page-b2'
      ],
      'B3' => [
        'visible' => false,
        'url' => '/page-b3.php',
        'icon' => 'page-b3'
      ],
    ]
  ],
  'C' => [
    'visible' => false,
    'sub_menus' => [
      'C1' => [
        'visible' => true,
        'url' => '/page-c1.php',
        'icon' => 'page-c1'
      ],
      'C2' => [
        'visible' => true,
        'url' => '/page-c2.php',
        'icon' => 'page-c2'
      ],
      'C3' => [
        'visible' => false,
        'url' => '/page-c3.php',
        'icon' => 'page-c3'
      ],
    ]
  ],
  'D' => [
    'visible' => true,
    'sub_menus' => [
      'D1' => [
        'visible' => true,
        'url' => '/page-d1.php',
        'icon' => 'page-d1'
      ],
      'D2' => [
        'visible' => true,
        'url' => '/page-d2.php',
        'icon' => 'page-d2'
      ],
      'D3' => [
        'visible' => false,
        'url' => '/page-d3.php',
        'icon' => 'page-d3'
      ],
    ]
  ],
];


// --- 2. ตัวอย่างการนำไปใช้งาน (การตรวจสอบสิทธิ์) ---

echo "<h1>ตรวจสอบสิทธิ์การเข้าถึงเมนู</h1>";

// ตรวจสอบเมนูหลัก A
if ($_SESSION['menu_permissions']['A']['visible']) {
  echo "เมนู A: แสดงผลได้<br>";

  // ตรวจสอบเมนูย่อย A2
  if ($_SESSION['menu_permissions']['A']['sub_menus']['A2']['visible']) {
    echo "- เมนูย่อย A2: แสดงผลได้<br>";
  } else {
    echo "- เมนูย่อย A2: ไม่สามารถแสดงผลได้<br>";
  }

  // ตรวจสอบเมนูย่อย A3
  if ($_SESSION['menu_permissions']['A']['sub_menus']['A3']['visible']) {
    echo "- เมนูย่อย A3: แสดงผลได้<br>";
  } else {
    echo "- เมนูย่อย A3: ไม่สามารถแสดงผลได้<br>";
  }
}
echo "<hr>";

// ตรวจสอบเมนูหลัก C
if ($_SESSION['menu_permissions']['C']['visible']) {
  echo "เมนู C: แสดงผลได้<br>";
} else {
  echo "เมนู C: ไม่สามารถแสดงผลได้ (เพราะ 'visible' เป็น false)<br>";
}
echo "<hr>";


// ตัวอย่างการสร้างเมนูแบบไดนามิก
echo "<h2>ตัวอย่างการสร้างเมนู</h2>";
echo "<ul>";

foreach ($_SESSION['menu_permissions'] as $menu_key => $menu_data) {
  // ตรวจสอบก่อนว่าเมนูหลักแสดงได้หรือไม่
  if ($menu_data['visible']) {
    echo "<li><b>เมนู $menu_key</b>";

    // ถ้ามีเมนูย่อย ให้สร้าง sub-list
    if (!empty($menu_data['sub_menus'])) {
      echo "<ul>";
      foreach ($menu_data['sub_menus'] as $submenu_key => $submenu_visible) {
        if ($submenu_visible) {
          echo "<li>เมนูย่อย $submenu_key (แสดง)</li>";
        } else {
          // ปกติถ้า false เราจะไม่แสดงเมนูนั้นเลย
          // echo "<li>เมนูย่อย $submenu_key (ซ่อน)</li>";
        }
      }
      echo "</ul>";
    }

    echo "</li>";
  }
}

echo "</ul>";
