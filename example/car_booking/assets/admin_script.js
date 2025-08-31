$(document).ready(function () {
  // ฟังก์ชันสำหรับแสดงข้อความ Alert
  function showAlert(message, type = "success") {
    const alertClass = type === "success" ? "alert-success" : "alert-danger";
    const alertHtml = `<div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                              ${message}
                              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                           </div>`;
    $("#message-container").html(alertHtml);
  }

  // โหลด Event ทั้งหมดมาใส่ใน Dropdown
  function loadEvents() {
    $.ajax({
      url: "api.php",
      type: "GET",
      data: { action: "get_active_events" },
      dataType: "json",
      success: function (response) {
        if (response.status === "success") {
          const select = $("#event_id");
          select.html('<option value="">-- กรุณาเลือกงาน --</option>');
          response.data.forEach((event) => {
            select.append(`<option value="${event.id}">${event.name}</option>`);
          });
        }
      },
    });
  }

  // เมื่อฟอร์มสร้าง Event ถูก Submit
  $("#create-event-form").on("submit", function (e) {
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
          $("#create-event-form")[0].reset();
          loadEvents(); // โหลด event ใหม่หลังสร้างสำเร็จ
        } else {
          showAlert(response.message, "danger");
        }
      },
      error: function () {
        showAlert("เกิดข้อผิดพลาดในการเชื่อมต่อกับ Server", "danger");
      },
    });
  });

  // เมื่อฟอร์มเพิ่ม Slot ถูก Submit
  $("#add-slot-form").on("submit", function (e) {
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
          $("#add-slot-form")[0].reset();
        } else {
          showAlert(response.message, "danger");
        }
      },
      error: function () {
        showAlert("เกิดข้อผิดพลาดในการเชื่อมต่อกับ Server", "danger");
      },
    });
  });

  // เริ่มโหลด Event เมื่อหน้าเว็บพร้อมใช้งาน
  loadEvents();
});
