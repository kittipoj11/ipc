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

  // let contract_value = isNaN(parseFloat($("#contract_value").val())) ? 0 : parseFloat($("#contract_value").val());
  // let interim_payment = parseFloat($("#interim_payment").val());
  // let interim_payment_less_previous = isNaN(parseFloat($("#interim_payment_less_previous").val())) ? 0 : parseFloat($("#interim_payment_less_previous").val());
  // let interim_payment_accumulated = isNaN(parseFloat($("#interim_payment_accumulated").val())) ? 0 : parseFloat($("#interim_payment_accumulated").val());
  // let interim_payment_remain = isNaN(parseFloat($("#interim_payment_remain").val())) ? 0 : parseFloat($("#interim_payment_remain").val());

  // let interim_payment_percent = isNaN(parseFloat($("#interim_payment_percent").val())) ? 0 : parseFloat($("#interim_payment_percent").val());
  // let interim_payment_less_previous_percent = isNaN(parseFloat($("#interim_payment_less_previous_percent").val())) ? 0 : parseFloat($("#interim_payment_less_previous_percent").val());
  // let interim_payment_accumulated_percent = isNaN(parseFloat($("#interim_payment_accumulated_percent").val())) ? 0 : parseFloat($("#interim_payment_accumulated_percent").val());
  // let interim_payment_remain_percent = isNaN(parseFloat($("#interim_payment_remain_percent").val())) ? 0 : parseFloat($("#interim_payment_remain_percent").val());

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

  // แก้ไขใหม่
  // $("#interim_payment").on("change keyup", function () {
  //   interim_payment = parseFloat($(this).val());

  //   if (!isNaN(interim_payment) && !isNaN(contract_value)) {
  //     interim_payment_accumulated = interim_payment + interim_payment_less_previous; //(คือ Total Value Of Interim Payment))
  //     interim_payment_remain = contract_value - interim_payment_accumulated;

  //     interim_payment_less_previous_percent = (interim_payment_less_previous * 100) / contract_value;
  //     interim_payment_percent = (interim_payment * 100) / contract_value;
  //     interim_payment_accumulated_percent = (interim_payment_accumulated * 100) / contract_value; //เปอร์เซ็นต์ของยอดเบิกเงินงวดสะสม
  //     interim_payment_remain_percent = (interim_payment_remain * 100) / contract_value;

  //     $("#interim_payment_less_previous").val(interim_payment_less_previous.toFixed(2)); // (ทศนิยม 2 ตำแหน่ง)
  //     $("#interim_payment_accumulated").val(interim_payment_accumulated.toFixed(2)); // (ทศนิยม 2 ตำแหน่ง)
  //     $("#interim_payment_remain").val(interim_payment_remain.toFixed(2)); // (ทศนิยม 2 ตำแหน่ง)

  //     $("#interim_payment_less_previous_percent").val(interim_payment_less_previous_percent.toFixed(2)); // (ทศนิยม 2 ตำแหน่ง)
  //     $("#interim_payment_percent").val(interim_payment_percent.toFixed(2)); // (ทศนิยม 2 ตำแหน่ง)
  //     $("#interim_payment_accumulated_percent").val(interim_payment_accumulated_percent.toFixed(2)); // (ทศนิยม 2 ตำแหน่ง)
  //     $("#interim_payment_remain_percent").val(interim_payment_remain_percent.toFixed(2)); // (ทศนิยม 2 ตำแหน่ง)
  //   } else {
  //     $("#interim_payment_less_previous").val("0"); // (ทศนิยม 2 ตำแหน่ง)
  //     $("#interim_payment_accumulated").val("0"); // (ทศนิยม 2 ตำแหน่ง)
  //     $("#interim_payment_remain").val("0"); // (ทศนิยม 2 ตำแหน่ง)

  //     $("#interim_payment_less_previous_percent").val("0"); // (ทศนิยม 2 ตำแหน่ง)
  //     $("#interim_payment_percent").val("0"); // (ทศนิยม 2 ตำแหน่ง)
  //     $("#interim_payment_accumulated_percent").val("0"); // (ทศนิยม 2 ตำแหน่ง)
  //     $("#interim_payment_remain_percent").val("0"); // (ทศนิยม 2 ตำแหน่ง)
  //   }
  // });

  // // แก้ไขใหม่
  // $("#workload_actual_completed_percent").on("keypress", function (event) {
  //   // Check if the pressed key is Enter (keyCode 13)
  //   if (event.which === 13) {
  //     calculateAndDisplay();
  //   }
  // });

  // // แก้ไขใหม่
  $("#workload_actual_completed_percent").on("blur", function () {
    calculateAndDisplay();
  });

  // แก้ไขใหม่
  function calculateAndDisplay() {
    let workload_actual_completed_percent = $("#workload_actual_completed_percent").val();

    if (!isNaN(workload_actual_completed_percent) && workload_actual_completed_percent !== "") {
      workload_remaining_percent = 100 - workload_actual_completed_percent; //(คือ Total Value Of Interim Payment))
      $("#workload_remaining_percent").val(workload_remaining_percent.toFixed(2));
    } else {
      $("#workload_remaining_percent").val("");
    }
  }

  // แก้ไขใหม่
  $("#floatingTextarea").on("click", function () {
    console.log($(this).val());
  });


  // - action มาจากการกดปุ่มว่าเป็นอะไร เช่น save, submit, approve, reject เป็นต้น  
  // โดย submit และ approve เป็นการเลื่อน level เหมือนกัน  ต่างกันแค่ชื่อ   ซึ่งอาจจะดึงข้อมูลชื่อมาจาก workflow_step 
  // - data เป็นข้อมูลที่มาจากฟอร์มเพื่อนำมาบันทึกข้อมูล 
  // เช่นถ้าเป็นการ save จะดึงข้อมูลของ inspection บน form ส่งมาให้   เพื่อมาทำการ save 
  // ถ้าเป็นการ submit sinv reject ไม่ต้องส่งข้อมูลของ form มาเพราะไม่ได้ใช้ข้อมูลบน form แต่ใช้การเลื่อนหรือถอย level จากการดึงข้อมูลใน workflow_step 
  // แต่ทุก action ต้องส่ง data ที่มีข้อมูลอย่างน้อยคือ inspection-id, user-id
  function sendRequest(action, data) {
    const myForm = $("#myForm");
    const inspectionId = myForm.data("inspection-id");
    const userId = myForm.data("user-id"); //ไม่ต้องส่งไปก็ได้เพราะ  เรียกใช้ $_SESSION['user_id] ใน inspection_handler_api.php หรือ inspection_service_class.php

    const data_sent = {
      action: action,
      inspectionId: inspectionId,
      userId: userId,
      ...data,
    };
    // console.log(JSON.stringify(data_sent));
    // exit;
    // return;
    // console.log(`data_sent: ${JSON.stringify(data_sent)}`);
    $.ajax({
      url: "inspection_handler_api.php",
      type: "POST",
      contentType: "application/json",
      dataType: "json",
      data: JSON.stringify(data_sent),
      // beforeSend: function () {
      //   // Optional: disable buttons to prevent double-clicking
      //   $("#submit").prop("disabled", true);
      // },
    })
      // ถ้า submit แสดง แต่ approve ไม่แสดง
      .done(function (result) {
        // console.log(`result: ${result}`);
        responseMessage.text(result.message).css("color", "green");
        Swal.fire({
          icon: result.status,
          title: result.message,
          color: "#716add",
          allowOutsideClick: false,
          background: "black",
        }).then((result) => {
          if (result.isConfirmed) {
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
  }

  // ทำการบันทึกข้อมูล inspection
  // current_approval_level = current_approval_level +1 (ค่า current_approval_level เดิม = 0) 
  // ดึงข้อมูลจาก workflow_step ที่มี id = 1 และ approval_level = current_approval_level โดยมี limit 1 (จะได้ approval_level(1), approver_id(1), approval_type_text(submit))
  // inspection_status เป็น 'pending-submit'('pending ' + workflow_step.approval_type_text) 
  // current_approver_id เป็น 1 (จาก workflow_step.approver_id)
  // และบันทึก inspection_approval_history.action เป็น 'create document'
  $("#myForm").on("submit", function (e) {
    e.preventDefault();
    const myForm = $("#myForm");
    const currentApprovalLevel = myForm.data("current-approval-level");
    const workflowId = myForm.data("workflow_id");
    const disbursement = $('input[name^="disbursement"]:checked').val() || null; // ถ้าไม่มีการเลือก ให้เป็น null

    const periodData = {
      po_id: $("#po_id").val(),
      period_id: $("#period_id").val(),
      inspection_id: $("#inspection_id").val(),
      workload_actual_completed_percent: $("#workload_actual_completed_percent").val() ?? 0,
      workload_remaining_percent: $("#workload_remaining_percent").val() ?? 0,
      workload_planned_percent: $("#workload_planned_percent").val() ?? 0,
      interim_payment: $("#interim_payment").val() ?? 0,
      interim_payment_percent: $("#interim_payment_percent").val() ?? 0,
      interim_payment_less_previous: $("#interim_payment_less_previous").val() ?? 0,
      interim_payment_less_previous_percent: $("#interim_payment_less_previous_percent").val() ?? 0,
      interim_payment_accumulated: $("#interim_payment_accumulated").val() ?? 0,
      interim_payment_accumulated_percent: $("#interim_payment_accumulated_percent").val() ?? 0,
      interim_payment_remain: $("#interim_payment_remain").val() ?? 0,
      interim_payment_remain_percent: $("#interim_payment_remain_percent").val() ?? 0,
      retention_value: $("#retention_value").val() ?? 0,
      plan_status_id: $("#plan_status_id").val() ?? 0,
      disbursement: disbursement, //$("#disbursement").val() ?? 0,
      remark: $("#remark").val() ?? "",
      inspection_status: "pending-submit",
      current_approval_level: currentApprovalLevel,
      workflow_id: workflowId,
    };
    // console.log(periodData);
    // return;
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

    let data = {
      periodData: periodData,
      detailsData: detailsData,
    };
    if (currentApprovalLevel == 0) {
      // console.log(`currentApprovalLevel = ${currentApprovalLevel}`);
      sendRequest('save', data);
    } else {
      // console.log(`currentApprovalLevel = ${currentApprovalLevel}`);
      sendRequest('update', data);
    }
  });

  // Approval *****************
  $(".approve").on("click", function (e) {
    // $(document).on("click", "#btnSave", function (e) {
    e.preventDefault();
    // sendRequest($(".approve").data("approve-text"));
    // console.log("click");
    sendRequest("approve");
  });

  $(".reject").on("click", function (e) {
    // $(document).on("click", "#btnSave", function (e) {
    e.preventDefault();
    // const comments = $("#reject_comments").val().trim();
    const comments = "#reject_comments";
    // if (!comments) {
    //   alert("กรุณากรอกเหตุผลในการปฏิเสธ");
    //   $("#reject_comments").focus();
    //   return;
    // }
    sendRequest("reject", { comments: comments });
  });

  $(".btnCancel").click(function () {
    window.history.back();
    // window.location.href = "inspection_view.php";
    // window.history.go(-1);
    // $('.main').load('open_area_schedule_main.php'); แบบนี้ไม่ได้
    // header('Location: main.php?page=open_area_schedule_main');แบบนี้ไม่ได้
  });

  // รอตรวจสอบฟังก์ชัน
  $(document).on("click", "#btnAttach", function (e) {
    e.preventDefault();

    const po_id = $("#po_id").val();
    const period_id = $("#period_id").val();
    const inspection_id = $("#inspection_id").val();

    // console.log(`po_id = ${po_id}`);
    // console.log(`period_id = ${period_id}`);
    // console.log(`inspection_id = ${inspection_id}`);
    window.location.href = `inspection_attach_form.php?po_id=${po_id}&period_id=${period_id}&inspection_id=${inspection_id}&mode=`;
  });

  function refreshForm() {
    const myForm = $('#myForm');
    // console.log(myForm.data('inspection-status'))interim_payment

    // จัดการปุ่ม save
    if ((myForm.data('inspection-status') == 'draft') && myForm.data('created-by') == myForm.data('user-id')) {
      $("#submit").addClass('inline');
      $("#submit").removeClass('d-none');
    }
    else if (myForm.data('inspection-status') == 'pending-submit' && myForm.data('created-by') == myForm.data('user-id')) {
      $("#btnAction").addClass('inline');
      $("#btnAction").removeClass('d-none');
    }
    else {
      $("#submit").addClass('d-none');
      $("#submit").removeClass('inline');
    }

    // จัดการปุ่ม action
    if (myForm.data('inspection-status') == 'pending-submit' && myForm.data('created-by') == myForm.data('user-id')) {
      $("#btnAction").addClass('inline');
      $("#btnAction").removeClass('d-none');
    }
    else if (myForm.data('inspection-status') == 'pending-submit' && myForm.data('current-approver-id') == myForm.data('user-id')) {
      $("#btnAction").addClass('inline');
      $("#btnAction").removeClass('d-none');
    }
    else if (myForm.data('inspection-status') == 'pending-approve' && myForm.data('current-approver-id') == myForm.data('user-id')) {
      $("#btnAction").addClass('inline');
      $("#btnAction").removeClass('d-none');
    }
    else {
      $("#btnAction").addClass('d-none');
      $("#btnAction").removeClass('inline');
    }

    // จัดหน้า form
    if ((myForm.data('inspection-status') == 'draft') && myForm.data('created-by') == myForm.data('user-id')) {
      // $("#checking").addClass('d-none');
      // $("#checking").removeClass('inline');
      $("#checking input,select,textarea").prop("disabled",true);

    }
    else if (myForm.data('inspection-status') == 'pending-submit' && myForm.data('created-by') == myForm.data('user-id')) {
      $("#checking input,select,textarea").prop("disabled",true);
      // $("#checking").addClass('d-none');
      // $("#checking").removeClass('inline');
    }
    else {
      // $("#checking input,select,textarea").prop("disabled",true);
      // $("#checking").addClass('inline');
      // $("#checking").removeClass('d-none');
    }

    // $("div input,select,textarea").prop("disabled",true);
    
  }

  refreshForm();
});