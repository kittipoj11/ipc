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
      url: "user_handler_api.php",
      type: "POST",
      contentType: "application/json",
      dataType: "json",
      data: JSON.stringify(data_sent),
    })
      .done(function (result) {
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
    let checked="";
    $.each(datas, function (index, data) {
      checked = data.is_deleted ? "checked" : "";
      tableBody += `
                      <tr data-user-id='${data.user_id}' data-fullname='${data.full_name}'>
                          <td class="tdMain p-0"><a class='link-opacity-100 pe-auto' title='View' style='margin: 0px 5px 5px 5px'>${data.user_code}</a></td>
                          <td class="tdMain p-0">${data.username}</td>
                          <td class="tdMain p-0">${data.full_name}</td>
                          <td class="tdMain p-0">${data.role_name}</td>
                          <td class="tdMain p-0 text-left">${data.department_name}</td>
                          <td class="tdMain p-0 text-center"><input type="checkbox" value="" ${checked}></td>
                          <td class="tdMain p-0 action" align='center'>
                              <div class='btn-group-sm'>
                                  <a class='btn btn-warning btn-sm btnEdit' style='margin: 0px 5px 5px 5px' data-user-id='${data.user_id}'>
                                      <i class='fa-regular fa-pen-to-square'></i>
                                  </a>
                                  <a class='btn btn-danger btn-sm btnDelete' style='margin: 0px 5px 5px 5px' data-user-id='${data.user_id}'>
                                      <i class='fa-regular fa-trash-can'></i>
                                  </a>
                              </div>
                          </td>
                      </tr>
                            
                    `;
    });
    return tableBody;
  }

  $("#tbody").on("click", ".btnDelete", function (e) {
    e.preventDefault();

    const tr = $(this).parents("tr");
    const user_id = $(this).parents("tr").data("user-id");
    const fullname = $(this).parents("tr").data("fullname");
    // console.log(`po_id=${po_id}`);
    Swal.fire({
      title: "Are you sure?",
      text: `You want to delete user: ${fullname}!`,
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
     e.preventDefault(); // ป้องกันการทำงาน default ของลิงก์ (ไม่ต้องเปลี่ยนหน้า)
    const user_id = $(this).parents("tr").data("user-id");
    // window.location.href = "po_edit.php?po_id=" + po_id;
    window.location.href = "user_form.php?action=update" + "&user_id=" + user_id;
  });

  $("#tbody").on("click", "a", function (e) {
    e.preventDefault(); // ป้องกันการทำงาน default ของลิงก์ (ไม่ต้องเปลี่ยนหน้า)
    const user_id = $(this).parents("tr").data("user-id");
    // window.location.href = "po_edit.php?po_id=" + po_id;
    window.location.href = "user_form.php?action=update" + "&user_id=" + user_id;
  });




















  // Click ที่รายการงวดงานใดๆใน tdMain ที่ไม่มี <a></a>
  // $(document).on("click", ".tdMain:not(:has(a))", function (e) {
  $(document).on("click", ".tdMain", function (e) {
    e.preventDefault();
    $(".content-period").removeClass("d-none");

    const po_id = $(this).closest("tr").data("po-id");
    const po_number = $(this).closest("tr").data("po-number");

    $(".card-title").html(po_number);

    const data_sent = {
      po_id: po_id,
      action: "selectInspectionPeriodAll",
    };
    $.ajax({
      url: "user_handler_api.php",
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
          $("#tbody-period").html("");
        }
      })
      .fail((jqXHR) => {
        const errorMsg = jqXHR.responseJSON
          ? jqXHR.responseJSON.message
          : "เกิดข้อผิดพลาดในการดึงข้อมูล";
        // showMessage(errorMsg, false);
        $("#tbody-period").html("");
      });
  });

  $(document).on("click", "a.po_number", function (e) {
    e.preventDefault();
    const po_id = $(this).closest("tr").data("po-id");
    window.location.href = "inspection_view.php?po_id=" + po_id;
  });
  

    $(document).on("click", "a.period_number", function (e) {
      e.preventDefault();
      // การใช้ตัวแปรในการเก็บค่า
      // // ค้นหา tr ที่ปุ่ม a.period อยู่
      // let row = $(this).closest('tr');
      // // ค้น input ที่มี class po_id
      // let inputPoId = row.find('input.po_id');
      // // ดึงค่าจาก inputPoId
      // let po_id = inputPoId.val();
      // // ค้น input ที่มี class period_id
      // let inputPoPeriodId = row.find('input.period_id');
      // // ดึงค่าจาก inputPoPeriodId
      // let period_id = inputPoPeriodId.val();

      const po_id = $(this).closest("tr").data("po-id");
      // console.log(`po_id = ${po_id}`);
      const period_id = $(this).closest("tr").data("period-id");
      const inspection_id = $(this).closest("tr").data("inspection-id");
      // console.log(`period_id = ${period_id}`);
      // let po_id=1;
      // let period_id=1;
      // console.log(`po_id = ${po_id}`);
      // console.log(`period_id = ${period_id}`);
      // console.log(`inspection_id = ${inspection_id}`);
      window.location.href = `inspection_form.php?po_id=${po_id}&period_id=${period_id}&inspection_id=${inspection_id}`;
    });

  // loadAllInspection();
  loadDataAll();
});
