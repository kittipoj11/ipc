<h1>เนื้อหาเมนูที่ 3</h1>
<p>นี่คือเนื้อหาของเมนูที่ 3</p>
<input type="text" id="content3-input" placeholder="พิมพ์อะไรบางอย่างที่นี่">
<div id="content3-keypress-result"></div>
<button id="button-content3-alert" class="content1-button">แสดง Alert จาก Content 3</button>

<script>
function initContent3() {
    $('#content3-input').on('keypress', function(event) {
        $('#content3-keypress-result').text('คุณพิมพ์: ' + event.key);
    });

    $('#button-content3-alert').on('click', function() {
        alert('ปุ่มจาก Content 3 ถูกคลิก!');
    });
}
</script>