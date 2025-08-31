$(document).ready(function () {
  // ฟังก์ชันสำหรับแสดงข้อความ Alert
  function showAlert(message, type = "success") {
    const alertClass = type === "success" ? "alert-success" : "alert-danger";
    const alertHtml = `<div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                              ${message}
                              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                           </div>`;
    $("#message-container").html(alertHtml);
    $("html, body").animate({ scrollTop: 0 }, "slow");
  }

  // Reset fields
  function resetFields(fields) {
    fields.forEach((field) => {
      $(field.id)
        .html(
          `<option selected disabled value="">${field.defaultText}</option>`
        )
        .prop("disabled", true);
    });
    $("#num_cars").prop("disabled", true).val("");
    $("#availability-info").hide();
  }

  // 1. โหลด Events ที่ยังไม่หมดอายุ
  $.ajax({
    url: "api.php?action=get_active_events",
    type: "GET",
    dataType: "json",
    success: function (response) {
      if (response.status === "success") {
        const select = $("#event_id");
        response.data.forEach((event) => {
          select.append(`<option value="${event.id}">${event.name}</option>`);
        });
      }
    },
  });

  // 2. เมื่อเลือก Event -> โหลดลานจอด และ วันที่
  $("#event_id").on("change", function () {
    const eventId = $(this).val();
    resetFields([
      { id: "#parking_lot", defaultText: "-- กรุณาเลือก --" },
      { id: "#booking_date", defaultText: "-- กรุณาเลือก --" },
      { id: "#start_time", defaultText: "-- กรุณาเลือกวันที่ก่อน --" },
    ]);

    if (!eventId) return;

    $.ajax({
      url: `api.php?action=get_event_details&event_id=${eventId}`,
      type: "GET",
      dataType: "json",
      success: function (response) {
        if (response.status === "success") {
          // Populate parking lots
          const lotSelect = $("#parking_lot");
          lotSelect.prop("disabled", false);
          response.data.lots.forEach((lot) => {
            lotSelect.append(`<option value="${lot}">${lot}</option>`);
          });

          // Populate dates
          const dateSelect = $("#booking_date");
          dateSelect.prop("disabled", false);
          response.data.dates.forEach((date) => {
            dateSelect.append(`<option value="${date}">${date}</option>`);
          });
        }
      },
    });
  });

  // 3. เมื่อเลือกวันที่ -> โหลดเวลา
  $("#booking_date").on("change", function () {
    const eventId = $("#event_id").val();
    const selectedDate = $(this).val();
    resetFields([{ id: "#start_time", defaultText: "-- กรุณาเลือก --" }]);

    if (!eventId || !selectedDate) return;

    $.ajax({
      url: `api.php?action=get_time_slots&event_id=${eventId}&date=${selectedDate}`,
      type: "GET",
      dataType: "json",
      success: function (response) {
        if (response.status === "success") {
          const timeSelect = $("#start_time");
          timeSelect.prop("disabled", false);
          response.data.forEach((slot) => {
            timeSelect.append(
              `<option value="${slot.start_time}">${slot.start_time.substring(
                0,
                5
              )} - ${slot.end_time.substring(0, 5)}</option>`
            );
          });
        }
      },
    });
  });

  // 4. เมื่อเลือกลานจอด หรือ เวลา -> เช็คจำนวนที่ว่าง
  function checkAvailability() {
    const eventId = $("#event_id").val();
    const lot = $("#parking_lot").val();
    const date = $("#booking_date").val();
    const time = $("#start_time").val();

    $("#availability-info").hide();
    $("#num_cars").prop("disabled", true).val("");

    if (!eventId || !lot || !date || !time) return;

    $.ajax({
      url: `api.php?action=get_availability&event_id=${eventId}&lot=${lot}&date=${date}&time=${time}`,
      type: "GET",
      dataType: "json",
      success: function (response) {
        if (response.status === "success") {
          const available = response.data.available;
          if (available > 0) {
            $("#availability-info")
              .html(`<strong>จำนวนที่จอดว่าง: ${available} คัน</strong>`)
              .show();
            $("#num_cars").prop("disabled", false);
          } else {
            $("#availability-info")
              .html(`<strong>ช่วงเวลานี้เต็มแล้ว</strong>`)
              .addClass("alert-danger")
              .removeClass("alert-info")
              .show();
          }
        }
      },
    });
  }

  $("#parking_lot, #start_time").on("change", checkAvailability);

  // 5. Submit form การจอง
  $("#booking-form").on("submit", function (e) {
    e.preventDefault();
    const formData = $(this).serialize();

    $.ajax({
      url: "api.php",
      type: "POST",
      data: formData,
      dataType: "json",
      success: function (response) {
        if (response.status === "success") {
          showAlert(response.message, "success");
          $("#booking-form")[0].reset();
          resetFields([
            { id: "#parking_lot", defaultText: "-- กรุณาเลือกงานก่อน --" },
            { id: "#booking_date", defaultText: "-- กรุณาเลือกงานก่อน --" },
            { id: "#start_time", defaultText: "-- กรุณาเลือกวันที่ก่อน --" },
          ]);
        } else {
          showAlert(response.message, "danger");
        }
      },
      error: function () {
        showAlert("เกิดข้อผิดพลาดในการเชื่อมต่อ", "danger");
      },
    });
  });
});
