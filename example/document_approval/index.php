<!DOCTYPE html>
<html lang="th">
<head>
    <title>Approval System Test</title>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        .container { border: 1px solid #ccc; padding: 15px; margin-bottom: 20px; }
        #user-selector { margin-bottom: 20px; }
        button { padding: 8px 15px; margin-right: 10px; cursor: pointer; }
    </style>
</head>
<body>
    <h1>Approval System Test Page</h1>

    <div id="user-selector">
        <strong>Test As User:</strong>
        <select id="user_id_selector">
            <option value="1">Admin User (ID 1)</option>
            <option value="2">Approver One (ID 2)</option>
            <option value="3">Approver Two (ID 3)</option>
            <option value="4">Approver Three (ID 4)</option>
            <option value="5">Final Approver (ID 5)</option>
        </select>
    </div>

    <div class="container">
        <h2>Document #<span id="doc-id">1</span></h2>
        <div id="doc-info">Loading...</div>
        <hr>
        <div id="action-panel">
            <h3>Your Action</h3>
            <textarea id="comments" placeholder="Add comments here..." rows="3" style="width: 90%;"></textarea><br><br>
            <div id="button-container"></div>
        </div>
    </div>
    
    <div id="response"></div>

<script>
    // Function to fetch and display document status
    function getDocumentStatus(docId) {
        // In a real app, you would have a 'get_status' action in api.php
        // For this example, we'll just show the buttons based on the user
        // and let the backend do the validation.
        
        // This is a simplified display logic.
        // A real app would have a dashboard showing only relevant documents.
        
        let docInfo = 'This is a test document. Check your database for the current status.';
        $('#doc-info').html(docInfo);

        let buttons = `
            <button class="action-btn" data-action="submit">Submit</button>
            <button class="action-btn" data-action="approve">Approve</button>
            <button class="action-btn" data-action="reject">Reject</button>
        `;
        $('#button-container').html(buttons);
    }

    $(document).ready(function() {
        // Initial load
        getDocumentStatus(1);
        
        // Handle user switching in the dropdown
        $('#user_id_selector').on('change', function() {
            let selectedUserId = $(this).val();
            // This is a client-side mock. The real logic is in api.php's session.
            console.log("Testing as User ID: " + selectedUserId + ". Remember to change this in api.php for actual POST requests.");
            alert("Frontend user changed. For the API call to work, please change the $_SESSION['user_id'] in api.php to " + selectedUserId);
        });


        // Handle button clicks
        $(document).on('click', '.action-btn', function() {
            let action = $(this).data('action');
            let documentId = $('#doc-id').text();
            let comments = $('#comments').val();

            $('#response').html('Processing...');

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
                    $('#response').html(`<p style="color:green;">Success: ${response.message}</p>`);
                    alert('Success: ' + response.message);
                },
                error: function(xhr) {
                    let errorMsg = xhr.responseJSON ? xhr.responseJSON.message : 'An unknown error occurred.';
                    $('#response').html(`<p style="color:red;">Error: ${errorMsg}</p>`);
                    alert('Error: ' + errorMsg);
                }
            });
        });
    });
</script>
</body>
</html>