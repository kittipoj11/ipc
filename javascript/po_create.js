$(document).ready(function () {
  $("#myForm").on("submit", function (e) {
    // $(document).on("click", "#btnSave", function (e) {
    // console.log('submit');
    e.preventDefault();
    let can_save = true;
    if (can_save == true) {
      let data_sent = $("#myForm").serializeArray();
      data_sent.push({
        name: "action",
        value: "insert",
      });
      // console.log(data_sent);
      $.ajax({
        type: "POST",
        url: "po_crud.php",
        // data: $(this).serialize(),
        data: data_sent,
        success: function (response) {
          Swal.fire({
            icon: "success",
            title: "Data saved successfully",
            color: "#716add",
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
              window.location.href = "po.php";
              // window.location.reload();
            }
          });
          // window.location.href = 'main.php?page=open_area_schedule';
        },
      });
    }
  });
});


$(document).ready(function () {
  let period_number;
  $("#btnAdd").click(function () {
    // console.log($(".firstTr:last").find(".period_number:last").val());
    period_number = $(".firstTr:last").find(".period_number:last").val();
    period_number++;
    $(".firstTr:last")
      .clone(false)
      .find(".period_number:last")
      .val(period_number)
      // .attr("id", "row" + i + "")
      .end()

      .find(".interim_payment:last")
      .val("")
      .end()

      .find(".interim_payment_percent:last")
      .val("")
      .end()

      .find(".remark:last")
      .val("")
      .end()

      // .find("a:first")
      // .css("display", "inline")
      // .css("color", "red")
      // .end()

      // .find("a:last")
      // .css("display", "inline")
      // .css("color", "red")
      // .end()

      // .find("a:first")
      // .attr("iid", "" + i + "")
      // .end()

      .appendTo("#tableBody");
  });

  $("#btnClear").click(function () {
    // ลบ tr ทั้งหมดที่ไม่ใช่ตัวแรกใน #tableBody
    // $("#tableBody tr:gt(0)").remove();
    // หรือ
    // $("#tableBody").find("tr:not(:first)").remove();
    // หรือ
    $("#tableBody").find("tr:gt(0)").remove();
  });

  $("#btnDeleteLast").click(function () {
    let period_number;
    // ลบ tr ตัวล่างสุดที่ไม่ใช่ tr ตัวแรก ใน #tableBody
    // $("#tableBody").find("tr:not(:first):last").remove();
    $("#tableBody tr:not(:first):last").remove();
  });

  // $(".btnDeleteThis").click(function() {
  // สำหรับใช้กับปุ่มที่อยู่ภายใน <td>
  $(document).on("click", ".btnDeleteThis", function () {
    // ส่วนสำหรับการลบ
    // let row_id = $(this).attr("iid");
    // console.log("#row" + row_id + "");
    // เมื่อปุ่มนี้ถูกกด(this)จะลบ tr ของปุ่มนี้ออกไป
    // $(this).closest("tr").remove();
    // หรือใช้
    $(this).parents("tr").remove();
  });

  $(document).ready(function () {
    $("#btnCancel").click(function () {
      // history.go(-1);
      // $('.main').load('open_area_schedule_main.php'); แบบนี้ไม่ได้
      // header('Location: main.php?page=open_area_schedule_main');แบบนี้ไม่ได้
      window.location.href = "po.php";
    });
  });
});

