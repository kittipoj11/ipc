<!DOCTYPE html>
<html>
<head>
    <title>File Storage with MySQL, PHP PDO, and AJAX</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { padding-top: 20px; }
        .file-link { display: block; margin-bottom: 5px; }
        .file-display {
            margin-bottom: 20px; /* เพิ่มระยะห่างระหว่างไฟล์ */
            border: 1px solid #ddd; /* เพิ่มเส้นขอบ */
            padding: 10px;
            border-radius: 5px;
        }
        .file-display img {
            display: block; /* ให้รูปภาพแสดงเต็มบรรทัด */
            margin-bottom: 10px; /* เพิ่มระยะห่างใต้รูปภาพ */
        }
        .file-link {
            display: block; /* ให้ลิงก์แสดงเต็มบรรทัด */
            margin-top: 10px; /* เพิ่มระยะห่างเหนือลิงก์ */
        }
    </style>
</head>
<body>

<div class="container">
    <h2>File Storage System</h2>

    <div class="card">
        <div class="card-header">
            Upload New Record
        </div>
        <div class="card-body">
            <form id="uploadForm" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="recordName">Record Name:</label>
                    <input type="text" class="form-control" id="recordName" name="record_name" required>
                </div>
                <div class="form-group">
                    <label for="files">Upload Files (PDF or Images):</label>
                    <input type="file" class="form-control-file" id="files" name="files[]" multiple required>
                    <small class="form-text text-muted">อนุญาตเฉพาะไฟล์ PDF, JPG, PNG และขนาดไม่เกิน 2MB ต่อไฟล์</small>
                </div>
                <button type="submit" class="btn btn-primary">Upload Record and Files</button>
            </form>
            <div id="uploadStatus" class="mt-3"></div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            Records and Files
        </div>
        <div class="card-body" id="recordsDisplay">
            </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="script.js">

</script>

</body>
</html>
