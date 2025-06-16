$(document).ready(function () {
  // $('#myForm').submit(function (e) {

  $(document).on("click", ".btnNew", function (e) {
    $("#role_id").val("[Autonumber]");
    $("#role_name").val("");
  });

  $(document).on("click", ".btnEdit", function (e) {
    e.preventDefault();
    let role_id = $(this).closest("tr").attr("id");
    // console.log(`role_id = ${role_id} `);
    $.ajax({
      url: "role_crud.php",
      type: "POST",
      data: {
        role_id: role_id,
        action: "selectdata",
      },
      success: function (response) {
        // console.log(`response=${response}`);
        data = JSON.parse(response);
        // console.log(data);
        $("#role_id").val(data.role_id);
        $("#role_name").val(data["role_name"]);
      },
    });
  });

  $(document).on("click", ".btnDelete", function (e) {
    e.preventDefault();
    // let id = $(this).attr("id").slice("delete".length);
    let role_id = $(this).closest("tr").attr("id");
    $.ajax({
      url: "role_crud.php",
      type: "POST",
      data: {
        role_id: role_id,
        action: "selectdata",
      },
      success: function (response) {
        data = JSON.parse(response);
        let role_name = data.role_name;
        Swal.fire({
          title: "Are you sure?",
          // text: `You want to delete this item!:${role_name}`,
          text: `You want to delete ${role_name} role`,
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
              url: "role_crud.php",
              type: "POST",
              data: {
                role_id: role_id,
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
                    loadAllRole();
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

    let role_id = $("#role_id").val();
    let role_name = $("#role_name").val();
    let action = "";
    if (role_id == "[Autonumber]") {
      action = "insertdata";
    } else {
      action = "updatedata";
    }
    // console.log(`action=${action}`);
    $.ajax({
      url: "role_crud.php",
      type: "POST",
      // data: $(this).serialize(),
      data: {
        role_id: role_id,
        role_name: role_name,
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
            loadAllRole();
          }
        });
      },
    });
  });

  // ฟังก์ชันสำหรับโหลดข้อมูลเริ่มต้น
  function loadAllRole() {
    // console.log("Start");
    $.ajax({
      url: "role_crud.php",
      type: "POST",
      data: {
        dataType: "json",
        action: "select",
      },
      success: function (response) {
        $("#tbody").html(response);
      },
    });
  }

  loadAllRole();
});
