$(document).ready(function () {
  // $('#myForm').submit(function (e) {

  $(document).on("click", ".btnNew", function (e) {
    $("#workflow_id").val("[Autonumber]");
    $("#workflow_name").val("");
  });

  $(document).on("click", ".btnEdit", function (e) {
    e.preventDefault();
    let workflow_id = $(this).closest("tr").attr("id");
    // console.log(`workflow_id = ${workflow_id} `);
    $.ajax({
      url: "workflows_crud.php",
      type: "POST",
      data: {
        workflow_id: workflow_id,
        action: "selectdata",
      },
      success: function (response) {
        console.log(`response=${response}`);
        data = JSON.parse(response);
        // console.log(data);
        $("#workflow_id").val(data.workflow_id);
        $("#workflow_name").val(data["workflow_name"]);
      },
    });
  });

  $(document).on("click", ".btnDelete", function (e) {
    e.preventDefault();
    // let id = $(this).attr("id").slice("delete".length);
    let workflow_id = $(this).closest("tr").attr("id");
    $.ajax({
      url: "workflows_crud.php",
      type: "POST",
      data: {
        workflow_id: workflow_id,
        action: "selectdata",
      },
      success: function (response) {
        data = JSON.parse(response);
        let workflow_name = data.workflow_name;
        Swal.fire({
          title: "Are you sure?",
          // text: `You want to delete this item!:${workflow_name}`,
          text: `You want to delete ${workflow_name} workflow`,
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
              url: "workflows_crud.php",
              type: "POST",
              data: {
                workflow_id: workflow_id,
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
                    loadAllWorkflow();
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
                    // // window.workflow.href = "103hall.php";
                    // window.workflow.reload();
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

    let workflow_id = $("#workflow_id").val();
    let workflow_name = $("#workflow_name").val();
    let action = "";
    if (workflow_id == "[Autonumber]") {
      action = 'create';
    } else {
      action = 'update';
    }
    console.log(`action=${action}`);
    $.ajax({
      url: "workflows_crud.php",
      type: "POST",
      // data: $(this).serialize(),
      data: {
        workflow_id: workflow_id,
        workflow_name: workflow_name,
        action: action,
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
            loadAllWorkflow();
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
            // // window.workflow.href = "103hall.php";
            // window.workflow.reload();
          }
        });
      },
    });
  });

  // ฟังก์ชันสำหรับโหลดข้อมูลเริ่มต้น
  function loadAllWorkflow() {
    // console.log("Start");
    $.ajax({
      url: "workflows_crud.php",
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

  loadAllWorkflow();
});
