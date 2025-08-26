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

  let contract_value = isNaN(parseFloat($("#contract_value").val())) ? 0 : parseFloat($("#contract_value").val());
  let interim_payment = parseFloat($("#interim_payment").val());
  let interim_payment_less_previous = isNaN(parseFloat($("#interim_payment_less_previous").val())) ? 0 : parseFloat($("#interim_payment_less_previous").val());
  let interim_payment_accumulated = isNaN(parseFloat($("#interim_payment_accumulated").val())) ? 0 : parseFloat($("#interim_payment_accumulated").val());
  let interim_payment_remain = isNaN(parseFloat($("#interim_payment_remain").val())) ? 0 : parseFloat($("#interim_payment_remain").val());

  let interim_payment_percent = isNaN(parseFloat($("#interim_payment_percent").val())) ? 0 : parseFloat($("#interim_payment_percent").val());
  let interim_payment_less_previous_percent = isNaN(parseFloat($("#interim_payment_less_previous_percent").val())) ? 0 : parseFloat($("#interim_payment_less_previous_percent").val());
  let interim_payment_accumulated_percent = isNaN(parseFloat($("#interim_payment_accumulated_percent").val())) ? 0 : parseFloat($("#interim_payment_accumulated_percent").val());
  let interim_payment_remain_percent = isNaN(parseFloat($("#interim_payment_remain_percent").val())) ? 0 : parseFloat($("#interim_payment_remain_percent").val());

  // - action มาจากการกดปุ่มว่าเป็นอะไร เช่น save, submit, approve, reject เป็นต้น  
  // โดย submit และ approve เป็นการเลื่อน level เหมือนกัน  ต่างกันแค่ชื่อ   ซึ่งอาจจะดึงข้อมูลชื่อมาจาก workflow_step 
  // - data เป็นข้อมูลที่มาจากฟอร์มเพื่อนำมาบันทึกข้อมูล 
  // เช่นถ้าเป็นการ save จะดึงข้อมูลของ ipc บน form ส่งมาให้   เพื่อมาทำการ save 
  // ถ้าเป็นการ submit sinv reject ไม่ต้องส่งข้อมูลของ form มาเพราะไม่ได้ใช้ข้อมูลบน form แต่ใช้การเลื่อนหรือถอย level จากการดึงข้อมูลใน workflow_step 
  // แต่ทุก action ต้องส่ง data ที่มีข้อมูลอย่างน้อยคือ ipc-id, user-id
  function sendRequest(action, data){
    const myForm = $("#myForm");
    const ipcId = myForm.data("ipc-id");
    const userId = myForm.data("user-id"); //ไม่ต้องส่งไปก็ได้เพราะ  เรียกใช้ $_SESSION['user_id] ใน inspection_handler_api.php หรือ inspection_service_class.php

    const data_sent = {
      action: action,
      ipcId: ipcId,
      userId: userId,
      ...data,
    };
    // console.log(action);
    // console.log(JSON.stringify(data_sent));
    // return;
    $.ajax({
      url: "ipc_handler_api.php",
      type: "POST",
      contentType: "application/json",
      dataType: "json",
      data: JSON.stringify(data_sent),
      // beforeSend: function () {
      //   // Optional: disable buttons to prevent double-clicking
      //   $("#submit").prop("disabled", true);
      // },
    })
      .done(function (result) {
        // console.log(`result: ${result}`);
        responseMessage.text(result.message).css("color", "green");
        Swal.fire({
          icon: result.status,
          title: result.message,
          color: "#716add",
          allowOutsideClick: false,
          background: "black",
        }).then((result) => {
          if (result.isConfirmed) {
            window.location.href = "ipc_list.php";
          }
        });
      })
      .fail((jqXHR) => {
        const errorMsg = jqXHR.responseJSON
          ? jqXHR.responseJSON.message
          : "เกิดข้อผิดพลาดรุนแรง";
        showMessage(errorMsg, false);
      });
  }

  $(".approve").on("click", function (e) {
    // $(document).on("click", "#btnSave", function (e) {
      e.preventDefault();
    // sendRequest($(".approve").data("approve-text"));
    // console.log("click");
    sendRequest("approve");
  });
  
  $(".reject").on("click", function (e) {
    // $(document).on("click", "#btnSave", function (e) {
      e.preventDefault();
    // const comments = $("#reject_comments").val().trim();
    const comments = "#reject_comments";
    // if (!comments) {
    //   alert("กรุณากรอกเหตุผลในการปฏิเสธ");
    //   $("#reject_comments").focus();
    //   return;
    // }
    sendRequest("reject", { comments: comments });
  });

  $(".btnCancel").click(function () {
    window.history.back();
    // window.location.href = "inspection_view.php";
    // window.history.go(-1);
    // $('.main').load('open_area_schedule_main.php'); แบบนี้ไม่ได้
    // header('Location: main.php?page=open_area_schedule_main');แบบนี้ไม่ได้
    
  });

  function refreshForm() {
    const myForm = $('#myForm');
    // จัดการปุ่ม action
    if (myForm.data('ipc-status') == 'pending-submit' && myForm.data('current-approver-id') == myForm.data('user-id')) {
      $("#btnAction").addClass('inline');
      $("#btnAction").removeClass('d-none');
    } 
    else if (myForm.data('ipc-status') =='pending-approve' && myForm.data('current-approver-id') == myForm.data('user-id')) {
      $("#btnAction").addClass('inline');
      $("#btnAction").removeClass('d-none');
    } 
    else{
      $("#btnAction").addClass('d-none');
      $("#btnAction").removeClass('inline');
    }

 
  }

  refreshForm();
});