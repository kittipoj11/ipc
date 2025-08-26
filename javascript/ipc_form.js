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

  $(document).on("click", "#prevBtn", function () {
    const myForm = $("#myForm");
    const ipcId = myForm.data("ipc-id");
    const dataSent = {
      action: "previewIpc",
      ipcId: ipcId,
    };

    $.ajax({
      url: "ipc_handler_api.php",
      type: "post",
      contentType: "application/json",
      dataType: "json",
      data: JSON.stringify(dataSent),
    }).done(function (result) {
      // console.log(result);
      // console.log(result.length);
      if (result) {
        content = loadIpc(result);
        $("#content").html(content);
      } else {
        $("#content").html("");
      }
    });

  });

  $(document).on("click", "#nextBtn", function () {
    const myForm = $("#myForm");
    const ipcId = myForm.data("ipc-id");
    const dataSent = {
      action: "previewInspection",
      ipcId: ipcId,
    };

    $.ajax({
      url: "ipc_handler_api.php",
      type: "post",
      contentType: "application/json",
      dataType: "json",
      data: JSON.stringify(dataSent),
    }).done(function (result) {
      // console.log(result);
      // console.log(result.length);
      if (result) {
        content = loadInspection(result);
        $("#content").html(content);
      } else {
        $("#content").html("");
      }
    });

  });

  function loadIpc(data) {
    let content = "";
    content = `
                <h3 class="">INTERIM CERTIFICATE</h3>
                <div class="header-info">
                  <!-- <div class="info-row">
                  <div class="fw-bold" style="width: 200px;">DATE</div>
                  <div class="flex-grow-1">18<sup>th</sup> May 2023</div>
                </div> -->
                  <div class="info-row">
                    <div class="fw-bold" style="width: 200px;">PROJECT</div>
                    <div class="flex-grow-1">${data.pomain.project_name}</div>
                  </div>
                  <div class="info-row">
                    <div class="fw-bold" style="width: 200px;">OWNER</div>
                    <div class="flex-grow-1">IMPACT Exhibition Management Co., Ltd.<br>47/569-576, 10th floor, Bangkok Land Building,<br>Popular 3 Road, Banmai Sub-district,<br>Pakkred District, Nonthaburi 11120</div>
                  </div>
                </div>

                <hr>

                <div class="header-info">
                  <div class="info-row">
                    <div class="fw-bold" style="width: 200px;">AGREEMENT DATE</div>
                    <!-- <div class="flex-grow-1">25<sup>th</sup> April 2023 (IMPO23020769-1)</div> -->
                    <div class="flex-grow-1">${data.ipc.agreement_date}</div>
                  </div>
                  <div class="info-row">
                    <div class="fw-bold" style="width: 200px;">CONTRACTOR</div>
                    <div class="flex-grow-1">${data.ipc.contractor}></div>
                  </div>
                  <div class="info-row">
                    <div class="fw-bold" style="width: 200px;">CONTRACT VALUE</div>
                    <div class="flex-grow-1">(Including Vat 7%)</div>
                    <div class="flex-grow-1" style="text-align: right; font-weight: bold;">${parseFloat(
                      data.ipc.contract_value
                    ).toFixed(2)}</div>
                  </div>
                </div>

                <div class="payment-boxx">
                  <h3>INTERIM PAYMENT CLAIM No.1</h3>
                </div>

                <div class="payment-details">
                  <div class="item">
                    <div class="flex-grow-1">Total Value Of Interim Payment</div>
                    <div class="text-end" style="width: 150px;">${parseFloat(
                      data.ipc.total_value_of_interim_payment
                    ).toFixed(2)}</div>
                  </div>
                  <div class="item">
                    <div class="flex-grow-1">Less Previous Interim Payment</div>
                    <div class="text-end" style="width: 150px;">${parseFloat(
                      data.ipc.less_previous_interim_payment
                    ).toFixed(2)}</div>
                  </div>
                  <div class="item">
                    <div class="flex-grow-1">Net Value of Current Claim</div>
                    <div class="text-end" style="width: 150px;">${parseFloat(
                      data.ipc.net_value_of_current_claim
                    ).toFixed(2)}</div>
                  </div>
                  <div class="item">
                    <div class="flex-grow-1">Less Retention 5% (Exclu. VAT)</div>
                    <div class="text-end" style="width: 150px;">${parseFloat(
                      data.ipc.less_retension_exclude_vat
                    ).toFixed(2)}</div>
                  </div>
                </div>

                <div class="d-flex justify-content-between fw-bold" style="font-size: 18px;">
                  <div class="">NET AMOUNT DUE FOR PAYMENT No.1</div>
                  <div class="text-end">${parseFloat(
                    data.ipc.net_amount_due_for_payment
                  ).toFixed(2)}</div>
                </div>

                <div class="payment-details">
                  <div class="item">
                    <div class="flex-grow-1">Total Value of Retention (Inclu. this certificate)</div>
                    <div class="text-end" style="width: 150px;">${parseFloat(
                      data.ipc.total_value_of_retention
                    ).toFixed(2)}</div>
                  </div>
                  <div class="item">
                    <div class="flex-grow-1">Total Value of Certification made (Inclu. this certificate)</div>
                    <div class="text-end" style="width: 150px;">${parseFloat(
                      data.ipc.total_value_of_certification_made
                    ).toFixed(2)}</div>
                  </div>
                  <div class="item">
                    <div class="flex-grow-1">Resulting Balance of Contract Sum Outstanding</div>
                    <div class="text-end" style="width: 150px;">${parseFloat(
                      data.ipc.resulting_balance_of_contract_sum_outstanding
                    ).toFixed(2)}</div>
                  </div>
                </div>
                `;
    return content;
  }
  function loadInspection(data) {
    let content = "";
    content = `
                <h3 class="">INSPECTION CERTIFICATE</h3>
                <div class="header-info">
                  <!-- <div class="info-row">
                  <div class="fw-bold" style="width: 200px;">DATE</div>
                  <div class="flex-grow-1">18<sup>th</sup> May 2023</div>
                </div> -->
                  <div class="info-row">
                    <div class="fw-bold" style="width: 200px;">PROJECT</div>
                    <div class="flex-grow-1">${data.pomain.project_name}</div>
                  </div>
                  <div class="info-row">
                    <div class="fw-bold" style="width: 200px;">OWNER</div>
                    <div class="flex-grow-1">IMPACT Exhibition Management Co., Ltd.<br>47/569-576, 10th floor, Bangkok Land Building,<br>Popular 3 Road, Banmai Sub-district,<br>Pakkred District, Nonthaburi 11120</div>
                  </div>
                </div>

                <hr>

                <div class="header-info">
                  <div class="info-row">
                    <div class="fw-bold" style="width: 200px;">AGREEMENT DATE</div>
                    <!-- <div class="flex-grow-1">25<sup>th</sup> April 2023 (IMPO23020769-1)</div> -->
                    <div class="flex-grow-1">${data.ipc.agreement_date}</div>
                  </div>
                  <div class="info-row">
                    <div class="fw-bold" style="width: 200px;">CONTRACTOR</div>
                    <div class="flex-grow-1">${data.ipc.contractor}></div>
                  </div>
                  <div class="info-row">
                    <div class="fw-bold" style="width: 200px;">CONTRACT VALUE</div>
                    <div class="flex-grow-1">(Including Vat 7%)</div>
                    <div class="flex-grow-1" style="text-align: right; font-weight: bold;">${parseFloat(
                      data.ipc.contract_value
                    ).toFixed(2)}</div>
                  </div>
                </div>

                <div class="payment-boxx">
                  <h3>INTERIM PAYMENT CLAIM No.1</h3>
                </div>

                <div class="payment-details">
                  <div class="item">
                    <div class="flex-grow-1">Total Value Of Interim Payment</div>
                    <div class="text-end" style="width: 150px;">${parseFloat(
                      data.ipc.total_value_of_interim_payment
                    ).toFixed(2)}</div>
                  </div>
                  <div class="item">
                    <div class="flex-grow-1">Less Previous Interim Payment</div>
                    <div class="text-end" style="width: 150px;">${parseFloat(
                      data.ipc.less_previous_interim_payment
                    ).toFixed(2)}</div>
                  </div>
                  <div class="item">
                    <div class="flex-grow-1">Net Value of Current Claim</div>
                    <div class="text-end" style="width: 150px;">${parseFloat(
                      data.ipc.net_value_of_current_claim
                    ).toFixed(2)}</div>
                  </div>
                  <div class="item">
                    <div class="flex-grow-1">Less Retention 5% (Exclu. VAT)</div>
                    <div class="text-end" style="width: 150px;">${parseFloat(
                      data.ipc.less_retension_exclude_vat
                    ).toFixed(2)}</div>
                  </div>
                </div>

                <div class="d-flex justify-content-between fw-bold" style="font-size: 18px;">
                  <div class="">NET AMOUNT DUE FOR PAYMENT No.1</div>
                  <div class="text-end">${parseFloat(
                    data.ipc.net_amount_due_for_payment
                  ).toFixed(2)}</div>
                </div>

                <div class="payment-details">
                  <div class="item">
                    <div class="flex-grow-1">Total Value of Retention (Inclu. this certificate)</div>
                    <div class="text-end" style="width: 150px;">${parseFloat(
                      data.ipc.total_value_of_retention
                    ).toFixed(2)}</div>
                  </div>
                  <div class="item">
                    <div class="flex-grow-1">Total Value of Certification made (Inclu. this certificate)</div>
                    <div class="text-end" style="width: 150px;">${parseFloat(
                      data.ipc.total_value_of_certification_made
                    ).toFixed(2)}</div>
                  </div>
                  <div class="item">
                    <div class="flex-grow-1">Resulting Balance of Contract Sum Outstanding</div>
                    <div class="text-end" style="width: 150px;">${parseFloat(
                      data.ipc.resulting_balance_of_contract_sum_outstanding
                    ).toFixed(2)}</div>
                  </div>
                </div>
                `;
    return content;
  }

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