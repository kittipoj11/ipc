<!DOCTYPE html>
<html lang="th">
<head>
    <title>Document Approval</title>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body>

    <h2>Document #<span id="doc-id">101</span></h2>
    <p>Status: <strong id="doc-status">Pending Approval</strong></p>
    
    <div id="action-panel">
        <h3>Your Action</h3>
        <textarea id="comments" placeholder="Add comments here (required for rejection)"></textarea><br><br>
        
        <button class="action-btn" data-action="approve">✅ Approve</button>
        <button class="action-btn" data-action="reject">❌ Reject</button>
    </div>

<script>
$(document).ready(function() {
    $('.action-btn').on('click', function(e) {
        e.preventDefault();

        const action = $(this).data('action');
        const documentId = $('#doc-id').text();
        const comments = $('#comments').val();

        if (action === 'reject' && comments.trim() === '') {
            alert('Please provide comments for rejection.');
            return;
        }
        
        $('.action-btn').prop('disabled', true);

        $.ajax({
            url: 'api.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: action,
                document_id: documentId,
                comments: comments
            },
            success: function(response) {
                alert(response.message);
                if (response.status === 'success') {
                    window.location.reload();
                } else {
                    $('.action-btn').prop('disabled', false);
                }
            },
            error: function() {
                alert("An error occurred.");
                $('.action-btn').prop('disabled', false);
            }
        });
    });
});
</script>

</body>
</html>