// Disable DataTables error prompt
if (window.jQuery && $.fn && $.fn.dataTable && $.fn.dataTable.ext) {
    $.fn.dataTable.ext.errMode = 'none';
}

if (window.jQuery) {
    $(document).ajaxError(function(event, jqxhr, settings, exception) {
        if (jqxhr.status === 401 || exception === 'Unauthorized') {
            var langMeta = document.querySelector('meta[name="lang"]');
            var locale = langMeta ? langMeta.getAttribute('content') : 'en';
            var logoutUrl = '/' + locale + '/partner/logout';

        // Prompt user if they'd like to be redirected to the login page
            if (window.bootbox) {
                bootbox.confirm("Your session has expired. Would you like to be redirected to the login page?", function(result) {
                    if (result) {
                        window.location.href = logoutUrl;
                    }
                });
            } else {
                window.location.href = logoutUrl;
            }
        }
    });
}

