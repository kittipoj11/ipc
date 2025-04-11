document.addEventListener('DOMContentLoaded', function() {
  const sidebarMenu = document.getElementById('sidebar-menu');
  const contentDiv = document.getElementById('content');

  // ฟังก์ชันสำหรับโหลดเมนูเริ่มต้น
  function loadSidebarMenu() {
      var xhr = new XMLHttpRequest();
      xhr.onreadystatechange = function() {
          if (xhr.readyState == 4 && xhr.status == 200) {
              sidebarMenu.innerHTML = xhr.responseText;
              attachMenuClickListeners(); // เรียกฟังก์ชันเพื่อเพิ่ม Event Listener หลังจากโหลดเมนู
          }
      };
      xhr.open('GET', 'get_sidebar_menu.php', true);
      xhr.send();
  }

  // ฟังก์ชันสำหรับโหลดเนื้อหาเมื่อคลิกเมนู
  function loadContent(contentFile) {
      var xhr = new XMLHttpRequest();
      xhr.onreadystatechange = function() {
          if (xhr.readyState == 4 && xhr.status == 200) {
              contentDiv.innerHTML = xhr.responseText;
          }
      };
      xhr.open('GET', contentFile, true);
      xhr.send();
  }

  // ฟังก์ชันสำหรับเพิ่ม Event Listener ให้กับลิงก์เมนู
  function attachMenuClickListeners() {
      const menuLinks = sidebarMenu.querySelectorAll('a');
      menuLinks.forEach(function(link) {
          link.addEventListener('click', function(event) {
              event.preventDefault();
              const contentFile = this.dataset.content;
              if (contentFile) {
                  loadContent(contentFile);
              }
          });
      });
  }

  // โหลดเมนูเริ่มต้นเมื่อหน้าเว็บโหลด
  loadSidebarMenu();
});