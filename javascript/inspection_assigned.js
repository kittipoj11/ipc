$(document).ready(function () {

  $(".approval_next").on("click", function (e) {
    // $(document).on("click", "#btnSave", function (e) {
    console.log("click");
    e.preventDefault();
    let current_approval_level = $(this).closest("ul").data("current_approval_level");
    let new_approval_level = current_approval_level + 1;

      let data_sent = $("#myForm").serializeArray();
      data_sent.push(
        {
          name: "action",
          value: "updateCurrentApprovalLevel",
        },
        {
          name: "new_approval_level",
          value: new_approval_level,
        },
        {
          name: "current_approval_level",
          value: current_approval_level,
        }
      );
      // console.log(data_sent);
      // return;
      $.ajax({
        type: "POST",
        url: "inspection_crud.php",
        // data: $(this).serialize(),
        data: data_sent,
        success: function (response) {
          Swal.fire({
            icon: "success",
            title: "Approved successfully",
            color: "#716add",
            allowOutsideClick: false,
            background: "black",
            // backdrop: `
            //                     rgba(0,0,123,0.4)
            //                     url("_images/paw.gif")
            //                     left bottom
            //                     no-repeat
            //                     `,
            // showConfirmButton: false,
            // timer: 15000
          }).then((result) => {
            if (result.isConfirmed) {
              window.location.href = "inspection.php";
              // window.location.reload();
            }
          });
          // window.location.href = 'main.php?page=open_area_schedule';
        },
      });
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
    const mode = 'd-none';

    // console.log(`po_id = ${po_id}`);
    // console.log(`period_id = ${period_id}`);
    // console.log(`inspection_id = ${inspection_id}`);
    window.location.href = `inspection_attach.php?po_id=${po_id}&period_id=${period_id}&inspection_id=${inspection_id}&mode=${mode}`;
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
