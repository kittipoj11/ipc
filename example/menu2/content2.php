<h1>เนื้อหาเมนูที่ 2</h1>
<p>นี่คือเนื้อหาของเมนูที่ 2</p>
<input type="text" id="content2-input" placeholder="พิมพ์อะไรบางอย่างที่นี่">
<div id="content2-keypress-result"></div>
<button id="button-content2-alert" class="content1-button">แสดง Alert จาก Content 2</button>

<script>
function initContent2() {
    $('#content2-input').on('keypress', function(event) {
        $('#content2-keypress-result').text('คุณพิมพ์: ' + event.key);
    });

    $('#button-content2-alert').on('click', function() {
        alert('ปุ่มจาก Content 2 ถูกคลิก!');
    });
}
</script>