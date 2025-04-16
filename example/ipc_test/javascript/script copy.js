$(document).ready(function() {
    const sidebarMenu = $('#sidebar-menu');
    const contentDiv = $('#content');

    // ฟังก์ชันสำหรับโหลดเมนูเริ่มต้น
    function loadSidebarMenu() {
        $.ajax({
            url: 'sidebar.php',
            type: 'GET',
            success: function(data) {
                $('#main-sidebar').html(data);
                attachMenuClickListeners();
            }
        });
    }

    // ฟังก์ชันสำหรับเพิ่ม Event Listener ให้กับลิงก์เมนู
    function attachMenuClickListeners() {
        // sidebarMenu.on('click', 'a', function(event) {
        $('#sidebar-menu').on('click', 'a', function(event) {
            event.preventDefault();
            const content_filename = $(this).data('content_filename');
            const function_name = $(this).data('function_name');
            if (content_filename) {
                loadContent(content_filename, function_name);
            }
        });
    }

    // ฟังก์ชันสำหรับโหลดเนื้อหาและเรียกฟังก์ชัน init ที่เกี่ยวข้อง
    function loadContent(content_filename, function_name) {
        $.ajax({
            url: content_filename,
            type: 'GET',
            success: function(data) {
                // contentDiv.html(data);
                $("#content").html(data);
                if (window[function_name] && typeof window[function_name] === 'function') {
                    window[function_name](); // เรียกฟังก์ชัน function_name ตามชื่อที่ระบุใน data-function_name
                }
            }
        });
    }

    // โหลดเมนูเริ่มต้นเมื่อ DOM พร้อมใช้งาน
    loadSidebarMenu();
    
    // โหลดหน้าเริ่มต้นเมื่อ DOM พร้อมใช้งาน
    // loadContent("dashboard2.php","dashboard2")
});
