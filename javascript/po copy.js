$(document).ready(function () {
  const $tableMainTbody = $("#tableMain #tbody");
  const $tbodyPeriod = $("#tbody-period");
  const $cardTitle = $(".card-title");
  const $contentPeriod = $(".content-period");

  // ฟังก์ชันสำหรับแสดงข้อความแจ้งเตือน SweetAlert2
  function showAlert(icon, title, text, confirmButtonText = "OK") {
    return Swal.fire({
      icon: icon,
      title: title,
      text: text,
      background: "black",
      color: "#fff", // ปรับสีข้อความให้เข้ากับพื้นหลัง
      confirmButtonColor: "#3085d6",
      confirmButtonText: confirmButtonText,
    });
  }

  // Event handler สำหรับปุ่ม Delete
  $tableMainTbody.on("click", ".btnDelete", function (e) {
    e.preventDefault();
    const $tr = $(this).closest("tr"); // ใช้ closest() เพื่อหา tr ที่ใกล้ที่สุด
    const poId = $tr.data("id");
    const poNumber = $tr.find("a.po_number").data("id"); // เลือก a ที่มี class po_number

    showAlert(
      "warning",
      "Are you sure?",
      `You want to delete PO NO: ${poNumber}!`,
      "Yes, delete it!"
    ).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: "po_crud.php",
          method: "POST",
          dataType: "json", // คาดหวัง Response เป็น JSON
          data: { action: "delete", po_id: poId },
          success: function (response) {
            if (response.status === "success") {
              showAlert("success", "Deleted!", response.message || "Your data has been deleted.").then(() => {
                $tr.fadeOut("fast", function () { // เพิ่ม Animation Fade Out
                  $(this).remove();
                  $contentPeriod.addClass("d-none"); // ซ่อนส่วนงวดงานเมื่อลบ PO หลัก
                });
              });
            } else {
              showAlert("error", "Oops...!", response.message || "Something went wrong!");
            }
          },
          error: function (xhr, status, error) {
            console.error("AJAX Error:", status, error);
            showAlert("error", "Error!", "Failed to delete data. Please try again.");
          },
        });
      }
    });
  });

  // Event handler สำหรับปุ่ม Edit
  $tableMainTbody.on("click", ".btnEdit, a.po_number", function (e) {
    e.preventDefault();
    const poId = $(this).closest("tr").data("id");
    window.location.href = `po_dml.php?action=update&po_id=${poId}`; // ใช้ Template Literal
  });

  // Event handler สำหรับคลิกที่ Row เพื่อแสดงงวดงาน
  $tableMainTbody.on("click", "tr:not(:has(a), :has(.action))", function (e) {
    e.preventDefault();
    $contentPeriod.removeClass("d-none");

    const $tr = $(this);
    const poId = $tr.data("id");
    const poNumber = $tr.find("a.po_number").data("id");
    $cardTitle.html(poNumber);

    $.ajax({
      url: "po_crud.php",
      method: "POST",
      dataType: "json", // คาดหวัง Response เป็น JSON
      data: {
        action: "selectperiod",
        po_id: poId,
      },
      success: function (response) {
        if (response.status === "success") {
          $tbodyPeriod.html(response.data); // คาดหวังข้อมูลงวดงานใน response.data
        } else {
          $tbodyPeriod.html(`<tr><td colspan="100%" class="text-center">${response.message || "No period data found."}</td></tr>`); // แสดงข้อความเมื่อไม่มีข้อมูล
        }
      },
      error: function (xhr, status, error) {
        console.error("AJAX Error:", status, error);
        $tbodyPeriod.html(`<tr><td colspan="100%" class="text-center">Error loading period data.</td></tr>`);
      },
    });
  });

  // ฟังก์ชันสำหรับโหลดข้อมูลเริ่มต้น
  function loadAllPurchaseOrder() {
    console.log("Start loading PO data...");
    $.ajax({
      url: "po_crud.php",
      method: "POST",
      dataType: "json", // คาดหวัง Response เป็น JSON
      data: {
        action: "select",
      },
      success: function (response) {
        console.log("PO data loaded successfully:", response);
        if (response.status === "success") {
          $tableMainTbody.html(response.data); // คาดหวังข้อมูลตาราง PO ใน response.data
        } else {
          $tableMainTbody.html(`<tr><td colspan="100%" class="text-center">${response.message || "No purchase order data found."}</td></tr>`); // แสดงข้อความเมื่อไม่มีข้อมูล
        }
      },
      error: function (xhr, status, error) {
        console.error("Error loading PO data:", status, error);
        $tableMainTbody.html(`<tr><td colspan="100%" class="text-center">Error loading purchase order data.</td></tr>`);
      },
    });
  }

  // โหลดข้อมูล PO เริ่มต้นเมื่อ Document พร้อมใช้งาน
  loadAllPurchaseOrder();
});