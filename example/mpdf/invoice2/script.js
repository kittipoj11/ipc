    $("#btnPreview").click(function(){
      $.ajax({
        url: "get_receipt.php",
        type: "GET",
        data: { id: 1 }, // ตัวอย่าง ใช้ใบเสร็จ id=1
        success: function(res){
          $("#preview").html(res);
        }
      });
    });

    // ฟังก์ชันกดพิมพ์ PDF
    function printPDF(id){
      window.open("generate_pdf.php?id="+id, "_blank");
    }