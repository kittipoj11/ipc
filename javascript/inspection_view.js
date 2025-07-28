$(document).ready(function () {

    // ฟังก์ชันสำหรับโหลดข้อมูลเริ่มต้น
  function loadDataAll() {
    const data_sent = {
      action: "select",
    };
    $.ajax({
      url: "inspection_handler_api.php",
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

  $(document).on("click", ".tdPeriod", function (e) {
    e.preventDefault();

    const po_id = $(this).closest("tr").data("po-id");
    const period_id = $(this).closest("tr").data("period-id");
    const inspection_id = $(this).closest("tr").data("inspection-id");
    window.location.href = `inspection_form.php?po_id=${po_id}&period_id=${period_id}&inspection_id=${inspection_id}`;
  });

  $("#btnCancel").click(function () {
    window.history.back();
  });

// loadDataAll();
});
