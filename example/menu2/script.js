$(document).ready(function() {
    const sidebarMenu = $('#sidebar-menu');
    const contentDiv = $('#content');

    // ฟังก์ชันสำหรับโหลดเมนูเริ่มต้น
    function loadSidebarMenu() {
        $.ajax({
            url: 'get_sidebar_menu.php',
            type: 'GET',
            success: function(data) {
                sidebarMenu.html(data);
                attachMenuClickListeners();
            }
        });
    }

    // ฟังก์ชันสำหรับโหลดเนื้อหาและเรียกฟังก์ชัน init ที่เกี่ยวข้อง
    function loadContent(contentFile, initScript) {
        $.ajax({
            url: contentFile,
            type: 'GET',
            success: function(data) {
                contentDiv.html(data);
                if (window[initScript] && typeof window[initScript] === 'function') {
                    window[initScript](); // เรียกฟังก์ชัน init ตามชื่อที่ระบุใน data-init
                }
            }
        });
    }

    // ฟังก์ชันสำหรับโหลดเนื้อหาและเรียกฟังก์ชัน init ที่เกี่ยวข้อง
    function loadContent_test(contentFile, initScript) {
        $.ajax({
            url: contentFile,
            type: 'GET',
            success: function(data) {
                contentDiv.html(data);
                if (window[initScript] && typeof window[initScript] === 'function') {
                    window[initScript](); // เรียกฟังก์ชัน init ตามชื่อที่ระบุใน data-init
                }
            }
        });
    }

    // ฟังก์ชันสำหรับเพิ่ม Event Listener ให้กับลิงก์เมนู
    function attachMenuClickListeners() {
        sidebarMenu.on('click', 'a', function(event) {
            event.preventDefault();
            const contentFile = $(this).data('content');
            const initScript = $(this).data('init');
            if (contentFile) {
                loadContent(contentFile, initScript);
            }
        });
    }

    // โหลดเมนูเริ่มต้นเมื่อ DOM พร้อมใช้งาน
    loadSidebarMenu();
});

