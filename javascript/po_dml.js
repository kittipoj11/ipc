$(document).ready(function () {
  $("#myForm").on("submit", function (e) {
    // $(document).on("click", "#btnSave", function (e) {
    // console.log('submit');
    e.preventDefault();
    let data_sent = $("#myForm").serializeArray();
    data_sent.push({
      name: "action",
      value: $("#submit").data("action"), //'create', //หรือ update
    });
    console.log(`data_sent=${data_sent}`);
    $.ajax({
      url: "po_crud.php",
      type: "POST",
      // data: $(this).serialize(),
      data: data_sent,
      success: function (response) {
        console.log(`response=${response}`);
        if (response) {
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
            // window.location.href = 'main.php?page=open_area_schedule';
          });
        }
      },
      error: function (xhr, status, error) {
        console.log("เกิดข้อผิดพลาดในการเชื่อมต่อ:", error);
        // console.error("เกิดข้อผิดพลาดในการเชื่อมต่อ:", error);
        // $('#loginError').text('เกิดข้อผิดพลาดในการเชื่อมต่อกับเซิร์ฟเวอร์');
      }
    });
  });

  $("#btnAdd").click(function () {
    let period_number;
    // console.log($(".firstTr:last").find(".period:last").val());
    // $(".firstTr:has(.crud:not([value='d'])):last")//แบบที่ 1
    // $(".firstTr").has(".crud:not([value='d'])").last()//แบบที่ 2
    if ($("#tbody-period").has(".firstTr[crud!='d']").length > 0) {
      //หมายความว่า
      // 1. เลือก element ที่มี ID เป็น "tbody-period"
      // 2. เลือกเฉพาะ element ที่มี element ลูกหลาน (descendant) ที่ตรงกับ selector ในวงเล็บ นั่นคือ element ที่มีคลาสเป็น "firstTr" และมี attribute ชื่อ "crud" ซึ่งมีค่าไม่เท่ากับ "d"
      // 3. .length คือนับจำนวน element ที่ถูกเลือกได้จากการกรองในขั้นตอนก่อนหน้า(ข้อ 2.) ว่ามากกว่า 0 หรือไม่

      period_number = $(".firstTr[crud!='d']:last").find('input[name="period_number"]').val();
      period_number++;
      $("#tbody-period tr[crud!='d']:last")
        .clone(false)
        .attr("crud", "i")
        .removeClass("d-none")

        .find('input[name="period_number"]')
        .val(period_number)
        .end()

        .find('input[name="workload_planned_percent"]')
        .val("")
        .end()

        .find('input[name="interim_payment"]')
        .val("")
        .end()

        .find('input[name="interim_payment_percent"]')
        .val("")
        .end()

        .find('input[name="remark"]')
        .val("")
        .end()

        .find('input[name="period_id"]')
        .val("")
        .end()

        .find('input[name="crud"]')
        .val("i")
        .end()

        .appendTo("#tbody-period");
    } else {
      // Create the new tr element using jQuery
      const firstTr = `<tr class='firstTr' crud='i'>
                            <td class='input-group-sm p-0'><input type='number' name='period_number' class='form-control period_number' value='1' readonly></td>
                            <td class='input-group-sm p-0'><input type='number' name='workload_planned_percent' class='form-control workload_planned_percent'></td>
                            <td class='input-group-sm p-0'><input type='number' name='interim_payment' class='form-control interim_payment'></td>
                            <td class='input-group-sm p-0'><input type='number' name='interim_payment_percent' class='form-control interim_payment_percent'></td>
                            <td class='input-group-sm p-0'><input type='text' name='remark' class='form-control remark'></td>
                            <td class='input-group-sm p-0'><input type='text' name='crud' class='form-control crud' value='i'></td>
                            <td class='input-group-sm p-0 d-nonex'><input type='text' name='period_id' class='form-control period_id' readonly></td>
                          </tr>`;

      $("#tbody-period").append(firstTr);
    }
  });

  //ปุ่มนี้จะใช้ได้แค่ตอนสร้าง po ใหม่เท่านั้น   ถ้าเป็นการ edit จะไม่สามารถใช้งานได้
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

  $(".btnCancel , .btnBack").click(function () {
    // history.go(-1);
    // $('.main').load('open_area_schedule_main.php'); แบบนี้ไม่ได้
    // header('Location: main.php?page=open_area_schedule_main');แบบนี้ไม่ได้
    window.history.back();
  });

  $("#contract_value_before").on("change keyup", function () {
    let contract_value_before = parseFloat($(this).val());
    let vat_rate = parseFloat($("#vat").data("vat_rate"));

    if (!isNaN(contract_value_before) && !isNaN(vat_rate)) {
      var vat_amount = contract_value_before * (vat_rate / 100);
      var contract_value = contract_value_before + vat_amount;

      $("#contract_value").val(contract_value.toFixed(2)); // แสดงผลรวม VAT (ทศนิยม 2 ตำแหน่ง)
      $("#vat").val(vat_amount.toFixed(2)); // แสดงผลรวม VAT (ทศนิยม 2 ตำแหน่ง)
    } else {
      $("#contract_value").val(""); // ล้างค่าถ้าป้อนไม่ถูกต้อง
      $("#vat").val(""); // ล้างค่าถ้าป้อนไม่ถูกต้อง
    }
  });

  $("#contract_value").on("change keyup", function () {
    let contract_value = parseFloat($(this).val());
    let vat_rate = parseFloat($("#vat").data("vat_rate"));

    if (!isNaN(contract_value) && !isNaN(vat_rate)) {
      var contract_value_before = contract_value / (1 + vat_rate / 100);
      $("#contract_value_before").val(contract_value_before.toFixed(2)); // แสดงผลลัพธ์ (ทศนิยม 2 ตำแหน่ง)
      $("#vat").val((contract_value - contract_value_before).toFixed(2)); // แสดงผลรวม VAT (ทศนิยม 2 ตำแหน่ง)
    } else {
      $("#contract_value_before").val(""); // ล้างค่าถ้าป้อนไม่ถูกต้อง
      $("#vat").val(""); // ล้างค่าถ้าป้อนไม่ถูกต้อง
    }
  });

  $("#working_date_from, #working_date_to").on("change", function () {
    var working_date_from = $("#working_date_from").val();
    var working_date_to = $("#working_date_to").val();

    if (working_date_from && working_date_to) {
      var start = new Date(working_date_from);
      var end = new Date(working_date_to);
      var timeDiff = Math.abs(end.getTime() - start.getTime());
      var working_day = Math.ceil(timeDiff / (1000 * 3600 * 24));
      $("#working_day").val(working_day + 1);
    } else {
      $("#working_day").val("");
    }
  });
});
