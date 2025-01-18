$(document).ready(function () {
  // $('#myForm').submit(function (e) {

  $(document).on("click", ".btnNew", function (e) {
    $("#location_id").val('[Autonumber]');
    $("#location_name").val('');
  });

  $(document).on("click", ".btnEdit", function (e) {
    e.preventDefault();
    let location_id = $(this).closest("tr").attr("id");
    console.log(`location_id = ${location_id} `);
    $.ajax({
      url: "location_crud.php",
      type: "POST",
      data: {
        location_id: location_id,
        action: 'selectdata'
      },
      success: function (response) {
        console.log(`response={$response}`);
        data = JSON.parse(response);
        // console.log(data);
        $("#location_id").val(data.location_id);
        $("#location_name").val(data["location_name"]);
      },
    });
  });

  $(document).on("click", ".btnDelete", function (e) {
    e.preventDefault();
    // let id = $(this).attr("id").slice("delete".length);
    let location_id = $(this).closest("tr").attr("id");
    $.ajax({
      url: "location_crud.php",
      type: "POST",
      data: {
        location_id: location_id,
        action: 'selectdata'
      },
      success: function (response) {
        data = JSON.parse(response);
        let location_name = data.location_name;
        Swal.fire({
          title: "Are you sure?",
          // text: `You want to delete this item!:${location_name}`,
          text: `You want to delete ${location_name} location`,
          icon: "warning",
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
              url: "location_crud.php",
              type: "POST",
              data: {
                location_id: location_id,
                action: 'deletedata'
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
                }).then((result) => {
                  /* Read more about isConfirmed, isDenied below */
                  if (result.isConfirmed) {
                  // Load เฉพาะ card-body
                    $("#frmOpen")[0].reset();
                    $("#card-body").empty();
                    $("#card-body").html(response);
                    $("#example1")
                      .DataTable({
                        responsive: true, //  true=การรองรับอุปกรณ์
                        lengthChange: true, //true ให้เลือกหน้าได้ว่าใน 1 หน้าต้องการให้แสดงกี่ row(มี 10, 25, 50, 100)
                        autoWidth: false, //กำหนดความกว้างอัตโนมัติ
                        buttons: ["copy", "csv", "excel", "pdf", "print", "colvis"],
                      })
                      .buttons()
                      .container()
                      .appendTo("#example1_wrapper .col-md-6:eq(0)");

                    // Load เฉพาะ tbody
                    // $("#frmOpen].reset();
                    // $("#tbody").empty();
                    // $("#tbody").html(response);

                    // // Load ใหม่ทั้งหน้า
                    // // window.location.href = "103hall.php";
                    // window.location.reload();
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

    let location_id = $("#location_id").val();
    let location_name = $("#location_name").val();
    let action = '';
    if (location_id == '[Autonumber]') {
      action = 'insertdata';
    } else {
      action = 'updatedata';
    }
    console.log(`action={$action}`);
    $.ajax({
      url: "location_crud.php",
      type: "POST",
      // data: $(this).serialize(),
      data: {
        location_id: location_id,
        location_name: location_name,
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
          // backdrop: `
          //                       rgba(0,0,123,0.4)
          //                       url("../images/fireworks.gif")
          //                       center center
          //                       no-repeat
          //                       `,
        }).then((result) => {
          /* Read more about isConfirmed, isDenied below */
          if (result.isConfirmed) {
          // Load เฉพาะ card-body
            $("#frmOpen")[0].reset();
            $("#card-body").empty();
            $("#card-body").html(response);
            $("#example1")
              .DataTable({
                responsive: true, //  true=การรองรับอุปกรณ์
                lengthChange: true, //true ให้เลือกหน้าได้ว่าใน 1 หน้าต้องการให้แสดงกี่ row(มี 10, 25, 50, 100)
                autoWidth: false, //กำหนดความกว้างอัตโนมัติ
                buttons: ["copy", "csv", "excel", "pdf", "print", "colvis"],
              })
              .buttons()
              .container()
              .appendTo("#example1_wrapper .col-md-6:eq(0)");

            // Load เฉพาะ tbody
            // $("#Open].reset();
            // $("#tbody").empty();
            // $("#tbody").html(response);

            // // Load ใหม่ทั้งหน้า
            // // window.location.href = "103hall.php";
            // window.location.reload();
          }
        });
      },
    });
  });


});
