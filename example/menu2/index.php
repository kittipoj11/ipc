<!DOCTYPE html>
<html>
<head>
    <title>ระบบจัดการเมนูตามบทบาท (ตัวอย่างรวม)</title>
    <style>
        #sidebar {
            width: 200px;
            float: left;
            padding: 10px;
            border-right: 1px solid #ccc;
        }
        #content {
            margin-left: 220px;
            padding: 10px;
        }
        #sidebar-menu ul {
            list-style: none;
            padding: 0;
        }
        #sidebar-menu ul li a {
            display: block;
            padding: 8px 10px;
            text-decoration: none;
            color: #333;
        }
        #sidebar-menu ul li a:hover {
            background-color: #f0f0f0;
        }
        .content1-button {
            padding: 5px 10px;
            cursor: pointer;
        }
        #content2-input {
            padding: 5px;
        }
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>
<body>
    <div id="sidebar">
        <h2>เมนู</h2>
        <div id="sidebar-menu">
            </div>
    </div>

    <div id="content">
        <h1>หน้าหลัก</h1>
        <p>นี่คือเนื้อหาเริ่มต้นของหน้าหลัก</p>
    </div>

    <script src="script.js"></script>
</body>
</html>
