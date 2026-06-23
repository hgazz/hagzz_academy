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

        event.preventDefault();

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

        fetch(form.action, {
            method: form.method || 'POST',
            body: formData,
            credentials: 'same-origin',
            headers: {
                'Accept': 'text/html,application/xhtml+xml,application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        }).then(function (response) {
            return response.text().then(function (body) {
                if (!response.ok) {
                    console.error('[Upload Debug] Upload failed', {
                        status: response.status,
                        statusText: response.statusText,
                        url: response.url,
                        responseBody: body
                    });

                    if (window.Swal) {
                        Swal.fire('Upload failed', 'Open the browser console to see the server response.', 'error');
                    } else {
                        alert('Upload failed. Open the browser console to see the server response.');
                    }

                    return;
                }

                console.info('[Upload Debug] Upload request completed', {
                    status: response.status,
                    redirected: response.redirected,
                    url: response.url
                });

                window.location.href = response.redirected ? response.url : window.location.href;
            });
        }).catch(function (error) {
            console.error('[Upload Debug] Upload request crashed before receiving a response', error);

            if (window.Swal) {
                Swal.fire('Upload failed', error.message || 'Network error', 'error');
            } else {
                alert(error.message || 'Network error');
            }
        }).finally(function () {
            if (submitButton) {
                submitButton.disabled = false;
            }
        });
    }, true);
})();
