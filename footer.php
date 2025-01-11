<!-- Control Sidebar -->
<aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
</aside>
<!-- /.control-sidebar -->

<!-- Main Footer -->
<footer class="main-footer">
    <strong>Copyright &copy; by <a href="https://www.impact.co.th">IMPACT Exhibition Management Co.,Ltd</a>.</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
        <b>IPC Version 1.0.0</b>
    </div>
</footer>
</div>
<!-- ./wrapper -->

<!-- Scroll to Top Button-->
<!-- <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a> -->

<!-- REQUIRED SCRIPTS -->
<!-- jQuery -->
<script src="plugins/jQuery-3.7.1/jquery-3.7.1.min.js"></script>
<!-- Bootstrap 4 -->
<!-- <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script> -->
<!-- Bootstrap 4.6.2 add by Poj-->
<!-- <script src="plugins/bootstrap-4.6.2-dist/js/bootstrap.bundle.min.js"></script> -->
<!-- Bootstrap 5.3.3 add by Poj-->
<!-- <script src="plugins/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script> -->
<!-- overlayScrollbars -->
<script src="plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- Summernote -->
<script src="plugins/summernote/summernote-bs4.min.js"></script>
<!-- AdminLTE App -->
<script src="plugins/dist/js/adminlte.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="plugins/dist/js/pages/dashboard.js"></script>
<!-- DataTables  & plugins -->
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/DataTables/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="plugins/DataTables/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="plugins/DataTables/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="plugins/DataTables/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="plugins/DataTables/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="plugins/jszip/jszip.min.js"></script>
<script src="plugins/pdfmake/pdfmake.min.js"></script>
<script src="plugins/pdfmake/vfs_fonts.js"></script>
<script src="plugins/DataTables/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="plugins/DataTables/datatables-buttons/js/buttons.print.min.js"></script>
<script src="plugins/DataTables/datatables-buttons/js/buttons.colVis.min.js"></script>

<!-- <script src="plugins/jszip/jszip.min.js"></script> -->
<script src="plugins/pdfmake/pdfmake.min.js"></script>
<script src="plugins/pdfmake/vfs_fonts.js"></script>
<!-- Sweet Alert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- <script src="//cdn.jsdelivr.net/npm/sweetalert2@11" ></script> -->
<!-- Page specific script -->

<!-- jQuery Timepicker -->
<script src="plugins/jquery-timepicker-1.3.5/jquery.timepicker-master.min.js"></script>

<!-- Bootstrap core JavaScript-->
<!-- <script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script> -->

<!-- Core plugin JavaScript-->
<!-- <script src="vendor/jquery-easing/jquery.easing.min.js"></script> -->

<!-- Custom scripts for all pages-->
<!-- <script src="../js/sb-admin-2.min.js"></script> -->
<!-- AdminLTE App -->
<!-- <script src="../dist/js/adminlte.js"></script> -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script>
    $(function() {
        $('.time').timepicker({
            minTime: $('#test').val(),
            maxTime: '18:00',
            timeFormat: 'H:i',
            show2400: true,
            step: 60,
            closeOnScroll: true,
            // orientation: 'c',
            disableTextInput: true
        });

    });
</script>
<script>
    $(function() {
        $("#example1").DataTable({
            "responsive": true, //  true=การรองรับอุปกรณ์
            "lengthChange": true, //true ให้เลือกหน้าได้ว่าใน 1 หน้าต้องการให้แสดงกี่ row(มี 10, 25, 50, 100)
            "autoWidth": false, //กำหนดความกว้างอัตโนมัติ
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
        $('#example2').DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "paging": true,
            "searching": false,
            "ordering": true,
            "info": true,
        });
    });
</script>
<script>
    $(function() {
        // Summernote
        $("#summernote").summernote();

        // CodeMirror
        CodeMirror.fromTextArea(document.getElementById("codeMirrorDemo"), {
            mode: "htmlmixed",
            theme: "monokai",
        });
    });
</script>