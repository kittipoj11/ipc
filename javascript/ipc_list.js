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
      url: "ipc_handler_api.php",
      type: "post",
      contentType: "application/json",
      dataType: "json",
      data: JSON.stringify(data_sent),
    })
      .done(function (result) {
        // console.log(result);
        if (result.length > 0) {
          tableBody = createTableBody(result);
          $("#tbody").html(tableBody); // นำ HTML ของตารางไปใส่ใน div ที่มี id="tbody"
        } else {
          $("#tbody").html();
        }
      })
      .fail((xhr) => {
        const errorMsg = xhr.responseJSON
          ? xhr.responseJSON.message
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
                          <td class="tdMain p-0"><a class='link-opacity-100 pe-auto po_number' title='View' style='margin: 0px 5px 5px 5px'>${data.po_number}</a></td>
                          <td class="tdMain p-0">${data.project_name}</td>
                          <td class="tdMain p-0">${data.supplier_name}</td>
                          <td class="tdMain p-0">${data.location_name}</td>
                          <td class="tdMain p-0">${data.working_name_th}</td>
                          <td class="tdMain p-0 text-right">${data.contract_value}</td>
                          <td class="tdMain p-0 text-right">${data.number_of_period}</td>
                      </tr>
                            
                    `;
    });
    return tableBody;
  }

  function createPeriodTable(datas) {
    let tableBody = "";
    $.each(datas, function (index, data) {
      tableBody += `
                    <tr data-po-id=${data.po_id} data-period-id=${data.period_id} data-inspection-id=${data.inspection_id} data-ipc-id=${data.ipc_id}>
                      <td class="tdPeriod text-right py-0 px-1"><a class="link-opacity-100 pe-auto period_number" style="margin: 0px 5px 5px 5px">${data.period_number}</a></td>
                      <td class="tdPeriod text-right py-0 px-1">${data.net_value_of_current_claim}</td>
                      <td class="tdPeriod text-right py-0 px-1">${data.less_retension_exclude_vat}</td>
                      <td class="tdPeriod text-right py-0 px-1">${data.total_value_of_retention}</td>
                      <td class="tdPeriod text-right py-0 px-1">${data.net_amount_due_for_payment}</td>
                      <td class="tdPeriod text-right py-0 px-1">${data.total_value_of_certification_made}</td>
                      <td class="tdPeriod text-right py-0 px-1 d-none">0</td>
                      <td class="tdPeriod text-right py-0 px-1">${data.agreement_date}</td>
                      <td class="tdPeriod text-left py-0 px-1">${data.remark}</td>
                    </tr>                      
                  `;
    });
    return tableBody;
  }

    // Click ที่รายการงวดงานใดๆใน tdMain ที่ไม่มี <a></a>
  // $(document).on("click", ".tdMain:not(:has(a))", function (e) {
  $(document).on("click", ".tdMain", function (e) {
    e.preventDefault();
    $(".content-period").removeClass("d-none");

    const po_id = $(this).closest("tr").data("po-id");
    const po_number = $(this).closest("tr").data("po-number");

    $(".card-title").html(po_number);

    const data_sent = {
      po_id: po_id,
      action: "selectIpcPeriodAll",
    };
    $.ajax({
      url: "ipc_handler_api.php",
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

  


  $(document).on("click", "a.po_number", function (e) {
    e.preventDefault();
    const po_id = $(this).closest("tr").data("po-id");
    window.location.href = "ipc_view.php?po_id=" + po_id;
  });


    $(document).on("click", "a.period_number", function (e) {
      e.preventDefault();

      const po_id = $(this).closest("tr").data("po-id");
      const period_id = $(this).closest("tr").data("period-id");
      const inspection_id = $(this).closest("tr").data("inspection-id");
      const ipc_id = $(this).closest("tr").data("ipc-id");
      // window.location.href = `ipc_form.php?po_id=${po_id}&period_id=${period_id}&inspection_id=${inspection_id}&ipc_id=${ipc_id}`;
      window.location.href = `ipc_form.php?ipc_id=${ipc_id}`;
    });

  // loadAllInspection();
  loadDataAll();
});
