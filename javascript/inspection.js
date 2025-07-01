$(document).ready(function () {
  function showMessage(message, isSuccess) {
    responseMessage
      .text(message)
      .removeClass("success error")
      .addClass(isSuccess ? "success" : "error")
      .show()
      .delay(5000)
      .fadeOut();
  }

  // ฟังก์ชันสำหรับโหลดข้อมูลเริ่มต้น
  function loadDataAll() {
    const data_sent = {
      action: "select",
    };
    $.ajax({
      url: "api_handler_inspection.php",
      type: "POST",
      contentType: "application/json",
      dataType: "json",
      data: JSON.stringify(data_sent),
    })
      .done(function (result) {
        console.log(`result: ${result}`);
        // responseMessage.text(result.message).css("color", "green");
        if (result.length > 0) {
          tableBody = createTableBody(result);
          $("#tbody").html(tableBody); // นำ HTML ของตารางไปใส่ใน div ที่มี id="tbody"
        } else {
          $("#tbody").html();
        }
      })
      .fail((jqXHR) => {
        const errorMsg = jqXHR.responseJSON
          ? jqXHR.responseJSON.message
          : "เกิดข้อผิดพลาดในการดึงข้อมูล";
        // showMessage(errorMsg, false);
        $("#tbody").html();
      });
  }

  function createTableBody(datas) {
    let tableBody = "";
    $.each(datas, function (index, data) {
      tableBody += `
                      <tr data-po-id='${data.po_id}' data-po-number='${data.po_number}'>
                          <td class="tdMain p-0 d-none">${data.po_id}</td>
                          <td class="tdMain p-0"><a class='link-opacity-100 pe-auto po_number' title='View' style='margin: 0px 5px 5px 5px'>${data.po_number}</a></td>
                          <td class="tdMain p-0">${data.project_name}</td>
                          <td class="tdMain p-0">${data.supplier_name}</td>
                          <td class="tdMain p-0">${data.location_name}</td>
                          <td class="tdMain p-0">${data.working_name_th}</td>
                          <td class="tdMain p-0 text-right">${data.contract_value}</td>
                          <td class="tdMain p-0 text-right">${data.number_of_period}</td>
                          <td class="tdMain p-0 action d-none" align='center'>
                              <div class='btn-group-sm'>
                                  <a class='btn btn-warning btn-sm btnEdit' style='margin: 0px 5px 5px 5px' data-po-id='${data.po_id}'>
                                      <i class='fa-regular fa-pen-to-square'></i>
                                  </a>
                                  <a class='btn btn-danger btn-sm btnDelete' style='margin: 0px 5px 5px 5px' data-po-id='${data.po_id}'>
                                      <i class='fa-regular fa-trash-can'></i>
                                  </a>
                              </div>
                          </td>
                      </tr>
                            
                    `;
    });
    return tableBody;
  }

  function createPeriodTable(datas) {
    let tableBody = "";
    $.each(datas, function (index, data) {
      tableBody += `
                    <tr data-po-id=${data.po_id} data-inspection-id=${data.inspection_id}>
                      <td class="tdPeriod text-right input-group-sm p-0 d-none"><input type="number" class="form-control text-right po_id" value="${data.po_id}" readonly></td>
                      <td class="tdPeriod text-right input-group-sm p-0 d-none"><input type="number" class="form-control text-right period_id" value="${data.period_id}" readonly></td>
                      <td class="tdPeriod text-right input-group-sm p-0 d-none"><input type="number" class="form-control text-right inspection_id" value="${data.inspection_id}" readonly></td>
                      <td class="tdPeriod text-right py-0 px-1"><a class="link-opacity-100 pe-auto period_number" style="margin: 0px 5px 5px 5px">${data.period_number}</a></td>
                      <td class="tdPeriod text-right py-0 px-1">${data.workload_planned_percent}</td>
                      <td class="tdPeriod text-right py-0 px-1">${data.workload_actual_completed_percent}</td>
                      <td class="tdPeriod text-right py-0 px-1">${data.workload_remaining_percent}</td>
                      <td class="tdPeriod text-right py-0 px-1">${data.interim_payment}</td>
                      <td class="tdPeriod text-right py-0 px-1">${data.interim_payment_less_previous}</td>
                      <td class="tdPeriod text-right py-0 px-1">${data.interim_payment_remain}</td>
                      <td class="tdPeriod text-left py-0 px-1">${data.remark}</td>
                    </tr>                      
                  `;
    });
    return tableBody;
  }

  $(document).on("click", "a.po_number", function (e) {
    e.preventDefault();
    const po_id = $(this).closest("tr").data("po-id");
    window.location.href = "inspection_view.php?po_id=" + po_id;
  });

  // Click ที่รายการงวดงานใดๆใน tdMain ที่ไม่มี <a></a>
  $(document).on("click", ".tdMain:not(:has(a))", function (e) {
    e.preventDefault();
    $(".content-period").removeClass("d-none");

    const po_id = $(this).closest("tr").data("po-id");
    const po_number = $(this).closest("tr").data("po-number");

    $(".card-title").html(po_number);

    const data_sent = {
      po_id: po_id,
      action: "selectInspectionPeriodAll",
    };
    $.ajax({
      url: "api_handler_inspection.php",
      type: "POST",
      contentType: "application/json",
      dataType: "json",
      data: JSON.stringify(data_sent),
    })
      .done(function (result) {
        if (result.length > 0) {
          tableBody = createPeriodTable(result);
          $("#tbody-period").html(tableBody);
        } else {
          $("#tbody-period").html();
        }
      })
      .fail((jqXHR) => {
        const errorMsg = jqXHR.responseJSON
          ? jqXHR.responseJSON.message
          : "เกิดข้อผิดพลาดในการดึงข้อมูล";
        // showMessage(errorMsg, false);
        $("#tbody-period").html();
      });
  });

    $(document).on("click", ".period_number", function (e) {
      e.preventDefault();
      // การใช้ตัวแปรในการเก็บค่า
      // // ค้นหา tr ที่ปุ่ม a.period อยู่
      // let row = $(this).closest('tr');
      // // ค้น input ที่มี class po_id
      // let inputPoId = row.find('input.po_id');
      // // ดึงค่าจาก inputPoId
      // let po_id = inputPoId.val();
      // // ค้น input ที่มี class period_id
      // let inputPoPeriodId = row.find('input.period_id');
      // // ดึงค่าจาก inputPoPeriodId
      // let period_id = inputPoPeriodId.val();

      const po_id = $(this).closest("tr").find("input.po_id").val();
      // console.log(`po_id = ${po_id}`);
      const period_id = $(this).closest("tr").find("input.period_id").val();
      const inspection_id = $(this)
        .closest("tr")
        .find("input.inspection_id")
        .val();
      // console.log(`period_id = ${period_id}`);
      // let po_id=1;
      // let period_id=1;
      // console.log(`po_id = ${po_id}`);
      // console.log(`period_id = ${period_id}`);
      // console.log(`inspection_id = ${inspection_id}`);
      window.location.href = `inspection_edit.php?po_id=${po_id}&period_id=${period_id}&inspection_id=${inspection_id}`;
    });

  // loadAllInspection();
  loadDataAll();
});
