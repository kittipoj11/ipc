<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index.php" class="brand-link">
        <img src="../dist/img/mypic.jpg" alt="Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">Car Staging</span>
    </a>


    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="true">
                <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
                <!-- Nav Item - หน้าหลัก -->
                <li class="nav-item">
                    <a href="index.php" class="nav-link">
                        <i class="nav-icon fas fa-landmark"></i>
                        <!-- <i class="nav-icon fi fi-rr-house-blank"></i> -->
                        <p>
                            หน้าหลัก
                        </p>
                    </a>
                </li>

                <!-- Divider -->
                <hr class="sidebar-divider">

                <!-- Nav Item - Today Activities -->
                <li class="nav-item">
                    <a href="001today.php" class="nav-link">
                        <!-- <i class="nav-icon fas fa-file"></i> -->
                        <i class="nav-icon fas fi-rr-chart-tree-map"></i>
                        <p>
                            Today Activities
                            <!-- <i class="nav-icon right fas fa-angle-left"></i> -->
                        </p>
                    </a>

                </li>

                <!-- Divider -->
                <hr class="sidebar-divider">

                <!-- Nav Item - ข้อมูลพื้นฐาน -->
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-database"></i>
                        <p>
                            ข้อมูลพื้นฐาน
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <!-- <ul class="nav nav-treeview"> -->
                    <ul class="nav nav-treeview bg-primary text-white py-2 collapse-inner rounded">
                        <li class="nav-item">
                            <a href="101category.php" class="nav-link text-dark">
                                <i class="nav-icon fa-solid fa-truck"></i>
                                <p>ประเภทวิชา</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="102grade.php" class="nav-link text-dark">
                                <i class="nav-icon fa-solid fa-city"></i>
                                <p>เกรด</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="103Department.php" class="nav-link text-dark">
                                <i class="nav-icon fa-solid fa-grip"></i>
                                <p>ภาควิชา</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="105Semester.php" class="nav-link text-dark">
                                <i class="nav-icon fa-solid fa-grip"></i>
                                <p>ภาคการเรียน</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="104Subject.php" class="nav-link text-dark">
                                <i class="nav-icon fa-solid fa-grip"></i>
                                <p>วิชาเรียน</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <!-- Nav Item - ภาคการศึกษา -->
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-fw fa-truck-loading"></i>
                        <p>
                            การจัดการศึกษา
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <!-- <ul class="nav nav-treeview"> -->
                    <ul class="nav nav-treeview bg-primary text-white py-2 collapse-inner rounded">
                        <li class="nav-item">
                            <a href="201education_plan.php?page=table" class="nav-link text-dark">
                                <i class="nav-icon fi fi-rr-calendar-clock"></i>
                                <p>แผนการเรียน</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="202booking_list.php" class="nav-link text-dark">
                                <i class="nav-icon fi fi-rr-calendar-days"></i>
                                <p>รายการการจอง</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="203booking_history.php" class="nav-link text-dark">
                                <i class="nav-icon fi fi-rr-calendar-days"></i>
                                <p>ประวัติการจอง</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="204live.php" class="nav-link text-dark">
                                <i class="nav-icon fi fi-rr-megaphone"></i>
                                <p>เรียกคิว</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Nav Item - Student -->
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fa-solid fa-user-gear"></i>
                        <p>
                            นักศึกษา
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <!-- <ul class="nav nav-treeview"> -->
                    <ul class="nav nav-treeview bg-primary text-white py-2 collapse-inner rounded">
                        <li class="nav-item">
                            <a href="301student.php?page=table" class="nav-link text-dark">
                                <i class="nav-icon fa-solid fa-users"></i>
                                <p>ชื่อนักศึกษา</p>
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
                    <!-- <ul class="nav nav-treeview"> -->
                    <ul class="nav nav-treeview bg-primary text-white py-2 collapse-inner rounded">
                        <li class="nav-item">
                            <a href="401report.php" class="nav-link text-dark">
                                <!-- <i class="nav-icon fa-solid fa-users"></i> -->
                                <p>รายงานจำนวนการจองรายวันแยกตามอีเวนต์ในแต่ละช่วงเวลา</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="402report.php" class="nav-link text-dark">
                                <!-- <i class="nav-icon fa-solid fa-user-check"></i> -->
                                <p>รายงานการจองรายวันตามวันที่จอง</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="403report.php" class="nav-link text-dark">
                                <!-- <i class="nav-icon fa-solid fa-user-check"></i> -->
                                <p>รายงานจำนวนรถตามวันในแต่ละช่วงเวลา</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="404report.php" class="nav-link text-dark">
                                <!-- <i class="nav-icon fa-solid fa-user-check"></i> -->
                                <p>รายงานจำนวนการจองของรถแต่ละประเภทแยกตามอีเวนต์</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="rpt_summary.php" class="nav-link text-dark">
                                <!-- <i class="nav-icon fa-solid fa-user-check"></i> -->
                                <p>รายงานสรุปการจองตามช่วงวัน-เวลา</p>
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