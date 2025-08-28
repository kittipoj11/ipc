<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>ระบบใบเสร็จ (Preview + PDF)</title>

  <!-- Bootstrap 5.0 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="container py-4">

  <h2 class="mb-4">ตัวอย่างระบบออกใบเสร็จ</h2>

  <div class="mb-3">
    <button id="btnPreview" class="btn btn-primary">พิมพ์ใบเสร็จ (แสดง Preview)</button>
    <span class="text-muted ms-2">ตัวอย่างนี้ดึงใบเสร็จ <strong>ID = 1</strong> จากฐานข้อมูล</span>
  </div>

  <div id="preview" class="border rounded p-3 bg-light"></div>

  <script>
    $("#btnPreview").on("click", function () {
      $.ajax({
        url: "get_receipt.php",
        method: "GET",
        data: { id: 1 },
        success: function (html) {
          $("#preview").html(html);
        },
        error: function (xhr) {
          alert("ดึงข้อมูลไม่สำเร็จ: " + xhr.responseText);
        }
      });
    });

    function printPDF(id) {
      window.open("generate_pdf.php?id=" + encodeURIComponent(id), "_blank");
    }
  </script>
</body>
</html>
