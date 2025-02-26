$(document).ready(function () {
  $("#myForm").on("submit", function (e) {
    // $(document).on("click", "#btnSave", function (e) {
    // console.log('submit');
    e.preventDefault();
    let can_save = true;
    if (can_save == true) {
      let data_sent = $("#myForm").serializeArray();
      data_sent.push({
        name: "action",
        value: "update",
      });
      console.log(data_sent);
      $.ajax({
        type: "POST",
        url: "po_crud.php",
        // data: $(this).serialize(),
        data: data_sent,
        success: function (response) {
          Swal.fire({
            icon: "success",
            title: "Data saved successfully",
            color: "#716add",
            background: "black",
            // backdrop: `
            //                     rgba(0,0,123,0.4)
            //                     url("_images/paw.gif")
            //                     left bottom
            //                     no-repeat
            //                     `,
            // showConfirmButton: false,
            // timer: 15000
          }).then((result) => {
            if (result.isConfirmed) {
              window.location.href = "po.php";
              // window.location.reload();
            }
          });
          // window.location.href = 'main.php?page=open_area_schedule';
        },
      });
    }
  });
});

$(document).ready(function () {
  // $('.btnDelete').on('click', function() { // แบบนี้ -> ไม่สามารถใช้งานได้เมื่อสร้างปุ่มขึ้นมาที่หลัง
  $(document).on("click", ".btnDelete", function (e) {
    e.preventDefault();
    const po_id = $(this).data("id");
    const po_no = $(this).parents("tr").find("a:first").data("id");

    Swal.fire({
      title: "Are you sure?",
      text: `You want to delete PO NO: ${po_no}!`,
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
                  // window.location.href = "201open_area_schedule.php";
                  window.location.reload();//เปลี่ยนเป็นโหลด Table Body ใหม่เพื่อไม่ให้หน้าเพจกระพริบ
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
});

$(document).ready(function () {
  // Click ที่รายการใดๆ
  $(document).on("click", ".tdMain", function (e) {
    e.preventDefault();
    $(".content-period").removeClass("d-none");
    // หรือ
    // $(".content-period").removeClass('d-none').addClass('d-flex');

    let po_id = $(this).parents("tr").attr("po-id"); //$(this).closest("tr")
    let po_no = $(this).parents("tr").find("a:first").html();
    // let po_id = $(this).closest('tr').attr('po-id');
    $(".card-title").html(po_no);

    $.ajax({
      url: "po_crud.php",
      type: "POST",
      data: {
        po_id: po_id,
        dataType: "json",
        action: "selectperiod",
      },
      success: function (response) {
        console.log(`response=${response}`);
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

$(document).ready(function () {
  let period;

  $("#btnAdd").click(function () {
    // console.log($(".firstTr:last").find(".period:last").val());
    // หา คลาส firstTr ตัวสุดท้ายที่มี class crud ซึ่ง class crud ต้องไม่มีค่าเท่ากับ 'd'
    // $(".firstTr:has(.crud:not([value='d'])):last")//แบบที่ 1
    // $(".firstTr").has(".crud:not([value='d'])").last()//แบบที่ 2
    period = $(".firstTr").has(".crud:not([value='d'])").last().find(".period").val();
    // period = $(".firstTr:has(.crud:not([value='d'])):last").find(".period").val();
    period++;
    console.log(`Period = ${period}`);
    $(".firstTr:first")
      .clone(false)
      // .removeClass("d-none")

      .find(".period")
      .val(period)
      .end()

      .find(".interim_payment")
      .val("")
      .end()

      .find(".interim_payment_percent")
      .val("")
      .end()

      .find(".remark")
      .val("")
      .end()

      .find(".po_period_id")
      .val("")
      .end()

      .find(".crud")
      .val("i")
      .end()

      // .find("a:first")
      // .css("display", "inline")
      // .css("color", "red")
      // .end()

      // .find("a:last")
      // .css("display", "inline")
      // .css("color", "red")
      // .end()

      // .find("a:first")
      // .attr("iid", "" + i + "")
      // .end()

      .appendTo("#tableBody");
  });

  $("#btnClear").click(function () {
    // ลบ tr ทั้งหมดที่ไม่ใช่ตัวแรกใน #tableBody
    // $("#tableBody tr:gt(0)").remove();
    // หรือ
    // $("#tableBody").find("tr:not(:first)").remove();
    // หรือ
    $("#tableBody").find("tr:gt(0)").remove();
  });

  $("#btnDeleteLast").click(function () {
    // ลบ tr ตัวล่างสุดที่ไม่ใช่ tr ตัวแรก ใน #tableBody
    // $("#tableBody tr:not(:first):last").remove();
    // $("#tableBody tr:not(:first)[crud!='d']:last").attr('crud','d').addClass('d-none');
    // $(".firstTr:has(.crud:not([value='d'])):last").find(".crud:not([value='d'])").first().val('d');
    // $(".firstTr:has(.crud:not([value='d'])):last").find(".crud:not([value='d'])").first().val('d');
    // $(".firstTr:has(.crud:not([value='d'])):last").css("background-color","red");
    // $(".firstTr").has(".crud:not([value='d'])").last().find(".crud").val('d');
    // $(".firstTr").has(".crud:not([value='d'])").last().addClass('d-none');

    $("#tableBody tr:not(:first):has(.crud[value!='d']):last").find(".crud").val('d');
  });

  // $(".btnDeleteThis").click(function() {
  $(document).on("click", ".btnDeleteThis", function () {
    // ส่วนสำหรับการลบ
    // let row_id = $(this).attr("iid");
    // console.log("#row" + row_id + "");
    // เมื่อปุ่มนี้ถูกกด(this)จะลบ tr ของปุ่มนี้ออกไป
    // $(this).closest("tr").remove();
    // หรือใช้
    $(this).parents("tr").remove();
  });

  $("#btnCancel").click(function () {
    // history.go(-1);
    // $('.main').load('open_area_schedule_main.php'); แบบนี้ไม่ได้
    // header('Location: main.php?page=open_area_schedule_main');แบบนี้ไม่ได้
    window.location.href = "po.php";
  });

});

$(document).ready(function(){
  let lastSelectedRow = null; // ตัวแปรเก็บแถวที่ถูกเลือกครั้งล่าสุด

  $("#runSelector").click(function(){
    console.log("--- เริ่มต้น Selector - รันใหม่ ---");

    // 1. ใช้ Selector เดิมเพื่อหาแถว "ควรจะ" เป็นตัวเลือก
    var potentialRow = $("#tableBody tr:not(:first):has(.crud[value!='d']):last");
    console.log("1. แถวที่ Selector เลือก (potential):", potentialRow);

    if (potentialRow.length > 0) { // ถ้า selector เจอแถว
      // 2. ตรวจสอบว่าเป็นแถวเดียวกับที่เคยเลือกไปแล้วหรือไม่
      if (potentialRow[0] !== lastSelectedRow) { // เปรียบเทียบ DOM element โดยตรง
        console.log("2. แถวใหม่! (ไม่ใช่แถวเดิม)");
        lastSelectedRow = potentialRow[0]; // เก็บแถวปัจจุบันไว้เป็นแถวที่เลือกครั้งล่าสุด

        // 3. ค้นหา .crud และเปลี่ยนค่า
        var crudElement = potentialRow.find(".crud");
        crudElement.val('d');
        console.log("3. กำหนดค่า 'd' ให้กับ .crud ในแถว:", potentialRow);
      } else {
        console.log("2. แถวเดิม! (ไม่ขยับขึ้นแล้ว)");
        console.log("ไม่พบแถวใหม่ที่ตรงเงื่อนไข");
      }
    } else {
      console.log("1. Selector ไม่พบแถวที่ตรงเงื่อนไขเลย");
      lastSelectedRow = null; // Reset lastSelectedRow เมื่อไม่เจอแถว
    }

    console.log("--- สิ้นสุด Selector - รันใหม่ ---");
  });
});

$(function () {
  // Event: on change
  // eventObject.on('change', function () {
  //     console.log('eventObject');
  //     let strEventId = $(this).val();
  //     $.get('get_event.php?event_id=' + strEventId, function (data) {
  //         let result = JSON.parse(data);
  //         startDateObject.val(result[0].date_start_booking);
  //         endDateObject.val(result[0].date_end_booking);

  //         $(".date_start").val(startDateObject.val());
  //         $(".date_start").attr("min", startDateObject.val());
  //         $(".date_start").attr("max", endDateObject.val());

  //         $(".date_end").val(endDateObject.val());
  //         $(".date_end").attr("min", startDateObject.val());
  //         $(".date_end").attr("max", endDateObject.val());
  //     });
  // });

  // Building: on change
  $("#building_id").on("change", function () {
    let building_id = $(this).val();
    // console.log(building_id);
    // console.log(`building_id = ${building_id}`);
    // hallObject.html('<option value="">-- เลือกพื้นที่ --</option>');
    $("#hall_id").html('<option value="">...</option>');
    $("#time_start_header").val("");
    $("#time_end_header").val("");
    $("#total_slots").val("");
    // $('#reservable_slots').val("");

    $.get("get_hall.php", { building_id: building_id }, function (data) {
      let result = JSON.parse(data);
      $.each(result, function (index, item) {
        $("#hall_id").append(
          $("<option></option>").val(item.hall_id).html(item.hall_name)
        );
      });
    });
  });

  // Hall: on change
  $("#hall_id").on("change", function () {
    let building_id = $("#building_id").val();
    let hall_id = $(this).val();
    var d = new Date();
    var day = d.getDate();

    $.get(
      "get_hall_time.php",
      { building_id: building_id, hall_id: hall_id },
      function (data) {
        let result = JSON.parse(data);
        // console.log(data);
        // console.log(result[4]);
        // $(".date_start_header").text(Date.now());
        // // $(".date_start").attr("min", startDateObject.val());
        // // $(".date_start").attr("max", endDateObject.val());

        // $(".date_end_header").text(Date.now());
        // // $(".date_end").attr("min", startDateObject.val());
        // // $(".date_end").attr("max", endDateObject.val());
        $("#total_slots").val(result[4]);
        $("#time_start_header").val(result[5]);
        $("#time_end_header").val(result[6]);
        // $('#reservable_slots').val(result[0].reservable_slots);

        $(".time_start").timepicker({
          // minTime: $("#time_start_header").val(),
          // maxTime: $("#time_end_header").val(),
          minTime: "00:00",
          maxTime: "23:30",
          timeFormat: "H:i",
          show2400: true,
          step: 30,
          closeOnScroll: true,
          scrollDefault: $("#time_start_header").val(),
          // orientation: 'c',
          listWidth: 1,
          disableTextInput: true,
        });
        $(".time_end").timepicker({
          // minTime: $("#time_start_header").val(),
          // maxTime: $("#time_end_header").val(),
          minTime: "00:00",
          maxTime: "23:30",
          timeFormat: "H:i",
          show2400: true,
          step: 30,
          closeOnScroll: true,
          scrollDefault: $("#time_end_header").val(),
          // orientation: 'c',
          listWidth: 1,
          disableTextInput: true,
        });

        // $(".time_start").attr("min", $('#time_start_header').val());
        // $(".time_start").attr("max", $('#time_end_header').val());
        $(".time_start").val($("#time_start_header").val());

        // $(".time_end").attr("min", $('#time_start_header').val());
        // $(".time_end").attr("max", $('#time_end_header').val());
        $(".time_end").val($("#time_end_header").val());

        $(".reservable_slots").attr("min", 0);
        $(".reservable_slots").attr("max", $("#total_slots").val());
        $(".reservable_slots").val($("#total_slots").val());
      }
    );
  });
});
