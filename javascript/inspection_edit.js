$(document).ready(function () {
  
  let contract_value = isNaN(parseFloat($("#contract_value").val())) ? 0 : parseFloat($("#contract_value").val());
  let interim_payment = parseFloat($("#interim_payment").val());
  let interim_payment_less_previous = isNaN(parseFloat($("#interim_payment_less_previous").val())) ? 0 : parseFloat($("#interim_payment_less_previous").val());
  let interim_payment_accumulated = isNaN(parseFloat($("#interim_payment_accumulated").val())) ? 0 : parseFloat($("#interim_payment_accumulated").val());
  let interim_payment_remain = isNaN(parseFloat($("#interim_payment_remain").val())) ? 0 : parseFloat($("#interim_payment_remain").val());
  
  let interim_payment_percent = isNaN(parseFloat($("#interim_payment_percent").val())) ? 0 : parseFloat($("#interim_payment_percent").val());
  let interim_payment_less_previous_percent = isNaN(parseFloat($("#interim_payment_less_previous_percent").val())) ? 0 : parseFloat($("#interim_payment_less_previous_percent").val());
  let interim_payment_accumulated_percent = isNaN(parseFloat($("#interim_payment_accumulated_percent").val())) ? 0 : parseFloat($("#interim_payment_accumulated_percent").val());
  let interim_payment_remain_percent = isNaN(parseFloat($("#interim_payment_remain_percent").val())) ? 0 : parseFloat($("#interim_payment_remain_percent").val());
  
  // let workload_planned_percent = isNaN(parseFloat($("#workload_planned_percent").val())) ? 0 : parseFloat($("#workload_planned_percent").val());
  // let workload_actual_completed_percent = isNaN(parseFloat($("#workload_actual_completed_percent").val())) ? 0 : parseFloat($("#workload_actual_completed_percent").val());
  // let workload_remaining_percent = isNaN(parseFloat($("#workload_remaining_percent").val())) ? 0 : parseFloat($("#workload_remaining_percent").val());
  // let workload_accumulated_percent = isNaN(parseFloat($("#workload_accumulated_percent").val())) ? 0 : parseFloat($("#workload_accumulated_percent").val());
  
  // interim_payment_less_previous = interim_payment_accumulated;
  // let interim_payment_less_previous_old = interim_payment_less_previous;
  // let interim_payment_accumulated_old = interim_payment_accumulated;

  $("#btnAdd").click(function () {
    let order_no;
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

  $("#interim_payment").on("change keyup", function () {
    interim_payment = parseFloat($(this).val());
    // let contract_value = isNaN(parseFloat($("#contract_value").val())) ? 0 : parseFloat($("#contract_value").val());
    // let interim_payment_percent = isNaN(parseFloat($("#interim_payment_percent").val())) ? 0 : parseFloat($("#interim_payment_percent").val());
    // let interim_payment_less_previous = isNaN(parseFloat($("#interim_payment_less_previous").val())) ? 0 : parseFloat($("#interim_payment_less_previous").val());
    // let interim_payment_less_previous_percent = isNaN(parseFloat($("#interim_payment_less_previous_percent").val())) ? 0 : parseFloat($("#interim_payment_less_previous_percent").val());
    // let interim_payment_remain = isNaN(parseFloat($("#interim_payment_remain").val())) ? 0 : parseFloat($("#interim_payment_remain").val());
    // let interim_payment_remain_percent = isNaN(parseFloat($("#interim_payment_remain_percent").val())) ? 0 : parseFloat($("#interim_payment_remain_percent").val());
    
    // console.clear();
    // console.log(`interim_payment = ${interim_payment}`);
    // console.log(`interim_payment_percent = ${interim_payment_percent}`);
    // console.log(`interim_payment_less_previous = ${interim_payment_less_previous}`);
    // console.log(`interim_payment_less_previous_percent = ${interim_payment_less_previous_percent}`);
    // console.log(`interim_payment_remain = ${interim_payment_remain}`);
    // console.log(`interim_payment_remain_percent = ${interim_payment_remain_percent}`);
    
    if (!isNaN(interim_payment) && !isNaN(contract_value)) {
      // interim_payment_less_previous = interim_payment_accumulated;
      interim_payment_accumulated	= interim_payment + interim_payment_less_previous;//(คือ Total Value Of Interim Payment))
      interim_payment_remain = contract_value - interim_payment_accumulated;

      interim_payment_less_previous_percent = (interim_payment_less_previous * 100) / contract_value;
      interim_payment_percent = (interim_payment * 100) / contract_value;
      interim_payment_accumulated_percent	=(interim_payment_accumulated * 100) / contract_value;	//เปอร์เซ็นต์ของยอดเบิกเงินงวดสะสม
      interim_payment_remain_percent = (interim_payment_remain * 100) / contract_value;

      $("#interim_payment_less_previous").val(interim_payment_less_previous.toFixed(2)); // (ทศนิยม 2 ตำแหน่ง)
      $("#interim_payment_accumulated").val(interim_payment_accumulated.toFixed(2)); // (ทศนิยม 2 ตำแหน่ง)
      $("#interim_payment_remain").val(interim_payment_remain.toFixed(2)); // (ทศนิยม 2 ตำแหน่ง)
      
      $("#interim_payment_less_previous_percent").val(interim_payment_less_previous_percent.toFixed(2)); // (ทศนิยม 2 ตำแหน่ง)
      $("#interim_payment_percent").val(interim_payment_percent.toFixed(2)); // (ทศนิยม 2 ตำแหน่ง)
      $("#interim_payment_accumulated_percent").val(interim_payment_accumulated_percent.toFixed(2)); // (ทศนิยม 2 ตำแหน่ง)
      $("#interim_payment_remain_percent").val(interim_payment_remain_percent.toFixed(2)); // (ทศนิยม 2 ตำแหน่ง)
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

  // $("#contract_value").on("change keyup", function () {
  //   let contract_value = parseFloat($(this).val());
  //   let vat_rate = parseFloat($("#vat").data("vat_rate"));

  //   if (!isNaN(contract_value) && !isNaN(vat_rate)) {
  //     var contract_value_before = contract_value / (1 + vat_rate / 100);
  //     $("#contract_value_before").val(contract_value_before.toFixed(2)); // แสดงผลลัพธ์ (ทศนิยม 2 ตำแหน่ง)
  //     $("#vat").val((contract_value - contract_value_before).toFixed(2)); // แสดงผลรวม VAT (ทศนิยม 2 ตำแหน่ง)
  //   } else {
  //     $("#contract_value_before").val(""); // ล้างค่าถ้าป้อนไม่ถูกต้อง
  //     $("#vat").val(""); // ล้างค่าถ้าป้อนไม่ถูกต้อง
  //   }
  // });

  $("#workload_actual_completed_percent").on("keypress", function(event) {
    // Check if the pressed key is Enter (keyCode 13)
    if (event.which === 13) {
      calculateAndDisplay();
    }
  });

  $("#workload_actual_completed_percent").on("blur", function() {
    calculateAndDisplay();
  });

  function calculateAndDisplay() {
    let workload_actual_completed_percent = $("#workload_actual_completed_percent").val();
    
    if (!isNaN(workload_actual_completed_percent) && workload_actual_completed_percent !== "") {
      workload_remaining_percent	= 100-workload_actual_completed_percent;//(คือ Total Value Of Interim Payment))
      $("#workload_remaining_percent").val(workload_remaining_percent.toFixed(2));
    } else {
      $("#workload_remaining_percent").val("");
    }
  }
  
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
        url: "inspection_crud.php",
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
              window.location.href = "inspection.php";
              // window.location.reload();
            }
          });
          // window.location.href = 'main.php?page=open_area_schedule';
        },
      });
    }
  });

  $("#btnCancel").click(function () {
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

    // console.log(`po_id = ${po_id}`);
    // console.log(`period_id = ${period_id}`);
    // console.log(`inspection_id = ${inspection_id}`);
    window.location.href = `inspection_attach.php?po_id=${po_id}&period_id=${period_id}&inspection_id=${inspection_id}`;
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
