<h1>เนื้อหาเมนูที่ 1</h1>
<p>นี่คือเนื้อหาของเมนูที่ 1</p>
<button id="button-content1-action" class="content1-button">คลิกเพื่อทำงานบางอย่างใน Content 1</button>
<div id="content1-result"></div>

<script>
function initContent1() {
    $('#button-content1-action').on('click', function() {
        $('#content1-result').text('ปุ่มใน Content 1 ถูกคลิกและทำงานแล้ว!');
    });
}
</script>
