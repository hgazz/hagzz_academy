// Flatpickr

function initializeFlatpickr(selector, options) {
    var element = document.getElementById(selector);

    if (!element || typeof flatpickr === 'undefined') {
        return null;
    }

    return flatpickr(element, options);
}

var f1 = initializeFlatpickr('basicFlatpickr', {
    defaultDate: new Date()
});
var f2 = initializeFlatpickr('dateTimeFlatpickr', {
    enableTime: true,
    dateFormat: "Y-m-d H:i",
    defaultDate: new Date()
});
var f3 = initializeFlatpickr('rangeCalendarFlatpickr', {
    mode: "range",
});
var f4 = initializeFlatpickr('timeFlatpickr', {
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i",
    defaultDate: "13:45",
});
