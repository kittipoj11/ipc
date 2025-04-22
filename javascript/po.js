$(document).ready(function () {
  // $('.btnDelete').on('click', function() { // แบบนี้ -> ไม่สามารถใช้งานได้เมื่อสร้างปุ่มขึ้นมาที่หลัง
  $(document).on("click", ".btnDelete", function (e) {
    e.preventDefault();
    // const po_id = $(this).data("id");
    const tr = $(this).parents("tr");
    const po_id = $(this).parents("tr").data("id");
    const po_number = $(this).parents("tr").find("a:first").data("id");

    Swal.fire({
      title: "Are you sure?",
      text: `You want to delete PO NO: ${po_number}!`,
      icon: "warning",
      showCancelButton: true,
      cancelButtonColor: "gray",
      confirmButtonColor: "red",
      confirmButtonText: "Yes, delete it!",
      // width: 600,
      // padding: '3em',
      color: "#ff0000",
      background: "black",
      // backdrop: `
      //                           rgba(0,0,123,0.4)
      //                           url("_images/Pyh.gif")
      //                           left top
      //                           no-repeat
      //                           `,
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: "po_crud.php", // ให้ส่งข้อมูลไปตาม url ที่กำหนด
          data: { action: "delete", po_id: po_id }, //จะทำการส่งเป็นรูปแบบ java object ->{name: value}
          method: "POST", //เป็นวิธีการส่ง POST หรือ GET อาจจะใช้เป็น type: 'post'
          success: function (response) {
            //ถ้าดึงข้อมูลมาเสร็จเรียบร้อยแล้วข้อมูลจะถูกส่งกลับมาไว้ที่ response
            if (response == "success") {
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
                // backdrop: `
                //                 rgba(0,0,123,0.4)
                //                 url("_images/Pyh.gif")
                //                 left top
                //                 no-repeat
                //                 `,
              }).then((result) => {
                if (result.isConfirmed) {
                  tr.remove();
                  $("#tbody-period").addClass("d-none");

                  // window.location.reload(); //เปลี่ยนเป็นโหลด Table Body ใหม่เพื่อไม่ให้หน้าเพจกระพริบ
                }
              });
            } else {
              // alert(response);
              // alert('ไม่สามารถลบรายการนี้ได้');
              Swal.fire({
                title: "Oops...!",
                text: `Something went wrong!`,
                icon: "error",
                // width: 600,
                // padding: '3em',
                color: "#716add",
                background: "black", //display dialog is black
                // backdrop: `
                //                 rgba(0,0,123,0.4)
                //                 url("_images/Pyh.gif")
                //                 left top
                //                 no-repeat
                //                 `,
              });
            }
          },
          //หรืออาจจะสั่งให้แสดง Modal ขึ้นมา เช่น $('#myModal').modal('show'); //ถ้าใช้ Bootstrap Modal
        });
      }
    });
  });

  // $('.btnEdit').each(function() {
  //     $(this).on('click', function(event) {
  //         event.preventDefault();
  //         const po_id = $(this).parents("tr").attr("po-id");
  //         window.location.href = "po_edit.php?id=" + po_id;
  //     });
  // });
  $(document).on("click", ".btnEdit", function (e) {
    const po_id = $(this).parents("tr").data("id");
    // window.location.href = "po_edit.php?po_id=" + po_id;
    window.location.href = "po_dml.php?action=update"+"&po_id=" + po_id;
  });
  
  // $(document).on("click", ".tdMain:has(a)", function (e) {
    $(document).on("click", "a.po_number", function (e) {
      e.preventDefault(); // ป้องกันการทำงาน default ของลิงก์ (ไม่ต้องเปลี่ยนหน้า)
      const po_id = $(this).parents("tr").data("id");
      // window.location.href = "po_edit.php?po_id=" + po_id;
      window.location.href = "po_dml.php?action=update"+"&po_id=" + po_id;
  });

  // $(".btnEdit").each(function () {
  //   // เลือกทุก element ที่มี class 'btnEdit'
  //   $(this).on("click", function (event) {
  //     event.preventDefault(); // ป้องกันการทำงาน default ของลิงก์ (ไม่ต้องเปลี่ยนหน้า)
  //     const po_id = $(this).data("id"); // อ่านค่า data-id จากลิงก์ที่คลิก
  //     const po_number = $(this).parents("tr").find("a:first").data("id");

  //     window.location.href = "po_edit.php?po_id=" + po_id;
  //   });

  // });
});

$(document).ready(function () {
  $(document).on("click", ".tdMain:not(:has(a),.action)", function (e) {
    //, (comma) ภายใน :not(...): ใช้เพื่อรวมเงื่อนไขหลายอย่าง ในที่นี้คือ :has(a), :has(.action)
    // หมายความว่า :not() จะกรอง <td> ที่ ไม่มีทั้ง <a> และ ไม่มีทั้ง .action
    e.preventDefault();
    $(".content-period").removeClass("d-none");
    // หรือ
    // $(".content-period").removeClass('d-none').addClass('d-flex');

    let po_id = $(this).parents("tr").data("id"); //$(this).closest("tr")
    let po_number = $(this).parents("tr").find("a:first").data("id");
    // let po_id = $(this).closest('tr').attr('po-id');
    $(".card-title").html(po_number);

    $.ajax({
      url: "po_crud.php",
      type: "POST",
      data: {
        po_id: po_id,
        dataType: "json",
        action: "selectperiod",
      },
      success: function (response) {
        // console.log(`response=${response}`);
        // data = JSON.parse(response);
        // console.log(data);

        $("#tbody-period").html(response);
      },
    });
  });
});

// document.getElementById("opt_event_id").addEventListener("change", complete_selection);
// document
//   .getElementById("building_id")
//   .addEventListener("change", complete_selection);
// document
//   .getElementById("hall_id")
//   .addEventListener("change", complete_selection);
// document
//   .getElementById("event_name")
//   .addEventListener("keyup", complete_selection);

// function complete_selection() {
//   if (
//     $("#event_name").val().trim().length === 0 ||
//     $("#building_id option:selected").text() == "..." ||
//     $("#hall_id option:selected").text() == "..."
//   ) {
//     $("#div_open_area_schedule").hide();
//   } else {
//     $("#div_open_area_schedule").show();
//   }
// }

// Hall: on change
