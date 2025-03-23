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
  $("#btnAdd").click(function () {
    let period;
    // console.log($(".firstTr:last").find(".period:last").val());
    // $(".firstTr:has(.crud:not([value='d'])):last")//แบบที่ 1
    // $(".firstTr").has(".crud:not([value='d'])").last()//แบบที่ 2
    if ($("#tbody-period").has(".firstTr[crud!='d']").length > 0) {
      period = $(".firstTr[crud!='d']:last").find(".period_number:last").val();
      period++;
      $(".firstTr[crud!='d']:last")
        .clone(false)
        .attr("crud", "i")
        .removeClass("d-none")

        .find(".period_number:last")
        .val(period)
        .end()

        .find(".workload_planned_percent:last")
        .val("")
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

        .find(".period_id:last")
        .val("")
        .end()

        .find("td input.crud")
        .val("i")
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

        .appendTo("#tbody-period");
    } else {
      // Create the new tr element using jQuery
      const firstTr = `<tr class='firstTr' crud='i'>
                            <td class='input-group-sm p-0'><input type='number' name='period_numbers[]' class='form-control period_number' value='1' readonly></td>
                            <td class='input-group-sm p-0'><input type='number' name='workload_planned_percents[]' class='form-control workload_planned_percent'></td>
                            <td class='input-group-sm p-0'><input type='number' name='interim_payments[]' class='form-control interim_payment'></td>
                            <td class='input-group-sm p-0'><input type='number' name='interim_payment_percents[]' class='form-control interim_payment_percent'></td>
                            <td class='input-group-sm p-0'><input type='text' name='remarks[]' class='form-control remark'></td>
                            <td class='input-group-sm p-0'><input type='text' name='cruds[]' class='form-control crud' value='i'></td>
                            <td class='input-group-sm p-0 d-nonex'><input type='text' name='period_id[]' class='form-control period_id' readonly></td>
                          </tr>`;

      $("#tbody-period").append(firstTr);
    }
  });

  $("#btnClear").click(function () {
    // ลบ tr ทั้งหมดที่ไม่ใช่ตัวแรกใน #tbody-period
    // $("#tbody-period tr:gt(0)").remove();
    // หรือ
    // $("#tbody-period").find("tr:not(:first)").remove();
    // หรือ
    // $("#tbody-period").find("tr:gt(0)").remove();

    // ลบ tr ทั้งหมด
    $("#tbody-period tr").remove();
  });

  $("#btnDeleteLast").click(function () {
    let period_number;
    // ลบ tr ตัวล่างสุดที่ไม่ใช่ tr ตัวแรก ใน #tbody-period
    // $("#tbody-period").find("tr:not(:first):last").remove();
          $("#tbody-period .firstTr[crud!='d']:last")
            // $("#tbody-period tr:not(:first)[crud!='d']:last")
            .attr("crud", "d")
            .addClass("d-none")

            .find("td input.crud")
            .val("d")
            .end();
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
      window.history.back();
    });
  });
});

