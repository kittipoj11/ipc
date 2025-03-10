$(document).ready(function () {
  displayRecords(); // เรียกฟังก์ชัน displayRecords() เมื่อหน้าเว็บโหลดเสร็จ

  $("#uploadForm").on("submit", function (e) {
      e.preventDefault(); // ป้องกันการ submit form แบบ default

      var fileInput = $("#fileUpload")[0];
      var file = fileInput.files[0];
      var fileTypeDropdown = $("#fileTypeDropdown");
      var fileType = fileTypeDropdown.val();


      if (!file) {
          alert("Please select a file to upload.");
          return;
      }

      if (!validateFileType(file)) { // เรียกฟังก์ชัน validateFileType() เพื่อตรวจสอบประเภทไฟล์
          return; // ถ้า validate ไม่ผ่าน ก็ไม่ต้อง upload
      }

      var formData = new FormData(this); // สร้าง FormData object จาก form
      formData.append('fileType', fileType); // เพิ่ม fileType ลงใน formData

      $.ajax({
          url: "upload.php",
          type: "POST",
          data: formData,
          dataType: 'json',
          contentType: false,
          processData: false,
          success: function (response) {
              if (response.status === "success") {
                  alert(response.message);
                  $("#uploadForm")[0].reset(); // reset ฟอร์ม
                  displayRecords(); // เรียกฟังก์ชัน displayRecords() เพื่อแสดงรายการใหม่
              } else {
                  alert(response.message);
              }
          },
          error: function (xhr, status, error) {
              console.error("Upload error:", status, error);
              alert("เกิดข้อผิดพลาดในการอัปโหลดไฟล์.");
          },
      });
  });

  $("#recordsDisplay").on("click", ".btnDeleteFile", function () {
      var file_id = $(this).data("file_id");
      var record_id = $(this).data("record_id");
      if (confirm("Are you sure you want to delete this file?")) {
          $.ajax({
              url: "delete_file.php",
              type: "POST",
              dataType: "json",
              data: { file_id: file_id , record_id: record_id}, // ส่ง file_id และ record_id ไปด้วย
              success: function (response) {
                  if (response.status === "success") {
                      alert(response.message);
                      displayRecords(); // รีเฟรชรายการ records
                  } else {
                      alert(response.message);
                  }
              },
              error: function () {
                  alert("Error deleting file.");
              },
          });
      }
  });

  $("#recordsDisplay").on("click", ".deleteRecord", function () {
      var record_id = $(this).data("id");
      if (confirm("Are you sure you want to delete this record and all associated files?")) {
          $.ajax({
              url: "delete_record.php",
              type: "POST",
              dataType: "json",
              data: { record_id: record_id },
              success: function (response) {
                  if (response.status === "success") {
                      alert(response.message);
                      displayRecords(); // รีเฟรชรายการ records
                  } else {
                      alert(response.message);
                  }
              },
              error: function () {
                  alert("Error deleting record.");
              },
          });
      }
  });

  // Initial population of file type dropdown on page load
  populateFileTypeDropdown();
});

function displayRecords() {
  $.ajax({
      url: "display.php",
      type: "GET",
      dataType: "json",
      success: function (response) {
          if (response.status === "success") {
              var records = response.data;
              var html = '<table class="table">';
              html += '<thead><tr><th>ID</th><th>Record Name</th><th>Actions</th></tr></thead>';
              html += '<tbody>';

              $.each(records, function (recordIndex, record) {
                  html += `
                      <tr>
                          <td colspan="3">
                              <h5>${record.record_name}
                                  <button class="btn btn-danger btn-sm float-right deleteRecord" data-id="${record.record_id}">Delete Record</button>
                              </h5>
                          </td>
                      </tr>
                  `;

                  if (record.files.length > 0) {
                      $.each(record.files, function (fileIndex, file) {
                          html += `
                              <tr data-file_id='${file.file_id}' data-record_id='${record.record_id}'>
                                  <td class="tdMain p-0">${file.file_id}</td>
                                  <td class="tdMain p-0"><span class="file-list-item" data-fileurl="${file.file_path}" data-filetype="${file.file_type}" data-filename="${file.file_name}">${file.file_name}</span></td>
                                  <td class="tdMain p-0 action" align='center'>
                                      <div class='btn-group-sm'>
                                          <button class='btn btn-danger btn-sm btnDeleteFile' style='margin: 0px 5px 5px 5px' data-record_id='${record.record_id}' data-file_id='${file.file_id}'>
                                              <i class='fa-regular fa-trash-can'></i>
                                          </button>
                                      </div>
                                  </td>
                              </tr>
                          `;
                      });
                  } else {
                      html += `
                          <tr>
                              <td colspan="3"><p>No files uploaded for this record.</p></td>
                          </tr>
                      `;
                  }
              });

              html += '</tbody></table>';
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


function validateFileType(file) {
  const allowedFileTypes = [
      'image/jpeg', 'image/png', 'image/gif', 'image/bmp', 'image/webp', 'image/svg+xml', // รูปภาพ
      'application/msword',
      'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // Word
      'application/vnd.ms-excel',
      'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // Excel
      'application/vnd.ms-powerpoint',
      'application/vnd.openxmlformats-officedocument.presentationml.presentation', // PowerPoint
      'application/pdf', // PDF
      'text/plain', // Text
      'application/rtf', // RTF
      'application/vnd.oasis.opendocument.text', // ODT (OpenDocument Text)
      'application/vnd.oasis.opendocument.spreadsheet', // ODS (OpenDocument Spreadsheet)
      'application/vnd.oasis.opendocument.presentation' // ODP (OpenDocument Presentation)

      // คุณสามารถเพิ่ม mime types อื่นๆ ที่ต้องการได้ที่นี่
  ];

  if (allowedFileTypes.includes(file.type)) {
      return true;
  } else {
      alert("Error: Please select a valid file type (Images, Documents, Spreadsheets, Presentations).");
      return false;
  }
}


function populateFileTypeDropdown() {
  const dropdown = $("#fileTypeDropdown");
  dropdown.empty();

  $('<option value="">-- Select File Type --</option>').appendTo(dropdown);

  const fileTypesByCategory = {
      "Images": [
          { value: "image/jpeg", text: "JPEG (.jpg, .jpeg)" },
          { value: "image/png", text: "PNG (.png)" },
          { value: "image/gif", text: "GIF (.gif)" },
          { value: "image/bmp", text: "BMP (.bmp)" },
          { value: "image/webp", text: "WebP (.webp)" },
          { value: "image/svg+xml", text: "SVG (.svg)" }
      ],
      "Documents": [
          { value: "application/msword", text: "Word (.doc)" },
          { value: "application/vnd.openxmlformats-officedocument.wordprocessingml.document", text: "Word (.docx)" },
          { value: "application/pdf", text: "PDF (.pdf)" },
          { value: "text/plain", text: "Text (.txt)" },
          { value: "application/rtf", text: "RTF (.rtf)" },
          { value: "application/vnd.oasis.opendocument.text", text: "OpenDocument Text (.odt)" }
      ],
      "Spreadsheets": [
          { value: "application/vnd.ms-excel", text: "Excel (.xls)" },
          { value: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet", text: "Excel (.xlsx)" },
          { value: "text/csv", text: "CSV (.csv)" },
          { value: "application/vnd.oasis.opendocument.spreadsheet", text: "OpenDocument Spreadsheet (.ods)" }
      ],
      "Presentations": [
          { value: "application/vnd.ms-powerpoint", text: "PowerPoint (.ppt)" },
          { value: "application/vnd.openxmlformats-officedocument.presentationml.presentation", text: "PowerPoint (.pptx)" },
          { value: "application/vnd.oasis.opendocument.presentation", text: "OpenDocument Presentation (.odp)" }
      ],
      "Others": [
          { value: "*", text: "All File Types" } // หรือเปลี่ยนเป็น "Other Files" ถ้าต้องการ
      ]
  };


  $.each(fileTypesByCategory, function (categoryName, fileTypes) {
      const optgroup = $('<optgroup label="' + categoryName + '"></optgroup>');
      $.each(fileTypes, function (index, fileType) {
          $('<option value="' + fileType.value + '">' + fileType.text + '</option>').appendTo(optgroup);
      });
      optgroup.appendTo(dropdown);
  });
}