$(document).ready(function () {
    loadBookings();

    let currentViewData = null; // เก็บข้อมูล booking ล่าสุดที่กด View

// View Booking (Read Only)
$(document).on("click", ".viewBtn", function () {
    let item = $(this).data("id");
    currentViewData = item; // เก็บไว้ใช้ export pdf

    // Header Info
    let headerHtml = `
      <li><b>ID:</b> ${item.id}</li>
      <li><b>ชื่อผู้จอง:</b> ${item.booking_name}</li>
      <li><b>Email:</b> ${item.email || "-"}</li>
      <li><b>Phone:</b> ${item.phone || "-"}</li>
      <li><b>Booth:</b> ${item.booth}</li>
      <li><b>Reservation ID:</b> ${item.reservation_id}</li>
    `;
    $("#viewHeader").html(headerHtml);

    // Details
    let detailsHtml = "";
    item.details.forEach(d => {
        detailsHtml += `
          <tr>
            <td>${d.booking_date}</td>
            <td>${d.booking_time_start}</td>
            <td>${d.booking_time_end}</td>
            <td>${d.car_license_number}</td>
            <td>${d.car_type_id}</td>
            <td>${d.driver_name || "-"}</td>
            <td>${d.driver_mobile || "-"}</td>
          </tr>`;
    });
    $("#viewDetails").html(detailsHtml);

    // Show Modal
    let modal = new bootstrap.Modal(document.getElementById("viewModal"));
    modal.show();
});

// Export PDF
$("#exportPdfBtn").on("click", function () {
    if (!currentViewData) return;
    $.ajax({
        url: "export_pdf.php",
        type: "POST",
        data: { booking: JSON.stringify(currentViewData) },
        xhrFields: { responseType: 'blob' }, // รับไฟล์ pdf
        success: function (blob) {
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement("a");
            a.href = url;
            a.download = `booking_${currentViewData.id}.pdf`;
            document.body.appendChild(a);
            a.click();
            a.remove();
            window.URL.revokeObjectURL(url);
        }
    });
});


    function loadBookings() {
        $.get("api.php?action=fetch", function (res) {
            let data = JSON.parse(res);
            let rows = "";
            data.forEach(item => {
                rows += `
                  <tr>
                    <td>${item.id}</td>
                    <td>${item.booking_name}</td>
                    <td>${item.email || ""}</td>
                    <td>${item.phone || ""}</td>
                    <td>${item.booth}</td>
                    <td>${item.reservation_id}</td>
                    <td>
                      <button class="btn btn-sm btn-primary viewBtn" data-id='${JSON.stringify(item)}'>👁 View</button>
                      <button class="btn btn-sm btn-info editBtn" data-id='${JSON.stringify(item)}'>✏ Edit</button>
                      <button class="btn btn-sm btn-danger deleteBtn" data-id="${item.id}">🗑 Delete</button>
                    </td>
                  </tr>`;
            });
            $("#bookingTable tbody").html(rows);
        });
    }

    // Add Detail Row
    $("#addDetail").on("click", function () {
        let row = `
          <tr>
            <td><input type="date" class="form-control" name="booking_date"></td>
            <td><input type="time" class="form-control" name="booking_time_start"></td>
            <td><input type="time" class="form-control" name="booking_time_end"></td>
            <td><input type="text" class="form-control" name="car_license_number"></td>
            <td><input type="text" class="form-control" name="car_type_id"></td>
            <td><input type="text" class="form-control" name="driver_name"></td>
            <td><input type="text" class="form-control" name="driver_mobile"></td>
            <td><button type="button" class="btn btn-sm btn-danger removeDetail">❌</button></td>
          </tr>`;
        $("#detailsTable tbody").append(row);
    });

    // Remove Detail Row
    $(document).on("click", ".removeDetail", function () {
        $(this).closest("tr").remove();
    });

    // Save Header + Details
    $("#bookingForm").on("submit", function (e) {
        e.preventDefault();

        let details = [];
        $("#detailsTable tbody tr").each(function () {
            let d = {
                booking_date: $(this).find("input[name=booking_date]").val(),
                booking_time_start: $(this).find("input[name=booking_time_start]").val(),
                booking_time_end: $(this).find("input[name=booking_time_end]").val(),
                car_license_number: $(this).find("input[name=car_license_number]").val(),
                car_type_id: $(this).find("input[name=car_type_id]").val(),
                driver_name: $(this).find("input[name=driver_name]").val(),
                driver_mobile: $(this).find("input[name=driver_mobile]").val()
            };
            details.push(d);
        });

        let formData = $(this).serializeArray();
        let data = {};
        formData.forEach(f => data[f.name] = f.value);
        data['details'] = JSON.stringify(details);

        $.post("api.php?action=save", data, function (res) {
            let r = JSON.parse(res);
            if (r.status == "success") {
                alert("บันทึกสำเร็จ");
                $("#bookingForm")[0].reset();
                $("#detailsTable tbody").empty();
                loadBookings();
            }
        });
    });

    // Edit Booking
    $(document).on("click", ".editBtn", function () {
        let item = $(this).data("id");
        $("#booking_id").val(item.id);
        $("input[name=booking_name]").val(item.booking_name);
        $("input[name=email]").val(item.email);
        $("input[name=phone]").val(item.phone);
        $("input[name=booth]").val(item.booth);
        $("input[name=reservation_id]").val(item.reservation_id);

        $("#detailsTable tbody").empty();
        item.details.forEach(d => {
            let row = `
              <tr>
                <td><input type="date" class="form-control" name="booking_date" value="${d.booking_date}"></td>
                <td><input type="time" class="form-control" name="booking_time_start" value="${d.booking_time_start}"></td>
                <td><input type="time" class="form-control" name="booking_time_end" value="${d.booking_time_end}"></td>
                <td><input type="text" class="form-control" name="car_license_number" value="${d.car_license_number}"></td>
                <td><input type="text" class="form-control" name="car_type_id" value="${d.car_type_id}"></td>
                <td><input type="text" class="form-control" name="driver_name" value="${d.driver_name}"></td>
                <td><input type="text" class="form-control" name="driver_mobile" value="${d.driver_mobile}"></td>
                <td><button type="button" class="btn btn-sm btn-danger removeDetail">❌</button></td>
              </tr>`;
            $("#detailsTable tbody").append(row);
        });
    });

    // View Booking
    $(document).on("click", ".viewBtn", function () {
        let item = $(this).data("id");

        // Header Info
        let headerHtml = `
          <li><b>ID:</b> ${item.id}</li>
          <li><b>ชื่อผู้จอง:</b> ${item.booking_name}</li>
          <li><b>Email:</b> ${item.email || "-"}</li>
          <li><b>Phone:</b> ${item.phone || "-"}</li>
          <li><b>Booth:</b> ${item.booth}</li>
          <li><b>Reservation ID:</b> ${item.reservation_id}</li>
        `;
        $("#viewHeader").html(headerHtml);

        // Details
        let detailsHtml = "";
        item.details.forEach(d => {
            detailsHtml += `
              <tr>
                <td>${d.booking_date}</td>
                <td>${d.booking_time_start}</td>
                <td>${d.booking_time_end}</td>
                <td>${d.car_license_number}</td>
                <td>${d.car_type_id}</td>
                <td>${d.driver_name || "-"}</td>
                <td>${d.driver_mobile || "-"}</td>
              </tr>`;
        });
        $("#viewDetails").html(detailsHtml);

        // Show Modal
        let modal = new bootstrap.Modal(document.getElementById("viewModal"));
        modal.show();
    });

    // Delete Booking
    $(document).on("click", ".deleteBtn", function () {
        if (!confirm("ยืนยันการลบ?")) return;
        let id = $(this).data("id");
        $.post("api.php?action=delete", { id }, function (res) {
            let r = JSON.parse(res);
            if (r.status == "deleted") {
                loadBookings();
            }
        });
    });
});
