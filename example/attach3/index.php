<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Storage System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>File Storage System</h1>
        </div>
        <div class="upload-form card">
            <div class="card-header">
                Upload New Record
            </div>
            <div class="card-body">
                <form id="uploadForm" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="record_name">Record Name:</label>
                        <input type="text" class="form-control" id="record_name" name="record_name" required>
                    </div>
                    <div class="form-group">
                        <label for="files">Upload Files (PDF, Images, or Excel):</label>
                        <input type="file" class="form-control-file" id="files" name="files[]" multiple accept="image/*,application/pdf,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" required>
                        <small class="form-text text-muted">อนุญาตเฉพาะไฟล์ PDF, JPG, PNG, Excel (.xls, .xlsx) และขนาดไม่เกิน 2MB ต่อไฟล์</small>
                    </div>
                    <button type="submit" class="btn btn-primary">Upload Files</button>
                </form>
                <div id="uploadStatus"></div>
            </div>
        </div>

        <div class="records-display card">
            <div class="card-header">
                Stored Records
            </div>
            <div class="card-body" id="recordsDisplay">
                </div>
        </div>

        <div class="file-display card" id="fileDisplayArea" style="display:none;">
            <div class="card-header">
                File Preview:
            </div>
            <div class="card-body">
                </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="script.js"></script>
</body>
</html>