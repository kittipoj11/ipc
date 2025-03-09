$(document).ready(function() {
  displayRecords();

  $("#uploadForm").on('submit', function(e) {
      e.preventDefault();
      var formData = new FormData(this);

      $.ajax({
          url: 'upload.php',
          type: 'POST',
          data: formData,
          contentType: false,
          processData: false,
          dataType: 'json',
          beforeSend: function() {
              $('#uploadStatus').html('<div class="alert alert-info">กำลังอัปโหลด...</div>');
          },
          success: function(response) {
              if (response.status === 'success') {
                  $('#uploadStatus').html('<div class="alert alert-success">' + response.message + '</div>');
                  $("#uploadForm")[0].reset(); // reset ฟอร์ม
                  displayRecords(); // รีเฟรชรายการ records
                  $("#fileDisplayArea").hide(); // ซ่อน fileDisplayArea หลังจากอัปโหลดใหม่
              } else {
                  $('#uploadStatus').html('<div class="alert alert-danger">' + response.message + '</div>');
              }
          },
          error: function() {
              $('#uploadStatus').html('<div class="alert alert-danger">เกิดข้อผิดพลาดในการอัปโหลดไฟล์</div>');
          }
      });
  });

  function displayRecords() {
      $.ajax({
          url: "display.php",
          type: "GET",
          dataType: "json",
          success: function (response) {
              if (response.status === "success") {
                  var records = response.data;
                  var html = '<ul class="list-group">';
                  if (records.length > 0) {
                      $.each(records, function (index, record) {
                          html += '<li class="list-group-item">';
                          html +=
                              "<h5>" +
                              record.record_name +
                              ' <button class="btn btn-danger btn-sm float-right deleteRecord" data-id="' +
                              record.record_id +
                              '">Delete Record</button></h5>';
                          if (record.files.length > 0) {
                              html += "<h6>Files:</h6>";
                              html += "<ul>"; // เริ่มรายการไฟล์
                              $.each(record.files, function (fileIndex, file) {
                                  var fileUrl = file.file_path;
                                  var fileName = file.file_name;
                                  var fileType = file.file_type;

                                  console.log(record);
                                  // สร้างลิงก์ชื่อไฟล์ที่เรียกฟังก์ชัน showFile เมื่อคลิก
                                  html += `<li><span class="file-list-item" data-fileurl="${fileUrl}" data-filetype="${fileType}" data-filename="${fileName}">${fileName}</span></li>`;
                              });
                              html += "</ul>"; // ปิดรายการไฟล์
                          } else {
                              html += "<p>No files uploaded for this record.</p>";
                          }
                          html += "</li>";
                      });
                  } else {
                      html += '<li class="list-group-item">No records found.</li>';
                  }
                  html += "</ul>";
                  $("#recordsDisplay").html(html);
              } else {
                  $("#recordsDisplay").html(
                      '<div class="alert alert-danger">' + response.message + "</div>"
                  );
              }
          },
          error: function () {
              $("#recordsDisplay").html(
                  '<div class="alert alert-danger">Failed to display records.</div>'
              );
          },
      });
  }


  $(document).on('click', '.deleteRecord', function() {
      var recordId = $(this).data('id');
      if (confirm("Are you sure you want to delete this record and all related files?")) {
          $.ajax({
              url: 'delete.php',
              type: 'POST',
              dataType: 'json',
              data: { record_id: recordId },
              success: function(response) {
                  if (response.status === 'success') {
                      alert(response.message);
                      displayRecords(); // รีเฟรชรายการ records
                      $("#fileDisplayArea").hide(); // ซ่อน fileDisplayArea หลังจากลบ record
                  } else {
                      alert('Error deleting record: ' + response.message);
                  }
              },
              error: function() {
                  alert('เกิดข้อผิดพลาดในการลบ record');
              }
          });
      }
  });

  $(document).on('click', '.file-list-item', function() {
      var fileUrl = $(this).data('fileurl');
      var fileType = $(this).data('filetype');
      var fileName = $(this).data('filename');

      showFile(fileUrl, fileType, fileName);
  });

  // ฟังก์ชันสำหรับแสดงไฟล์ในพื้นที่แสดงไฟล์ (แก้ไข - รองรับ Excel)
  function showFile(fileUrl, fileType, fileName) {
      var fileDisplayArea = $("#fileDisplayArea .card-body");
      fileDisplayArea.empty(); // เคลียร์เนื้อหาเดิม
      $("#fileDisplayArea").show(); // แสดงพื้นที่แสดงไฟล์

      if (fileType.startsWith('image/')) {
          // แสดงรูปภาพ
          fileDisplayArea.html(`<img src="${fileUrl}" alt="${fileName}">`);
      } else if (fileType === 'application/pdf') {
          // แสดง PDF
          fileDisplayArea.html(`<embed src="${fileUrl}#toolbar=0&navpanes=0&scrollbar=0" type="application/pdf">`);
      } else if (fileType === 'application/vnd.ms-excel' || fileType === 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
          // แสดงลิงก์ดาวน์โหลดสำหรับ Excel
          fileDisplayArea.html(`<p>File Type: Excel Spreadsheet</p><a href="${fileUrl}" target="_blank">Download ${fileName}</a>`);
      }
       else {
          // ประเภทไฟล์อื่น ๆ (รวมถึง Excel ถ้าไม่ตรงกับ MIME types ที่ระบุ)
          fileDisplayArea.html(`<p>Cannot display this file type: ${fileName}</p><a href="${fileUrl}" target="_blank">Download ${fileName}</a>`); // ให้ลิงก์ดาวน์โหลดเผื่อกรณีไฟล์ประเภทอื่น
      }

      // อัปเดต header ของ fileDisplayArea ให้แสดงชื่อไฟล์
      $("#fileDisplayArea .card-header").text(`File Preview: ${fileName}`);
  }
});