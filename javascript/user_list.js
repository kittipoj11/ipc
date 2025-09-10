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
    let checked = "";
    $.each(datas, function (index, data) {
      checked = data.is_deleted ? "" : "checked";
      display = data.is_deleted ? "d-none" : "d-flex";
      tableBody += `
                      <tr data-user-id='${data.user_id}' data-full-name='${data.full_name}'>
                          <td class="tdMain p-0"><a class='link-opacity-100 pe-auto user_code' title='View' style='margin: 0px 5px 5px 5px'>${data.user_code}</a></td>
                          <td class="tdMain p-0">${data.username}</td>
                          <td class="tdMain p-0">${data.full_name}</td>
                          <td class="tdMain p-0">${data.role_name}</td>
                          <td class="tdMain p-0 text-left">${data.department_name}</td>
                          <td class="tdMain p-0 text-center"><input type="checkbox" value="" ${checked}></td>
                          <td class="tdMain p-0 action" align='center'>
                              <div class='btn-group-sm ${display}'>
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
    const userId = $(this).parents("tr").data("user-id");
    const full_name = $(this).parents("tr").data("full-name");

    const headerData = {
      user_id: userId,
      full_name: full_name,
    };

    const data_sent = {
      headerData: headerData,
      action: "delete",
    };

    Swal.fire({
      title: "Are you sure?",
      text: `You want to delete user: ${full_name}!`,
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
          url: "user_handler_api.php", // ให้ส่งข้อมูลไปตาม url ที่กำหนด
          type: "POST", //เป็นวิธีการส่ง POST หรือ GET อาจจะใช้เป็น type: 'post'
          data: JSON.stringify(data_sent), //จะทำการส่งเป็นรูปแบบ java object ->{name: value}
          contentType: "application/json",
          dataType: "json",
        })
          .done(function (result) {
            console.log(result);
            responseMessage.text(result.message).css("color", "green");
            //ถ้าดึงข้อมูลมาเสร็จเรียบร้อยแล้วข้อมูลจะถูกส่งกลับมาไว้ที่ response
            if (result) {
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
                  // tr.remove();
                  // หรือ
                  loadDataAll();
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
          })
          .fail((jqXHR) => {
            const errorMsg = jqXHR.responseJSON
              ? jqXHR.responseJSON.message
              : "เกิดข้อผิดพลาดรุนแรง";
            showMessage(errorMsg, false);
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

  $("#tbody").on("click", "a.user_code", function (e) {
    e.preventDefault(); // ป้องกันการทำงาน default ของลิงก์ (ไม่ต้องเปลี่ยนหน้า)
    const user_id = $(this).parents("tr").data("user-id");
    // window.location.href = "po_edit.php?po_id=" + po_id;
    window.location.href = "user_form.php?action=update" + "&user_id=" + user_id;
  });



  loadDataAll();
});
