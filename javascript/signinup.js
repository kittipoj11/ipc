$(document).ready(function () {
  $("#username").val("");
  $("#password").val("");

  $("#frmLogin").submit(function (e) {
    e.preventDefault();

    const data_sent = {
      action:'check_login',
      username: $("#username").val(),
      password: $("#password").val(),
    };

    console.log(data_sent);
    // return;
    $.ajax({
      url: "user_handler_api.php",
      type: "POST",
      contentType: "application/json",
      dataType: "json",
      data: JSON.stringify(data_sent),
    }).done(function(result){
        if (result) {
          // return;
          window.location = "index.php";
        } else {
          Swal.fire({
            icon: "error",
            title: "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง",
            color: "#DB4437",
            background: "#fff url(images/trees.png)",
            backdrop: `
            rgba(219, 68, 55,0.4)
            url("images/ani.gif")
            left top
            no-repeat
            `,
          }).then((result) => {
            if (result.isConfirmed) {
              window.location = "login.php";
            }
          });
        }
    })
      .fail(function (jqXHR, textStatus, errorThrown) {
        console.error("เกิดข้อผิดพลาดในการเชื่อมต่อ:", errorThrown);
        alert('เกิดข้อผิดพลาดในการเชื่อมต่อกับเซิร์ฟเวอร์: ' + textStatus)
        // $("#loginError").text("เกิดข้อผิดพลาดในการเชื่อมต่อกับเซิร์ฟเวอร์");
      });
  });

  $("#frmMemberRegistration").submit(function (e) {
    e.preventDefault();

    if ($("#enter_password").val() != $("#confirm_password").val()) {
      Swal.fire({
        icon: "error",
        title: "รหัสผ่านไม่ตรงกัน กรุณาใส่รหัสผ่านอีกครั้ง",
        color: "#DB4437",
        background: "#fff url(images/trees.png)",
        backdrop: `
                                rgba(219, 68, 55,0.4)
                                url("_images/ani.gif")
                                left top
                                no-repeat
                                `,
        // showConfirmButton: false,
        // timer: 1500
      });
      return;
    } else {
      //

      let data_sent =
        $("#frmMemberRegistration").serialize() + "&action=register";

      $.ajax({
        url: "customer_crud.php",
        data: data_sent,
        method: "POST",
        success: function (response) {
          // alert("res = " + response);
          if (response) {
            // var jsonData = JSON.parse(response); //ส่งกลับมาเป็น html ว่าสำเร็จหรือไม่
            Swal.fire({
              icon: "success",
              title: "ทำการสมัครเรียบร้อยแล้ว",
              color: "#716add",
              background: "#fff url(images/trees.png)",
              backdrop: `
                                rgba(15, 157, 88,0.4)
                                url("_images/paw.gif")
                                left bottom
                                no-repeat
                                `,
              // showConfirmButton: false,
              // timer: 1500
            });

            $("#registrationForm").modal("hide");
            $("#frmMemberRegistration")[0].reset();
          } else {
            Swal.fire({
              icon: "error",
              title: "มีบุคคลอื่นใช้  Username นี้แล้ว ให้ลองชื่ออื่น",
              color: "#DB4437",
              background: "#fff url(images/trees.png)",
              backdrop: `
                                rgba(0,0,123,0.4)
                                url("_images/paw.gif")
                                left bottom
                                no-repeat
                                `,
              // showConfirmButton: false,
              // timer: 1500
            });
          }
          // window.location = "login.php";
        },
      });
    }
  });

  // Delete
  $(document).on("click", ".btnEdit", function (e) {
    e.preventDefault();
    let id = $(this).attr("id");
    console.log(id);
    $.ajax({
      url: "impact/customer_crud.php",
      type: "POST",
      data: { edit_id: id },
      success: function (response) {
        console.log(response);
        data = JSON.parse(response);
        console.log(data);
        $(".username").val(data[0].username);
        $("#firstname").val(data[0].firstname);
        $("#lastname").val(data[0].lastname);
        $("#address").val(data[0].address);
        $("#phone").val(data[0].phone);
        $("#email").val(data[0].email);
      },
    });
  });

  $(document).on("click", "#btnUpdateData", function (e) {
    e.preventDefault();

    let data_sent = $("#frmEdit").serialize() + "&action=updatedata";
    // data_sent.push({
    //     name: "action",
    //     value: 'update'
    // });
    console.log(data_sent);
    $.ajax({
      url: "impact/customer_crud.php",
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
          background: "#fff url(/images/trees.png)",
          backdrop: `
                                rgba(0,0,123,0.4)
                                url("_images/paw.gif")
                                left bottom
                                no-repeat
                                `,
        });

        // $("#modalInsert").modal('hide');
        $("#frmEdit")[0].reset();
        $("#tbody").empty();
        $("#tbody").html(response);
        // window.location()
      },
    });
  });

  $(document).on("click", ".btnDelete", function (e) {
    e.preventDefault();
    let id = $(this).attr("id");
    Swal.fire({
      title: "Are you sure?",
      text: "You want to delete this item!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Yes, delete it!",
      // width: 600,
      // padding: '3em',
      color: "#716add",
      background: "#fff url(/images/trees.png)",
      backdrop: `
                                rgba(0,0,123,0.4)
                                url("_images/Pyh.gif")
                                left top
                                no-repeat
                                `,
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: "impact/customer_crud.php",
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
              background: "#fff url(/images/trees.png)",
              backdrop: `
                                rgba(0,0,123,0.4)
                                url("_images/Pyh.gif")
                                left top
                                no-repeat
                                `,
            });

            // $("#modalInsert").modal('hide');
            $("#frmEdit")[0].reset();
            $("#tbody").empty();
            $("#tbody").html(response);

            // $("#modalInsert").modal('hide');
            $("#tbody").empty();
            $("#tbody").html(response);
          },
        });
      }
    });
  });
});
