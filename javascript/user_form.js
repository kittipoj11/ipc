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

  // preview บน modal
  $("#fileInput").on("change", function (e) {
    const file = e.target.files[0];
    if (file) {
      selectedFile = file;
      const reader = new FileReader();
      reader.onload = function (ev) {
        $("#imgModalPreview").attr("src", ev.target.result).show();
      };
      reader.readAsDataURL(file);
    }
  });

  // กด OK -> แสดงที่หน้าหลัก
  $("#btnOk").on("click", function () {
    if (selectedFile) {
      $("#imgMainPreview").attr("src", URL.createObjectURL(selectedFile)).show();
      $("#filename").val(selectedFile.name);
      $("#imageModal").modal("hide");
    }
  });

  $("#myForm").on("submit", async function (e) {
    // $(document).on("click", "#btnSave", function (e) {
    e.preventDefault();

    // 1) upload รูปก่อน
    if(selectedFile){ 
      const resUpload = await uploadSignatureFile(selectedFile);
    }
    
    // 2) save user + path ของรูป
    const myForm = $("#myForm");
    const userId = myForm.data("user-id");
    const filename = `uploads/signatures/${$("#filename").val()}`;

    const headerData = {
      user_id: userId,
      user_code: $("#user_code").val(),
      username: $("#username").val(),
      full_name: $("#full_name").val(),
      password: $("#password").val(),
      role_id: $("#role_id").val(),
      department_id: $("#department_id").val(),
      filename: filename,
    };

    const data_sent = {
      headerData: headerData,
      action: "save",
    };

    $.ajax({
      url: "user_handler_api.php",
      type: "POST",
      contentType: "application/json",
      dataType: 'json',
      data: JSON.stringify(data_sent),
    })
      .done(function (result) {
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


  
function uploadSignatureFile(selectedFile) {
  return new Promise((resolve, reject) => {
    const formData = new FormData();
    formData.append("file", selectedFile);

    $.ajax({
      url: "upload_handler_api.php",
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: function (res) {
        if (res.status === "ok") {
          resolve(res); // คืน path + filename
        } else {
          reject(res.message);
        }
      },
      error: function (xhr) {
        reject("upload error");
      }
    });
  });
}

});
