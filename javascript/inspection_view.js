$(document).ready(function () {

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

  $(document).on("click", "a.po_number", function (e) {
    e.preventDefault();
    const po_id = $(this).closest("tr").data("id");
    window.location.href = "inspection_view.php?po_id=" + po_id;
  });

  // Click ที่รายการใดๆ
  $(document).on("click", ".tdMain:not(:has(a))", function (e) {
    e.preventDefault();
    $(".content-period").removeClass("d-none");
    // หรือ
    // $(".content-period").removeClass('d-none').addClass('d-flex');

    let po_id = $(this).closest("tr").data("id"); //$(this).closest("tr")
    let po_number = $(this).closest("tr").find("a:first").html();
    // let po_id = $(this).closest('tr').attr('po-id');
    $(".card-title").html(po_number);

    $.ajax({
      url: "inspection_crud.php",
      type: "POST",
      data: {
        po_id: po_id,
        action: "selectInspectionPeriodAll",
      },
      dataType: "json",
      success: function (response) {
        $("#tbody-period").html(response);
      },
    });
  });

  $(document).on("click", ".tdPeriod", function (e) {
    e.preventDefault();

    const po_id = $(this).closest("tr").data("po_id");
    const period_id = $(this).closest("tr").data("period_id");
    const inspection_id = $(this).closest("tr").data("inspection_id");
    window.location.href = `inspection_edit.php?po_id=${po_id}&period_id=${period_id}&inspection_id=${inspection_id}`;
  });

  $("#btnCancel").click(function () {
    window.history.back();
  });

// loadDataAll();
});
