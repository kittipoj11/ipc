$(document).ready(function () {
  // $('#myForm').submit(function (e) {

  $(document).on("click", ".btnNew", function (e) {
    $("#po_status_id").val('[Autonumber]');
    $("#po_status_name").val('');
  });

  $(document).on("click", ".btnEdit", function (e) {
    e.preventDefault();
    let po_status_id = $(this).closest("tr").attr("id");
  // console.log(`po_status_id = ${po_status_id} `);
    $.ajax({
      url: "po_status_crud.php",
      type: "POST",
      data: {
        po_status_id: po_status_id,
        action: 'selectdata'
      },
      success: function (response) {
        console.log(`response=${response}`);
        data = JSON.parse(response);
        // console.log(data);
        $("#po_status_id").val(data.po_status_id);
        $("#po_status_name").val(data["po_status_name"]);
      },
    });
  });

  $(document).on("click", ".btnDelete", function (e) {
    e.preventDefault();
    // let id = $(this).attr("id").slice("delete".length);
    let po_status_id = $(this).closest("tr").attr("id");
    $.ajax({
      url: "po_status_crud.php",
      type: "POST",
      data: {
        po_status_id: po_status_id,
        action: 'selectdata'
      },
      success: function (response) {
        data = JSON.parse(response);
        let po_status_name = data.po_status_name;
        Swal.fire({
          title: "Are you sure?",
          // text: `You want to delete this item!:${po_status_name}`,
          text: `You want to delete ${po_status_name} po_status`,
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
              url: "po_status_crud.php",
              type: "POST",
              data: {
                po_status_id: po_status_id,
                action: "deletedata",
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
                    loadAllPoStatus();
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
                    // // window.po_status.href = "103hall.php";
                    // window.po_status.reload();
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

    let po_status_id = $("#po_status_id").val();
    let po_status_name = $("#po_status_name").val();
    let action = '';
    if (po_status_id == '[Autonumber]') {
      action = 'insertdata';
    } else {
      action = 'updatedata';
    }
    console.log(`action=${action}`);
    $.ajax({
      url: "po_status_crud.php",
      type: "POST",
      // data: $(this).serialize(),
      data: {
        po_status_id: po_status_id,
        po_status_name: po_status_name,
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
            loadAllPoStatus();
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
            // // window.po_status.href = "103hall.php";
            // window.po_status.reload();
          }
        });
      },
    });
  });

  // ฟังก์ชันสำหรับโหลดข้อมูลเริ่มต้น
  function loadAllPoStatus() {
    // console.log("Start");
    $.ajax({
      url: "po_status_crud.php",
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

  loadAllPoStatus();
});
