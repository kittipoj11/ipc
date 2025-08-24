<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>การแบ่งหน้าด้วย PHP และ jQuery</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        .content {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 20px;
        }

        .pagination-container {
            text-align: center;
        }

        .pagination-link {
            display: inline-block;
            padding: 8px 16px;
            margin: 0 4px;
            border: 1px solid #ddd;
            text-decoration: none;
            color: #333;
            cursor: pointer;
        }

        .pagination-link.active {
            background-color: #4CAF50;
            color: white;
            border: 1px solid #4CAF50;
        }
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            // เมื่อคลิกที่ลิงก์การแบ่งหน้า
            $('.pagination-container').on('click', '.pagination-link', function(e) {
                e.preventDefault(); // ป้องกันการเปลี่ยนหน้าแบบปกติ

                var page = $(this).attr('href').split('=')[1]; // ดึงหมายเลขหน้าจาก href

                // ส่งคำขอ AJAX ไปยัง server
                $.ajax({
                    url: 'fetch_data.php', // ไฟล์ที่จะประมวลผลคำขอ
                    type: 'GET',
                    data: {
                        page: page
                    },
                    success: function(response) {
                        $('#content-area').html(response); // อัปเดตเนื้อหาในพื้นที่แสดงผล

                        // อัปเดตสถานะของลิงก์
                        $('.pagination-link').removeClass('active');
                        $(`.pagination-link[href='?page=${page}']`).addClass('active');
                    }
                });
            });
        });
    </script>
</head>

<body>

    <h1>ตัวอย่างการแบ่งหน้า</h1>

    <div id="content-area">
        <?php
        // ข้อมูลสมมติ
        $data = [
            "รายการที่ 1",
            "รายการที่ 2",
            "รายการที่ 3",
            "รายการที่ 4",
            "รายการที่ 5",
            "รายการที่ 6",
            "รายการที่ 7",
            "รายการที่ 8",
            "รายการที่ 9",
            "รายการที่ 10",
            "รายการที่ 11",
            "รายการที่ 12",
            "รายการที่ 13",
            "รายการที่ 14",
            "รายการที่ 15"
        ];
        $items_per_page = 5;
        $total_items = count($data);
        $total_pages = ceil($total_items / $items_per_page);

        // รับหมายเลขหน้าปัจจุบันจาก URL หรือกำหนดให้เป็น 1
        $current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($current_page < 1) {
            $current_page = 1;
        }
        if ($current_page > $total_pages) {
            $current_page = $total_pages;
        }

        // คำนวณจุดเริ่มต้นของข้อมูลสำหรับหน้านี้
        $start_index = ($current_page - 1) * $items_per_page;
        $end_index = $start_index + $items_per_page;

        // แสดงผลข้อมูลสำหรับหน้านี้
        for ($i = $start_index; $i < $end_index; $i++) {
            if (isset($data[$i])) {
                echo "<div class='content'>" . $data[$i] . "</div>";
            }
        }
        ?>
    </div>

    <div class="pagination-container">
        <?php
        // สร้างลิงก์สำหรับแต่ละหน้า
        for ($i = 1; $i <= $total_pages; $i++) {
            $active_class = ($i == $current_page) ? 'active' : '';
            echo "<a href='?page=$i' class='pagination-link $active_class'>$i</a>";
        }
        ?>
    </div>

</body>

</html>