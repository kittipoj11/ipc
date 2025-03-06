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
        dataType: "json",
        action: "selectperiod",
      },
      success: function (response) {
        // console.log(`response=${response}`);
        // data = JSON.parse(response);
        // console.log(data);

        $("#tbody-period").html(response);
      },
    });
  });

  $(document).on("click", ".tdPeriod", function (e) {
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

    const po_id = $(this).closest("tr").find(".po_id").data("id");
    // console.log(`po_id = ${po_id}`);
    const period_id = $(this).closest("tr").find(".period_id").data("id");
    // console.log(`period_id = ${period_id}`);
    // let po_id=1;
    // let period_id=1;
    console.log(`po_id = ${po_id}`);
    console.log(`period_id = ${period_id}`);
    window.location.href = `inspection_edit.php?po_id=${po_id}&period_id=${period_id}`;
  });

  $("#btnCancel").click(function () {
    window.history.back();
    // window.location.href = "inspection_view.php";
    // window.history.go(-1);
    // $('.main').load('open_area_schedule_main.php'); แบบนี้ไม่ได้
    // header('Location: main.php?page=open_area_schedule_main');แบบนี้ไม่ได้
  });
});