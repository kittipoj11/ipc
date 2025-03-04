$(document).ready(function () {
  $(document).on("click", "a.po_no", function (e) {
      e.preventDefault(); // ป้องกันการทำงาน default ของลิงก์ (ไม่ต้องเปลี่ยนหน้า)
      const po_id = $(this).closest("tr").data("id");
      // window.location.href = "po_edit.php?po_id=" + po_id + "&href=inspect.php";
      window.location.href = "po_edit.php?po_id=" + po_id;
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
    let po_no = $(this).closest("tr").find("a:first").html();
    // let po_id = $(this).closest('tr').attr('po-id');
    $(".card-title").html(po_no);

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

  $(document).on("click", ".period", function (e) {
    e.preventDefault();
// การใช้ตัวแปรในการเก็บค่า
// // ค้นหา tr ที่ปุ่ม a.period อยู่
// let row = $(this).closest('tr');
// // ค้น input ที่มี class po_id
// let inputPoId = row.find('input.po_id');
// // ดึงค่าจาก inputPoId
// let po_id = inputPoId.val();
// // ค้น input ที่มี class po_period_id
// let inputPoPeriodId = row.find('input.po_period_id');
// // ดึงค่าจาก inputPoPeriodId
// let po_period_id = inputPoPeriodId.val();

const po_id = $(this).closest("tr").find("input.po_id").val();
// console.log(`po_id = ${po_id}`);
const po_period_id = $(this).closest("tr").find("input.po_period_id").val();
// console.log(`po_period_id = ${po_period_id}`);
// let po_id=1;
// let po_period_id=1;
console.log(`po_id = ${po_id}`);
console.log(`po_period_id = ${po_period_id}`);
    window.location.href = `inspection_edit.php?po_id=${po_id}&po_period_id=${po_period_id}`;
  });
});

// document.getElementById("opt_event_id").addEventListener("change", complete_selection);
// document
//   .getElementById("building_id")
//   .addEventListener("change", complete_selection);
// document
//   .getElementById("hall_id")
//   .addEventListener("change", complete_selection);
// document
//   .getElementById("event_name")
//   .addEventListener("keyup", complete_selection);

// function complete_selection() {
//   if (
//     $("#event_name").val().trim().length === 0 ||
//     $("#building_id option:selected").text() == "..." ||
//     $("#hall_id option:selected").text() == "..."
//   ) {
//     $("#div_open_area_schedule").hide();
//   } else {
//     $("#div_open_area_schedule").show();
//   }
// }

// Hall: on change
