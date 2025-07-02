$(document).ready(function () {
  $(document).on("click", ".tdMain", function (e) {
    e.preventDefault();

    const po_id = $(this).closest("tr").data("po_id");
    const period_id = $(this).closest("tr").data("period_id");
    const inspection_id = $(this).closest("tr").data("inspection_id");
    // const po_id = $(this).closest("tr").find("input.po_id").val();
    // const period_id = $(this).closest("tr").find("input.period_id").val();
    // const inspection_id = $(this).closest("tr").find("input.inspection_id").val();
    // console.log(`period_id = ${period_id}`);
    // let po_id=1;
    // let period_id=1;
    // console.log(`po_id = ${po_id}`);
    // console.log(`period_id = ${period_id}`);
    // console.log(`inspection_id = ${inspection_id}`);
    window.location.href = `inspection_action.php?po_id=${po_id}&period_id=${period_id}&inspection_id=${inspection_id}`;
  });

  $(".btnCancel, button[name='btnClose']").click(function () {
    window.history.back();
    // window.location.href = "inspection_view.php";
    // window.history.go(-1);
    // $('.main').load('open_area_schedule_main.php'); แบบนี้ไม่ได้
    // header('Location: main.php?page=open_area_schedule_main');แบบนี้ไม่ได้
  });

  $(document).on("click", "#btnAttach", function (e) {
    e.preventDefault();

    const po_id = $("#po_id").val();
    const period_id = $("#period_id").val();
    const inspection_id = $("#inspection_id").val();
    const mode = "d-none";

    // console.log(`po_id = ${po_id}`);
    // console.log(`period_id = ${period_id}`);
    // console.log(`inspection_id = ${inspection_id}`);
    window.location.href = `inspection_period_attach_form.php?po_id=${po_id}&period_id=${period_id}&inspection_id=${inspection_id}&mode=${mode}`;
  });

  $("#floatingTextarea").on("click", function () {
    console.log($(this).val());
  });

  function loadPage() {
    // $.ajax({
    //   url: "get_files.php",
    //   type: "GET",
    //   success: function (response) {
    //     $("#fileDisplay").html(response);
    //   },
    //   error: function () {
    //     $("#fileDisplay").html("ไม่สามารถโหลดไฟล์ได้.");
    //   },
    // });
    if ($("#submit").data("current_approval_level") > 1) {
      $("#submit").addClass("d-none");
    } else {
      $("#submit").removeClass("d-none");
    }
  }

  loadPage();
});
