$(document).ajaxError(function(event, jqxhr, settings, exception) {

    if (exception == 'Unauthorized') {

        // Prompt user if they'd like to be redirected to the login page
        bootbox.confirm("Your session has expired. Would you like to be redirected to the login page?", function(result) {
            if (result) {
                window.location = '/partner/login';
            }
        });

    }
});

// disable datatables error prompt
$.fn.dataTable.ext.errMode = 'none';
