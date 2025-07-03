// app_order_logic.js
$(function () {
    const API_URL = 'api_order_handler.php';
    const poIdToLoad = 1001; // ★★★ ID ของ PO ที่ต้องการโหลดมาแก้ไข ★★★
    const tbody = $('#periods-tbody');
    const responseMessage = $('#response-message');
const itemsTbody = $('#items-tbody');
    const periodsTbody = $('#periods-tbody');

    function showMessage(message, isSuccess) {
        responseMessage.text(message)
            .removeClass('success error')
            .addClass(isSuccess ? 'success' : 'error')
            .show().delay(5000).fadeOut();
    }
    
    function loadData(poId) {
        responseMessage.hide();
        tbody.html('<tr><td colspan="5">กำลังโหลดข้อมูล...</td></tr>');

        $.ajax({
            url: `${API_URL}?po_id=${poId}`,
            type: 'GET', dataType: 'json'
        })
        .done(function(result) {
            if (result.status === 'success') {
                const { header, details } = result.data;
                $('#display-po-id').text(header.po_id);
                $('#po_id').val(header.po_id);
                $('#po_number').val(header.po_number);
                $('#project_name').val(header.project_name);
                $('#contract_value').val(header.contract_value);
                $('#working_date_from').val(header.working_date_from);
                $('#working_date_to').val(header.working_date_to);

                tbody.empty();
                if (details.length > 0) {
                    details.forEach(period => appendPeriodRow(period));
                }
             // Render Items
                itemsTbody.empty();
                if (items.length > 0) items.forEach(item => appendItemRow(item));
                
                // Render Periods
                periodsTbody.empty();
                if (periods.length > 0) periods.forEach(period => appendPeriodRow(period));
            } else {
                showMessage(result.message, false);
            }
        })
        .fail(() => showMessage('เกิดข้อผิดพลาดในการโหลดข้อมูล', false));
    }

    function appendPeriodRow(data = {}) {
        const periodId = data.period_id || '';
        const isNew = periodId === '';
        const row = $(`
            <tr data-period-id="${periodId}" data-crud-status="${isNew ? 'create' : 'clean'}">
                <td><input type="number" name="period_no" value="${data.period_no || ''}" class="period-no-input" readonly></td>
                <td><input type="number" name="work_percent" value="${data.work_percent || ''}" step="0.01"></td>
                <td><input type="number" name="interim_payments" value="${data.interim_payments || ''}" step="0.01"></td>
                <td><input type="text" name="remarks" value="${data.remarks || ''}"></td>
                <td class="action-cell"><button type="button" class="delete-btn">ลบ</button></td>
            </tr>
        `);
        tbody.append(row);
        updatePeriodNumbers();
    }
    
    function updatePeriodNumbers() {
        tbody.find('tr:visible').each(function(index) {
            $(this).find('.period-no-input').val(index + 1);
        });
    }

    $('#add-row-btn').on('click', () => appendPeriodRow());

    tbody.on('click', '.delete-btn', function() {
        const row = $(this).closest('tr');
        if (!confirm('คุณต้องการลบงวดงานนี้ใช่หรือไม่?')) return;
        
        if (row.data('crud-status') === 'create') {
            row.remove();
        } else {
            row.hide().data('crud-status', 'delete');
        }
        updatePeriodNumbers();
    });

    tbody.on('input', 'input', function() {
        const row = $(this).closest('tr');
        if (row.data('crud-status') === 'clean') {
            row.data('crud-status', 'update');
        }
    });

    $('#order-form').on('submit', function(event) {
        event.preventDefault();

        const headerData = {
            po_id: $('#po_id').val(), po_number: $('#po_number').val(),
            project_name: $('#project_name').val(), contract_value: $('#contract_value').val(),
            working_date_from: $('#working_date_from').val(), working_date_to: $('#working_date_to').val()
        };

        const detailsData = [];
        tbody.find('tr').each(function() {
            const row = $(this);
            const status = row.data('crud-status');
            if (status !== 'clean') {
                detailsData.push({
                    period_id: row.data('period-id'), crud_status: status,
                    period_no: row.find('input[name="period_no"]').val(),
                    work_percent: row.find('input[name="work_percent"]').val(),
                    interim_payments: row.find('input[name="interim_payments"]').val(),
                    remarks: row.find('input[name="remarks"]').val()
                });
            }
        });
        
        const payload = { header: headerData, details: detailsData };

        $.ajax({
            url: API_URL, type: 'POST',
            contentType: 'application/json', data: JSON.stringify(payload)
        })
        .done(result => {
            showMessage(result.message, result.status === 'success');
            if (result.status === 'success') {
                loadData(result.data.po_id);
            }
        })
        .fail(jqXHR => {
            const errorMsg = jqXHR.responseJSON ? jqXHR.responseJSON.message : 'เกิดข้อผิดพลาดรุนแรง';
            showMessage(errorMsg, false);
        });
    });

    loadData(poIdToLoad);
});