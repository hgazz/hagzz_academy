(function () {
    function isMultipartForm(form) {
        return form instanceof HTMLFormElement &&
            (form.enctype || '').toLowerCase() === 'multipart/form-data';
    }

    function describeFormData(formData) {
        var fields = [];

        formData.forEach(function (value, key) {
            if (value instanceof File) {
                fields.push({
                    key: key,
                    fileName: value.name,
                    fileType: value.type,
                    fileSize: value.size
                });
                return;
            }

            fields.push({ key: key });
        });

        return fields;
    }

    document.addEventListener('submit', function (event) {
        var form = event.target;

        if (!isMultipartForm(form)) {
            return;
        }

        var formData = new FormData(form);
        var submitButton = form.querySelector('[type="submit"]');

        if (submitButton) {
            submitButton.disabled = true;
        }

        console.info('[Upload Debug] Submitting multipart form', {
            action: form.action,
            method: form.method || 'POST',
            fields: describeFormData(formData)
        });
    }, true);
})();
