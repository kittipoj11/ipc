$(document).ready(function () {
  function displayRecords() {
    let menu1 = $("#menu1").val();
    let menu2 = $("#menu2").val();
    let menu3 = $("#menu3").val();

    $.ajax({
      url: "load_menu.php",
      type: "POST",
      data: {
        menu1: menu1,
        menu2: menu2,
        menu3: menu3,
        action: "create_menu",
      },
      dataType: "json",
      success: function (response) {
        // console.log(response.data);
        // exit();
        if (response.status === "success") {
          let records = response.data;
          let htmlJavascript = "";
          $("#tbody").empty();
          if (records.length > 0) {
            $.each(records, function (index, row) {
              // <td class="tdMain p-0"><span class="file-list-item" data-fileurl="${row.file_path}" data-filetype="${row.file_type}" data-filename="${row.file_name}">${row.file_name}</span></td>
              htmlJavascript += `
                                    <tr data-file_id='${row.file_id}'>
                                        <td class="tdMain p-0">${row.file_id}</td>
                                        <td class="tdMain p-0"><a href="#" class="file-list-item" data-fileurl="${row.file_path}" data-filetype="${row.file_type}" data-filename="${row.file_name}">${row.file_name}</a></td>
                                        <td class="tdMain p-0 action" align='center'>
                                            <div class='btn-group-sm'>
                                                <a class='btn btn-danger btn-sm deleteFile' style='margin: 0px 5px 5px 5px' data-file_id='${row.file_id}'>
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

    console.log(`fileUrl = ${fileUrl}`);
    console.log(`fileType = ${fileType}`);
    console.log(`fileName = ${fileName}`);
    if (fileType.startsWith("image/")) {
      // แสดงรูปภาพ
      fileDisplayArea.html(`<img src="${fileUrl}" alt="${fileName}">`);
    } else if (fileType === "application/pdf") {
      // แสดง PDF
      fileDisplayArea.html(
        `<embed src="${fileUrl}"#toolbar=0&navpanes=0&scrollbar=0" type="application/pdf">`
      );
    } else if (
      fileType === "application/vnd.ms-msword" ||
      fileType ===
        "application/vnd.openxmlformats-officedocument.wordprocessingml.document"
    ) {
      // แสดงลิงก์ดาวน์โหลดสำหรับ Word
      fileDisplayArea.html(
        `<p>File Type: MS Word</p><a href="${fileUrl}" target="_blank">Download ${fileName}</a>`
      );
    } else if (
      fileType === "application/vnd.ms-excel" ||
      fileType ===
        "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
    ) {
      // แสดงลิงก์ดาวน์โหลดสำหรับ Excel
      fileDisplayArea.html(
        `<p>File Type: MS Excel</p><a href="${fileUrl}" target="_blank">Download ${fileName}</a>`
      );
    } else if (
      fileType === "application/vnd.ms-powerpoint" ||
      fileType ===
        "application/vnd.openxmlformats-officedocument.presentationml.presentation"
    ) {
      // แสดงลิงก์ดาวน์โหลดสำหรับ PowerPoint
      fileDisplayArea.html(
        `<p>File Type: MS PowerPoint</p><a href="${fileUrl}" target="_blank">Download ${fileName}</a>`
      );
    } else {
      // ประเภทไฟล์อื่น ๆ
      fileDisplayArea.html(
        `<p>Cannot display this file type: "${fileUrl}"</p>`
      );
    }

    // อัปเดต header ของ fileDisplayArea ให้แสดงชื่อไฟล์
    $("#fileDisplayArea .card-header").text("File Preview: " + fileName);
  }

  // ฟังก์ชันสำหรับเคลียร์พื้นที่แสดงไฟล์
  function clearFileDisplay() {
    var fileDisplayArea = $("#fileDisplayArea .card-body");
    fileDisplayArea.html("<p>No file selected.</p>");
    $("#fileDisplayArea .card-header").text("File Preview"); // รีเซ็ต header
  }

  // ฟังก์ชันสำหรับลบ record (เหมือนเดิม)
  $(document).on("click", ".deleteFile", function () {
    var file_id = $(this).data("file_id");
    console.log(`file_id = ${file_id}`);
    if (
      confirm(
        "Are you sure you want to delete this record and all associated files?"
      )
    ) {
      // deleteInspectionFiles
      $.ajax({
        url: "inspection_crud.php",
        type: "POST",
        dataType: "json",
        data: {
          file_id: file_id,
          action: "deleteInspectionFiles",
        },
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
  $(document).on("click", ".file-list-item", function () {
    var fileUrl = $(this).data("fileurl");
    var fileType = $(this).data("filetype");
    var fileName = $(this).data("filename");
    showFile(fileUrl, fileType, fileName);
  });

  // เรียกฟังก์ชัน displayRecords เมื่อหน้าเว็บโหลด
  displayRecords();

  //     // เคลียร์พื้นที่แสดงไฟล์เริ่มต้น
  clearFileDisplay();
});
