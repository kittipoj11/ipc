$(document).ready(function () {
    loadBookings();

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
                      <button class="btn btn-sm btn-info editBtn" data-id='${JSON.stringify(item)}'>✏ Edit</button>
                      <button class="btn btn-sm btn-danger deleteBtn" data-id="${item.id}">🗑 Delete</button>
                    </td>
                  </tr>`;
            });
            $("#bookingTable tbody").html(rows);
        });
    }

    // เพิ่ม Detail Row
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

    // ลบ Detail Row
    $(document).on("click", ".removeDetail", function () {
        $(this).closest("tr").remove();
    });

    // บันทึก Header + Details
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
