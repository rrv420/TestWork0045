jQuery(document).ready(function($) {
    $('#city-search-input').on('input', function() {
        var searchQuery = $(this).val();

        $.ajax({
            url: ajax_data.ajaxurl, // Correct usage of localized script object
            type: 'POST',
            data: {
                action: 'search_cities', // AJAX action name
                search: searchQuery
            },
            success: function(response) {
                $('#cities-table-body').html(response); // Update the table body with response
            },
            error: function(xhr, status, error) {
                console.log('AJAX Error: ' + error); // Log any AJAX errors
            }
        });
    });
});
