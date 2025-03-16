<!DOCTYPE html>
<html>
<head>
    <title>สร้างรายงาน PDF</title>
</head>
<body>
    <h1>สร้างรายงาน PDF</h1>
    <form method="post" action="generate_pdf.php" enctype="multipart/form-data">
        <div>
            <label for="company_logo">เลือกโลโก้บริษัท:</label>
            <input type="file" name="company_logo" id="company_logo">
        </div>
        <div>
            <label for="report_title">หัวข้อรายงาน:</label>
            <input type="text" name="report_title" id="report_title" value="รายงานข้อมูล">
        </div>
        <div>
            <label for="header_text">ข้อความส่วนหัวทุกหน้า:</label>
            <input type="text" name="header_text" id="header_text" value="บริษัทตัวอย่าง จำกัด">
        </div>
        <div>
            <label for="filter_category">เลือกหมวดหมู่:</label>
            <select name="filter_category" id="filter_category">
                <option value="">ทั้งหมด</option>
                <option value="A">หมวดหมู่ A</option>
                <option value="B">หมวดหมู่ B</option>
                </select>
        </div>
        <div>
            <label>เลือกสถานะ:</label><br>
            <input type="checkbox" name="status" value="1">ใช้งาน
            <input type="checkbox" name="status" value="0">ไม่ใช้งาน
        </div>
        <div>
            <label for="search_keyword">คำค้นหา:</label>
            <input type="text" name="search_keyword" id="search_keyword">
        </div>
        <button type="submit">สร้างรายงาน PDF</button>
    </form>
</body>
</html>