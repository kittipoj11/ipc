// app_order_logic.js
$(function () {
  const API_URL = "api_order_handler.php";
  const poIdToLoad = 1001; // ★★★ ID ของ PO ที่ต้องการโหลดมาแก้ไข ★★★

  const itemsTbody = $("#items-tbody");
  const periodsTbody = $("#periods-tbody");
  const responseMessage = $("#response-message");

  // ฟังก์ชันแสดงข้อความตอบกลับ
  function showMessage(message, isSuccess) {
    responseMessage
      .text(message)
      .removeClass("success error")
      .addClass(isSuccess ? "success" : "error")
      .show()
      .delay(5000)
      .fadeOut();
  }

  // โหลดข้อมูลทั้งหมดจาก Server
  function loadData(poId) {
    showMessage("กำลังโหลดข้อมูล...", true);
    $.ajax({
      url: `${API_URL}?po_id=${poId}`,
      type: "GET",
      dataType: "json",
    })
      .done((result) => {
        if (result.status !== "success") {
          showMessage(result.message, false);
          return;
        }
        const { header, items, periods } = result.data;

        // 1. แสดงผล Header
        $("#display-po-id").text(header.po_id);
        $("#po_id").val(header.po_id);
        // ... (ใส่โค้ด populate header input fields ที่เหลือ) ...

        // 2. แสดงผล Items
        itemsTbody.empty();
        if (items.length > 0)
          items.forEach((item) => appendRow(itemsTbody, "item", item));

        // 3. แสดงผล Periods
        periodsTbody.empty();
        if (periods.length > 0)
          periods.forEach((period) =>
            appendRow(periodsTbody, "period", period)
          );

        responseMessage.hide();
      })
      .fail(() => showMessage("เกิดข้อผิดพลาดในการโหลดข้อมูล", false));
  }

  // ฟังก์ชันกลางสำหรับสร้างแถว
  function appendRow(tbody, type, data = {}) {
    const id = data[type + "_id"] || "";
    const isNew = id === "";
    let rowHtml = `<tr data-${type}-id="${id}" data-crud-status="${
      isNew ? "create" : "clean"
    }">`;
    if (type === "item") {
      rowHtml += `
                <td><input type="text" name="product_name" value="${
                  data.product_name || ""
                }"></td>
                <td><input type="number" name="quantity" value="${
                  data.quantity || ""
                }" step="0.01"></td>
                <td><input type="number" name="unit_price" value="${
                  data.unit_price || ""
                }" step="0.01"></td>
            `;
    } else {
      // type === 'period'
      rowHtml += `
                <td><input type="number" name="period_no" value="${
                  data.period_no || ""
                }" class="period-no-input" readonly></td>
                <td><input type="number" name="work_percent" value="${
                  data.work_percent || ""
                }" step="0.01"></td>
                <td><input type="number" name="interim_payments" value="${
                  data.interim_payments || ""
                }" step="0.01"></td>
                <td><input type="text" name="remarks" value="${
                  data.remarks || ""
                }"></td>
            `;
    }
    rowHtml += `<td><button type="button" class="btn-delete delete-btn">ลบ</button></td></tr>`;
    tbody.append(rowHtml);
    if (type === "period") updatePeriodNumbers();
  }

  function updatePeriodNumbers() {
    periodsTbody.find("tr:visible").each((i, el) =>
      $(el)
        .find(".period-no-input")
        .val(i + 1)
    );
  }

  // --- Event Handlers ---
  $("#add-item-btn").on("click", () => appendRow(itemsTbody, "item"));
  $("#add-period-btn").on("click", () => appendRow(periodsTbody, "period"));

  $("table").on("click", ".delete-btn", function () {
    const row = $(this).closest("tr");
    if (!confirm("คุณต้องการลบรายการนี้ใช่หรือไม่?")) return;
    if (row.data("crud-status") === "create") row.remove();
    else row.hide().data("crud-status", "delete");
    updatePeriodNumbers();
  });

  $("table").on("input", "input", function () {
    const row = $(this).closest("tr");
    if (row.data("crud-status") === "clean") row.data("crud-status", "update");
  });

  // --- Form Submit ---
  $("#order-form").on("submit", function (e) {
    e.preventDefault();

    const headerData = {
      /* ... รวบรวมข้อมูล header ... */
    };

    const collectDetails = (tbody) => {
      const data = [];
      tbody.find("tr").each(function () {
        const row = $(this);
        const status = row.data("crud-status");
        if (status === "clean") return; // ข้ามแถวที่ไม่เปลี่ยนแปลง
        const record = { crud_status: status };
        row.find("input").each(function () {
          const input = $(this);
          record[input.attr("name")] = input.val();
        });
        record[
          row.closest("table").find("tbody").attr("id").split("-")[0] + "_id"
        ] = row.data(
          row.closest("table").find("tbody").attr("id").split("-")[0] + "-id"
        );
        data.push(record);
      });
      return data;
    };

    const payload = {
      header: headerData,
      items: collectDetails(itemsTbody),
      periods: collectDetails(periodsTbody),
    };

    showMessage("กำลังบันทึกข้อมูล...", true);

    $.ajax({
      url: API_URL,
      type: "POST",
      contentType: "application/json",
      data: JSON.stringify(payload),
    })
      .done((result) => {
        showMessage(result.message, result.status === "success");
        if (result.status === "success") loadData(result.data.po_id);
      })
      .fail((jqXHR) =>
        showMessage(
          jqXHR.responseJSON?.message || "เกิดข้อผิดพลาดรุนแรง",
          false
        )
      );
  });

  loadData(poIdToLoad); // โหลดข้อมูลครั้งแรก
});
