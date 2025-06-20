$(document).ready(function () {
  // $('#myForm').submit(function (e) {

  $(document).on("click", ".btnNew", function (e) {
    $("#permission_id").val('[Autonumber]');
    $("#permission_name").val('');
    $("#menu_name").val('');
    $("#content_filename").val('');
    $("#function_name").val('');
  });

  $(document).on("click", ".btnEdit", function (e) {
    e.preventDefault();
    let permission_id = $(this).closest("tr").attr("id");
    // console.log(`permission_id = ${permission_id} `);
    $.ajax({
      url: "permission_crud.php",
      type: "POST",
      data: {
        permission_id: permission_id,
        action: 'selectdata'
      },
      success: function (response) {
        console.log(`response=${response}`);
        data = JSON.parse(response);
        // console.log(data);
        $("#permission_id").val(data.permission_id);
        $("#permission_name").val(data["permission_name"]);
        $("#menu_name").val(data["menu_name"]);
        $("#content_filename").val(data["content_filename"]);
        $("#function_name").val(data["function_name"]);
      },
    });
  });

  $(document).on("click", ".btnDelete", function (e) {
    e.preventDefault();
    // let id = $(this).attr("id").slice("delete".length);
    let permission_id = $(this).closest("tr").attr("id");
    $.ajax({
      url: "permission_crud.php",
      type: "POST",
      data: {
        permission_id: permission_id,
        action: 'selectdata'
      },
      success: function (response) {
        data = JSON.parse(response);
        let permission_name = data.permission_name;
        Swal.fire({
          title: "Are you sure?",
          // text: `You want to delete this item!:${permission_name}`,
          text: `You want to delete ${permission_name} permission`,
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
              url: "permission_crud.php",
              type: "POST",
              data: {
                permission_id: permission_id,
                action: 'delete',
              },
              success: function (response) {
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
                    loadAllPermission();
                    // Load เฉพาะ card-body
                    // $("#card-body").empty();
                    // $("#card-body").html(response);
                    // $("#example1")
                    //   .DataTable({
                    //     responsive: true, //  true=การรองรับอุปกรณ์
                    //     lengthChange: true, //true ให้เลือกหน้าได้ว่าใน 1 หน้าต้องการให้แสดงกี่ row(มี 10, 25, 50, 100)
                    //     autoWidth: false, //กำหนดความกว้างอัตโนมัติ
                    //     buttons: [
                    //       "copy",
                    //       "csv",
                    //       "excel",
                    //       "pdf",
                    //       "print",
                    //       "colvis",
                    //     ],
                    //   })
                    //   .buttons()
                    //   .container()
                    //   .appendTo("#example1_wrapper .col-md-6:eq(0)");

                    // Load เฉพาะ tbody
                    // $("#frmOpen].reset();
                    // $("#tbody").empty();
                    // $("#tbody").html(response);

                    // // Load ใหม่ทั้งหน้า
                    // // window.permission.href = "103hall.php";
                    // window.permission.reload();
                  }
                });
              },
            });
          }
        });
      },
    });


  });


  $(document).on("click", "#btnSaveData", function (e) {
    e.preventDefault();

    let permission_id = $("#permission_id").val();
    let permission_name = $("#permission_name").val();
    let menu_name = $("#menu_name").val();
    let content_filename = $("#content_filename").val();
    let function_name = $("#function_name").val();
    let action = '';
    if (permission_id == '[Autonumber]') {
      action = 'create';
    } else {
      action = 'update';
    }
    console.log(`action=${action}`);
    $.ajax({
      url: "permission_crud.php",
      type: "POST",
      // data: $(this).serialize(),
      data: {
        permission_id: permission_id,
        permission_name: permission_name,
        menu_name: menu_name,
        content_filename: content_filename,
        function_name: function_name,
        action: action
      },
      success: function (response) {
      // Swal.fire({
        //     icon: 'success',
        //     title: 'Data updated successfully'
        //     // showConfirmButton: false,
        //     // timer: 1500
        // })
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
            loadAllPermission();
            // Load เฉพาะ card-body
            // $("#card-body").empty();
            // $("#card-body").html(response);
            // $("#example1")
            //   .DataTable({
            //     responsive: true, //  true=การรองรับอุปกรณ์
            //     lengthChange: true, //true ให้เลือกหน้าได้ว่าใน 1 หน้าต้องการให้แสดงกี่ row(มี 10, 25, 50, 100)
            //     autoWidth: false, //กำหนดความกว้างอัตโนมัติ
            //     buttons: ["copy", "csv", "excel", "pdf", "print", "colvis"],
            //   })
            //   .buttons()
            //   .container()
            //   .appendTo("#example1_wrapper .col-md-6:eq(0)");

            // Load เฉพาะ tbody
            // $("#Open].reset();
            // $("#tbody").empty();
            // $("#tbody").html(response);

            // // Load ใหม่ทั้งหน้า
            // // window.permission.href = "103hall.php";
            // window.permission.reload();
          }
        });
      },
    });
  });

  // ฟังก์ชันสำหรับโหลดข้อมูลเริ่มต้น
  function loadAllPermission() {
    // console.log("Start");
    $.ajax({
      url: "permission_crud.php",
      type: "POST",
      data: {
        dataType: "json",
        action: "select",
      },
      success: function (response) {
        // console.log("load");
        // console.log(response);
        $("#tbody").html(response);
        // attachMenuClickListeners();
      },
    });
  }

  loadAllPermission();
});
