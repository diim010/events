jQuery(document).ready(function($) {
    $('#leadForm').submit(function(e) {
        e.preventDefault(); // Prevent default form submission
        
        // Get form data
        var formData = {
            action: 'process_lead_form',
            name: $('#name-input').val(),
            phone: $('#phone-input').val(),
            email: $('#email-input').val(),
            event_id: $('#event_id').val() // Assuming you have an input field for event name with ID 'event-name-input'
            // Add other form data if necessary
        };
        
        // Perform AJAX request
        $.ajax({
            type: 'POST',
            url: ajax_object.ajax_url,
            data: formData,
            success: function(response) {
                // Handle success response
                console.log(response);
            },
            error: function(xhr, status, error) {
                // Handle error response
                console.error(error);
            }
        });
    });
});
