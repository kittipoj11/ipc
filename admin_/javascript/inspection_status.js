$(document).ready(function () {
  // $('#myForm').submit(function (e) {
  $(document).on("click", ".btnNew", function (e) {
    $("#inspection_status_id").val("[Autonumber]");
    $("#inspection_status_name").val("");
  });

  $(document).on("click", ".btnEdit", function (e) {
    e.preventDefault();
    let inspection_status_id = $(this).closest("tr").data("id");
    // console.log(`inspection_status_id = ${inspection_status_id} `);
    $.ajax({
      url: "inspection_status_crud.php",
      type: "POST",
      data: {
        inspection_status_id: inspection_status_id,
        action: "selectdata",
      },
      success: function (data) {
        // console.log(`data=${data}`);
        if (data) {
          $("#inspection_status_id").val(data.inspection_status_id);
          $("#inspection_status_name").val(data["inspection_status_name"]);
        } else {
        }
      },
    });
  });

  $(document).on("click", ".btnDelete", function (e) {
    e.preventDefault();
    // let id = $(this).data("id").slice("delete".length);
    let inspection_status_id = $(this).closest("tr").data("id");
    $.ajax({
      url: "inspection_status_crud.php",
      type: "POST",
      data: {
        inspection_status_id: inspection_status_id,
        action: "selectdata",
      },
      success: function (data) {
        if (data) {
          let inspection_status_name = data.inspection_status_name;
          Swal.fire({
            title: "Are you sure?",
            // text: `You want to delete this item!:${inspection_status_name}`,
            text: `You want to delete ${inspection_status_name} inspection_status`,
            icon: "warning",
            allowOutsideClick: false,
            showCancelButton: true,
            // cancelButtonColor: '#00ff00',
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
                url: "inspection_status_crud.php",
                type: "POST",
                data: {
                  inspection_status_id: inspection_status_id,
                  action: 'delete',
                },
                success: function (data) {
                  // console.log(`data=${JSON.parse(data)}`);
                  Swal.fire({
                    title: "Deleted!",
                    text: "Your data has been deleted.",
                    icon: "success",
                    // width: 600,
                    // padding: '3em',
                    color: "#716add",
                    background: "black", //display dialog is black
                    allowOutsideClick: false,
                  }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                      $("#frmOpen")[0].reset();
                      loadDataAll();
                    }
                  });
                },
              });
            }
          });
        } else {
        }
      },
    });
  });

  $(document).on("click", "#btnSaveData", function (e) {
    e.preventDefault();

    let inspection_status_id = $("#inspection_status_id").val();
    let inspection_status_name = $("#inspection_status_name").val();
    let action = "";
    if (inspection_status_id == "[Autonumber]") {
      action = 'create';
    } else {
      action = 'update';
    }
    // console.log(`action=${action}`);
    $.ajax({
      url: "inspection_status_crud.php",
      type: "POST",
      // data: $(this).serialize(),
      data: {
        inspection_status_id: inspection_status_id,
        inspection_status_name: inspection_status_name,
        action: action,
      },
      success: function (data) {
        if (data) {
          console.log(`data=${JSON.parse(data)}`);
          Swal.fire({
            title: "Data updated successfully.",
            icon: "success",
            // width: 600,
            // padding: '3em',
            color: "#716add",
            // background: "#fff url(../images/IMPACT_Arena2.jpg)",//display dialog with image
            // background: "000000",//Transparent
            background: "black", //display dialog is black
            allowOutsideClick: false,
            // backdrop: `
            //                       rgba(0,0,123,0.4)
            //                       url("../images/fireworks.gif")
            //                       center center
            //                       no-repeat
            //                       `,
          }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {
              $("#frmOpen")[0].reset();
              loadDataAll();
            }
          });
        }
      },
    });
  });

  // ฟังก์ชันสำหรับโหลดข้อมูลเริ่มต้น
  function loadDataAll() {
    $.ajax({
      url: "inspection_status_crud.php",
      data: {
        action: "select",
      },
      method: "POST",
      datatype: "json",
      success: function (data) {
        //data เป็นข้อมูล json ที่ส่งกลับมา
        // ฟังก์ชันที่จะทำงานเมื่อการดึงข้อมูลสำเร็จ
        if (data.length > 0) {
          tableBody = createTableBody(data);
          $("#tbody").html(tableBody); // นำ HTML ของตารางไปใส่ใน div ที่มี id="tbody"
        } else {
          $("#tbody").html();
        }
      },
      error: function (xhr, status, error) {
        // ฟังก์ชันที่จะทำงานเมื่อเกิดข้อผิดพลาดในการดึงข้อมูล
        console.error("เกิดข้อผิดพลาดในการดึงข้อมูล:", error);
        $("#tbody").html();
      },
    });
  }

  function createTableBody(data) {
    let tableBody = "";
    $.each(data, function (index, inspection_status) {
      tableBody += `
                        <tr data-id=${inspection_status.inspection_status_id}>
                          <td>${inspection_status.inspection_status_id}</td>
                          <td>${inspection_status.inspection_status_name}</td>
                          <td align='center'>
                              <div class='btn-group-sm'>
                                  <a class='btn btn-warning btn-sm btnEdit' data-bs-toggle='modal'  data-bs-placement='right' title='Edit' data-bs-target='#openModal' data-id='${inspection_status.inspection_status_id}' style='margin: 0px 5px 5px 5px'>
                                      <i class='fa-regular fa-pen-to-square'></i>
                                  </a>
                                  <a class='btn btn-danger btn-sm btnDelete' data-bs-toggle='modal'  data-bs-placement='right' title='Delete' data-bs-target='#deleteModal' data-id='${inspection_status.inspection_status_id}' style='margin: 0px 5px 5px 5px'>
                                      <i class='fa-regular fa-trash-can'></i>
                                  </a>
                              </div>
                          </td>
                        </tr>
                      `;
    });
    return tableBody;
  }

  loadDataAll();
});
