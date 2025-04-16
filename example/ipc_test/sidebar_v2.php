<!-- Brand Logo -->
<a href="index.php" class="brand-link">
    <img src="images/mypic.jpg" alt="Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
    <span class="brand-text font-weight-light">IPC</span>
</a>

<div class="row">
    <div class="accordion" id="accordionPanelsStayOpenExample">
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseOne" aria-expanded="false" aria-controls="panelsStayOpen-collapseOne">
                    Accordion Item #1
                </button>
            </h2>
            <div id="panelsStayOpen-collapseOne" class="accordion-collapse collapse">
                <div class="accordion-body">
                    <strong>This is the first item's accordion body.</strong> It is shown by default, until the collapse plugin adds the appropriate classes that we use to style each element. These classes control the overall appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go within the <code>.accordion-body</code>, though the transition does limit overflow.
                </div>
            </div>
        </div>

        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseTwo" aria-expanded="false" aria-controls="panelsStayOpen-collapseTwo">
                    Accordion Item #2
                </button>
            </h2>
            <div id="panelsStayOpen-collapseTwo" class="accordion-collapse collapse">
                <div class="accordion-body">
                    <strong>This is the second item's accordion body.</strong> It is hidden by default, until the collapse plugin adds the appropriate classes that we use to style each element. These classes control the overall appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go within the <code>.accordion-body</code>, though the transition does limit overflow.
                </div>
            </div>
        </div>

        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseThree" aria-expanded="false" aria-controls="panelsStayOpen-collapseThree">
                    Accordion Item #3
                </button>
            </h2>
            <div id="panelsStayOpen-collapseThree" class="accordion-collapse collapse">
                <div class="accordion-body">
                    <strong>This is the third item's accordion body.</strong> It is hidden by default, until the collapse plugin adds the appropriate classes that we use to style each element. These classes control the overall appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go within the <code>.accordion-body</code>, though the transition does limit overflow.
                </div>
            </div>
        </div>
    </div>
</div>

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
                        <a href="approval_status.php" class="nav-link text-dark">
                            <i class="nav-icon fi fi-rr-calendar-days d-none"></i>
                            <p>Approval status</p>
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
                        <a href="po.php" class="nav-link text-dark">
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
                        <a href="inspection.php" class="nav-link text-dark">
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

            <!-- Heading -->
            <!-- <div class="sidebar-heading">
                    Reports
                </div> -->

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


        </ul>
    </nav>
    <!-- /.sidebar-menu -->
</div>
<!-- /.sidebar -->