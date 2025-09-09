$(document).ready(function () {
  const responseMessage = $("#response-message");
  let selectedFile; // เก็บไฟล์ที่เลือก

  function showMessage(message, isSuccess) {
    responseMessage
      .text(message)
      .removeClass("success error")
      .addClass(isSuccess ? "success" : "error")
      .show()
      .delay(5000)
      .fadeOut();
  }

  $("#myForm").on("submit", function (e) {
    // $(document).on("click", "#btnSave", function (e) {
    e.preventDefault();

    const myForm = $("#myForm");
    const userId = myForm.data("user-id");
    // const $signature_path = $("#signature_path").val();// หรือ
    const $signature_path_ = `uploads/signatures/${$("#signature_path").val()}`;
    const $signature_path = `uploads/signatures/${$("#signature_path").val() || (selectedFile ? selectedFile.name : "")}`;


    let formData = new FormData();
    formData.append("action", "save");

    // ใส่ไฟล์จริง
    formData.append("file", selectedFile);
    formData.append("filename", $("#signature_path").val());

    // JSON -> string ใส่ไปใน headerData
    formData.append(
      "headerData",
      JSON.stringify({
        user_id: userId,
        user_code: $("#user_code").val(),
        full_name: $("#full_name").val(),
        password: $("#password").val(),
        role_id: $("#role_id").val(),
        department_id: $("#department_id").val(),
        signature_path: $signature_path,
      })
    );


    $.ajax({
      url: "user_handler_api.php",
      type: "POST",
      contentType: false,
      cache: false,
      processData: false,
      data: formData,
      dataType: "json", // สำคัญ: บอกให้ jQuery แปลง response เป็น JSON
    })
      .done(function (result) {
        // console.log(`result: ${result}`);
        responseMessage.text(result.message).css("color", "green");
        Swal.fire({
          icon: "success",
          title: "Data saved successfully",
          color: "#716add",
          allowOutsideClick: false,
          background: "black",
          // backdrop: `
          //                     rgba(0,0,123,0.4)
          //                     url("_images/paw.gif")
          //                     left bottom
          //                     no-repeat
          //                     `,
          // showConfirmButton: false,
          // timer: 15000
        }).then((result) => {
          if (result.isConfirmed) {
            // loadData(); // โหลดข้อมูลใหม่ทั้งหมด
            window.location.href = "user_list.php";
          }
        });
      })
      .fail((jqXHR) => {
        const errorMsg = jqXHR.responseJSON
          ? jqXHR.responseJSON.message
          : "เกิดข้อผิดพลาดรุนแรง";
        showMessage(errorMsg, false);
      });
  });

  $(".btnCancel , .btnBack").click(function () {
    console.log("click");
    window.history.back();
  });

  // preview บน modal
  $("#fileInput").on("change", function (e) {
    const file = e.target.files[0];
    if (file) {
      selectedFile = file;
      const reader = new FileReader();
      reader.onload = function (ev) {
        $("#modalPreview").attr("src", ev.target.result).show();
      };
      reader.readAsDataURL(file);
    }
  });

  // กด OK -> แสดงที่หน้าหลัก
  $("#btnOk").on("click", function () {
    if (selectedFile) {
      $("#mainPreview").attr("src", URL.createObjectURL(selectedFile)).show();
      $("#signature_path").val(selectedFile.name);
      $("#imageModal").modal("hide");
    }
  });
});
