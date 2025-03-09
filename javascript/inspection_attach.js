$(document).ready(function () {
  // ฟังก์ชันสำหรับอัปโหลดไฟล์
  $("#uploadForm").on("submit", function (e) {
    e.preventDefault();
    var formData = new FormData(this);

    $.ajax({
      url: "upload.php",
      type: "POST",
      data: formData,
      contentType: false,
      cache: false,
      processData: false,
      dataType: "json",
      success: function (response) {
        if (response.status === "success") {
          $("#uploadStatus").html(
            '<div class="alert alert-success">' + response.message + "</div>"
          );
          $("#uploadForm")[0].reset();
          displayRecords(); // รีเฟรชรายการ records
          clearFileDisplay(); // เคลียร์พื้นที่แสดงไฟล์เมื่ออัปโหลดสำเร็จ
        } else {
          $("#uploadStatus").html(
            '<div class="alert alert-danger">' + response.message + "</div>"
          );
        }
      },
      error: function () {
        $("#uploadStatus").html(
          '<div class="alert alert-danger">เกิดข้อผิดพลาดในการอัปโหลด</div>'
        );
      },
    });
  });

  // ฟังก์ชันสำหรับแสดง records และรายชื่อไฟล์ 
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
    function displayRecordsNew() {
    $.ajax({
      url: "display.php",
      type: "GET",
      dataType: "json",
        success: function (response) {
            console.log(response);
            // exit();
        if (response.status === "success") {
          let records = response.data;
          let htmlJavascript='';
            if (records.length > 0) {
              $.each(records, function (index, row) {
                htmlJavascript += `
                                        <tr data-file_id='${row.file_id}' data-record_id='${row.record_id}'>
                                            <td class="tdMain p-0">${row.file_id}</td>
                                            <td class="tdMain p-0"><span class="file-list-item" data-fileurl="${row.file_path}" data-filetype="${row.file_type}" data-filename="${row.file_name}">${row.file_name}</span></td>
                                            <td class="tdMain p-0 action" align='center'>
                                                <div class='btn-group-sm'>
                                                    <a class='btn btn-danger btn-sm btnDelete' style='margin: 0px 5px 5px 5px' data-id='${row.record_id}'>
                                                        <i class='fa-regular fa-trash-can'></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    `;
            });
          } else {
          }
          $("#tbody").append(htmlJavascript);
        } else {
          $("#recordsDisplay").html(
            '<div class="alert alert-danger">ERROR</div>'
          );
        }
      },
      error: function () {
        $("#recordsDisplay").html(
          '<div class="alert alert-danger">Failed to display records.Why!!!</div>'
        );
      },
    });
  }

  // ฟังก์ชันสำหรับแสดงไฟล์ในพื้นที่แสดงไฟล์ (ใหม่)
  function showFile(fileUrl, fileType, fileName) {
    var fileDisplayArea = $("#fileDisplayArea .card-body");
    fileDisplayArea.empty(); // เคลียร์เนื้อหาเดิม

    if (fileType.startsWith("image/")) {
      // แสดงรูปภาพ
      fileDisplayArea.html(
        '<img src="' + fileUrl + '" alt="' + fileName + '">'
      );
    } else if (fileType === "application/pdf") {
      // แสดง PDF
      fileDisplayArea.html(
        '<embed src="' +
          fileUrl +
          '#toolbar=0&navpanes=0&scrollbar=0" type="application/pdf">'
      );
    } else if (
      fileType === "application/vnd.ms-excel" ||
      fileType ===
        "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
    ) {
      // แสดงลิงก์ดาวน์โหลดสำหรับ Excel
      fileDisplayArea.html(
        `<p>File Type: Excel Spreadsheet</p><a href="${fileUrl}" target="_blank">Download ${fileName}</a>`
      );
    } else {
      // ประเภทไฟล์อื่น ๆ
      fileDisplayArea.html(
        "<p>Cannot display this file type: " + fileName + "</p>"
      );
    }

    // อัปเดต header ของ fileDisplayArea ให้แสดงชื่อไฟล์
    $("#fileDisplayArea .card-header").text("File Preview: " + fileName);
  }

  // ฟังก์ชันสำหรับเคลียร์พื้นที่แสดงไฟล์ (ใหม่)
  function clearFileDisplay() {
    var fileDisplayArea = $("#fileDisplayArea .card-body");
    fileDisplayArea.html("<p>No file selected.</p>");
    $("#fileDisplayArea .card-header").text("File Preview"); // รีเซ็ต header
  }

  // ฟังก์ชันสำหรับลบ record (เหมือนเดิม)
  $(document).on("click", ".deleteRecord", function () {
    var recordId = $(this).data("id");
    if (
      confirm(
        "Are you sure you want to delete this record and all associated files?"
      )
    ) {
      $.ajax({
        url: "delete.php",
        type: "POST",
        dataType: "json",
        data: { record_id: recordId },
        success: function (response) {
          if (response.status === "success") {
            alert(response.message);
            displayRecords(); // รีเฟรชรายการ records
            clearFileDisplay(); // เคลียร์พื้นที่แสดงไฟล์เมื่อลบ record
          } else {
            alert("Error deleting record: " + response.message);
          }
        },
        error: function () {
          alert("Failed to delete record.");
        },
      });
    }
  });

  // Event delegation สำหรับ click ที่ .file-list-item 
  $("#recordsDisplay").on("click", ".file-list-item", function () {
    var fileUrl = $(this).data("fileurl");
    var fileType = $(this).data("filetype");
    var fileName = $(this).data("filename");
    showFile(fileUrl, fileType, fileName);
  });

  // เรียกฟังก์ชัน displayRecords เมื่อหน้าเว็บโหลด
  displayRecordsNew();
  
//     // เคลียร์พื้นที่แสดงไฟล์เริ่มต้น
//   clearFileDisplay();
});
