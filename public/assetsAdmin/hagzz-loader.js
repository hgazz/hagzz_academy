(function () {
    function hideInitialLoader() {
        var loader = document.getElementById('load_screen');
        if (!loader) return;

        loader.classList.add('hagzz-loader-hide');
        window.setTimeout(function () {
            if (loader && loader.parentNode) {
                loader.parentNode.removeChild(loader);
            }
        }, 260);
    }

    function getOverlay() {
        var overlay = document.getElementById('hagzzPageTransition');
        if (overlay) return overlay;

        overlay = document.createElement('div');
        overlay.id = 'hagzzPageTransition';
        overlay.className = 'hagzz-page-transition is-hidden';
        overlay.innerHTML = [
            '<div class="hagzz-page-transition__content" role="status" aria-live="polite">',
            '<div class="hagzz-loader-mark">H</div>',
            '<div class="hagzz-loader-copy">',
            '<strong data-hagzz-loader-title>جار تنفيذ الطلب</strong>',
            '<span data-hagzz-loader-message>نجهز الصفحة التالية<span class="hagzz-loader-dots"><i></i><i></i><i></i></span></span>',
            '</div>',
            '</div>'
        ].join('');
        document.body.appendChild(overlay);
        return overlay;
    }

    function showTransition(title, message) {
        var overlay = getOverlay();
        var titleElement = overlay.querySelector('[data-hagzz-loader-title]');
        var messageElement = overlay.querySelector('[data-hagzz-loader-message]');

        if (titleElement && title) titleElement.textContent = title;
        if (messageElement && message) {
            messageElement.innerHTML = message + '<span class="hagzz-loader-dots"><i></i><i></i><i></i></span>';
        }

        overlay.classList.remove('is-hidden');
    }

    function isPlainNavigationLink(link) {
        if (!link || !link.href) return false;
        if (link.target && link.target !== '_self') return false;
        if (link.hasAttribute('download')) return false;
        if (link.href.indexOf('#') === link.href.length - 1) return false;
        if (link.href.indexOf('javascript:') === 0) return false;

        var currentOrigin = window.location.origin;
        return link.href.indexOf(currentOrigin) === 0;
    }

    function bindNavigationFeedback() {
        document.addEventListener('submit', function (event) {
            var form = event.target;
            if (!form || form.dataset.noLoader === 'true') return;
            if (form.method && form.method.toLowerCase() === 'get') return;

            showTransition(
                document.documentElement.lang === 'ar' ? 'جار حفظ التغييرات' : 'Saving changes',
                document.documentElement.lang === 'ar' ? 'نرسل البيانات بأمان' : 'Sending your data securely'
            );
        }, true);

        document.addEventListener('click', function (event) {
            var link = event.target.closest && event.target.closest('a');
            if (!isPlainNavigationLink(link)) return;
            if (link.dataset.noLoader === 'true') return;
            if (event.defaultPrevented || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;

            showTransition(
                document.documentElement.lang === 'ar' ? 'جار فتح الصفحة' : 'Opening page',
                document.documentElement.lang === 'ar' ? 'لحظة واحدة فقط' : 'Just a moment'
            );
        }, true);
    }

    window.HagzzLoader = {
        show: showTransition,
        hide: function () {
            var overlay = document.getElementById('hagzzPageTransition');
            if (overlay) overlay.classList.add('is-hidden');
        }
    };

    window.addEventListener('load', hideInitialLoader);
    window.setTimeout(hideInitialLoader, 2200);

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bindNavigationFeedback);
    } else {
        bindNavigationFeedback();
    }
})();
