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
            <ul class="nav nav-pills nav-sidebar flex-column d-none">
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

                <!-- Nav Item - ข้อมูลระบบ -->
                <li class="nav-item <?php echo $_SESSION['system'] ?>">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-database"></i>
                        <p>
                            ข้อมูลระบบ
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
                            <a href="approval_type.php" class="nav-link text-dark">
                                <i class="nav-icon fi fi-rr-calendar-days d-none"></i>
                                <p>Approval type</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="approval_status.php" class="nav-link text-dark">
                                <i class="nav-icon fi fi-rr-calendar-days d-none"></i>
                                <p>Approval status</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="permission.php" class="nav-link text-dark">
                                <i class="nav-icon fi fi-rr-calendar-days d-none"></i>
                                <p>Permission</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Nav Item - ข้อมูลพื้นฐาน -->
                <li class="nav-item <?php echo $_SESSION['general_basic'] ?>">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-database"></i>
                        <p>
                            ข้อมูลพื้นฐาน
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
                            <a href="basic/department.php" class="nav-link text-dark">
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
                <li class="nav-item">
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
                <li class="nav-item <?php echo $_SESSION['purchase_order'] ?>">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-database"></i>
                        <p>
                            Purchase Order
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview bg-primary text-white py-2 collapse-inner rounded">
                        <li class="nav-item">
                            <a href="po_list.php" class="nav-link text-dark">
                                <i class="nav-icon fi fi-rr-calendar-days d-none"></i>
                                <p>All purchase orders</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Nav Item - Inspection(การตรวจรับงาน) -->
                <li class="nav-item <?php echo $_SESSION['inspection'] ?>">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-database"></i>
                        <p>
                            Inspection
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview bg-primary text-white py-2 collapse-inner rounded">
                        <li class="nav-item">
                            <a href="inspection_list.php" class="nav-link text-dark">
                                <i class="nav-icon fi fi-rr-calendar-days d-none"></i>
                                <p>All Inspection</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Nav Item - IPC -->
                <li class="nav-item <?php echo $_SESSION['ipc'] ?>">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-database"></i>
                        <p>
                            IPC
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
                <!-- <li class="nav-item <?php echo $_SESSION['assign_to_me'] ?>"> -->
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
                <li class="nav-item <?php echo $_SESSION['manage_user'] ?>">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fa-solid fa-user-gear"></i>
                        <p>
                            Users
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

                <!-- Nav Item - รายงาน -->
                <li class="nav-item <?php echo $_SESSION['report'] ?>">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fa-regular fa-file-lines"></i>
                        <p>
                            รายงาน
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

                <!-- Divider -->
                <hr class="sidebar-divider">

                <!-- Nav Item - รายงาน -->
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fa-regular fa-file-lines"></i>
                        <p>
                            Roles & Menus
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview bg-primary text-white py-2 collapse-inner rounded">
                        <?php if ($_SESSION['role_name'] === 'System Admin'): ?>
                            <li>
                                <a href="admin/manage_roles.php" class="nav-link text-dark">
                                    <i class="icon fa-solid fa-user-shield"></i>
                                    <p>จัดการสิทธิ์ (Roles)</p>
                                </a>
                            </li>
                            <li>
                                <a href="admin/manage_menus.php" class="nav-link text-dark">
                                    <i class="icon fa-solid fa-list-check"></i>
                                    <p>จัดการเมนู (Menus)</p>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </li>

                <!-- Divider -->
                <hr class="sidebar-divider">

                <li class="nav-item">
                    <a href="logout.php" class="nav-link">
                        <i class="nav-icon fa-solid fa-right-from-bracket"></i>
                        <p>ออกจากระบบ</p>
                    </a>
                </li>
            </ul>

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

                <?php foreach ($_SESSION['user_menu'] as $menu_data):
                ?>
                    <li class="nav-item">
                        <a href="<?php echo htmlspecialchars($menu_data['url']); ?>" class="nav-link">
                            <i class="nav-icon <?php echo htmlspecialchars($menu_data['icon']); ?>"></i>
                            <span><?php echo htmlspecialchars($menu_data['title']); ?></span>
                        </a>
                        <?php if (!empty($menu_data['sub_menus'])): ?>
                            <ul class="nav nav-treeview bg-primary text-white py-2 collapse-inner rounded">
                                <?php foreach ($menu_data['sub_menus'] as $submenu_data): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo htmlspecialchars($submenu_data['url']); ?>" class="nav-link text-dark">
                                            <i class="nav-icon <?php echo htmlspecialchars($submenu_data['icon']); ?>"></i>
                                            <p><?php echo htmlspecialchars($submenu_data['title']); ?></p>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>

                <!-- Divider -->
                <hr class="sidebar-divider">

                <!-- Nav Item - รายงาน -->
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fa-regular fa-file-lines"></i>
                        <p>
                            Roles & Menus
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview bg-primary text-white py-2 collapse-inner rounded">
                        <?php if ($_SESSION['role_name'] === 'System Admin'): ?>
                            <li>
                                <a href="admin/manage_roles.php" class="nav-link text-dark">
                                    <i class="icon fa-solid fa-user-shield"></i>
                                    <p>จัดการสิทธิ์ (Roles)</p>
                                </a>
                            </li>
                            <li>
                                <a href="admin/manage_menus.php" class="nav-link text-dark">
                                    <i class="icon fa-solid fa-list-check"></i>
                                    <p>จัดการเมนู (Menus)</p>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </li>

                <!-- Divider -->
                <hr class="sidebar-divider">

                <li class="nav-item">
                    <a href="logout.php" class="nav-link">
                        <i class="nav-icon fa-solid fa-right-from-bracket"></i>
                        <p>ออกจากระบบ</p>
                    </a>
                </li>

            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>