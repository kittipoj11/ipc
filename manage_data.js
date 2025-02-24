$(document).ready(function() {
  $('.editLink').each(function() { // เลือกทุก element ที่มี class 'editLink'
      $(this).on('click', function(event) {
          event.preventDefault(); // ป้องกันการทำงาน default ของลิงก์ (ไม่ต้องเปลี่ยนหน้า)
          const dataId = $(this).data('id'); // อ่านค่า data-id จากลิงก์ที่คลิก

          // AJAX Request ไปยัง edit_form.php เพื่อดึงฟอร์มแก้ไข
          $.ajax({
              url: 'edit_form.php', // URL ของไฟล์ที่จะไปดึงข้อมูล
              type: 'GET', // Method เป็น GET
              data: { id: dataId }, // ส่ง ID ไปเป็น GET parameter
              success: function(response) { // ฟังก์ชันที่ทำงานเมื่อ AJAX สำเร็จ
                  $('#editFormContainer').html(response); // นำ HTML form ที่ได้มาใส่ใน div#editFormContainer
              },
              error: function() { // ฟังก์ชันที่ทำงานเมื่อ AJAX ไม่สำเร็จ (error)
                  alert('เกิดข้อผิดพลาดในการโหลดฟอร์มแก้ไข'); // แสดงข้อความ error (ปรับปรุงได้ตามต้องการ)
              }
          });
      });
  });
});