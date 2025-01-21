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
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-database"></i>
                        <p>
                            ข้อมูลระบบ
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview bg-primary text-white py-2 collapse-inner rounded">
                        <li class="nav-item">
                            <a href="inspect_status.php" class="nav-link text-dark">
                                <i class="nav-icon fa-solid fa-truck d-none"></i>
                                <p>Inspection status(สถานะการตรวจสอบ) ***system only</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="plan_status.php" class="nav-link text-dark">
                                <i class="nav-icon fa-solid fa-city d-none"></i>
                                <p>Plan Status(สถานะแผนงาน) ***system only</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <!-- Nav Item - ข้อมูลพื้นฐาน -->
                <li class="nav-item">
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
                    </ul>
                </li>

                <!-- Nav Item - Approval(การอนุมัติ) -->
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-database"></i>
                        <p>
                            ข้อมูลการอนุมัติ
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview bg-primary text-white py-2 collapse-inner rounded">
                        <li class="nav-item">
                            <a href="approval_status.php" class="nav-link text-dark">
                                <i class="nav-icon fi fi-rr-calendar-days d-none"></i>
                                <p>Approval status (สถานะในการอนุมัติ) ***system only</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="approval_workflow.php?page=table" class="nav-link text-dark">
                                <i class="nav-icon fi fi-rr-calendar-clock d-none"></i>
                                <p>approval_workflow (รูปแบบการอนุมัติ) ***admin</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="approval_levels.php" class="nav-link text-dark">
                                <i class="nav-icon fi fi-rr-calendar-days d-none"></i>
                                <p>approval_levels (กำหนดระดับการอนุมัติในแต่ละ Workflow) ***admin</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Nav Item - Inspection(การตรวจรับงาน) -->
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-database"></i>
                        <p>
                            Inspection
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
                        <li class="nav-item">
                            <a href="inspection.php" class="nav-link text-dark">
                                <i class="nav-icon fi fi-rr-calendar-days d-none"></i>
                                <p>All Inspection</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="approval_workflow.php?page=table" class="nav-link text-dark">
                                <i class="nav-icon fi fi-rr-calendar-clock d-none"></i>
                                <p>Inspect status (สถานะการตรวจสอบ) ***system only</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="approval_levels.php" class="nav-link text-dark">
                                <i class="nav-icon fi fi-rr-calendar-days d-none"></i>
                                <p>Plan status (สถานะแผน) ***system only</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Nav Item - Student -->
                <li class="nav-item">
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

                <!-- Heading -->
                <div class="sidebar-heading">
                    Reports
                </div>

                <!-- Nav Item - รายงาน -->
                <li class="nav-item">
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


            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>