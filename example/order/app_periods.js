// app_periods_jquery.js

// $(function() { ... }); คือ document.ready ของ jQuery 
// รอให้หน้าเว็บโหลดเสร็จก่อนเริ่มทำงาน
$(function () {

    // ผูก event click กับปุ่มบันทึก
    $('#save-periods-btn').on('click', function() {

        // --- 1. รวบรวมข้อมูลจากทุกแถวในตารางด้วย jQuery ---
        const periodsData = []; // เตรียม array ว่าง

        // ใช้ .each() เพื่อวนลูปทุก <tr> ที่อยู่ใน #periods-tbody
        $('#periods-tbody tr').each(function() {
            
            // $(this) จะหมายถึง <tr> ของแถวปัจจุบัน
            const row = $(this);

            // สร้าง object สำหรับเก็บข้อมูลของแถวนี้
            const periodRecord = {
                period_id: row.data('period-id'), // .data() สำหรับดึงค่า data-*
                // ใช้ .find() เพื่อหา input ที่อยู่ในแถวนี้ แล้ว .val() เพื่อดึงค่า
                period: row.find('input[name="period"]').val(),
                work_percent: row.find('input[name="work_percent"]').val(),
                interim_payments: row.find('input[name="interim_payments"]').val(),
                interim_payment_percents: row.find('input[name="interim_payment_percents"]').val(),
                remarks: row.find('input[name="remarks"]').val(),
                action: row.find('input[name="action"]').val()
            };
            // เพิ่ม object ของแถวนี้เข้าไปใน array หลัก
            periodsData.push(periodRecord);
        });

        console.log('Data to be sent (jQuery):', periodsData);

        // --- 2. ส่งข้อมูลไปที่ Server ด้วย $.ajax ---
        if (periodsData.length === 0) {
            alert('ไม่พบข้อมูลที่จะบันทึก');
            return;//ออกจากฟังก์ชั่น $('#save-periods-btn').on('click', function(){})
        }

        $.ajax({
            url: 'api_period_handler.php',
            type: 'POST', // หรือ 'method'
            contentType: 'application/json; charset=utf-8', // บอก Server ว่าเราส่ง JSON
            dataType: 'json', // บอก jQuery ว่าเราคาดหวังจะได้รับ JSON กลับมา
            data: JSON.stringify(periodsData) // แปลง Array เป็น JSON string
        })
        .done(function(result) {
            // .done() จะทำงานเมื่อ AJAX สำเร็จ (เทียบเท่ากับ .then() บล็อกแรก)
            // 'result' จะถูกแปลงเป็น JS Object ให้อัตโนมัติ
            alert(result.message);
            if (result.status === 'success') {
                window.location.reload();
            }
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            // .fail() จะทำงานเมื่อ AJAX ล้มเหลว (เทียบเท่ากับ .catch())
            console.error('AJAX Error:', textStatus, errorThrown);
            alert('เกิดข้อผิดพลาดในการเชื่อมต่อกับเซิร์ฟเวอร์: ' + textStatus);
        });
    });
});