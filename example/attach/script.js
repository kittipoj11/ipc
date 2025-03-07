$(document).ready(function() {
  $('#uploadForm').submit(function(e) {
      e.preventDefault();
      var formData = new FormData(this);
      $.ajax({
          url: 'upload.php',
          type: 'POST',
          data: formData,
          contentType: false,
          processData: false,
          success: function(response) {
              $('#uploadStatus').html(response);
              loadFilePaths(); // โหลด path ไฟล์ใหม่หลังอัปโหลดสำเร็จ
          },
          error: function() {
              $('#uploadStatus').html('เกิดข้อผิดพลาดในการอัปโหลดไฟล์.');
          }
      });
  });

  function loadFilePaths() {
      $.ajax({
          url: 'get_files.php',
          type: 'GET',
          success: function(response) {
              $('#fileDisplay').html(response);
          },
          error: function() {
              $('#fileDisplay').html('ไม่สามารถโหลดไฟล์ได้.');
          }
      });
  }

  $('#fileDisplay').on('click', '.remove-file', function() { // ใช้ .on() สำหรับ event delegation
      var attachId = $(this).data('attach-id');
      var filePath = $(this).data('file-path');

      $.ajax({
          url: 'delete_file.php',
          type: 'POST',
          data: { attach_id: attachId, file_path: filePath }, // ส่งข้อมูลแบบ object
          dataType: 'json', // คาดหวัง response เป็น JSON
          success: function(response) {
              if (response.status === 'success') {
                  $('#uploadStatus').html(response.message);
                  loadFilePaths(); // โหลดรายการไฟล์ใหม่หลังลบสำเร็จ
              } else {
                  $('#uploadStatus').html('เกิดข้อผิดพลาดในการลบไฟล์: ' + response.message);
              }
          },
          error: function() {
              $('#uploadStatus').html('เกิดข้อผิดพลาดในการลบไฟล์.');
          }
      });
  });

  // โหลดไฟล์เริ่มต้นเมื่อหน้าเว็บโหลด
  loadFilePaths();
});
