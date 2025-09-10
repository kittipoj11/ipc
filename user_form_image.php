<?php
// db connect
// $conn = new mysqli("localhost", "root", "", "testdb");
// if ($conn->connect_error) { die("DB Fail: " . $conn->connect_error); }
// ?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>Upload รูปภาพ</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="p-4">

  <!-- ปุ่มเปิด modal -->
  <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#imageModal">เลือกรูป</button>

  <!-- input แก้ไขชื่อไฟล์ -->
  <div class="mt-3">
    <input type="text" id="txtFileName" class="form-control w-50" placeholder="ชื่อไฟล์" readonly>
  </div>

  <!-- preview หลัก -->
  <div class="mt-3">
    <img id="mainPreview" src="" alt="" style="max-width:300px; display:none;" class="border rounded">
  </div>

  <!-- ปุ่ม save -->
  <div class="mt-3">
    <button class="btn btn-success" id="btnSave">Save</button>
  </div>

  <!-- Modal -->
  <div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">เลือกไฟล์รูปภาพ</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="file" id="fileInput" accept="image/jpeg" class="form-control">
          <img id="imgModalPreview" src="" alt="" style="max-width:100%; margin-top:10px; display:none;" class="border rounded">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" id="btnOk">OK</button>
        </div>
      </div>
    </div>
  </div>

<script>
let selectedFile; // เก็บไฟล์ที่เลือก

// preview บน modal
$("#fileInput").on("change", function(e){
  const file = e.target.files[0];
  if(file){
    selectedFile = file;
    const reader = new FileReader();
    reader.onload = function(ev){
      $("#imgModalPreview").attr("src", ev.target.result).show();
    }
    reader.readAsDataURL(file);
  }
});

// กด OK -> แสดงที่หน้าหลัก
$("#btnOk").on("click", function(){
  if(selectedFile){
    $("#mainPreview").attr("src", URL.createObjectURL(selectedFile)).show();
    $("#txtFileName").val(selectedFile.name);
    // $("#imageModal").modal("hide");
  }
});

// กด Save -> upload
$("#btnSave").on("click", function(){
  if(!selectedFile){ alert("กรุณาเลือกรูปก่อน"); return; }

  let formData = new FormData();
  formData.append("file", selectedFile);
  formData.append("filename", $("#txtFileName").val());

  $.ajax({
    url: "upload.php",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    success: function(res){
      alert(res);
    }
  });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
