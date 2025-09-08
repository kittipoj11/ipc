$(document).ready(function () {
  const responseMessage = $("#response-message");

  function showMessage(message, isSuccess) {
    responseMessage
      .text(message)
      .removeClass("success error")
      .addClass(isSuccess ? "success" : "error")
      .show()
      .delay(5000)
      .fadeOut();
  }

  $("#myForm").on("submit", function (e) {// $(document).on("click", "#btnSave", function (e) {
    e.preventDefault();

    const myForm = $("#myForm");
    const userId = myForm.data("user-id");

    const headerData = {
      user_id: userId,
      user_code: $("#user_code").val(),
      username: $("#username").val(),
      full_name: $("#full_name").val(),
      role_id: $("#role_id").val(),
      department_id: $("#department_id").val(),
      signature_path: $("#signature_path").val(),
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

  // รอตรวจสอบฟังก์ชัน
  $(document).on("click", "#btnAttach", function (e) {
    e.preventDefault();

    const po_id = $("#po_id").val();
    const period_id = $("#period_id").val();
    const inspection_id = $("#inspection_id").val();

    // console.log(`po_id = ${po_id}`);
    // console.log(`period_id = ${period_id}`);
    // console.log(`inspection_id = ${inspection_id}`);
    window.location.href = `inspection_attach_form.php?po_id=${po_id}&period_id=${period_id}&inspection_id=${inspection_id}&mode=`;
  });

});