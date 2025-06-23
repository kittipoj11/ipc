// app_periods_jquery.js

$(function () {
  const API_URL = "api_period_handler.php";
  const orderId = 1; // สมมติว่าเรากำลังจัดการ order ID 1
  const tbody = $("#periods-tbody");
  const responseMessage = $("#response-message");

  // --- ฟังก์ชันสำหรับโหลดข้อมูลเริ่มต้น ---
  function loadPeriods() {
    responseMessage.text("");
    tbody.html('<tr><td colspan="5">กำลังโหลดข้อมูล...</td></tr>');

    $.ajax({
      url: API_URL + "?order_id=" + orderId,
      type: "GET",
      dataType: "json",
    })
      .done(function (result) {
        tbody.empty(); // ล้างข้อมูลเก่า
        if (result.status === "success" && result.data.length > 0) {
          result.data.forEach(function (period) {
            appendPeriodRow(period);
          });
        } else {
          tbody.html('<tr><td colspan="5">ไม่พบข้อมูลงวดงาน</td></tr>');
        }
      })
      .fail(function () {
        tbody.html(
          '<tr><td colspan="5">เกิดข้อผิดพลาดในการโหลดข้อมูล</td></tr>'
        );
      });
  }

  // --- ฟังก์ชันสำหรับสร้างแถวในตาราง ---
  function appendPeriodRow(periodData = {}) {
    const periodId = periodData.period_id || "";
    const isNew = periodId === "";
    const nextPeriodNo = isNew
      ? tbody.find("tr").length + 1
      : periodData.period_no;

    const row = `
            <tr data-period-id="${periodId}" data-crud-status="${
      isNew ? "create" : "clean"
    }">
                <td><input type="number" name="period_no" value="${nextPeriodNo}" readonly></td>
                <td><input type="number" name="work_percent" value="${
                  periodData.work_percent || ""
                }"></td>
                <td><input type="number" name="interim_payments" value="${
                  periodData.interim_payments || ""
                }"></td>
                <td><input type="text" name="remarks" value="${
                  periodData.remarks || ""
                }"></td>
                <td class="action-cell"><button type="button" class="delete-btn">ลบ</button></td>
            </tr>
        `;
    tbody.append(row);
  }

  // --- จัดการ Event ต่างๆ ---
  $("#add-row-btn").on("click", function () {
    appendPeriodRow();
  });

  // ใช้ Event Delegation สำหรับปุ่มลบและ input ที่ถูกเพิ่มเข้ามาทีหลัง
  tbody.on("click", ".delete-btn", function () {
    const row = $(this).closest("tr");
    if (confirm("คุณต้องการลบงวดงานนี้ใช่หรือไม่?")) {
      // ถ้าเป็นแถวที่ยังไม่เคยบันทึก ให้ลบออกจากหน้าจอเลย
      if (row.data("crud-status") === "create") {
        row.remove();
      } else {
        // ถ้าเป็นแถวที่มีข้อมูลอยู่แล้ว ให้ซ่อนและเปลี่ยนสถานะเป็น 'delete'
        row.hide().data("crud-status", "delete");
      }
    }
  });

  /*
โค้ดส่วนนี้ทำหน้าที่สำคัญอย่างหนึ่งคือ: "คอยติดตามการเปลี่ยนแปลงข้อมูลในแต่ละแถวโดยอัตโนมัติ"

เมื่อใดก็ตามที่ผู้ใช้ พิมพ์หรือแก้ไขข้อมูล ในช่อง <input> ใดๆ ในตาราง โค้ดนี้จะทำงานทันทีเพื่อตรวจสอบสถานะของ "แถว" (<tr>) นั้นๆ 
และถ้าแถวนั้นเป็นข้อมูลเก่าที่ยังไม่เคยถูกแก้ไขมาก่อน (มีสถานะเป็น clean) มันจะเปลี่ยนสถานะของแถวนั้นให้เป็น update ทันที

เป้าหมาย: เพื่อให้ตอนที่เรากดปุ่ม "บันทึกข้อมูลทั้งหมด" เราจะรู้ได้ว่าแถวไหนบ้างที่ถูกผู้ใช้แก้ไข และจำเป็นต้องส่งไปให้เซิร์ฟเวอร์ทำการ UPDATE ข้อมูล 
ซึ่งช่วยให้เราส่งเฉพาะข้อมูลที่มีการเปลี่ยนแปลงจริงๆ ไปเท่านั้น ทำให้ระบบมีประสิทธิภาพมากขึ้น
*/
  tbody.on("input", "input", function () {
    const row = $(this).closest("tr");
    // ถ้าแถวไม่ใช่แถวใหม่ (สถานะเป็น clean) ให้เปลี่ยนเป็น update
    if (row.data("crud-status") === "clean") {
      row.data("crud-status", "update");
    }
  });

  // --- จัดการการ Submit ฟอร์ม ---
  $("#periods-form").on("submit", function (event) {
    event.preventDefault();

    const dataToSend = [];
    tbody.find("tr").each(function () {
      const row = $(this);
      const status = row.data("crud-status");

      // เราจะส่งข้อมูลเฉพาะแถวที่มีการเปลี่ยนแปลง (create, update, delete)
      if (status !== "clean") {
        dataToSend.push({
          period_id: row.data("period-id"),
          crud_status: status, // ใช้ชื่อนี้เพื่อให้ PHP รู้ว่าต้องทำอะไร
          period_no: row.find('input[name="period_no"]').val(),
          work_percent: row.find('input[name="work_percent"]').val(),
          interim_payments: row.find('input[name="interim_payments"]').val(),
          remarks: row.find('input[name="remarks"]').val(),
        });
      }
    });

    if (dataToSend.length === 0) {
      alert("ไม่มีการเปลี่ยนแปลงข้อมูลที่จะบันทึก");
      return;
    }

    console.log("Data sending to server:", dataToSend);

    // ส่ง AJAX
    $.ajax({
      url: API_URL,
      type: "POST",
      contentType: "application/json",
      dataType: "json",
      data: JSON.stringify(dataToSend),
    })
      .done(function (result) {
        responseMessage.text(result.message).css("color", "green");
        // โหลดข้อมูลใหม่ทั้งหมดหลังบันทึกสำเร็จ
        loadPeriods();
      })
      .fail(function (jqXHR) {
        const errorMsg = jqXHR.responseJSON
          ? jqXHR.responseJSON.message
          : "เกิดข้อผิดพลาดในการบันทึกข้อมูล";
        responseMessage.text(errorMsg).css("color", "red");
      });
  });

  // --- โหลดข้อมูลครั้งแรกเมื่อเปิดหน้า ---
  loadPeriods();
});
