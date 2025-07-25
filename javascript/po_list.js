$(document).ready(function () {
  function showMessage(message, isSuccess) {
    responseMessage
      .text(message)
      .removeClass("success error")
      .addClass(isSuccess ? "success" : "error")
      .show()
      .delay(5000)
      .fadeOut();
  }

  // ฟังก์ชันสำหรับโหลดข้อมูลเริ่มต้น
  function loadDataAll() {
    const data_sent = {
      action: "select",
    };
    $.ajax({
      url: "po_handler_api.php",
      type: "POST",
      contentType: "application/json",
      dataType: "json",
      data: JSON.stringify(data_sent),
    })
      .done(function (result) {
        console.log(`result: ${result}`);
        // responseMessage.text(result.message).css("color", "green");
        if (result.length > 0) {
          tableBody = createTableBody(result);
          $("#tbody").html(tableBody); // นำ HTML ของตารางไปใส่ใน div ที่มี id="tbody"
        } else {
          $("#tbody").html();
        }
      })
      .fail((jqXHR) => {
        const errorMsg = jqXHR.responseJSON
          ? jqXHR.responseJSON.message
          : "เกิดข้อผิดพลาดในการดึงข้อมูล";
        // showMessage(errorMsg, false);
        $("#tbody").html();
      });
  }

  function createTableBody(datas) {
    let tableBody = "";
    $.each(datas, function (index, data) {
      tableBody += `
                      <tr data-id='${data.po_id}'>
                          <td class="tdMain p-0 d-none">${data.po_id}</td>
                          <td class="tdMain p-0"><a class='link-opacity-100 pe-auto po_number' title='Edit' style='margin: 0px 5px 5px 5px' data-po-number='${data.po_number}'>${data.po_number}</a></td>
                          <td class="tdMain p-0">${data.project_name}</td>
                          <td class="tdMain p-0">${data.supplier_name}</td>
                          <td class="tdMain p-0">${data.location_name}</td>
                          <td class="tdMain p-0">${data.working_name_th}</td>
                          <td class="tdMain p-0 text-right">${data.contract_value_before}</td>
                          <td class="tdMain p-0 text-right">${data.contract_value}</td>
                          <td class="tdMain p-0 text-right">${data.number_of_period}</td>
                          <td class="tdMain p-0 action" align='center'>
                              <div class='btn-group-sm'>
                                  <a class='btn btn-warning btn-sm btnEdit' style='margin: 0px 5px 5px 5px' data-id='${data.po_id}'>
                                      <i class='fa-regular fa-pen-to-square'></i>
                                  </a>
                                  <a class='btn btn-danger btn-sm btnDelete' style='margin: 0px 5px 5px 5px' data-id='${data.po_id}'>
                                      <i class='fa-regular fa-trash-can'></i>
                                  </a>
                              </div>
                          </td>
                      </tr>
                    `;
    });
    return tableBody;
  }

  function createPeriodTable(datas) {
    let tableBody = "";
    $.each(datas, function (index, data) {
      tableBody += `
                      <tr data-period-id = '${data.period_id}'>
                        <td class="p-0 d-none">${data.period_id}</td>
                        <td class="text-center py-0 px-1">${data.period_number}</td>
                        <td class="text-right py-0 px-1">${data.workload_planned_percent}</td>
                        <td class="text-right py-0 px-1">${data.interim_payment}</td>
                        <td class="text-right py-0 px-1">${data.interim_payment_percent}</td>
                        <td class="text-left py-0 px-1">${data.remark}</td>
                      </tr>
                    `;
    });
    return tableBody;
  }

  // $(document).on("click", ".tdMain:has(a)", function (e) {
  $("#tbody").on("click", "a.po_number", function (e) {
    e.preventDefault(); // ป้องกันการทำงาน default ของลิงก์ (ไม่ต้องเปลี่ยนหน้า)
    const po_id = $(this).parents("tr").data("id");
    // window.location.href = "po_edit.php?po_id=" + po_id;
    window.location.href = "po_form.php?action=update" + "&po_id=" + po_id;
  });

  // $("#tbody").on("click", ".tdMain:not(:has(a), .action)", function (e) {
    //, (comma) ภายใน :not(...): ใช้เพื่อรวมเงื่อนไขหลายอย่าง ในที่นี้คือ :has(a), :has(.action)
    // หมายความว่า :not() จะกรอง <td> ที่ ไม่มีทั้ง <a> และ ไม่มีทั้ง .action
    $("#tbody").on("click", ".tdMain", function (e) {
    e.preventDefault();
    $(".content-period").removeClass("d-none");

    const po_id = $(this).parents("tr").data("id"); //$(this).closest("tr")
    const po_number = $(this).parents("tr").find("a:first").data("id");

    $(".card-title").html(po_number);

    const data_sent = {
      po_id: po_id,
      action: "selectperiod",
    };
    $.ajax({
      url: "po_handler_api.php",
      type: "POST",
      contentType: "application/json",
      dataType: "json",
      data: JSON.stringify(data_sent),
    })
      .done(function (result) {
        if (result.length > 0) {
          tableBody = createPeriodTable(result);
          $("#tbody-period").html(tableBody);
        } else {
          $("#tbody-period").html();
        }
      })
      .fail((jqXHR) => {
        const errorMsg = jqXHR.responseJSON
          ? jqXHR.responseJSON.message
          : "เกิดข้อผิดพลาดในการดึงข้อมูล";
        // showMessage(errorMsg, false);
        $("#tbody-period").html();
      });
  });

  // ฟังก์ชันสำหรับเพิ่ม Event Listener ให้กับลิงก์เมนู
  // function attachMenuClickListeners() {
  // $('#tbody').on('click', 'a', function(event) {
  //     event.preventDefault();
  //     const content_filename = $(this).data('content_filename');
  //     const function_name = $(this).data('function_name');
  //     if (content_filename) {
  //         loadContent(content_filename, function_name);
  //     }
  // });
  // }

  // $('.btnDelete').on('click', function() { // แบบนี้ -> ไม่สามารถใช้งานได้เมื่อสร้างปุ่มขึ้นมาที่หลัง

  $("#tbody").on("click", ".btnDelete", function (e) {
    e.preventDefault();

    const tr = $(this).parents("tr");
    const po_id = $(this).parents("tr").data("id");
    const po_number = $(this).parents("tr").find("a:first").data("id");
    // console.log(`po_id=${po_id}`);
    Swal.fire({
      title: "Are you sure?",
      text: `You want to delete PO NO: ${po_number}!`,
      icon: "warning",
      showCancelButton: true,
      cancelButtonColor: "gray",
      confirmButtonColor: "red",
      confirmButtonText: "Yes, delete it!",
      // width: 600,
      // padding: '3em',
      color: "#ff0000",
      background: "black",
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: "po_crud.php", // ให้ส่งข้อมูลไปตาม url ที่กำหนด
          method: "POST", //เป็นวิธีการส่ง POST หรือ GET อาจจะใช้เป็น type: 'post'
          data: {
            po_id: po_id,
            action: "delete",
          }, //จะทำการส่งเป็นรูปแบบ java object ->{name: value}
          success: function (response) {
            // console.log(`response=${response}`);
            //ถ้าดึงข้อมูลมาเสร็จเรียบร้อยแล้วข้อมูลจะถูกส่งกลับมาไว้ที่ response
            if (response) {
              Swal.fire({
                title: "Deleted!",
                text: "Your data has been deleted.",
                icon: "success",
                // width: 600,
                // padding: '3em',
                color: "#716add",
                background: "black", //display dialog is black
                // confirmButtonColor: '#3085d6',
                confirmButtonText: "OK",
              }).then((result) => {
                if (result.isConfirmed) {
                  tr.remove();
                  $("#tbody-period").addClass("d-none");
                  // หรือ
                  loadDataAll();
                  // header('Location: user_list.php');
                  // window.location.reload(); //เปลี่ยนเป็นโหลด Table Body ใหม่เพื่อไม่ให้หน้าเพจกระพริบ
                }
              });
            } else {
              Swal.fire({
                title: "Oops...!",
                text: `Something went wrong!`,
                icon: "error",
                // width: 600,
                // padding: '3em',
                color: "#716add",
                background: "black", //display dialog is black
              });
            }
          },
          //หรืออาจจะสั่งให้แสดง Modal ขึ้นมา เช่น $('#myModal').modal('show'); //ถ้าใช้ Bootstrap Modal
        });
      }
    });
  });

  $("#tbody").on("click", ".btnEdit", function (e) {
    const po_id = $(this).parents("tr").data("id");
    // window.location.href = "po_edit.php?po_id=" + po_id;
    window.location.href = "po_form.php?action=update" + "&po_id=" + po_id;
  });

  loadDataAll();
});

// code ข้างล่างไม่เกี่ยวข้อง ใช้อ้างอิงในการทำในส่วนของ event change ของ dropdown
// document.getElementById("opt_event_id").addEventListener("change", complete_selection);
// document
//   .getElementById("building_id")
//   .addEventListener("change", complete_selection);
// document
//   .getElementById("hall_id")
//   .addEventListener("change", complete_selection);
// document
//   .getElementById("event_name")
//   .addEventListener("keyup", complete_selection);

// function complete_selection() {
//   if (
//     $("#event_name").val().trim().length === 0 ||
//     $("#building_id option:selected").text() == "..." ||
//     $("#hall_id option:selected").text() == "..."
//   ) {
//     $("#div_open_area_schedule").hide();
//   } else {
//     $("#div_open_area_schedule").show();
//   }
// }

// Hall: on change
