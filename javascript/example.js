$(document).ready(function () {
  // $('#myForm').submit(function (e) {
  $(document).on("click", "#btnInsertData", function (e) {
    e.preventDefault();
    // แบบที่ 1 ใช้ serializeArray() แล้ว push ค่าเพิ่มเติมให้ array
    // let data_sent = $("#frmInsert").serializeArray();
    // data_sent.push({
    //   name: "action",
    //   value: "insertdata",
    // });

    // แบบที่ 2 ใช้ serialize() แล้ว + ด้วย "&action=insertdata"
    // let data_sent = $("#frmInsert").serialize() + "&action=insertdata";

    // แบบที่ 3 ใช้กำหนดที่ละตัวแปร 
    let department_name = $("#department_name").val();
    // let data_sent = {
    //   var1: ตัวแปรvar1,
    //   var2: ตัวแปรvar2,
    //   action: 'insertdata',
    // };

    $.ajax({
      url: "department_crud.php",
      data: {
        department_name: department_name,
        action: 'insertdata',
      },
      method: "POST",
      datatype: "json",
      success: function (response) {
        // var jsonData = JSON.parse(response); //ส่งกลับมาเป็น html ว่าสำเร็จหรือไม่
        Swal.fire({
          icon: "success",
          title: "Data added successfully",
          color: "#716add",
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
            // $(".result").html("Data added successfully");
            // Load เฉพาะ card-body
            $("#frmInsert")[0].reset();
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
            // $("#frmInsert")[0].reset();
            // $("#tbody").empty();
            // $("#tbody").html(response);
            // $("#example1").DataTable();

            // Load ใหม่ทั้งหน้า
            // window.department.href = "103hall.php";
            // window.department.reload();
          }
        });
      },
    });
  });

  $(document).on("click", ".btnEdit", function (e) {
    e.preventDefault();
    // let id = $(this).attr("id").slice("edit".length);
    let iid = $(this).attr("iid");
    let id = $(this).closest("tr").attr("id");
    console.log("id");
    $.ajax({
      url: "department_crud.php",
      type: "POST",
      data: {
        edit_id: id,
        action: 'updatedata'
      },
      success: function (response) {
        // console.log(response);
        data = JSON.parse(response);
        // console.log(data);
        $("#department_id").val(data.department_id);
        $("#department_name").val(data["department_name"]);
      },
    });
  });

  $(document).on("click", "#btnUpdateData", function (e) {
    e.preventDefault();

    let data_sent = $("#frmEdit").serialize() + "&action=updatedata";
    // data_sent.push({
    //     name: "action",
    //     value: "updatedata"
    // });
    // console.log(data_sent);
    $.ajax({
      url: "department_crud.php",
      type: "POST",
      // data: $(this).serialize(),
      data: data_sent,
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
            $("#frmEdit")[0].reset();
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
            // $("#frmEdit")[0].reset();
            // $("#tbody").empty();
            // $("#tbody").html(response);

            // // Load ใหม่ทั้งหน้า
            // // window.department.href = "103hall.php";
            // window.department.reload();
          }
        });
      },
    });
  });

  $(document).on("click", ".btnDelete", function (e) {
    e.preventDefault();
    // let id = $(this).attr("id").slice("delete".length);
    let id = $(this).attr("iid");
    Swal.fire({
      title: "Are you sure?",
      text: "You want to delete this item!",
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
      // backdrop: `
      //                       rgba(0,0,123,0.4)
      //                       url("../images/fireworks.gif")
      //                       center center
      //                       no-repeat
      //                       `,
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: "department_crud.php",
          type: "POST",
          data: { delete_id: id },
          success: function (response) {
            // Swal.fire({
            //     title: 'Deleted!',
            //     text: 'Your data has been deleted.',
            //     icon: 'success'
            //     // showConfirmButton: false,
            //     // timer: 1500
            // })
            Swal.fire({
              title: "Deleted!",
              text: "Your data has been deleted.",
              icon: "success",
              // width: 600,
              // padding: '3em',
              color: "#716add",
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
                $("#frmEdit")[0].reset();
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
                // $("#frmEdit")[0].reset();
                // $("#tbody").empty();
                // $("#tbody").html(response);

                // // Load ใหม่ทั้งหน้า
                // // window.department.href = "103hall.php";
                // window.department.reload();
              }
            });
          },
        });
      }
    });
  });
});
