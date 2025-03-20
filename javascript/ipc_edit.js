$(document).ready(function () {

  $(document).on("click", "#btnAttach", function (e) {
    e.preventDefault();

const po_id = $('#po_id').val();
const period_id = $('#period_id').val();
const inspection_id = $('#inspection_id').val();

// console.log(`po_id = ${po_id}`);
// console.log(`period_id = ${period_id}`);
// console.log(`inspection_id = ${inspection_id}`);
    window.location.href = `inspection_attach.php?po_id=${po_id}&period_id=${period_id}&inspection_id=${inspection_id}`;
  });

  $("#myForm").on("submit", function (e) {
    // $(document).on("click", "#btnSave", function (e) {
    // console.log('submit');
    e.preventDefault();
    let can_save = true;
    if (can_save == true) {
      let data_sent = $("#myForm").serializeArray();
      data_sent.push({
        name: "action",
        value: "updateInspectionPeriod",
      });
      // console.log(data_sent);
      // return;
      $.ajax({
        type: "POST",
        url: "ipc_crud.php",
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
              window.location.href = "inspection.php";
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
  let order_no;

  $("#btnAdd").click(function () {
    // console.log($(".firstTr:last").find(".order_no:last").val());
        // $(".firstTr:has(.crud:not([value='d'])):last")//แบบที่ 1
    // $(".firstTr").has(".crud:not([value='d'])").last()//แบบที่ 2
    if ($("#tbody-order").has(".firstTr[crud!='d']").length > 0) {
      order_no = $(".firstTr[crud!='d']:last").find(".order_no:last").val();
      order_no++;
      $(".firstTr[crud!='d']:last")
        .clone(false)
        .attr("crud", "i")
        .removeClass("d-none")

        .find(".order_no:last")
        .val(order_no)
        .end()

        .find(".detail:last")
        .val("")
        .end()

        .find(".remark:last")
        .val("")
        .end()

        .find(".rec_id:last")
        .val("")
        .end()

        .find("td input.crud")
        .val("i")
        .end()

        .appendTo("#tbody-order");
    } else {
      // Create the new tr element using jQuery
      const firstTr = `<tr class='firstTr' crud='i'>
                        <td class='input-group-sm p-0'><input type='number' name='order_nos[]' class='form-control order_no' value='1' readonly></td>
                        <td class='input-group-sm p-0'><input type='text' name='details[]' class='form-control detail'></td>
                        <td class='input-group-sm p-0'><input type='text' name='remarks[]' class='form-control remark'></td>
                        <td class='input-group-sm p-0'><input type='text' name='cruds[]' class='form-control crud' value='i'></td>
                        <td class='input-group-sm p-0 d-nonex'><input type='text' name='rec_id[]' class='form-control rec_id' readonly></td>
                      </tr>`;

      $("#tbody-order").append(firstTr);
    }
  });

  $("#btnClear").click(function () {
    // ลบ tr ทั้งหมดที่ไม่ใช่ตัวแรกใน #tbody-order
    // $("#tbody-order tr:gt(0)").remove();
    // หรือ
    // $("#tbody-order").find("tr:not(:first)").remove();
    // หรือ
    $("#tbody-order").find("tr:gt(0)").remove();
  });

    $("#btnDeleteLast").click(function () {
      let order;
      // ลบ tr ตัวล่างสุดที่ไม่ใช่ tr ตัวแรก ใน #tbody-order
      // $("#tbody-order").find("tr:not(:first):last").remove();
      // $("#tbody-order tr:not(:first):last").remove();
      $("#tbody-order .firstTr[crud!='d']:last")
        .attr("crud", "d")
        .addClass("d-none")

        .find("td input.crud")
        .val("d")
        .end();
    });
  
  $("#btnCancel").click(function () {
    window.history.back();
    // window.location.href = "inspection_view.php";
    // window.history.go(-1);
    // $('.main').load('open_area_schedule_main.php'); แบบนี้ไม่ได้
    // header('Location: main.php?page=open_area_schedule_main');แบบนี้ไม่ได้
  });
  

});

