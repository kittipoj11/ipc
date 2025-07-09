$(document).ready(function () {
  const responseMessage = $("#response-message");

  function showMessage(message, isSuccess) {
    responseMessage
      .text(message)
      .removeClass("success error")
      .addClass(isSuccess ? "success" : "error")
      .show()
      .delay(5000)
      .fadeOut();
  }

  let contract_value = isNaN(parseFloat($("#contract_value").val())) ? 0 : parseFloat($("#contract_value").val());
  let interim_payment = parseFloat($("#interim_payment").val());
  let interim_payment_less_previous = isNaN(parseFloat($("#interim_payment_less_previous").val())) ? 0 : parseFloat($("#interim_payment_less_previous").val());
  let interim_payment_accumulated = isNaN(parseFloat($("#interim_payment_accumulated").val())) ? 0 : parseFloat($("#interim_payment_accumulated").val());
  let interim_payment_remain = isNaN(parseFloat($("#interim_payment_remain").val())) ? 0 : parseFloat($("#interim_payment_remain").val());

  let interim_payment_percent = isNaN(parseFloat($("#interim_payment_percent").val())) ? 0 : parseFloat($("#interim_payment_percent").val());
  let interim_payment_less_previous_percent = isNaN(parseFloat($("#interim_payment_less_previous_percent").val())) ? 0 : parseFloat($("#interim_payment_less_previous_percent").val());
  let interim_payment_accumulated_percent = isNaN(parseFloat($("#interim_payment_accumulated_percent").val())) ? 0 : parseFloat($("#interim_payment_accumulated_percent").val());
  let interim_payment_remain_percent = isNaN(parseFloat($("#interim_payment_remain_percent").val())) ? 0 : parseFloat($("#interim_payment_remain_percent").val());

  $("#btnAdd").click(function () {
    let order_no;
    if ($("#tbody-order").has("tr[data-crud!='delete']").length > 0) {
      order_no = $("#tbody-order tr[data-crud!='delete']:last").find('input[name="order_no"]').val();
      order_no++;
      $("#tbody-order tr[data-crud!='delete']:last")
        .clone(false)
        .attr("data-crud", "create")
        .attr("data-rec-id", "")
        .removeClass("d-none")

        .find('input[name="order_no"]')
        .val(order_no)
        .end()

        .find('input[name="detail"]')
        .val("")
        .end()

        .find('input[name="remark"]')
        .val("")
        .end()

        .find('input[name="crud"]')
        .val("create")
        .end()

        .find('input[name="rec_id"]')
        .val("")
        .end()

        .appendTo("#tbody-order");
    } else {
      // Create the new tr element using jQuery
      const addTr = `<tr data-crud='create' data-rec-id=''>
                        <td class='input-group-sm p-0'><input type='number' name='order_no' class='form-control' value='1' readonly></td>
                        <td class='input-group-sm p-0'><input type='text' name='detail' class='form-control'></td>
                        <td class='input-group-sm p-0'><input type='text' name='remark' class='form-control'></td>
                        <td class='input-group-sm p-0'><input type='text' name='crud' class='form-control' value='i'></td>
                        <td class='input-group-sm p-0 d-nonex'><input type='text' name='rec_i' class='form-control' readonly></td>
                      </tr>`;

      $("#tbody-order").append(addTr);
    }
  });

  $("#btnClear").click(function () {
    // ลบ tr ทั้งหมดที่ไม่ใช่ตัวแรกใน #tbody-order
    // $("#tbody-order tr:gt(0)").remove();
    // หรือ
    // $("#tbody-order").find("tr:not(:first)").remove();
    // หรือ
    $("#tbody-order tr").remove();
  });

  $("#btnDeleteLast").click(function () {
    const row = $("#tbody-order tr[data-crud!='delete']:last");
    if (confirm("คุณต้องการลบรายละเอียดการตรวจสอบรายการสุดท้ายใช่หรือไม่?")) {
      // อ่านค่า data-crud จาก Attribute โดยตรง
      if (row.attr("data-crud") == "create") {
        row.remove();
      } else {
        row.find('input[name="crud"]').val("delete");
        row.addClass("d-none").attr("data-crud", "delete");
        // row.attr("data-crud", "delete");
      }
    }
    // console.log(`row = ${row.attr("data-crud")} input=${row.find('input[name="crud"]').val()}`);
  });

  $("#tbody-order").on("input", "input", function () {
    const row = $(this).closest("tr");
    // ถ้าแถวไม่ใช่แถวใหม่ (สถานะเป็น select) ให้เปลี่ยนเป็น update
    if (row.attr("data-crud") == "select") {
      row.attr("data-crud", "update");
      row.find('input[name="crud"]').val("update");
    }
    console.log(
      `row = ${row.attr("data-crud")} input=${row
        .find('input[name="crud"]')
        .val()}`
    );
  });

  $("#interim_payment").on("change keyup", function () {
    interim_payment = parseFloat($(this).val());

    if (!isNaN(interim_payment) && !isNaN(contract_value)) {
      interim_payment_accumulated =
        interim_payment + interim_payment_less_previous; //(คือ Total Value Of Interim Payment))
      interim_payment_remain = contract_value - interim_payment_accumulated;

      interim_payment_less_previous_percent =
        (interim_payment_less_previous * 100) / contract_value;
      interim_payment_percent = (interim_payment * 100) / contract_value;
      interim_payment_accumulated_percent =
        (interim_payment_accumulated * 100) / contract_value; //เปอร์เซ็นต์ของยอดเบิกเงินงวดสะสม
      interim_payment_remain_percent =
        (interim_payment_remain * 100) / contract_value;

      $("#interim_payment_less_previous").val(
        interim_payment_less_previous.toFixed(2)
      ); // (ทศนิยม 2 ตำแหน่ง)
      $("#interim_payment_accumulated").val(
        interim_payment_accumulated.toFixed(2)
      ); // (ทศนิยม 2 ตำแหน่ง)
      $("#interim_payment_remain").val(interim_payment_remain.toFixed(2)); // (ทศนิยม 2 ตำแหน่ง)

      $("#interim_payment_less_previous_percent").val(
        interim_payment_less_previous_percent.toFixed(2)
      ); // (ทศนิยม 2 ตำแหน่ง)
      $("#interim_payment_percent").val(interim_payment_percent.toFixed(2)); // (ทศนิยม 2 ตำแหน่ง)
      $("#interim_payment_accumulated_percent").val(
        interim_payment_accumulated_percent.toFixed(2)
      ); // (ทศนิยม 2 ตำแหน่ง)
      $("#interim_payment_remain_percent").val(
        interim_payment_remain_percent.toFixed(2)
      ); // (ทศนิยม 2 ตำแหน่ง)
    } else {
      $("#interim_payment_less_previous").val("0"); // (ทศนิยม 2 ตำแหน่ง)
      $("#interim_payment_accumulated").val("0"); // (ทศนิยม 2 ตำแหน่ง)
      $("#interim_payment_remain").val("0"); // (ทศนิยม 2 ตำแหน่ง)

      $("#interim_payment_less_previous_percent").val("0"); // (ทศนิยม 2 ตำแหน่ง)
      $("#interim_payment_percent").val("0"); // (ทศนิยม 2 ตำแหน่ง)
      $("#interim_payment_accumulated_percent").val("0"); // (ทศนิยม 2 ตำแหน่ง)
      $("#interim_payment_remain_percent").val("0"); // (ทศนิยม 2 ตำแหน่ง)
    }
  });

  $("#workload_actual_completed_percent").on("keypress", function (event) {
    // Check if the pressed key is Enter (keyCode 13)
    if (event.which === 13) {
      calculateAndDisplay();
    }
  });

  $("#workload_actual_completed_percent").on("blur", function () {
    calculateAndDisplay();
  });

  function calculateAndDisplay() {
    let workload_actual_completed_percent = $(
      "#workload_actual_completed_percent"
    ).val();

    if (
      !isNaN(workload_actual_completed_percent) &&
      workload_actual_completed_percent !== ""
    ) {
      workload_remaining_percent = 100 - workload_actual_completed_percent; //(คือ Total Value Of Interim Payment))
      $("#workload_remaining_percent").val(
        workload_remaining_percent.toFixed(2)
      );
    } else {
      $("#workload_remaining_percent").val("");
    }
  }

  $("#myForm").on("submit", function (e) {
    const radioButtons = document.querySelectorAll('input[name="disbursement"]');
    e.preventDefault();

    let disbursement = 0; // กำหนดค่าเริ่มต้นเป็น 0
    for (const radioButton of radioButtons) {
      if (radioButton.checked) {
        disbursement = radioButton.value; // ดึงค่าจาก value
        break;
      }
    }

    const periodData = {
      po_id: $("#po_id").val(),
      period_id: $("#period_id").val(),
      inspection_id: $("#inspection_id").val(),
      workload_actual_completed_percent: $("#workload_actual_completed_percent").val() ?? 0,
      workload_remaining_percent : $("workload_remaining_percent").val() ?? 0,
      workload_planned_percent : $("workload_planned_percent").val() ?? 0,
      interim_payment : $("interim_payment").val() ?? 0,
      interim_payment_percent : $("interim_payment_percent").val() ?? 0,
      interim_payment_less_previous : $("interim_payment_less_previous").val() ?? 0,
      interim_payment_less_previous_percent : $("interim_payment_less_previous_percent").val() ?? 0,
      interim_payment_accumulated : $("interim_payment_accumulated").val() ?? 0,
      interim_payment_accumulated_percent : $("interim_payment_accumulated_percent").val() ?? 0,
      interim_payment_remain : $("interim_payment_remain").val() ?? 0,
      interim_payment_remain_percent : $("interim_payment_remain_percent").val() ?? 0,
      retention_value : $("retention_value").val() ?? 0,
      plan_status_id : $("plan_status_id").val() ?? 0,
      disbursement : $("disbursement").val() ?? 0,
      remark : $("remark").val() ?? 0,
    };

    const detailsData = [];
    $("#tbody-order tr").each(function () {
      const row = $(this);

      // สร้าง object สำหรับเก็บข้อมูลของแถวนี้
      // row.removeData("crud"); ทำการ clear ค่า data-* ที่อยู่ใน cache ถ้าใช้ row.data() ให้ clear ก่อน  ไม่เช่นนั้นจะได้ค่าที่ยังเก็บอยู่ใน cache
      const detailRecord = {
        inspection_id: $("#inspection_id").val(),
        rec_id: row.attr("data-rec-id"), // ถ้าใช้ row.data() ให้ clear ก่อน  ไม่เช่นนั้นจะได้ค่าที่ยังเก็บอยู่ใน cache
        order_crud: row.attr("data-crud"), //
        order_no: row.find('input[name="order_no"]').val(), // ใช้ .find() เพื่อหา input ที่อยู่ในแถวนี้ แล้ว .val() เพื่อดึงค่า
        detail: row.find('input[name="detail"]').val(),
        remark: row.find('input[name="remark"]').val(),
      };
      // เพิ่ม object ของแถวนี้เข้าไปใน array หลัก
      detailsData.push(detailRecord);
    });

    const data_sent = {
      periodData: periodData,
      detailsData: detailsData,
      action: "save",
    };

    // console.log(data_sent);
    // return;
    $.ajax({
      url: "inspection_handler_api.php",
      type: "POST",
      contentType: "application/json",
      data: JSON.stringify(data_sent),
    })
      .done(function (result) {
        // console.log(`result: ${result}`);
        responseMessage.text(result.message).css("color", "green");
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
            // loadData(); // โหลดข้อมูลใหม่ทั้งหมด
            window.location.href = "inspection_list.php";
          }
        });
      })
      .fail((jqXHR) => {
        const errorMsg = jqXHR.responseJSON
          ? jqXHR.responseJSON.message
          : "เกิดข้อผิดพลาดรุนแรง";
        showMessage(errorMsg, false);
      });
  });

  $(".btnCancel").click(function () {
    window.history.back();
    // window.location.href = "inspection_view.php";
    // window.history.go(-1);
    // $('.main').load('open_area_schedule_main.php'); แบบนี้ไม่ได้
    // header('Location: main.php?page=open_area_schedule_main');แบบนี้ไม่ได้
  });



// รอตรวจสอบ 3 ฟังก์ชัน
  $(document).on("click", "#btnAttach", function (e) {
    e.preventDefault();

    const po_id = $("#po_id").val();
    const period_id = $("#period_id").val();
    const inspection_id = $("#inspection_id").val();

    // console.log(`po_id = ${po_id}`);
    // console.log(`period_id = ${period_id}`);
    // console.log(`inspection_id = ${inspection_id}`);
    window.location.href = `inspection_period_attach_form.php?po_id=${po_id}&period_id=${period_id}&inspection_id=${inspection_id}&mode=`;
  });

  $("#floatingTextarea").on("click", function () {
    console.log($(this).val());
  });

  $(".approval_next").on("click", function (e) {
    // $(document).on("click", "#btnSave", function (e) {
    console.log("click");
    e.preventDefault();
    let current_approval_level = $(this)
      .closest("ul")
      .data("current_approval_level");
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
            window.location.href = "inspection_list.php";
            // window.location.reload();
          }
        });
        // window.location.href = 'main.php?page=open_area_schedule';
      },
    });
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
    // });btn-group
    if ($("#submit").data("current_approval_level") > 1) {
      $("#submit").addClass("d-none");
      $(".btn-group").addClass("d-none");
    } else {
      $("#submit").removeClass("d-none");
      $(".btn-group").removeClass("d-none");
    }
    // $("#submit").addClass("d-none");
    // $(".btn-group").addClass("d-none");
  }

  loadPage();
});
