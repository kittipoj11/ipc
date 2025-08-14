$(document).ready(function () {
  $(document).on("click", "a.po_number", function (e) {
      e.preventDefault(); // ป้องกันการทำงาน default ของลิงก์ (ไม่ต้องเปลี่ยนหน้า)
      const po_id = $(this).closest("tr").data("id");
      // window.location.href = "po_edit.php?po_id=" + po_id + "&href=inspect.php";
      window.location.href = "inspection_view.php?po_id=" + po_id;
  });
});

$(document).ready(function () {
  // Click ที่รายการใดๆ
  // $(document).on("click", ".tdMain:not(:has(a))", function (e) {
  //   e.preventDefault();
  //   $(".content-period").removeClass("d-none");
  //   // หรือ
  //   // $(".content-period").removeClass('d-none').addClass('d-flex');

  //   let po_id = $(this).closest("tr").data("id"); //$(this).closest("tr")
  //   let po_number = $(this).closest("tr").find("a:first").html();
  //   // let po_id = $(this).closest('tr').attr('po-id');
  //   $(".card-title").html(po_number);

  //   $.ajax({
  //     url: "ipc_crud.php",
  //     type: "POST",
  //     data: {
  //       po_id: po_id,
  //       action: "selectInspectionPeriodAll",
  //     },
  //     dataType: "json",
  //     success: function (response) {
  //       // console.log(`response=${response}`);
  //       // data = JSON.parse(response);
  //       // console.log(data);

  //       $("#tbody-period").html(response);
  //     },
  //   });
  // });

  $(document).on("click", ".tdPeriod", function (e) {
    e.preventDefault();

    // const po_id = $(this).closest("tr").data("po_id");
    // const period_id = $(this).closest("tr").data("period_id");
    // const inspection_id = $(this).closest("tr").data("inspection_id");
    const ipcId = $(this).closest("tr").data("ipc-id");
    // window.location.href = `inspection_form.php?po_id=${po_id}&period_id=${period_id}&inspection_id=${inspection_id}`;
    window.location.href = `ipc_form.php?ipc_id=${ipcId}`;
  });

  $("#btnCancel").click(function () {
    window.history.back();
    // window.location.href = "inspection_view.php";
    // window.history.go(-1);
    // $('.main').load('open_area_schedule_main.php'); แบบนี้ไม่ได้
    // header('Location: main.php?page=open_area_schedule_main');แบบนี้ไม่ได้
  });
});
