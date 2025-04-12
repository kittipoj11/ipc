<?php
session_start();

require_once  'class/menu_class.php';
$role_id = isset($_SESSION['role_id']) ? $_SESSION['role_id'] : 1; // Default role เป็น 1
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 1; // Default role เป็น 1

$menu = new Menu;
$rsMenu = $menu->getMenuByUsername($username);

// ในระบบจริง คุณจะต้องดึง role_id จาก session
// นี่คือการจำลอง role_id เพื่อให้ทดสอบได้
// $role_id = 1; // สมมติผู้ใช้มีบทบาท ID เป็น 1

$menus = array();

foreach ($rsMenu as $row) {
    // $menus[$row['menu_name']] = array('menu_name' => $row['menu_name'], 'link' => '#', 'content_filename' => $row['content_filename'], 'function_name' => $row['function_name']);
    $menus[$row['menu_name']] = array('menu_name' => $row['menu_name'], 'link' => '#', 'content_filename' => $row['content_filename'], 'function_name' => $row['function_name'], 'permission_name' => $row['permission_name'], 'menu_display' => $row['menu_display']);
}
?>

<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index.php" class="brand-link">
        <img src="images/mypic.jpg" alt="Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">IPC</span>
    </a>


    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="true">
                <!-- Nav Item - หน้าหลัก -->
                <li class="nav-item">
                    <a href="index.php" class="nav-link">
                        <i class="nav-icon fas fa-landmark"></i>
                        <p>
                            หน้าหลัก
                        </p>
                    </a>
                </li>

                <!-- Divider -->
                <hr class="sidebar-divider">

                <!-- Nav Item - Today Activities -->
                <!-- <li class="nav-item">
                    <a href="index.php" class="nav-link">
                        <i class="nav-icon fas fa-file"></i>
                        <i class="nav-icon fas fi-rr-chart-tree-map"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li> -->

                <!-- Divider -->
                <!-- <hr class="sidebar-divider"> -->

                <!-- Nav Item - ข้อมูลระบบ -->
                <li class="nav-item <?php echo $menus['system']['menu_display'] ?>">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-database"></i>
                        <p>
                            <?php echo $menus['system']['permission_name'] ?>
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview bg-primary text-white py-2 collapse-inner rounded">
                        <li class="nav-item">
                            <a href="po_status.php" class="nav-link text-dark">
                                <i class="nav-icon fa-solid fa-truck d-none"></i>
                                <p>PO status</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="inspection_status.php" class="nav-link text-dark">
                                <i class="nav-icon fa-solid fa-truck d-none"></i>
                                <p>Inspection status</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="plan_status.php" class="nav-link text-dark">
                                <i class="nav-icon fa-solid fa-city d-none"></i>
                                <p>Plan Status</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="approval_status.php" class="nav-link text-dark">
                                <i class="nav-icon fi fi-rr-calendar-days d-none"></i>
                                <p>Approval status</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <!-- Nav Item - ข้อมูลพื้นฐาน -->
                <li class="nav-item <?php echo $menus['general_basic']['menu_display'] ?>">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-database"></i>
                        <p>
                            <?php echo $menus['general_basic']['permission_name'] ?>
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview bg-primary text-white py-2 collapse-inner rounded">
                        <li class="nav-item">
                            <a href="location.php" class="nav-link text-dark">
                                <i class="nav-icon fa-solid fa-truck d-none"></i>
                                <p>Location</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="department.php" class="nav-link text-dark">
                                <i class="nav-icon fa-solid fa-city d-none"></i>
                                <p>Department</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="supplier.php" class="nav-link text-dark">
                                <i class="nav-icon fa-solid fa-grip d-none"></i>
                                <p>Suppliers</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="workflows.php" class="nav-link text-dark">
                                <i class="nav-icon fi fi-rr-calendar-clock d-none"></i>
                                <p>Workflows (รูปแบบ workflow)</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Nav Item - Approval(การอนุมัติ) -->
                <li class="nav-item <?php echo $menus['system']['menu_display'] ?>">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-database"></i>
                        <p>
                            รูปแบบการอนุมัติ
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview bg-primary text-white py-2 collapse-inner rounded">


                        <li class="nav-item d-none">
                            <a href="workflow_steps.php" class="nav-link text-dark">
                                <i class="nav-icon fi fi-rr-calendar-days d-none"></i>
                                <p>workflow_steps (กำหนดระดับการอนุมัติในแต่ละ Workflow) ***admin</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="workflows.php" class="nav-link text-dark">
                                <i class="nav-icon fi fi-rr-calendar-clock d-none"></i>
                                <p>Approval Workflow (กำหนดรูปแบบและระดับการอนุมัติในแต่ละ Workflow) ***admin</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Nav Item - Purchase Order -->
                <li class="nav-item <?php echo $menus['purchase_order']['menu_display'] ?>">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-database"></i>
                        <p>
                            <?php echo $menus['purchase_order']['permission_name'] ?>
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview bg-primary text-white py-2 collapse-inner rounded">
                        <li class="nav-item">
                            <a href="po.php" class="nav-link text-dark">
                                <i class="nav-icon fi fi-rr-calendar-days d-none"></i>
                                <p>All purchase orders</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Nav Item - Inspection(การตรวจรับงาน) -->
                <li class="nav-item <?php echo $menus['inspection']['menu_display'] ?>">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-database"></i>
                        <p>
                            <?php echo $menus['inspection']['permission_name'] ?>
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview bg-primary text-white py-2 collapse-inner rounded">
                        <li class="nav-item">
                            <a href="inspection.php" class="nav-link text-dark">
                                <i class="nav-icon fi fi-rr-calendar-days d-none"></i>
                                <p>All Inspection</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Nav Item - IPC -->
                <li class="nav-item <?php echo $menus['ipc']['menu_display'] ?>">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-database"></i>
                        <p>
                            <?php echo $menus['ipc']['permission_name'] ?>
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview bg-primary text-white py-2 collapse-inner rounded">
                        <li class="nav-item">
                            <a href="ipc.php" class="nav-link text-dark">
                                <i class="nav-icon fi fi-rr-calendar-days d-none"></i>
                                <p>All IPC</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Nav Item - Assign to me -->
                <!-- <li class="nav-item"> -->
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-database"></i>
                        <p>
                            Assign to me
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview bg-primary text-white py-2 collapse-inner rounded">
                        <li class="nav-item">
                            <a href="inspection_assigned.php" class="nav-link text-dark">
                                <i class="nav-icon fi fi-rr-calendar-days d-none"></i>
                                <p>Lists</p>
                            </a>
                            <a href="inspection_action.php?po_id=1&period_id=1&inspection_id=1" class="nav-link text-dark">
                                <i class="nav-icon fi fi-rr-calendar-days d-none"></i>
                                <p>Lists</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Nav Item - Student -->
                <li class="nav-item <?php echo $menus['manage_user']['menu_display'] ?>">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fa-solid fa-user-gear"></i>
                        <p>
                            <?php echo $menus['manage_user']['permission_name'] ?>
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview bg-primary text-white py-2 collapse-inner rounded">
                        <li class="nav-item">
                            <a href="role.php?page=table" class="nav-link text-dark">
                                <i class="nav-icon fa-solid fa-users"></i>
                                <p>roles</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="user.php?page=table" class="nav-link text-dark">
                                <i class="nav-icon fa-solid fa-users"></i>
                                <p>Users</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <!-- Divider -->
                <hr class="sidebar-divider">

                <!-- Heading -->
                <!-- <div class="sidebar-heading">
                    Reports
                </div> -->

                <!-- Nav Item - รายงาน -->
                <li class="nav-item <?php echo $menus['report']['menu_display'] ?>">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fa-regular fa-file-lines"></i>
                        <p>
                            <?php echo $menus['report']['permission_name'] ?>
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview bg-primary text-white py-2 collapse-inner rounded">
                        <li class="nav-item">
                            <a href="401report.php" class="nav-link text-dark">
                                <!-- <i class="nav-icon fa-solid fa-users"></i> -->
                                <p>รายงาน...</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="402report.php" class="nav-link text-dark">
                                <!-- <i class="nav-icon fa-solid fa-user-check"></i> -->
                                <p>รายงาน...</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="403report.php" class="nav-link text-dark">
                                <!-- <i class="nav-icon fa-solid fa-user-check"></i> -->
                                <p>รายงาน...</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="404report.php" class="nav-link text-dark">
                                <!-- <i class="nav-icon fa-solid fa-user-check"></i> -->
                                <p>รายงาน...</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="rpt_summary.php" class="nav-link text-dark">
                                <!-- <i class="nav-icon fa-solid fa-user-check"></i> -->
                                <p>รายงาน...</p>
                            </a>
                        </li>
                    </ul>
                </li>


            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>