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

  // --- ฟังก์ชันสำหรับโหลดข้อมูลเริ่มต้น ---
  // แก้ไขส่วนนี้ หรือ ยกเลิกฟังก์ชันนี้ถ้าไม่ได้ใช้****************************************************************************
  function loadPeriods() {
    const tbody = $("#tbody-period");
    responseMessage.text("");
    tbody.html('<tr><td colspan="5">กำลังโหลดข้อมูล...</td></tr>');
    // console.log($("#po_id").val());
    // return;
    const data_sent = {
      po_id: $("#po_id").val(),
    };

    $.ajax({
      url: "po_handler_api.php",
      type: "POST",
      contentType: "application/json",
      dataType: "json",
      data: JSON.stringify(data_sent),
    })
      .done(function (result) {
        tbody.empty(); // ล้างข้อมูลเก่า
        if (result.status === "success" && result.data.length > 0) {
          result.data.forEach(function (period) {
            appendPeriodRow(period);
          });
        } else {
          tbody.html('<tr><td colspan="5">ไม่พบข้อมูลงวดงาน</td></tr>');
        }
      })
      .fail(function () {
        tbody.html(
          '<tr><td colspan="5">เกิดข้อผิดพลาดในการโหลดข้อมูล</td></tr>'
        );
      });
  }


    /*
โค้ดส่วนนี้ทำหน้าที่สำคัญอย่างหนึ่งคือ: "คอยติดตามการเปลี่ยนแปลงข้อมูลในแต่ละแถวโดยอัตโนมัติ"

เมื่อใดก็ตามที่ผู้ใช้ พิมพ์หรือแก้ไขข้อมูล ในช่อง <input> ใดๆ ในตาราง โค้ดนี้จะทำงานทันทีเพื่อตรวจสอบสถานะของ "แถว" (<tr>) นั้นๆ 
และถ้าแถวนั้นเป็นข้อมูลเก่าที่ยังไม่เคยถูกแก้ไขมาก่อน (มีสถานะเป็น clean) มันจะเปลี่ยนสถานะของแถวนั้นให้เป็น update ทันที

เป้าหมาย: เพื่อให้ตอนที่เรากดปุ่ม "บันทึกข้อมูลทั้งหมด" เราจะรู้ได้ว่าแถวไหนบ้างที่ถูกผู้ใช้แก้ไข และจำเป็นต้องส่งไปให้เซิร์ฟเวอร์ทำการ UPDATE ข้อมูล 
ซึ่งช่วยให้เราส่งเฉพาะข้อมูลที่มีการเปลี่ยนแปลงจริงๆ ไปเท่านั้น ทำให้ระบบมีประสิทธิภาพมากขึ้น
*/
  $("#tbody-period").on("input", "input", function () {
    const row = $(this).closest("tr");
    // ถ้าแถวไม่ใช่แถวใหม่ (สถานะเป็น select) ให้เปลี่ยนเป็น update
    if (row.attr("data-crud") == "select") {
      row.attr("data-crud", "update");
      row.find('input[name="crud"]').val("update");
    }
    console.log(
      `row3 = ${row.attr("data-crud")} input=${row
        .find('input[name="crud"]')
        .val()}`
    );
  });
  /*Note:
  เมื่อใดก็ตามที่ผู้ใช้ พิมพ์หรือแก้ไขข้อมูล ในช่อง <input> ใดๆ ในตาราง โค้ดนี้จะทำงานทันทีเพื่อตรวจสอบสถานะของ "แถว" (<tr>) นั้นๆ 
และถ้าแถวนั้นเป็นข้อมูลเก่าที่ยังไม่เคยถูกแก้ไขมาก่อน (มีสถานะเป็น select) มันจะเปลี่ยนสถานะของแถวนั้นให้เป็น update
*/

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

  // $(".btnDeleteThis").click(function() {
  // สำหรับใช้กับปุ่มที่อยู่ภายใน <td>
  // $(document).on("click", ".btnDeleteThis", function () {
  //   // ส่วนสำหรับการลบ
  //   // let row_id = $(this).attr("iid");
  //   // console.log("#row" + row_id + "");
  //   // เมื่อปุ่มนี้ถูกกด(this)จะลบ tr ของปุ่มนี้ออกไป
  //   // $(this).closest("tr").remove();
  //   // หรือใช้
  //   $(this).parents("tr").remove();
  // });

  // Note: การเปลี่ยนแปลงค่า data-crud ด้วย .data() จะไม่ส่งผลต่อ Selector โดยตรงในทันทีที่ตัว Selector ถูกเรียกใช้อีกครั้งในรอบการทำงานเดียวกันของฟังก์ชัน

  $("#btnDeleteLast").click(function () {
    const row = $("#tbody-period tr[data-crud!='delete']:last");
    // console.log(row.attr("data-crud"));
    if (confirm("คุณต้องการลบงวดงานรายการสุดท้ายใช่หรือไม่?")) {
      // อ่านค่า data-crud จาก Attribute โดยตรง
      if (row.attr("data-crud") == "create") {
        row.remove();
      } else {
        row.find('input[name="crud"]').val("delete");
        row.addClass("d-none").attr("data-crud", "delete");
        // row.attr("data-crud", "delete");
      }
    }
    console.log(
      `row3 = ${row.attr("data-crud")} input=${row
        .find('input[name="crud"]')
        .val()}`
    );
    // console.log(row.attr("data-crud"));
  });

  $("#btnAdd").click(function () {
    let period_number;
    // $(".firstTr:has(.crud:not([value='d'])):last")//แบบที่ 1
    // $(".firstTr").has(".crud:not([value='d'])").last()//แบบที่ 2
    if ($("#tbody-period").has("tr[data-crud!='delete']").length > 0) {
      //หมายความว่า
      // 1. เลือก element ที่มี ID เป็น "tbody-period"
      // 2. เลือกเฉพาะ element ที่มี element ลูกหลาน (descendant) ที่ตรงกับ selector ในวงเล็บ นั่นคือ element ที่มีคลาสเป็น "firstTr" และมี attribute ชื่อ "crud" ซึ่งมีค่าไม่เท่ากับ "d"
      // 3. .length คือนับจำนวน element ที่ถูกเลือกได้จากการกรองในขั้นตอนก่อนหน้า(ข้อ 2.) ว่ามากกว่า 0 หรือไม่

      period_number = $("#tbody-period tr[data-crud!='delete']:last").find('input[name="period_number"]').val();
      period_number++;
      // console.log(`period number after = ${period_number}`);
      $("#tbody-period tr[data-crud!='delete']:last")
        .clone(false)
        .attr("data-crud", "create")
        .attr("data-period-id", "")
        .removeClass("d-none")

        .find('input[name="period_number"]')
        .val(period_number)
        .end()

        .find('input[name="workload_planned_percent"]')
        .val("0")
        .end()

        .find('input[name="interim_payment"]')
        .val("0")
        .end()

        .find('input[name="interim_payment_percent"]')
        .val("0")
        .end()

        .find('input[name="remark"]')
        .val("")
        .end()

        .find('input[name="crud"]')
        .val("create")
        .end()

        .appendTo("#tbody-period");
    } else {
      // Create the new tr element using jQuery
      const addTr = `<tr data-crud='create' data-period-id=''>
                            <td class='input-group-sm p-0'><input type='number' name='period_number' class='form-control period_number' value='1' readonly></td>
                            <td class='input-group-sm p-0'><input type='number' name='workload_planned_percent' class='form-control workload_planned_percent' value='0'></td>
                            <td class='input-group-sm p-0'><input type='number' name='interim_payment' class='form-control interim_payment' value='0'></td>
                            <td class='input-group-sm p-0'><input type='number' name='interim_payment_percent' class='form-control interim_payment_percent' value='0'></td>
                            <td class='input-group-sm p-0'><input type='text' name='remark' class='form-control remark'></td>
                            <td class='input-group-sm p-0'><input type='text' name='crud' class='form-control crud' value='create'></td>
                          </tr>`;

      $("#tbody-period").append(addTr);
    }
  });

  $(".btnCancel , .btnBack").click(function () {
    // history.go(-1);
    // $('.main').load('open_area_schedule_main.php'); แบบนี้ไม่ได้
    // header('Location: main.php?page=open_area_schedule_main');แบบนี้ไม่ได้
    window.history.back();
  });

  $("#contract_value_before").on("change keyup", function () {
    let contract_value_before = parseFloat($(this).val());
    let vat_rate = parseFloat($("#vat").attr("data-vat_rate"));

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
    let vat_rate = parseFloat($("#vat").attr("data-vat_rate"));

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
      $("#working_day").val("0");
    }
  });

  $("#myForm").on("submit", function (e) {// $(document).on("click", "#btnSave", function (e) {
    e.preventDefault();
    // const action = { action: "save" };

    const headerData = {
      po_id: $("#po_id").val(),
      po_number: $("#po_number").val(),
      project_name: $("#project_name").val(),
      supplier_id: $("#supplier_id").val(),
      location_id: $("#location_id").val(),
      working_name_th: $("#working_name_th").val(),
      working_name_en: $("#working_name_en").val(),
      contract_value_before: $("#contract_value_before").val() ?? 0,
      contract_value: parseFloat($("#contract_value").val() ?? 0),
      vat: $("#vat").val() ?? 0,
      is_include_vat: 1,
      // is_deposit: $("#is_deposit").val(),
      deposit_percent: $("#deposit_percent").val() ?? 0,
      deposit_value: (parseFloat($("#deposit_percent").val() ?? 0) * parseFloat($("#contract_value").val() ?? 0)) / 100,
      retention_percent: $("#retention_percent").val() ?? 0,
      retention_value: (parseFloat($("#retention_percent").val() ?? 0) * parseFloat($("#contract_value").val() ?? 0)) / 100,
      working_date_from: $("#working_date_from").val(),
      working_date_to: $("#working_date_to").val(),
      working_day: parseFloat($("#working_day").val() ?? 0),
      number_of_period: $("#tbody-period tr").not('[data-crud="delete"]')
        .length, //จำนวนตรงนี้จะไม่เอารายการที่ลบไป
    };

    const periodsData = [];
    $("#tbody-period tr").each(function () {
      const row = $(this);

      // สร้าง object สำหรับเก็บข้อมูลของแถวนี้
      // row.removeData("crud"); ทำการ clear ค่า data-* ที่อยู่ใน cache ถ้าใช้ row.data() ให้ clear ก่อน  ไม่เช่นนั้นจะได้ค่าที่ยังเก็บอยู่ใน cache
      const periodRecord = {
        po_id: $("#po_id").val(),
        period_id: row.attr("data-period-id"), // ถ้าใช้ row.data() ให้ clear ก่อน  ไม่เช่นนั้นจะได้ค่าที่ยังเก็บอยู่ใน cache
        period_crud: row.attr("data-crud"), //
        period_number: row.find('input[name="period_number"]').val(), // ใช้ .find() เพื่อหา input ที่อยู่ในแถวนี้ แล้ว .val() เพื่อดึงค่า
        workload_planned_percent: row
          .find('input[name="workload_planned_percent"]')
          .val(),
        interim_payment: row.find('input[name="interim_payment"]').val(),
        interim_payment_percent: row
          .find('input[name="interim_payment_percent"]')
          .val(),
        remark: row.find('input[name="remark"]').val(),
        crud: row.find('input[name="crud"]').val(),
      };
      // เพิ่ม object ของแถวนี้เข้าไปใน array หลัก
      periodsData.push(periodRecord);
    });

    const data_sent = {
      headerData: headerData,
      periodsData: periodsData,
      action: "save",
    };
    // data_sent['action'] = "save";
    console.log("Data to be sent:", JSON.stringify(data_sent));
    console.log("Data to be sent:", data_sent);
    // return;
    $.ajax({
      url: "po_handler_api.php",
      type: "POST",
      contentType: "application/json",
      dataType:'json',
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
            window.location.href = "po_list.php";
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



  // loadPeriods();
});
