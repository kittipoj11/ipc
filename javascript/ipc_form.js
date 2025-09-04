// import numberFormatter from 'NumberFormatter.js';
function loadPage(page = 1) {
  const myForm = $("#myForm");
  const ipcId = myForm.data("ipc-id");
  const inspectionId = myForm.data("inspection-id");

  const dataSent = {};

  // Add Properties
  dataSent.action = "getCountOfInspectionFilesByInspectionId";
  dataSent.inspectionId = inspectionId;

  // console.log(page);
  $.ajax({
    url: "ipc_handler_api.php",
    type: "post",
    contentType: "application/json",
    dataType: "json",
    data: JSON.stringify(dataSent),
  }).done(function (result) {
    renderPagination(result + 2, page); //2 คือจำนวนหน้าของ IPC(หน้าที่1) และ Inspection(หน้าที่2)
  });

  // ทำการ Load หน้าต่างๆตรงนี้
  switch (page) {
    case 1://หน้าที่ 1 เป็น IPC เสมอ
      // ใช้ Object.keys วนลบ property ใน dataSent
      Object.keys(dataSent).forEach(key => delete dataSent[key]);
      dataSent.action = "previewIpc";
      dataSent.ipcId = ipcId;

      $.ajax({
        url: "ipc_handler_api.php",
        type: "post",
        contentType: "application/json",
        dataType: "json",
        data: JSON.stringify(dataSent),
      }).done(function (result) {
        // console.log(result);
        if (result) {
          content = loadIpc(result);
          $("#content").html(content);
        } else {
          $("#content").html("");
        }
      });
      break;

    case 2://หน้าที่ 2 เป็น Inspection เสมอ
      // ใช้ Object.keys วนลบ property ใน dataSent
      Object.keys(dataSent).forEach(key => delete dataSent[key]);
      dataSent.action = "previewInspection";
      dataSent.inspectionId = inspectionId;

      $.ajax({
        url: "ipc_handler_api.php",
        type: "post",
        contentType: "application/json",
        dataType: "json",
        data: JSON.stringify(dataSent),
      }).done(function (result) {
        console.log(result);
        // console.log(result);
        if (result) {
          content = loadInspection(result);
          $("#content").html(content);
        } else {
          $("#content").html("");
        }
      });
      break;
    default://หน้าอื่นๆเป็นไฟล์ Attach ของ Inspection
      // ใช้ Object.keys วนลบ property ใน dataSent
      Object.keys(dataSent).forEach(key => delete dataSent[key]);
      dataSent.action = "loadAttach";
      dataSent.inspectionId = inspectionId;
      dataSent.page = page;

      $.ajax({
        url: "ipc_handler_api.php",
        type: "post",
        contentType: "application/json",
        dataType: "json",
        data: JSON.stringify(dataSent),
      }).done(function (result) {
        // console.log(result);
        if (result) {
          content = loadAttach(result);
          $("#content").html(content);
        }
        else {
          $("#content").html("");
        }
      })
  }
}

function renderPagination(totalPages, currentPage) {
  pagination = "";

  // ปุ่ม Previous
  pagination += `
        <li class="page-item ${currentPage === 1 ? "disabled" : ""}">
          <a class="page-link" href="#" onclick="loadPage(${currentPage - 1})">Previous</a>
        </li>
      `;

  // เลขหน้า
  for (let i = 1; i <= totalPages; i++) {
    pagination += `
          <li class="page-item ${i === currentPage ? "active" : ""}">
            <a class="page-link" href="#" onclick="loadPage(${i})">${i}</a>
          </li>
        `;
  }

  // ปุ่ม Next
  pagination += `
        <li class="page-item ${currentPage === totalPages ? "disabled" : ""}">
          <a class="page-link" href="#" onclick="loadPage(${currentPage + 1})">Next</a>
        </li>
      `;
  $(".pagination").html(pagination);
}

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
                    <div class="flex-grow-1" style="text-align: right; font-weight: bold;">${parseFloat(data.ipc.contract_value).toFixed(2)}</div>
                  </div>
                </div>

                <div class="payment-boxx">
                  <h3>INTERIM PAYMENT CLAIM No.1</h3>
                </div>

                <div class="payment-details">
                  <div class="item">
                    <div class="flex-grow-1">Total Value Of Interim Payment</div>
                    <div class="text-end" style="width: 150px;">${parseFloat(data.ipc.total_value_of_interim_payment).toFixed(2)}</div>
                  </div>
                  <div class="item">
                    <div class="flex-grow-1">Less Previous Interim Payment</div>
                    <div class="text-end" style="width: 150px;">${parseFloat(data.ipc.less_previous_interim_payment).toFixed(2)}</div>
                  </div>
                  <div class="item">
                    <div class="flex-grow-1">Net Value of Current Claim</div>
                    <div class="text-end" style="width: 150px;">${parseFloat(data.ipc.net_value_of_current_claim).toFixed(2)}</div>
                  </div>
                  <div class="item">
                    <div class="flex-grow-1">Less Retention 5% (Exclu. VAT)</div>
                    <div class="text-end" style="width: 150px;">${parseFloat(data.ipc.less_retension_exclude_vat).toFixed(2)}</div>
                  </div>
                </div>

                <div class="d-flex justify-content-between fw-bold" style="font-size: 18px;">
                  <div class="">NET AMOUNT DUE FOR PAYMENT No.1</div>
                  <div class="text-end">${parseFloat(data.ipc.net_amount_due_for_payment).toFixed(2)}</div>
                </div>

                <div class="payment-details">
                  <div class="item">
                    <div class="flex-grow-1">Total Value of Retention (Inclu. this certificate)</div>
                    <div class="text-end" style="width: 150px;">${parseFloat(data.ipc.total_value_of_retention).toFixed(2)}</div>
                  </div>
                  <div class="item">
                    <div class="flex-grow-1">Total Value of Certification made (Inclu. this certificate)</div>
                    <div class="text-end" style="width: 150px;">${parseFloat(data.ipc.total_value_of_certification_made).toFixed(2)}</div>
                  </div>
                  <div class="item">
                    <div class="flex-grow-1">Resulting Balance of Contract Sum Outstanding</div>
                    <div class="text-end" style="width: 150px;">${parseFloat(data.ipc.resulting_balance_of_contract_sum_outstanding).toFixed(2)}</div>
                  </div>
                </div>
                `;
  return content;
}

function loadInspection(data) {
  let content = "";
  content = `
                <div class="d-flex justify-content-between">
                  <div class="col d-flex justify-content-start">
                    <div class="fw-bold" style="width: 200px;">ผู้รับเหมา</div>
                    <div>${data.header.supplier_name}</div>
                  </div>
                </div>

                <div class="d-flex justify-content-between">
                  <div class="col d-flex justify-content-start">
                    <div class="fw-bold" style="width: 200px;">โครงการ</div>
                    <div >${data.header.project_name}</div>
                  </div>

                  <div class="col col-4 d-flex justify-content-start">
                    <div class="fw-bold" style="width: 200px;">สถานที่</div>
                    <div>${data.header.location_name}</div>
                  </div>
                </div>

                <div class="d-flex justify-content-between">
                  <div class="col d-flex justify-content-start">
                    <div class="fw-bold" style="width: 200px;">งาน</div>
                    <div>${data.header.working_name_th} (${data.header.working_name_en})</div>
                  </div>
                </div>

                <div class="d-flex justify-content-between">
                  <div class="col col-4 d-flex justify-content-start">
                    <div class="fw-bold" style="width: 200px;">ระยะเวลาดำเนินการ</div>
                    <div >${data.header.working_date_from}</div>
                  </div>

                  <div class="col col-4 d-flex justify-content-start">
                    <div class="fw-bold" style="width: 200px;">ถึง</div>
                    <div>${data.header.working_date_to}</div>
                  </div>

                  <div class="col col-4 d-flex justify-content-start">
                    <div class="fw-bold" style="width: 200px;">(รวม ${data.header.working_day} วัน)</div>
                  </div>
                </div>

                <hr class="hr border border-dark">

                <div class="d-flex justify-content-between">
                  <div class="col col-6 d-flex justify-content-start">
                    <div class="fw-bold" style="width: 200px;">เลขที่ PO</div>
                    <div >${data.header.po_number}</div>
                  </div>

                  <div class="col col-6 d-flex justify-content-start">
                    <div class="fw-bold" style="width: 200px;">มูลค่างานตาม PO</div>
                    <div>${formatWithComma(data.header.contract_value)} บาท(Includeing VAT${data.header.is_include_vat}%)</div>
                  </div>
                </div>

                <hr class="hr border border-dark">

                <div class="d-flex">
                  <div class="col-3 border-end border-dark-subtle m-0 p-0">
                    <div class="col d-flex justify-content-start">
                      <div class="fw-bold" style="width: 200px;">เบิกงวดงานที่</div>
                      <div>${data.period.period_number}</div>
                    </div>

                    <div class="col d-flex flex-column justify-content-start">
                      <div class="col d-flex justify-content-start">
                        <input type="checkbox" name="deposit" onclick="return false;" checked>
                        <div class="fw-bold" style="width: 200px;">มี Deposit</div>
                        <div>${data.header.deposit_percent}</div>
                      </div>
                      
                      <div class="col d-flex justify-content-start">
                        <input type="checkbox" name="deposit" onclick="return false;">
                        <div class="fw-bold" style="width: 200px;">ไม่มี Deposit</div>
                      </div>
                    </div>
                  </div>

                  <div class="col-9">
                    <div class="col d-flex justify-content-between m-1">
                        <div class="col-5">ยอดเบิกเงินงวดปัจจุบัน</div>
                        <div class="col-5 text-end">${formatWithComma(data.period.interim_payment)} บาท (Including VAT7%) คิดเป็น</div>
                        <div class="col-2 text-end ">${data.period.interim_payment_percent} %</div>
                    </div>

                    <div class="col d-flex justify-content-between m-1">
                        <div class="col-5">ยอดเบิกเงินงวดสะสมไม่รวมปัจจุบัน</div>
                        <div class="col-5 text-end">${formatWithComma(data.period.interim_payment_less_previous)} บาท (Including VAT7%) คิดเป็น</div>
                        <div class="col-2 text-end ">${data.period.interim_payment_less_previous_percent} %</div>
                    </div>

                    <div class="col d-flex justify-content-between m-1">
                        <div class="col-5">ยอดเบิกเงินงวดสะสมถึงปัจจุบัน</div>
                        <div class="col-5 text-end">${formatWithComma(data.period.interim_payment_accumulated)} บาท (Including VAT7%) คิดเป็น</div>
                        <div class="col-2 text-end ">${data.period.interim_payment_accumulated_percent} %</div>
                    </div>

                    <div class="col d-flex justify-content-between m-1">
                        <div class="col-5">ยอดเงินงวดคงเหลือ</div>
                        <div class="col-5 text-end">${formatWithComma(data.period.interim_payment_remain)} บาท (Including VAT7%) คิดเป็น</div>
                        <div class="col-2 text-end ">${data.period.interim_payment_remain_percent} %</div>
                    </div>

                  </div>
                </div>

                <hr class="hr border border-dark">

                <div class="d-flex justify-content-between">
                  <div class="col col-4 d-flex justify-content-between">
                    <div>ปริมาณที่ต้องแล้วเสร็จตามแผนงาน</div>
                    <div>${data.period.workload_planned_percent} %</div>
                  </div>

                  <div class="col col-4 d-flex justify-content-between">
                    <div>ปริมาณที่แล้วเสร็จจริง</div>
                    <div>${data.period.workload_actual_completed_percent} %</div>
                  </div>

                  <div class="col col-4 d-flex justify-content-between">
                    <div>ปริมาณงานคงเหลือ</div>
                    <div>${data.period.workload_remaining_percent} %</div>
                  </div>
                </div>

                <div class="card border border-1 border-dark m-1">
                  <div class="card-body p-0">
                    <table class="table table-bordered justify-content-center text-center" id="tableOrder">
                      <thead>
                        <tr>
                          <th class="p-1" width="10%">ลำดับที่</th>
                          <th class="p-1" width="20%">รายละเอียดการตรวจสอบ</th>
                          <th class="p-1">หมายเหตุ</th>
                        </tr>
                      </thead>

                      <tbody id="tbody-order">`;
  let tableBody = "";
  $.each(data['periodDetails'], function (index, detail) {
    tableBody += `
                    <tr>
                      <td class="tdPeriod text-right py-0 px-1">${detail.order_no}</td>
                      <td class="tdPeriod text-right py-0 px-1">${detail.details}</td>
                      <td class="tdPeriod text-right py-0 px-1">${detail.remark}</td>
                    </tr>       
                    `;
  });
  content += `           ${tableBody}
                      </tbody>
                    </table>
                  </div>
                </div>

                <div class="d-flex justify-content-between">
                  <div class="col d-flex justify-content-start">
                    <div>ปริมาณที่ต้องแล้วเสร็จเมื่อเปรียบเทียบกับแผนงาน : ${data.plan_status.plan_status_name} %</div>
                  </div>
                </div>

                <div>หมายเหตุ:</div>
                <div class="form-floating">
                  <textarea name="remark" class="form-control" id="remark" rows="4" style="min-height: 4em;height: auto;" readonly>${data.period.remark}</textarea>
                </div>

                <div class="col d-flex justify-content-start">
                  <div>ผู้รับเหมาได้ดำเนินการตามรายละเอียดดังกล่าวข้างต้น จึงเห็นสมควร</div>
                  <div class="col d-flex justify-content-start">
                    <input type="radio" name="disbursement" onclick="return false;" checked>
                    <div class="fw-bold" style="width: 200px;">อนุมัติเบิกจ่าย</div>
                  </div>
                  
                  <div class="col d-flex justify-content-start">
                    <input type="radio" name="disbursement" onclick="return false;">
                    <div class="fw-bold" style="width: 200px;">ไม่อนุมัติเบิกจ่าย</div>
                  </div>
                </div>
                `;
  return content;
}

function loadAttach(data) {
  let content = "";

  content += `
        <div class="card mb-3 shadow-sm">
          <div class="card-body">
            <h5 class="card-title">${data.file_name}</h5>
            <p class="card-text">${data.file_type}</p>
      `;

  if (data.file_type === "image/jpeg") {
    content += `<img src="${data.file_path}" class="img-fluid rounded">`;
  } else if (data.file_type === "application/pdf") {
    content += `
          <div class="ratio ratio-16x9">
            <iframe src="${data.file_path}" frameborder="0"></iframe>
          </div>
        `;
  }

  content += `
          </div>
        </div>
      `;
  console.log(content);
  return content;
}

function refreshForm() {
  const myForm = $('#myForm');
  // จัดการปุ่ม action
  if (myForm.data('ipc-status') == 'pending-submit' && myForm.data('current-approver-id') == myForm.data('user-id')) {
    $("#btnAction").addClass('inline');
    $("#btnAction").removeClass('d-none');
  }
  else if (myForm.data('ipc-status') == 'pending-approve' && myForm.data('current-approver-id') == myForm.data('user-id')) {
    $("#btnAction").addClass('inline');
    $("#btnAction").removeClass('d-none');
  }
  else {
    $("#btnAction").addClass('d-none');
    $("#btnAction").removeClass('inline');
  }
}

function formatNumber(num) {
  return num.toLocaleString('th-TH', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  });
}

// ฟังก์ชันบังคับใช้ comma เสมอ
function formatWithComma(num) {
  return Number(num).toLocaleString('th-TH', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  });
}

// ฟังก์ชันกดพิมพ์ PDF
function printPDF(ipcId, inspectionId) {
  window.open("ipc_generate_pdf.php?ipc_id=" + ipcId + "&inspection_id=" + inspectionId, "_blank");
}

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

  // - action มาจากการกดปุ่มว่าเป็นอะไร เช่น save, submit, approve, reject เป็นต้น
  // โดย submit และ approve เป็นการเลื่อน level เหมือนกัน  ต่างกันแค่ชื่อ   ซึ่งอาจจะดึงข้อมูลชื่อมาจาก workflow_step
  // - data เป็นข้อมูลที่มาจากฟอร์มเพื่อนำมาบันทึกข้อมูล
  // เช่นถ้าเป็นการ save จะดึงข้อมูลของ ipc บน form ส่งมาให้   เพื่อมาทำการ save
  // ถ้าเป็นการ submit sinv reject ไม่ต้องส่งข้อมูลของ form มาเพราะไม่ได้ใช้ข้อมูลบน form แต่ใช้การเลื่อนหรือถอย level จากการดึงข้อมูลใน workflow_step
  // แต่ทุก action ต้องส่ง data ที่มีข้อมูลอย่างน้อยคือ ipc-id, user-id
  function sendRequest(action, data) {
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



  refreshForm();
  loadPage(1);
});