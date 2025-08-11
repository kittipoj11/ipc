$(function () {
    function showNotification(type, message) {
        const notif = $('#notification');
        notif.removeClass('success error').addClass(type).text(message).fadeIn();
        setTimeout(() => notif.fadeOut(), 5000);
    }

    function sendRequest(action, data) {
        const docContainer = $('.doc-container');
        const docId = docContainer.data('doc-id');
        // ใช้ user_id จาก data attribute เพื่อความสอดคล้อง
        const userId = docContainer.data('user-id');

        let requestData = {
            action: action,
            doc_id: docId,
            user_id: userId,
            ...data
        };

        $.ajax({
            url: 'api_handler.php',
            type: 'POST',
            data: requestData,
            dataType: 'json',
            beforeSend: function() {
                // Optional: disable buttons to prevent double-clicking
                $('.actions button').prop('disabled', true);
            },
            success: function (response) {
                if (response.status === 'success') {
                    showNotification('success', response.message);
                    setTimeout(() => {
                        if (action === 'create_doc_a' && response.doc_id) {
                            window.location.href = `document_view.php?doc_id=${response.doc_id}&user_id=${userId}`;
                        } else {
                            location.reload();
                        }
                    }, 1500);
                } else {
                    showNotification('error', 'Error: ' + response.message);
                }
            },
            error: function (xhr, status, error) {
                showNotification('error', 'An unexpected server error occurred: ' + error);
            },
            complete: function() {
                // Re-enable buttons
                $('.actions button').prop('disabled', false);
            }
        });
    }

    $('#btnCreateDocA').on('click', function(e) {
        e.preventDefault();
        const userId = $('.doc-container').data('user-id') || 1; // ใช้ user id ปัจจุบัน หรือ 1 ถ้าไม่มี
        sendRequest('create_doc_a', { user_id: userId });
    });

    $('#btnSave').on('click', function () {
        const title = $('#doc_title').val();
        const content = $('#doc_content').val();
        sendRequest('update_doc', { title: title, content: content });
    });

    $('#btnSubmit').on('click', function () {
        sendRequest('submit_doc');
    });

    $('#btnApprove').on('click', function () {
        sendRequest('approve_doc');
    });

    $('#btnReject').on('click', function () {
        const comments = $('#reject_comments').val().trim();
        if (!comments) {
            alert('กรุณากรอกเหตุผลในการปฏิเสธ');
            $('#reject_comments').focus();
            return;
        }
        sendRequest('reject_doc', { comments: comments });
    });
});