$(document).ready(function () {

  $(".btnEdit").each(function () {
    // เลือกทุก element ที่มี class 'btnEdit'
    $(this).on("click", function (event) {
      event.preventDefault(); // ป้องกันการทำงาน default ของลิงก์ (ไม่ต้องเปลี่ยนหน้า)
      const po_id = $(this).data("id"); // อ่านค่า data-id จากลิงก์ที่คลิก
      const po_no = $(this).parents("tr").find("a:first").data("id");

      window.location.href = "po_edit.php?po_id=" + po_id;
    });
  });
});

$(document).ready(function () {
  // Click ที่รายการใดๆ
  $(document).on("click", ".tdMain:not(.action)", function (e) {
    e.preventDefault();
    $(".content-period").removeClass("d-none");
    // หรือ
    // $(".content-period").removeClass('d-none').addClass('d-flex');

    let po_id = $(this).parents("tr").data("id"); //$(this).closest("tr")
    let po_no = $(this).parents("tr").find("a:first").html();
    // let po_id = $(this).closest('tr').attr('po-id');
    $(".card-title").html(po_no);

    $.ajax({
      url: "inspect_crud.php",
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
