console.log("hellow there. - from employee search page.");
jQuery(document).ready(function($) {
    $('#search_button').on('click', function() {
        var searchQuery = $('#search_employee').val();
        console.log(" ** check ajax url ", ajax_object);
        $.ajax({
            url: ajax_object.ajax_url,
            method: 'POST',
            data: {
                action: 'search_employees',
                search: searchQuery
            },
            success: function(response) {
                $('#employee_list').html(response);
            },
            error: function() {
                $('#employee_list').html('<p>An error occurred while searching for employees.</p>');
            }
        });
    });
});