$(document).ready(function () {
  $(document).on("click", "a.po_number", function (e) {
      e.preventDefault(); // ป้องกันการทำงาน default ของลิงก์ (ไม่ต้องเปลี่ยนหน้า)
      const po_id = $(this).closest("tr").data("po_id");
      // window.location.href = "po_edit.php?po_id=" + po_id + "&href=inspect.php";
      window.location.href = "ipc_view.php?po_id=" + po_id;
  });
});


$(document).ready(function () {
  // Click ที่รายการใดๆ
  $(document).on("click", ".tdMain:not(:has(a))", function (e) {
    e.preventDefault();
    $(".content-period").removeClass("d-none");
    // หรือ
    // $(".content-period").removeClass('d-none').addClass('d-flex');

    let po_id = $(this).closest("tr").data("po_id"); //$(this).closest("tr")
    let po_number = $(this).closest("tr").find("a:first").data("po_number");
    console.log(`po_id = ${po_id}` );
    console.log(`po_number = ${po_number}` );
    // let po_id = $(this).closest('tr').attr('po-id');
    $(".card-title").html(po_number);

    $.ajax({
      url: "ipc_crud.php",
      type: "POST",
      data: {
        po_id: po_id,
        action: "selectInspectionPeriodAll",
      },
      // dataType: "json",
      success: function (response) {
        console.log(`response=${response}`);
        // data = JSON.parse(response);
        // console.log(data);

        $("#tbody-period").html(response);
      },
      error:function(){
        console.log(`Error!!!`);
      }
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
const inspection_id = $(this).closest("tr").find("input.inspection_id").val();
// console.log(`period_id = ${period_id}`);
// let po_id=1;
// let period_id=1;
console.log(`po_id = ${po_id}`);
console.log(`period_id = ${period_id}`);
console.log(`inspection_id = ${inspection_id}`);
    window.location.href = `inspection_period_form.php?po_id=${po_id}&period_id=${period_id}&inspection_id=${inspection_id}`;
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
