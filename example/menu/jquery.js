$(document).ready(function() {
  const sidebarMenu = $('#sidebar-menu');
  const contentDiv = $('#content');

  // ฟังก์ชันสำหรับโหลดเมนูเริ่มต้น
  function loadSidebarMenu() {
      $.ajax({
          url: 'get_sidebar_menu.php',
          type: 'POST',
          success: function(data) {
              sidebarMenu.html(data);
              attachMenuClickListeners(); // เรียกฟังก์ชันเพื่อเพิ่ม Event Listener หลังจากโหลดเมนู
          }
      });
  }

  // ฟังก์ชันสำหรับโหลดเนื้อหาเมื่อคลิกเมนู
  function loadContent(contentFile) {
      $.ajax({
          url: contentFile,
          type: 'POST',
          success: function(data) {
              contentDiv.html(data);
          }
      });
  }

  // ฟังก์ชันสำหรับเพิ่ม Event Listener ให้กับลิงก์เมนู (ใช้ Event Delegation)
  sidebarMenu.on('click', 'a', function(event) {
      event.preventDefault();
      const contentFile = $(this).data('content');
      if (contentFile) {
          loadContent(contentFile);
      }
  });

  // โหลดเมนูเริ่มต้นเมื่อ DOM พร้อมใช้งาน
  loadSidebarMenu();
});

// การเปลี่ยนแปลงที่สำคัญ:

// $(document).ready(function() { ... });: ใช้แทน document.addEventListener('DOMContentLoaded', function() { ... }); เพื่อให้แน่ใจว่าโค้ดจะทำงานหลังจากที่ DOM โหลดเสร็จสมบูรณ์

// $('#sidebar-menu') และ $('#content'): ใช้ jQuery Selector เพื่อเลือก Element แทน document.getElementById().

// $.ajax(): ใช้ฟังก์ชัน AJAX ของ jQuery แทน XMLHttpRequest. การตั้งค่าต่างๆ เช่น url, type, และ success จะถูกส่งเป็น Object

// .html(data): ใช้ฟังก์ชัน html() ของ jQuery เพื่อกำหนดเนื้อหาของ Element แทน element.innerHTML = data.

// .on('click', 'a', function(event) { ... });: ใช้ฟังก์ชัน on() ของ jQuery สำหรับการจัดการ Event Delegation ซึ่งมีไวยากรณ์ที่กระชับกว่า

// $(this): ภายใน Event Handler, $(this) อ้างถึง Element ที่เกิด Event (ในที่นี้คือลิงก์ <a> ที่ถูกคลิก).

// .data('content'): ใช้ฟังก์ชัน data() ของ jQuery เพื่อเข้าถึง data- attributes แทน element.dataset.content.