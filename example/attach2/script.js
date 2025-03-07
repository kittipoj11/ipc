$(document).ready(function() {
    // ฟังก์ชันสำหรับอัปโหลดไฟล์
    $("#uploadForm").on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);

        $.ajax({
            url: 'upload.php',
            type: 'POST',
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $("#uploadStatus").html('<div class="alert alert-success">' + response.message + '</div>');
                    $("#uploadForm")[0].reset(); // ล้างฟอร์ม
                    displayRecords(); // รีเฟรชรายการ records
                } else {
                    $("#uploadStatus").html('<div class="alert alert-danger">' + response.message + '</div>');
                }
            },
            error: function() {
                $("#uploadStatus").html('<div class="alert alert-danger">เกิดข้อผิดพลาดในการอัปโหลด</div>');
            }
        });
    });

    // ฟังก์ชันสำหรับแสดง records และไฟล์ (แก้ไข)
    function displayRecords() {
        $.ajax({
            url: 'display.php',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    var records = response.data;
                    var html = '<ul class="list-group">';
                    if (records.length > 0) {
                        $.each(records, function(index, record) {
                            html += '<li class="list-group-item">';
                            html += '<h5>' + record.record_name + ' <button class="btn btn-danger btn-sm float-right deleteRecord" data-id="' + record.record_id + '">Delete Record</button></h5>';
                            if (record.files.length > 0) {
                                html += '<h6>Files:</h6>';
                                $.each(record.files, function(fileIndex, file) {
                                    var fileUrl = file.file_path;
                                    var fileName = file.file_name;
                                    var fileType = file.file_type;
                                    var linkText = fileName;

                                    if (fileType.startsWith('image/')) {
                                        // แสดงรูปภาพโดยตรง
                                        html += '<div class="file-display">';
                                        html += '<img src="' + fileUrl + '" alt="' + fileName + '" style="max-width: 200px; max-height: 200px;">'; // ปรับขนาดรูปภาพตามต้องการ
                                        html += '<a href="' + fileUrl + '" target="_blank" class="file-link">' + fileName + ' (Open in new tab)</a>'; // เพิ่มลิงก์สำหรับเปิดแท็บใหม่
                                        html += '</div>';
                                    } else if (fileType === 'application/pdf') {
                                        // แสดงไฟล์ PDF โดยตรง (ใช้ <embed>)
                                        html += '<div class="file-display">';
                                        html += '<embed src="' + fileUrl + '#toolbar=0&navpanes=0&scrollbar=0" type="application/pdf" width="100%" height="500px">'; // ปรับความสูงตามต้องการ และซ่อน toolbar, navpanes, scrollbar
                                        html += '<a href="' + fileUrl + '" target="_blank" class="file-link">PDF File: ' + fileName + ' (Open in new tab)</a>'; // เพิ่มลิงก์สำหรับเปิดแท็บใหม่
                                        html += '</div>';
                                    } else {
                                        html += '<span class="file-link">Unknown File: ' + fileName + '</span>';
                                    }
                                });
                            } else {
                                html += '<p>No files uploaded for this record.</p>';
                            }
                            html += '</li>';
                        });
                    } else {
                        html += '<li class="list-group-item">No records found.</li>';
                    }
                    html += '</ul>';
                    $("#recordsDisplay").html(html);
                } else {
                    $("#recordsDisplay").html('<div class="alert alert-danger">' + response.message + '</div>');
                }
            },
            error: function() {
                $("#recordsDisplay").html('<div class="alert alert-danger">Failed to display records.</div>');
            }
        });
    }

    // ฟังก์ชันสำหรับลบ record
    $(document).on('click', '.deleteRecord', function() {
        var recordId = $(this).data('id');
        if (confirm("Are you sure you want to delete this record and all associated files?")) {
            $.ajax({
                url: 'delete.php',
                type: 'POST',
                dataType: 'json',
                data: { record_id: recordId },
                success: function(response) {
                    if (response.status === 'success') {
                        alert(response.message);
                        displayRecords(); // รีเฟรชรายการ records
                    } else {
                        alert('Error deleting record: ' + response.message);
                    }
                },
                error: function() {
                    alert('Failed to delete record.');
                }
            });
        }
    });

    // เรียกฟังก์ชัน displayRecords เมื่อหน้าเว็บโหลด
    displayRecords();
});